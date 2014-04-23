<?php

class Footer
{
  function footer_display($common,$db_object)
  {
  		$path=$common->path;
    $filename=$path."templates/career/footer.html";
    $filecontent=$common->return_file_content($db_object,$filename);

$vals=array();
$exist=$_COOKIE["viewasadmin"];
if($exist!="")
{
$filecontent=preg_replace("/<{(.*?)}>/s","",$filecontent);
}
else
{
$filecontent=preg_replace("/<{Admin_mode_Setup}>(.*)<{Admin_mode_Setup}>/s","",$filecontent);
}

$filecontent=$common->direct_replace($db_object,$filecontent,$vals);
	echo $filecontent;
  }

}
$ftobj=new Footer;
$ftobj->footer_display($common,$db_object);

?>

		