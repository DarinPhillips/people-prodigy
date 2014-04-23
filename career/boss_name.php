<?php
include_once("../session.php");

include_once("header.php");

class boss
{
	function return_boss_name($db_object,$common,$pos_id)
	{
		$user_table=$common->prefix_table("user_table");
		
		$path=$common->path;
		
		$xtemplate=$path."/templates/career/boss_name.html";
		
		$file=$common->return_file_content($db_object,$xtemplate);
		
		$sql="select user_id,email from $user_table where position='$pos_id'";
		
		$sql_res=$db_object->get_a_line($sql);
		
		$user_id=$sql_res[user_id];
		
		$imm_boss=$common->immediate_boss($db_object,$user_id);
		
		$xArray['boss_name']=$common->name_display($db_object,$imm_boss);
		
		$xArray['email']=$sql_res[email];
		
		$file=$common->direct_replace($db_object,$file,$xArray);
		
		echo $file;
		
	}
}
$obj=new boss();

$obj->return_boss_name($db_object,$common,$pos_id);

?>
