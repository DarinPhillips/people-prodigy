<?php
include("../session.php");
include("header.php");
class Alter_Family
{
	function show_familes($common,$db_object,$user_id,$error_msg)
	{
	$path=$common->path;
$xFile=$path."templates/career/core_data/families_without_position.html";
$xTemplate=$common->return_file_content($db_object,$xFile);
$family_table=$common->prefix_table("family");
$family_position=$common->prefix_table("family_position");
$admins_table=$common->prefix_table("admins");

$selqry="select $family_table.family_id,$family_table.family_name,

date_format($family_table.date_added,'%m.%d.%Y.%i:%s') as date_added,
$family_table.added_by from
$family_table left join $family_position on $family_table.family_id=$family_position.family_id
left join $admins_table on $family_table.family_id=$admins_table.fam_id where $admins_table.user_id='$user_id' and $family_position.family_id is null";
$familyset=$db_object->get_rsltset($selqry);
if($familyset[0]=="")
{
	echo $error_msg['cNoFamilyNoPosition'];
	
	include_once("footer.php");
	exit;
}

for($i=0;$i<count($familyset);$i++)
{
	$family_id=$familyset[$i]["family_id"];
	$family_name=$familyset[$i]["family_name"];
	$date_added=$familyset[$i]["date_added"];
	$added_user_id=$familyset[$i]["added_by"];
	$added_by=$common->name_display($db_object,$added_user_id);
	 $familyset[$i]["added_by"]=$added_by;
	
}
$values["family_loop"]=$familyset;
$xTemplate=$common->multipleloop_replace($db_object,$xTemplate,$values,$selarr);
$xTemplate=$common->direct_replace($db_object,$xTemplate,$vals);
echo $xTemplate;
	}
}
$famobj=new Alter_Family;
$famobj->show_familes($common,$db_object,$user_id,$error_msg);
include("footer.php");
?>
