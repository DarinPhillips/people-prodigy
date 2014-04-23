<?php
include("../session.php");
include("header.php");
class Employee_without_position
{
function show_employees($common,$db_object,$user_id)
	{
		$path=$common->path;
		$xFile=$path."templates/core/positions_without_employees.html";
		$xTemplate=$common->return_file_content($db_object,$xFile);
		$user_table=$common->prefix_table("user_table");
		$position_table=$common->prefix_table("position");
//$selqry="select $user_table.user_id,$user_table.username,$user_table.position,$user_table.reg_date from  $user_table left join $position_table on  $position_table.pos_id=$user_table.position where $user_table.position is null and $user_table.user_type<>'external'";

//------here the query takes the positions that are all not in the usertable


$selqry="select $position_table.pos_id,$position_table.position_name,$position_table.date_added,$position_table.added_by,$user_table.position from $position_table left join $user_table on $position_table.pos_id=$user_table.position where $user_table.position is null";
$userset=$db_object->get_rsltset($selqry);
for($i=0;$i<count($userset);$i++)
{
	$temp_user_id=$userset[$i]["added_by"];
	$userset[$i]["added_by"]=$common->name_display($db_object,$temp_user_id);
}

$values["employee_loop"]=$userset;
$xTemplate=$common->multipleloop_replace($db_object,$xTemplate,$values,$selarr);
$vals=array();
$xTemplate=$common->direct_replace($db_object,$xTemplate,$vals);
		
		echo $xTemplate;
	}
}
$empobj= new Employee_without_position;
$empobj->show_employees($common,$db_object,$user_id);
include("footer.php");
?>