<?php
include("../session.php");
include("header.php");
class Add_locations
{

  function add_location($common,$db_object,$denotes,$id)
  {
	$path=$common->path;
	$xFile=$path."templates/core/add_location.html";
	$xTemplate=$common->return_file_content($db_object,$xFile);
	$values["directreplace"]["denotes"]=$denotes;
	$values["directreplace"]["id"]=$id;
	$xTemplate=$common->direct_replace($db_object,$xTemplate,$values);
	echo $xTemplate;

   }
}
$obj=new Add_locations;
$obj->add_location($common,$db_object,$denotes,$id);
include("footer.php");
?>