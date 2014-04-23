<?php
include("session.php");
include("header.php");
class Outer
{
 function display($common,$db_object,$user_id)
	{
	$xFile="templates/outer.html";	
	$xTemplate=$common->return_file_content($db_object,$xFile);
	if($user_id!=1)
	{
		$xTemplate=preg_replace("/<{admin_area}>(.*?)<{admin_area}>/s","",$xTemplate);
		//$xTemplate=preg_replace("/<{adminlearning_area}>(.*?)<{adminlearning_area}>/s","",$xTemplate);
		
	}
	else
	{
		$xTemplate=preg_replace("/<{(.*?)}>/s","",$xTemplate);
	}

$values=array();
$xTemplate=$common->direct_replace($db_object,$xTemplate,$values);



	
	echo $xTemplate;
	}
}
$otrobj= new Outer;
echo $user_id;
$otrobj->display($common,$db_object,$user_id);
include("footer.php");
?>
