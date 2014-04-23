<?
include_once("../session.php");
include_once("header.php");
class alert
	{
	function view($db_object,$common,$user_id,$err)
		{
		
			$path = $common->path;
			$path = $path."/templates/performance/performance_alert.html";
			$file = $common->return_file_content($db_object,$path,$user_id);
			$unappcat = $common->prefix_table("unapproved_category");
			$alert_table = $common->prefix_table("performance_alert");
			$user = $common->prefix_table("user_table");
			$reject = $common->prefix_table("rejected_objective");
			$app_feedback = $common->prefix_table("approved_feedback");

	//Plan approval
			$qry = "select user_id from $unappcat where status='NP' group by user_id";			
			$res = $db_object->get_single_column($qry);

			$rejqry = "select user_id from $unappcat where status='RJ' and  user_id='$user_id' group by user_id";
			$rejres = $db_object->get_single_column($rejqry);

	//objective approval
			$usqry  = "select position from $user where user_id='$user_id'";
			$usres = $db_object->get_a_line($usqry);

			$bossid = $usres['position'];

			$aqry ="select user_id from $alert_table where boss_id='$bossid'";
			$ares = $db_object->get_single_column($aqry);

			$rqry = "select count(r_id),user_id,boss_id from $reject where user_id='$user_id' group by r_id";
			$rres = $db_object->get_a_line($rqry);
			//echo "res = $rqry<br>";
	//approved feedback table
			$appres = array();
			$appqry = "select user_id from $app_feedback where boss_id='$user_id' and status='1' and active='A' and (reject is null or reject='') group by user_id";	
			$appres = $db_object->get_rsltset($appqry);
			$ct = $rres[0];

	//Displaying Plan approval
			$pattern4="/<!--planapproval_start-->(.*?)<!--planapproval_end-->/s";
			$space="";
	
			if(count($res)==0)
			{
				$file = preg_replace($pattern4,$space,$file);
			}
	//Performance Plan Approval
			$pattern="/<{record_loopstart(.*?)<{record_loopend}>/s";
			preg_match($pattern,$file,$arr);
			$match=$arr[0];
			$str="";
			for($i=0;$i<count($res);$i++)
			{
				$qry = "select username from $user where user_id='$res[$i]'";
				$rs = $db_object->get_a_line($qry);
				$userid = $res[$i];
				$uname = $rs[0];
				$str.=preg_replace("/\<\{(.*?)\}\>/e","$$1",$match);
			}			
			$file=preg_replace($pattern,$str,$file);

	//Performance Objective Approval
			$pattern6 = "/<!--objectiveapproval_start-->(.*?)<!--objectiveapproval_end-->/s";	
			if(count($ares)==0)
			{
				$file = preg_replace($pattern6,$space,$file);
			}
			
				
			$pattern1 = "/<{objective_loopstart(.*?)<{objective_loopend}>/s";	
			preg_match($pattern1,$file,$arr1);
			$match1=$arr1[0];
			$str1="";
			for($i=0;$i<count($ares);$i++)
			{
				$qry = "select first_name,last_name,username from $user where user_id='$ares[$i]'";				
				$rs = $db_object->get_a_line($qry);
				$userid = $ares[$i];
				$uname = $rs['username'];
				$str1.=preg_replace("/\<\{(.*?)\}\>/e","$$1",$match1);
			}			
			$file=preg_replace($pattern1,$str1,$file);
		
	//Rejected Objectives			
			$pattern8 ="/<!--rejected_objective_start-->(.*?)<!--rejected_objective_end-->/s";
				if($ct=="")
				{
					$file = preg_replace($pattern8,$space,$file);
				}
				$rrs = $rres['user_id'];
				$qry = "select first_name,last_name,username from $user where user_id='$rrs'";
				$rs = $db_object->get_a_line($qry);
				$val[rej_userid] = $rres['user_id'];
				$val[rej_uname] = $rs['username'];
				if($ct!=0)
				{
					$val[rej_mess] = $err["cResubmitagain"];
				}
	//Performance Verification
		$pattern7 = "/<!--performance_verificationstart-->(.*?)<!--performance_verificationend-->/s";
		if(count($appres)==0)
		{
			$file = preg_replace($pattern7,$space,$file);
		}
		$pattern3="/<{verification_loopstart}>(.*?)<{verification_loopend}>/s";
		preg_match($pattern3,$file,$arr3);
		$match3 = $arr3[0];
		$str3 = "";
		for($i=0;$i<count($appres);$i++)
		{
			$id  = $appres[$i][user_id];
			$selvname = "select username from $user where user_id='$id'";
			$vres = $db_object->get_a_line($selvname);
			$vname = $vres['username'];
			$uid = $id;
			$val['PerverApproval']= $err['cPerverApproval'];
			$str3.=preg_replace("/<{(.*?)}>/e","$$1",$match3);
		}
		$file = preg_replace($pattern3,$str3,$file);	
	//reject plan display
		$pattern5 ="/<!--rejectplan_start-->(.*?)<!--rejectplan_end-->/s";
		if(count($rejres)==0)
		{
			$file = preg_replace($pattern5,$space,$file);
		}
	//Rejected Plan
		$pattern2 = "/<{rejected_loopstart(.*?)<{rejected_loopend}>/s";
		preg_match($pattern2,$file,$arr2);
		$match2 = $arr2[0];
		$str2="";
		for($i=0;$i<count($rejres);$i++)
		{
			$uid = $rejres[$i];
			$selqry = "select username from $user where user_id='$uid'";
			$selres = $db_object->get_a_line($selqry);
			$uname = $selres['username'];
			$str2.=preg_replace("/<{(.*?)}>/e","$$1",$match2);
		}
		$file = preg_replace($pattern2,$str2,$file);
		$file = $common->direct_replace($db_object,$file,$val);
		echo $file;			
		}//end view
	}//end class
	$ob = new alert;
	$ob->view($db_object,$common,$user_id,$error_msg);
include_once("footer.php");
?>
