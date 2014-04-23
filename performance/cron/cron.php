<?
include_once("../../includes/database.class");
include_once("../../includes/common.class");
include_once("../../lang/1/lang.php");
$common  = new common;
$db_object = new database;
class cron
{

/* 			--Approve Objective Starts--
The below function sends mail to the boss who have to approve the objective set by the 
users who are all under his chain of command
*/
	function approve_objective($db_object,$common,$err)
	{						
		$alert_table = $common->prefix_table("performance_alert");
		$user_table = $common->prefix_table("user_table");
		$message_table  = $common->prefix_table("performance_message");
		$default = 1;
		$messqry = "select obj_subject_$default as subject,obj_message_$default as message from
				$message_table";
		$messres = $db_object->get_a_line($messqry);
		$subject = $messres['subject'];
		$message = $messres['message'];

		$aqry ="select boss_id,user_id from $alert_table";
		$ares = $db_object->get_rsltset($aqry);
		$bossid =array();
		$userid = array();
		
		for($i=0;$i<count($ares);$i++)
		{
			$bossid = $ares[$i]['boss_id'];
			$userid = $ares[$i]['user_id'];
			$userqry = "select username,password,email from $user_table where user_id='$userid'";
			$userres = $db_object->get_a_line($userqry);
			$bossqry = "select username,password,email from $user_table where position='$bossid'";
			$bossres = $db_object->get_a_line($bossqry);
		//send mail
			$to = $bossres['email'];
			$tousername = $bossres['username'];
			$from  = $userres['email'];
			$username = $userres['username'];
			$path = $common->http_path;
			$path = $path."/performance/approve_objective_list.php";
			$mess = preg_replace("/{{(.*?)}}/e","$$1",$message);
			//echo "to - $to : mess = $mess : from = $from <br> ";
			if($to!="")
			{
				$common->send_mail($to,$subject,$mess,$from);	
			}
					
		}
	}//end approve_objective



/*			--Approve_feedback starts here--
	The below function sends mail to the users who have to give 
	feedback to the requested users.
*/
	function approve_feedback($db_object,$common,$err)
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
			if($to!="")
			{
				$common->send_mail($to,$subject,$from,$mess);
			}
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
			
		//echo $err['cMailsent'];
	}//end approve_feedback


/*			--Resubmit obnjective starts--

The below function sends mail to the users who's plan has been rejected
*/
	function resubmit_objective($db_object,$common,$err)
	{						
		$reject_table = $common->prefix_table("rejected_objective");
		$user_table = $common->prefix_table("user_table");
		$message_table  = $common->prefix_table("performance_message");
		$default = 1;
		$messqry = "select reject_subject_$default as subject,reject_message_$default as message from
				$message_table";
		$messres = $db_object->get_a_line($messqry);
		$subject = $messres['subject'];
		$message = $messres['message'];	
		$qry = "select user_id,boss_id from $reject_table";
		$res = $db_object->get_rsltset($qry);
		for($i=0;$i<count($res);$i++)
		{	
			$userid = $res[$i]['user_id'];
			$bossid = $res[$i]['boss_id'];
		
			$userqry = "select username,password,email from $user_table where user_id='$userid'";
			$userres = $db_object->get_a_line($userqry);	

			$bossqry = "select username,password,email from $user_table where user_id='$bossid'";
			$bossres = $db_object->get_a_line($bossqry);

		//send mail
			$to = $userres['email'];
			$from  = $bossres['email'];	
			$username = $userres['username'];
			$path = $common->http_path;
			$path = $path."/performance/performance_alert.php";
			$password = $userres['password'];
			$mess = preg_replace("/{{(.*?)}}/e","$$1",$message);
			//echo "to - $to : mess = $mess : from = $from <br> ";
			if($to!="")
			{
				$common->send_mail($to,$subject,$mess,$from);			
			}
		}
	}//end resubmit objective


/*			--Approve Plan
The below function sends mail to the superadmin to approve the 
plans set by the admins
*/
	function approve_plan($db_object,$common,$err)
	{						
		$unapproved_category = $common->prefix_table("unapproved_category");
		$user_table = $common->prefix_table("user_table");
		$message_table  = $common->prefix_table("performance_message");
		$default = 1;
		$messqry = "select appsub_subject_$default as subject,appsub_message_$default as message from
				$message_table";
		$messres = $db_object->get_a_line($messqry);
		$subject = $messres['subject'];
		$message = $messres['message'];
	
		$qry = "select user_id from $unapproved_category where status='NP' group by user_id";
		$res = $db_object->get_single_column($qry);

		$ad_qry = "select email from $user_table where user_id='1'";
		$ad_res = $db_object->get_a_line($ad_qry);
		
		$to  = $ad_res['email'];

		for($i=0;$i<count($res);$i++)
		{	
			$userid = $res[$i];
			$userqry = "select username,email from $user_table where user_id='$userid'";
			$userres = $db_object->get_a_line($userqry);	
		//send mail		
			$from  = $userres['email'];	
			$username = $userres['username'];
			$path = $common->http_path;
			$path = $path."/performance/performance_alert.php";
			$password = $userres['password'];
			$mess = preg_replace("/{{(.*?)}}/e","$$1",$message);
			//echo "to - $to : mess = $mess : from = $from <br> ";
			if($to!="")
			{
				$common->send_mail($to,$subject,$mess,$from);			
			}
		}		
	}//end approve_plan

/*		--Performance Improvement Plan--
	This script is for sendind mails to the user,
	imm boss and the admin when the plan expires.
*/
	function performance_improvement_plan($db_object,$common,$err)
	{
		$plan=$common->prefix_table("plan");
		
		$performance_message=$common->prefix_table("performance_message");
		
		$user_table=$common->prefix_table("user_table");
		
		$current_date=date("Y-m-d H:i:s",time());
		
		$current_date=@explode(" ",$current_date);
		
		$current_date1=@explode("-",$current_date[0]);
		
		$current_date1=mktime(0,0,0,$current_date1[1],$current_date1[2],$current_date1[0]);
		
		$sql="select employee_id,plan_id,due_date from $plan where status='a'";
		
		$result=$db_object->get_rsltset($sql);
		
		for($i=0;$i<count($result);$i++)
		{
			$user_id=$result[$i][employee_id];
			
			$plan_id=$result[$i][plan_id];
			
			$date=$result[$i][due_date];
			
			$date=@explode(" ",$date);
			
			$date1=@explode("-",$date[0]);
		
			$date1=mktime(0,0,0,$date1[1],$date1[2],$date1[0]);
			
			if($current_date1>=$date1)
			{
				$qry="update $plan set status='h' where plan_id='$plan_id'";
				
				$db_object->insert($qry);
				
				$imm_boss=$common->immediate_boss($db_object,$user_id);
				
				if($imm_boss!='1')
				{
					$id="(".$user_id.",".$imm_boss.",".'1'.")";
				}
				else
				{
					$id="(".$user_id.",".'1'.")";
				}
				
				$admin_qry="select email username,email from $user_table where user_id='1'";
				
				$admin_mail=$db_object->get_a_line($admin_qry);
				
				$mail_qry="select username,email,user_id from $user_table where user_id in $id";
				
				$mail_res=$db_object->get_rsltset($mail_qry);
				
				$mess_qry="select plan_expiry_subject_1,plan_expiry_message_1 from $performance_message";
				
				$mess_res=$db_object->get_a_line($mess_qry);
				
				$message=$mess_res[plan_expiry_message_1];
				
				$subject=$mess_res[plan_expiry_subject_1];
				
				for($a=0;$a<count($mail_res);$a++)
				{
				
					
					$from=$admin_mail[email];
					
					$to=$mail_res[$a][email];
					
					$xArray[recepient]=$mail_res[$a][username];
					
					$xArray[user]=$common->name_display($db_object,$user_id);
					
					$message1=$common->direct_replace($db_object,$message,$xArray);
					
										
					$common->send_mail($to,$subject,$message1,$from);
				}
			}
			
			
		}
		
	}//end performance_improvement_plan
}//end class

	$ob = new cron;
	$path = $common->http_path;
	switch ($action)
	{

		case 'AO':
			$ob->approve_objective($db_object,$common,$error_msg);
			header("location:$path/performance/runcron.php");
		break;
		
		case 'AF':
			$ob->approve_feedback($db_object,$common,$error_msg);
			header("location:$path/performance/runcron.php");
		break;
		
		case 'RO':
			$ob->resubmit_objective($db_object,$common,$error_msg);
			header("location:$path/performance/runcron.php");
	        break;

		case 'IP':
			$ob->performance_improvement_plan($db_object,$common,$error_msg);
			header("location:$path/performance/runcron.php");
		break;
		
		case 'AP':
			$ob->approve_plan($db_object,$common,$error_msg);
			header("location:$path/performance/runcron.php");
		break;
		
	}

?>
