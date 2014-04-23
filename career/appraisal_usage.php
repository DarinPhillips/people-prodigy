<?php
/*---------------------------------------------
SCRIPT:appraisal_usage.php
AUTHOR:info@chrisranjana.com	
UPDATED:Jan 8th

DESCRIPTION:
This script displays the appraisal usage for admin view...

---------------------------------------------*/
include("../session.php");
include_once("header.php");

class AppraisalUsage
{
function show_usage($db_object,$common,$user_id)
	{
		$xPath=$common->path;
		$returncontent=$xPath."/templates/career/appraisal_usage.html";
		$returncontent=$common->return_file_content($db_object,$returncontent);
			
		
		$returncontent = $common->direct_replace($db_object,$returncontent,$values);
		echo $returncontent;
		
	}
}
$obj = new AppraisalUsage;
$obj->show_usage($db_object,$common,$user_id);


include_once("footer.php");
?>
