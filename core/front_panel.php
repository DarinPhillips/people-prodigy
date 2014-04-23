<?php
include("../session.php");
include("header.php");
class front
{
	function front_display($common,$db_object,$user_id)
	{
		$path=$common->path;
		$filename=$path."templates/core/front_panel.html";
		$filecontent=$common->return_file_content($db_object,$filename,$user_id);
		$vals=array();
		$filecontent=$common->direct_replace($db_object,$filecontent,$vals);
		echo $filecontent;
	}
}
$frobj=new front;
$frobj->front_display($common,$db_object,$user_id);
include("footer.php");
?>