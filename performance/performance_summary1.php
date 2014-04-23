<?
include_once("../session.php");
include_once("header.php");
class summary
{
	function view_form($db_object,$common,$default,$user_id,$uid,$gbl_met_value,$err,$mailto)
	{
	//table declaration
		$app_sel_objective = $common->prefix_table("approved_selected_objective");
		$user_table = $common->prefix_table("user_table");
		$approved_feedback = $common->prefix_table("approved_feedback");
		$config_table = $common->prefix_table("config");
		$rating_table = $common->prefix_table("rating");
		$performance_feedback = $common->prefix_table("performance_feedback");
		$approved_affected = $common->prefix_table("approved_affected");
		$position_table = $common->prefix_table("position");
		$approveduser_objective = $common->prefix_table("approveduser_objective");
		$verified_user = $common->prefix_table("verified_user");

	//check valid user
		if($uid!="")
		{
			$val['uid'] = $uid;
			$psel = "select position from $user_table where user_id='$user_id'";
			$pres = $db_object->get_a_line($psel);
			$position = $pres['position'];
			$below = $common->get_chain_below($position,$db_object,$arr);
			$below_user = $common->get_user_id($db_object,$below);
			$spt_user = array();
			for($c=0;$c<count($below_user);$c++)
			{
				$spt_user[] = $below_user[$c]['user_id'];
			}
	
			$split = @implode("','",$spt_user);

			if($user_id!=1)
			{
				$selqry="select user_id from $user_table where admin_id='$user_id' and $user_table.user_id not in('$split')";
			}
			else
			{
				$selqry="select $user_table.user_id from $user_table,$position_table where
					 $user_table.position=$position_table.pos_id and ($user_table.position<>NULL or $user_table.position<>0) and
					 $user_table.user_id!=1 and $user_table.user_id not in('$split')
					 order by $position_table.level_no desc";			
			}

			$userset=$db_object->get_single_column($selqry);

			if(!is_array($userset))
			{
				$userset = array();
			}
			
			for($b=0;$b<count($below_user);$b++)
			{	
				$userset[] = $below_user[$b]['user_id'];		
			}		

		}

		if($uid!="")
		{
			$user_id  = $uid;
		}
		else
		{
			$userset[] = $user_id;
		}
//if the person is an valin user
if(in_array($user_id,$userset))
{

		
			$val['uid']  = $uid;
			$path = $common->path;
		$filename = $path."templates/performance/performance_summary1.html";
		$file = $common->return_file_content($db_object,$filename);
	
		$name = $common->name_display($db_object,$user_id);
		$val['uname'] = $name;

	//from user_table
		$userqry = "select username,position from $user_table where user_id='$user_id'";
		$userres = $db_object->get_a_line($userqry);
		$val['username'] = $userres['username'];
		$position = $userres['position'];

	//immediate boss
		$bossid = $common->immediate_boss($db_object,$user_id);

	//from user table 
		$bossqry = "select username from $user_table where user_id = '$bossid'";
		$boss = $db_object->get_a_line($bossqry);
		$val['bossname'] = $boss['username'];
		
	//from approved_selected_objective		
		$selobj = "select sl_id,o_id,objective_$default as objective,priority,committed_no,percent from $app_sel_objective 
				where user_id='$user_id' and status='A' order by sl_id";
		$selres = $db_object->get_rsltset($selobj);
		$countselres = count($selres);

		
	//from Config
		$boss=0;
		$conqry = "select person_affected from $config_table";
		$conres = $db_object->get_a_line($conqry);
		$noofperson = $conres['person_affected'];
		$boss = 1;	
	//Total rater is 4 (without self),noofperson(the raters we have selected) + boss (boss's rating)
		$totalperson = $noofperson + $boss;	
	//from rating
		$ratqry = "select rval from $rating_table where rval='$gbl_met_value'";
		$ratres = $db_object->get_a_line($ratqry);
		$r_val = $ratres['rval'];

	//met expectation value;
		$metexpectation = $r_val * $totalperson;


	//onjective loop
		$pattern = "/<{objective_loopstart}>(.*?)<{objective_loopend}>/s";
		preg_match($pattern,$file,$arr);
		$match = $arr[0];
		$str="";
	//raters loop
		$pattern1 = "/<{rater_loopstart}>(.*?)<{rater_loopend}>/s";
		preg_match($pattern1,$file,$arr1);
		$match1 = $arr1[0];
		$str1="";
		$count = 0;

	//if no record
		$pattern2 = "/<!--ifrecord_loopstart-->(.*?)<!--ifrecord_loopend-->/s";


	//main loop start
	//last metric approved(Active state)

		for($i=0;$i<count($selres);$i++)
		{
			$str1="";
			$actual="";
			$Cfulfill = "";
			$count = $count + 1;
			$objective = $selres[$i]['objective'];			
			$o_id = $selres[$i]['o_id'];
			$priority = $selres[$i]['priority'];
			$sl_id = $selres[$i]['sl_id'];
			$checkcumulative = $selres[$i]['percent'];

		//get all  metrics for the given o_id
			$oqry = "select met_id from $approveduser_objective where o_id='$o_id' and 
				user_id='$user_id'";
			$ores = $db_object->get_a_line($oqry);
			$met_id = $ores['met_id'];
			$mqry = "select o_id from $approveduser_objective where met_id='$met_id' and 
				user_id='$user_id'";
			$mres = $db_object->get_single_column($mqry);
			$aver  = count($mres);
			$oid = implode("','",$mres);				
		
		//get the raters rated value
			$Ratervalue = "select sum(r_id) from $approved_feedback where o_id in ('$oid')
					 and user_id='$user_id' and status<>'1' and status<>'2'";
			$Resvalue = $db_object->get_single_column($Ratervalue);
			$actual = $Resvalue[0];
			$actual = @($actual/$aver);
			$actual = @sprintf("%01.2f",$actual);
		//oid - diff o_id with same metrics

		$get = $common->get_fullfilled($db_object,$o_id,$user_id,$dates);

			
		//calculation for met expectation value
			$expected = @($actual/$metexpectation);
			$expected = $expected * 100;
		//-------
			$fulfilled = $get['Cfulfill'];
			
		//All form display value
			$expected = @sprintf("%01.2f",$expected);
			$fulfilled = @sprintf("%01.2f",$fulfilled);
			$committed = $get['Ccommit'];
			$accomplish = $get['Caccomplish'];
		
		$perqry1 = "select user_id from $performance_feedback where request_from='$user_id' and 
				status='A' and latest='N' and sl_id='$sl_id' and user_id<>'$user_id' and user_id<>'$bossid'";
			$perres1 = $db_object->get_single_column($perqry1);

		//raters loop
			 //$perres1 - (no of raters who has finished rating)
			for($j=0;$j<count($perres1);$j++)
			{
				$cnt = $j+1;
				$rater_id = $perres1[$j];
				$raterqry = "select username from $user_table where
						 user_id='$perres1[$j]'";
				$raterres = $db_object->get_a_line($raterqry);
				$ratername = $raterres['username'];
				$str1.= preg_replace("/<{(.*?)}>/e","$$1",$match1);
			}
			$temp = preg_replace($pattern1,$str1,$match);
		$str.=preg_replace("/<{(.*?)}>/e","$$1",$temp);		
		}//main loop end
		$file = preg_replace($pattern,$str,$file);
		$mail = "select email from $user_table where user_id='$user_id'";
		$mailres = $db_object->get_a_line($mail);
		$val['email'] = $mailres['email'];
				
		$pattern3 = "/<!--ifself_loopstart-->(.*?)<!--ifself_loopend-->/s";
		if($uid=="")
		{
			$file = preg_replace($pattern3,"",$file);
		}

		if($countselres==0)//selif starts
		{
			$space="";
			$val['norecord'] = $err['cEmptyrecords'];
			$file  = preg_replace($pattern2,$space,$file);
			$file = preg_replace("/<!--backstart/s",$space,$file);
			$file = preg_replace("/backend-->/s",$space,$file);
		}				
			
		$file = $common->direct_replace($db_object,$file,$val);
		echo $file;
}
else
{
	echo $err['cSorrynotallowed'];
}
	}//end view
	
	
	
}//end class
	$ob = new summary;

	$ob->view_form($db_object,$common,$default,$user_id,$uid,$gbl_met_value,$error_msg,$mailto);
include_once("footer.php");
?>

