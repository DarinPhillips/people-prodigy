<?php
include("../session.php");
include("header.php");
class solution
{
function insert($db_object,$common)
{
	$xFile="../templates/admin/dev_solution.html";
	$xTemplate=$common->return_file_content($db_object,$xFile);
	echo $xTemplate;
}
}
$obj=new solution;
$obj->insert($ob_object,$common);
?>
