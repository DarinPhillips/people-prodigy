<?
include_once("../session.php");
if(($accept=="")&&($reject==""))
{
	include_once("header.php");
}
class verification
{
	function view_form($db_object,$common,$default,$user_id,$uid)
	{
		
		$path = $common->path;
		$filename = $path."templates/performance/performance_verification.html";
		$file = $common->return_file_content($db_object,$filename);
	//table
		$user_table = $common->prefix_table("user_table");
		$fieldtable = $common->prefix_table("name_fields");
		$app_feedback = $common->prefix_table("approved_feedback");
		$app_userobjective = $common->prefix_table("approved_userobjective");
		$app_selected_obj = $common->prefix_table("approved_selected_objective");

	//approved feedback table
		$appqry = "select fd_id,user_id,o_id,approved_date,idelivered from $app_feedback where 
				boss_id='$user_id' and user_id='$uid' and active='A' and status=1 order by fd_id";
		$appres = $db_object->get_rsltset($appqry);

		
		$val['empname'] = $common->name_display($db_object,$uid);
		$val['uid'] = $uid;
		
		$pattern = "/<{record_loopstart}>(.*?)<{record_loopend}>/s";
		preg_match($pattern,$file,$arr);
		$match=$arr[0];
		$str="";
		for($i=0;$i<count($appres);$i++)
		{
			$o_id = $appres[$i]['o_id'];
			$selqry = "select sl_id,objective_$default as objective,committed_no
			 	from $app_selected_obj where user_id='$uid' and o_id='$o_id'
				order by sl_id";

			$selres = $db_object->get_a_line($selqry);
			$date = $appres[$i]['approved_date'];
			$sp = split("-",$date);
			$spl  = split(" ",$sp[2]);
			$fdate = $sp[1]."/".$spl[0]."/".$sp[0];
			$objective = $selres['objective'];
			$Delivered = $appres[$i]['idelivered'];
			$fdid = $appres[$i]['fd_id'];
			$slid  = $selres['sl_id'];
			$str.=preg_replace("/\<\{(.*?)\}\>/e","$$1",$match);
		}//i loop
		$file = preg_replace($pattern,$str,$file);
		$file = $common->direct_replace($db_object,$file,$val);
		echo $file;
	}//end view
	function rejected_verification($db_object,$common,$default,$user_id,$post_var)
	{
		$key_array = array();
		$app_feedback = $common->prefix_table("approved_feedback");
		$user_table = $common->prefix_table("user_table");
		$message_table = $common->prefix_table("performance_message");
		while(list($key,$value)=each($post_var))
		{
			$$key = $value;
			//echo "key=$key : value=$value<br>";
			if(ereg("^fDelivered_",$key))
			{
				$key_array[] = $key;
			}			
		}
			$fdid_array = array();
			for($i=0;$i<count($key_array);$i++)
			{
				$key = $key_array[$i];				
				list($name,$fdid,$slid) = split("_",$key);
				$fdid_array[] = $fdid;				
			}
			$fid = @implode("','",$fdid_array);
			
			$qry = "Update $app_feedback set reject='R' where fd_id in ('$fid')";
			$db_object->insert($qry);
			
			$mailqry = "select email from $user_table where user_id='$user_id'";
			$mailres = $db_object->get_a_line($mailqry);

			$userqry = "select username from $user_table where user_id='$uid'";
			$userres = $db_object->get_a_line($userqry);
			$username = $userres['username'];
		
			$messqry = "select verification_remind_sub_$default as subject,
					verification_remind_message_$default as message
					from $message_table ";
			$messres = $db_object->get_a_line($messqry);
			$to = $mailres['email'];
			$from = $to;
			$subject = $messres['subject'];
			$message = $messres['message'];
			$path = $common->http_path;
			$path = $path."/index.php";
			$message = preg_replace("/{{(.*?)}}/e","$$1",$message);
			
			$common->send_mail($to,$subject,$message,$from);							
	}//end reject

	function accepted_verification($db_object,$common,$default,$user_id,$post_var)
	{
		$key_array = array();
		$app_feedback = $common->prefix_table("approved_feedback");
		$user_table = $common->prefix_table("user_table");
		$message_table = $common->prefix_table("performance_message");
		$position = $common->prefix_table("position");
		$verified_user = $common->prefix_table("verified_user");
		
		while(list($key,$value)=each($post_var))
		{
			$$key = $value;
			//echo "key=$key : value=$value<br>";
			if(ereg("^fDelivered_",$key))
			{
				$key_array[] = $key;
			}			
		}

		for($i=0;$i<count($key_array);$i++)
		{
			$key = $key_array[$i];
			list($name,$fdid,$slid)=split("_",$key);
			$committed = $$key;
			$qry = "update $app_feedback set status='2',vaccept='A',accept_date=now() where fd_id='$fdid'";
			$db_object->insert($qry);
			$delivered = "fDelivered_".$fdid."_".$slid;
			$delive = $$delivered;
			$vqry = "insert into $verified_user set delivered='$delive',verified_user_id='$user_id',
				verified_date=now(),for_user_id='$uid',sl_id='$slid'";			
			$db_object->insert($vqry);
			
			$bqry = "select fd_id,boss_id  from $app_feedback where raters_id='$user_id' and user_id='$user_id' and
				active='A'";
			$bres = $db_object->get_rsltset($bqry);						

		}

		$boss_id=$common->immediate_boss($db_object,$uid);															
	}//end accept
}//end class
	$ob = new verification;
	if($reject!="")
	{
		$ob->rejected_verification($db_object,$common,$default,$user_id,$post_var);
		header("location:per_setting.php");
	}
	if($accept!="")
	{
		$ob->accepted_verification($db_object,$common,$default,$user_id,$post_var);
		header("location:per_setting.php");
	}
	
	$ob->view_form($db_object,$common,$default,$user_id,$uid);
include_once("footer.php");
?>
