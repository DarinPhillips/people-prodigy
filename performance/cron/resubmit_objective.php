<?
include_once("../../includes/database.class");
include_once("../../includes/common.class");
$common  = new common;
$db_object = new database;
class cron
{
	function run_script($db_object,$common)
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
			$common->send_mail($to,$subject,$mess,$from);			
		}		
	}//end 
}//end class
	$ob = new cron;
	$ob->run_script($db_object,$common);

?>
