<?php


class Footer
{
  function footer_display($common,$db_object,$user_id)
  {
    $filename="../templates/career/footer.html";
    $filecontent=$common->return_file_content($db_object,$filename);
    
    $user_table=$common->prefix_table("user_table");
    
	$sql="select user_type from $user_table where user_id='$user_id'";
	
	$sql_res=$db_object->get_a_line($sql);

	if($sql_res[user_type]=="external")
	{

		/* $filename="../templates/footer.html";
    $filecontent=$common->return_file_content($db_object,$filename);
    $filecontent=$common->direct_replace($db_object,$filecontent,$vals);
	


echo $filecontent;exit;*/
include_once("../footer.php");exit;
	}
else
{
	
$vals=array();
$exist=$_COOKIE["viewasadmin"];
if($exist!="")
{
$filecontent=preg_replace("/<{Admin_mode_Setup(.*?)}>/s","",$filecontent);
}
else
{
$filecontent=preg_replace("/<{Admin_mode_Setup}>(.*)<{Admin_mode_Setup}>/s","",$filecontent);
}


if($user_id == 1)
{
	
	$filecontent = preg_replace("/<{admins_area_(.*?)}>/s","",$filecontent);
}
else
{

	$filecontent = preg_replace("/<{admins_area_start}>(.*?)<{admins_area_end}>/s","",$filecontent);	


}

$path = $common->path;

$filecontent = $common->is_module_purchased($db_object,$path,$filecontent,$common->lfvar);
$filecontent = $common->is_module_purchased($db_object,$path,$filecontent,$common->pfvar);
$filecontent = $common->is_module_purchased($db_object,$path,$filecontent,$common->cavar);
$filecontent = $common->is_module_purchased($db_object,$path,$filecontent,$common->covar);


$filecontent=$common->direct_replace($db_object,$filecontent,$vals);
	


echo $filecontent;
}
  }






}
$ftobj=new Footer;
$ftobj->footer_display($common,$db_object,$user_id);

?>

		
