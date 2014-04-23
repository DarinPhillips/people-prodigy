<?
include_once("../../includes/database.class");
include_once("../../includes/common.class");
$common  = new common;
$db_object = new database;
class cron
{
	function run_script($db_object,$common)
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

			$bossqry = "select username,password,email from $user_table where user_id='$bossid'";
			$bossres = $db_object->get_a_line($bossqry);			
		//send mail
			$to = $bossres['email'];
			$tousername = $bossres['username'];
			$from  = $userres['email'];
			$username = $userres['username'];
			$path = $common->http_path;
			$path = $path."/performance/approve_objective_list.php";
			$mess = preg_replace("/{{(.*?)}}/e","$$1",$message);
			$common->send_mail($to,$subject,$mess,$from);			
		}		
	}//end 
}//end class
	$ob = new cron;
	$ob->run_script($db_object,$common);

?>
