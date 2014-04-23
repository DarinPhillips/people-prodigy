<?php
/*---------------------------------------------
SCRIPT:position_model4.php
AUTHOR:info@chrisranjana.com	
UPDATED:4th Dec

DESCRIPTION:
This script displays the models and their comparisions.
---------------------------------------------*/

include("../session.php");
include("header.php");
class mobility
{
function show_mobility_reports($db_object,$common,$user_id,$default,$post_var)
{
	$xPath		= $common->path;
	$returncontent	= $xPath."/templates/career/mobility_reports.html";
	$returncontent	= $common->return_file_content($db_object,$returncontent);
	
	while(list($kk,$vv) = @each($post_var))
		{
			$$kk = $vv;
		}
	
		if($empl != '')
		{
			$values['empl']=$empl;
		}
		
	
	
	$returncontent = $common->direct_replace($db_object,$returncontent,$values);
	echo $returncontent;

}
}

$obj = new mobility;
$obj->show_mobility_reports($db_object,$common,$user_id,$default,$post_var);
include_once("footer.php");
?>
