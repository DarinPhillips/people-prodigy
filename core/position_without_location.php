<?php
include("../session.php");
include("header.php");
class Alter_Position
{
function alter_the_position($common,$db_object,$user_id)
{
	$path=$common->path;
	$xFile=$path."templates/core/position_without_location.html";
	$xTemplate=$common->return_file_content($db_object,$xFile);
	$position_table=$common->prefix_table("position");
	$fields=$common->return_fields($db_object,$position_table);

	$selqry="select $fields,

date_format(date_added,'%m.%d.%Y.%H:%i') as date_added 

  from $position_table where location is null or location=0";

	//echo $selqry;

	
	$positionset=$db_object->get_rsltset($selqry);

for($i=0;$i<count($positionset);$i++)
{
	$temp_user_id=$positionset[$i]["added_by"];
	$positionset[$i]["added_by"]=$common->name_display($db_object,$temp_user_id);
}
	
	$values["position_loop"]=$positionset;
	
$xTemplate=$common->multipleloop_replace($db_object,$xTemplate,$values,$sel_arr);
$xTemplate=$common->direct_replace($db_object,$xTemplate,$values);
	echo $xTemplate;
}
}
$posobj=new Alter_Position;
$posobj->alter_the_position($common,$db_object,$user_id);

include("footer.php");
?>
