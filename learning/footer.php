<?php

class Footer
{
  function footer_display($common,$db_object,$user_id)
  {

    $path=$common->path;
    $filename=$path."/templates/learning/footer.html";
     $filecontent=$common->return_file_content($db_object,$filename);
 

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

$filecontent=$common->direct_replace($db_object,$filecontent,$array);
	echo $filecontent;
  }

}
$ftobj=new Footer;
$ftobj->footer_display($common,$db_object,$user_id);

?>

		