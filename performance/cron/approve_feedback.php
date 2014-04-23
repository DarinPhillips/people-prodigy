<?
include_once("../../includes/database.class");
include_once("../../includes/common.class");
include_once("../../lang/1/lang.php");
$common  = new common;
$db_object = new database;
class cron
{
	function run_script($db_object,$common,$err)
	{
		$feedback = $common->prefix_table("performance_feedback");
		$approved_affected = $common->prefix_table("approved_affected");
		$usertable = $common->prefix_table("user_table");
		$performance_message = $common->prefix_table("performance_message");
		$default = 1;

		$qry = "select frequency,$approved_affected.user_id as userid,$feedback.request_from as requestfrom, 
			s_date,f_id from $approved_affected,$feedback where $approved_affected.sl_id=$feedback.sl_id and
			 $approved_affected.user_id=$feedback.user_id and $feedback.status='I'";
		$res  = $db_object->get_rsltset($qry);
		
		$messqry = "select app_feedback_subject_$default as subject,app_feedback_message_$default
				 as message from $performance_message ";
		$messres = $db_object->get_a_line($messqry);
	
		$subject = $messres['subject'];
		$message = $messres['message'];

		$fromqry = "select username,email from $usertable where user_id='1'";
		$fromres = $db_object->get_a_line($fromqry);
		$fromusername = $fromres['username'];
		$from = $fromres['email'];

		for($i=0;$i<count($res);$i++)
		{
			$userid = $res[$i]['userid'];	
			$requestfrom = $res[$i]['requestfrom'];
			$frequency   = $res[$i]['frequency'];
			$sdate = $res[$i]['s_date'];
			$fid = $res[$i]['f_id'];


		//raters
			$userqry = "select username,password,email  from $usertable
				 where user_id='$userid'";
			$userres = $db_object->get_a_line($userqry);
			$username = $userres['username'];
		//users
			$reqqry = "select username,password,email from $usertable
					where user_id='$requestfrom'";
			$reqres = $db_object->get_a_line($reqqry);
			$to = $reqres['email'];
			$path = $common->http_path;
			$path = $path."/performance/selected_for_feedback.php";
			$mess = preg_replace("/{{(.*?)}}/e","$$1",$message);

		//send mail to all the selected person to give feedback
			$common->send_mail($to,$subject,$from,$mess);
			//echo "to - $to : mess = $mess : from = $from :freq = $frequency<br> ";					
					
		}
		
		//check the frequency
		
		$freqry = "select frequency,$approved_affected.user_id as userid,$feedback.request_from as requestfrom, 
			s_date,f_id,status from $approved_affected,$feedback where $approved_affected.sl_id=$feedback.sl_id and
			 $approved_affected.user_id=$feedback.user_id and (to_days(now())- to_days($feedback.s_date)) = $approved_affected.frequency";
		$freqres = $db_object->get_rsltset($freqry);
					
			for($j=0;$j<count($freqres);$j++)
			{
				$status = $freqres[$j]['status'];
				$fid = $freqres[$j]['f_id'];
			//A = APPROVED
				if($status=='A')
				{
				
					$update = "update $feedback set status='I',s_date=now(),latest='' where f_id='$fid'";
					$db_object->insert($update);
				}
			}

			
		echo $err['cMailsent'];

	}
}//end class
	$ob = new cron;
	$ob->run_script($db_object,$common,$error_msg);
?>
