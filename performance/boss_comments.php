<?php
include_once("../session.php");

include_once("header.php");

class comments
{
	function display_boss_comment($db_object,$common,$_GET)
	{
		$assign_performance_appraisal=$common->prefix_table("assign_performance_appraisal");
		
		$appraisal_id=$_GET['fAppraisal_id'];
		
		$path=$common->path;
		
		$xtemplate=$path."templates/performance/boss_comments.html";
		
		$file=$common->return_file_content($db_object,$xtemplate);
		
		$qry="select reject_exp from $assign_performance_appraisal where dummy_id='$appraisal_id'
		
		and status='r'";
		
		$qry_res=$db_object->get_a_line($qry);
		
		$xArray[boss_comments]=$qry_res[reject_exp];

		$file=$common->direct_replace($db_object,$file,$xArray);
		
		echo $file;
		
		
	}
}

$obj=new comments();

$obj->display_boss_comment($db_object,$common,$_GET);

?>
