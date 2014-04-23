<?php
include_once("../session.php");

include_once("header.php");

class mobility
{
	function show_reports($db_object,$common,$user_id)
	{
		$user_table=$common->prefix_table("user_table");
		
		if($user_id!=1)
		{
			$users=$common->employees_under_admin_boss($db_object,$user_id);
		}
		else
		{
			$sql="select user_id from $user_table where user_id <>'$user_id'";
			
			$users=$db_object->get_single_column($sql);
		}
		
		$xPath=$common->path;
		
		$xTemplate=$xPath."templates/career/report_mobility.html";
		
		$content=$common->return_file_content($db_object,$xTemplate);

		preg_match("/<{user_loopstart}>(.*?)<{user_loopend}>/s",$content,$match);
		
		$match=$match[0];
		
		
		for($i=0;$i<count($users);$i++)
		{
					
			$user_id=$users[$i];
			
			$username=$common->name_display($db_object,$user_id);
			
			$str.=preg_replace("/<{(.*?)}>/e","$$1",$match);
		}
		
		$content=preg_replace("/<{user_loopstart}>(.*?)<{user_loopend}>/s",$str,$content);
		
		$content=$common->direct_replace($db_object,$content,$array);
		
		echo $content;
	}
}
$obj=new mobility();

$obj->show_reports($db_object,$common,$user_id);

include_once("footer.php");
?>
