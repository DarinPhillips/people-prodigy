<?php
/*---------------------------------------------
SCRIPT:model_usage.php
AUTHOR:info@chrisranjana.com	
UPDATED:Jan 8th

DESCRIPTION:
This script displays the usage of models

---------------------------------------------*/
include("../session.php");
include_once("header.php");

class ModelUsage
{
function show_usage($db_object,$common,$user_id,$default)
	{
		$xPath=$common->path;
		$returncontent=$xPath."/templates/career/model_usage.html";
		$returncontent=$common->return_file_content($db_object,$returncontent);
		
		
		preg_match("/<{percent_loopstart}>(.*?)<{percent_loopend}>/s",$returncontent,$permatchold);
		$permatchnew = $permatchold[1];
		
		for($i=1;$i<=100;$i++)
		{
			$percent = $i;
			$str .= preg_replace("/<{(.*?)}>/e","$$1",$permatchnew);
		}
		
		$returncontent = preg_replace("/<{percent_loopstart}>(.*?)<{percent_loopend}>/s",$str,$returncontent);
		
		$returncontent = $common->direct_replace($db_object,$returncontent,$values);
		echo $returncontent;
	}
}
$obj = new ModelUsage;
$obj->show_usage($db_object,$common,$user_id,$default);

include_once("footer.php");
?>
