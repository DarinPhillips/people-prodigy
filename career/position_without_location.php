<?php
include("../session.php");
include("header.php");
class Alter_Position
{
function alter_the_position($common,$db_object,$user_id,$error_msg)
{
	$path=$common->path;
	$xFile=$path."templates/career/core_data/position_without_location.html";
	$xTemplate=$common->return_file_content($db_object,$xFile);
	$position_table=$common->prefix_table("position");
	$admins_table=$common->prefix_table("admins");
	$fields=$common->return_fields($db_object,$position_table);
/*if($user_id==1)
{	
	$selqry="select $fields from $position_table  where location is null or location=0";

}
else
{*/
	$selqry="select distinct($position_table.pos_id),
$position_table.position_name,$position_table.added_by,

date_format($position_table.date_added,'%m.%d.%Y.%H:%i') as date_added 

 from $position_table left join $admins_table on $admins_table.pos_id=$position_table.pos_id where ($position_table.location is null or $position_table.location=0) and $admins_table.pos_id is not null or $admins_table.pos_id='0'";

//}

	$positionset=$db_object->get_rsltset($selqry);
	if($positionset[0]=="")
	{
		echo $error_msg['cNoPositionNoLocation'];
		
		include_once("footer.php");exit;
	}

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
$posobj->alter_the_position($common,$db_object,$user_id,$error_msg);
include("footer.php");
?>
