<?
include_once("../session.php");
include_once("header.php");
class objective
{
	function view_form($db_object,$common,$user_id,$default,$err,$date_format)
	{
		$path = $common->path;
		$filename = $path."/templates/performance/approve_objective_list.html";
		$file = $common->return_file_content($db_object,$filename);
			
		$user_table = $common->prefix_table("user_table");
		$performance_alert = $common->prefix_table("performance_alert");
				

		$usqry  = "select position from $user_table where user_id='$user_id'";
		$usres = $db_object->get_a_line($usqry);
		$bossid = $usres['position'];

		$aqry ="select user_id,date_format(submit_date,'$date_format') as submit_date from $performance_alert where boss_id='$bossid'";
		$ares = $db_object->get_rsltset($aqry);

		$pattern1 = "/<{objective_loopstart(.*?)<{objective_loopend}>/s";	
		preg_match($pattern1,$file,$arr1);
			$match1=$arr1[0];
			$str1="";
			for($i=0;$i<count($ares);$i++)
			{
				$uid = $ares[$i]['user_id'];
				$username = $common->name_display($db_object,$uid);
				$date = $ares[$i]['submit_date'];
				$str1.=preg_replace("/\<\{(.*?)\}\>/e","$$1",$match1);
			}			
			$file=preg_replace($pattern1,$str1,$file);
			if($str1=="")
			{
				$file = preg_replace("/\<table(.*?)table>/s",$err['cEmptyrecords'],$file);
			}

		$file = $common->direct_replace($db_object,$file,$val);
		echo $file;
	}//end view
}//end class
	$ob = new objective;
	$ob->view_form($db_object,$common,$user_id,$default,$error_msg,$gbl_date_format);
include_once("footer.php");
?>
