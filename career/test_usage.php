<?php
include("../session.php");
include("header.php");
class Tests_Usage
{
	function display_page($common,$db_object)
	{
		$path=$common->path;
		$xFile=$path."templates/career/test_usage.html";
		$xTemplate=$common->return_file_content($db_objectr,$xFile);
		$xTemplate=$common->direct_replace($db_object,$xTemplate,$vals);
		echo $xTemplate;
	}
}
$tobj= new Tests_Usage;
$tobj->display_page($common,$db_object);
include("footer.php");
?>