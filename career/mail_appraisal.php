<?php
/*---------------------------------------------
SCRIPT: mail_appraisal.php
AUTHOR:info@chrisranjana.com	
UPDATED:30th Sept

DESCRIPTION:
This script displays alert for the 360.

---------------------------------------------*/
include("../session.php");
include_once("header.php");

class mailAppraisal
{ 
function show_screen($db_object,$common,$form_array)
{
		while(list($kk,$vv)=@each($form_array))
		{

		$$kk=$vv;
	
		}
	
		$xPath=$common->path;
		$returncontent=$xPath."/templates/career/mail_appraisal.html";
		$returncontent=$common->return_file_content($db_object,$returncontent);
		
		echo $returncontent;
}



}
$obj = new mailAppraisal;
$form_array	= array_merge($_POST,$_GET);
$obj->show_screen($db_object,$common,$form_array);

include_once("footer.php");
