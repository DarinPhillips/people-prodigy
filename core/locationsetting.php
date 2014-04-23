<?php
include("../session.php");
include("header.php");
class Location
{
function display_location($common,$db_object,$user_id,$error_msg)
{
	$path=$common->path;
	$xFile=$path."templates/core/locationsetting.html";
	$xTemplate=$common->return_file_content($common,$xFile);
	$location_table=$common->prefix_table("location_table");
	$fieldnames=$common->return_fields($db_object,$location_table);
	$selqry="select $fieldnames from $location_table";
	$locationset=$db_object->get_rsltset($selqry);
preg_match("/<{location_loopstart}>(.*?)<{location_loopend}>/s",$xTemplate,$mat);
$replace=$mat[1];
for($i=0;$i<count($locationset);$i++)
{
	$id=$locationset[$i]["location_id"];
	$first_level=$locationset[$i]["first_level"];
	$second_level=$locationset[$i]["second_level"];
	$third_level=$locationset[$i]["third_level"];
	$fourth_level=$locationset[$i]["fourth_level"];
	$fifth_level=$locationset[$i]["fifth_level"];

	$sixth_level=$locationset[$i]["sixth_level"];
	$seventh_level=$locationset[$i]["seventh_level"];
	$replaced.=preg_replace("/<{(.*?)}>/e","$$1",$replace);
}

$xTemplate=preg_replace("/<{location_loopstart}>(.*?)<{location_loopend}>/s",$replaced,$xTemplate);
$xTemplate=$common->direct_replace($db_object,$xTemplate,$values);
	echo $xTemplate;

}
}
$locobj= new Location;
$locobj->display_location($common,$db_object,$user_id,$error_msg);
include("footer.php");
?>