<?php
include("../session.php");
include("header.php");
class Admin
{
  function admin_display($common,$db_object,$form_array)
  {
  	$xFile="../templates/career/adminsettings_panel.html";
  	$xTemplate=$common->return_file_content($db_object,$xFile);
  	echo $xTemplate;
   }
}
$adobj=new Admin;
$adobj->admin_display($common,$db_object,$form_array);

include("footer.php");
?>
