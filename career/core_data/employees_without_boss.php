<?php
include("../session.php");
include("header.php");
class Employee_without_Boss
{
	function show_positions($common,$db_object,$user_id)
	{
	$path=$common->path;
	$xFile=$path."templates/core/employees_without_boss.html";
	$xTemplate=$common->return_file_content($db_object,$xFile);
	$position_table=$common->prefix_table("position");
	$user_table=$common->prefix_table("user_table");
$selqry="select $user_table.user_id,$user_table.username,$user_table.reg_date,$user_table.added_by,$user_table.position,$position_table.boss_no
from $user_table left join $position_table
on $user_table.position=$position_table.pos_id
where $user_table.user_type='employee' and
($position_table.boss_no is null or $position_table.boss_no=0) and
$user_table.user_id<>1";
$userset=$db_object->get_rsltset($selqry);
for($i=0;$i<count($userset);$i++)
{
$temp_added_user_id=$userset[$i]["added_by"];
$userset[$i]["added_by"]=$common->name_display($db_object,$temp_added_user_id);
}

$values["employee_loop"]=$userset;


$xTemplate=$common->multipleloop_replace($db_object,$xTemplate,$values,$sel_arr);
$vals=array();
$xTemplate=$common->direct_replace($db_object,$xTemplate,$vals);
	echo $xTemplate;

	}
}
$posobj= new Employee_without_Boss;
$posobj->show_positions($common,$db_object,$user_id); 
include("footer.php");
?>