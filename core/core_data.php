<?php
include("../session.php");
include("header.php");
class Core_data
{
	function display_core_data($common,$db_object,$user_id)
	{
		$path=$common->path;
$xFile=$path."templates/core/core_data.html";
$xTemplate=$common->return_file_content($db_object,$xFile);
$xTemplate=$common->direct_replace($db_object,$xTemplate,$vals);
echo $xTemplate;
	}
}
$coreobj= new Core_data;
if($user_id==1)
{
$coreobj->display_core_data($common,$db_object,$user_id);
}
include("footer.php");
?>