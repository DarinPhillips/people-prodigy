<?php
include("../session.php");
include("header.php");
class Tech_skill_alert
{
	function display_alert($common,$db_object,$user_id,$error_msg)
	{
		$path=$common->path;
		$xFile=$path."templates/career/alertfortechskillbuild.html";
		$xTemplate=$common->return_file_content($db_object,$xFile);


$assign_tech_skill=$common->prefix_table("assign_tech_skill_builder");
$position_table=$common->prefix_table("position");
$selqry="select $position_table.position_name as position_name,$assign_tech_skill.position_id as pos_id,date_format($assign_tech_skill.date,'%m.%d.%Y.%H.%i') as date from $assign_tech_skill,$position_table where $position_table.pos_id=$assign_tech_skill.position_id and  $assign_tech_skill.user_id='$user_id'";
$positionset=$db_object->get_rsltset($selqry);
preg_match("/<{position_loopstart}>(.*?)<{position_loopend}>/s",$xTemplate,$mat);
$replace=$mat[1];


for($i=0;$i<count($positionset);$i++)
{
	$position_name=$positionset[$i]["position_name"];
	$pos_id=$positionset[$i]["pos_id"];
	$date=$positionset[$i]["date"];
	$replaced.=preg_replace("/<{(.*?)}>/e","$$1",$replace);

}

if($replaced=="")
{

	$replaced=$error_msg["cEmptyrecords"];
}
$xTemplate=preg_replace("/<{position_loopstart}>(.*?)<{position_loopend}>/s",$replaced,$xTemplate);


	$xTemplate=$common->direct_replace($db_object,$xTemplate,$vals);
		echo $xTemplate;
	}
 	
}
$alertobj= new Tech_skill_alert;
$alertobj->display_alert($common,$db_object,$user_id,$error_msg);
include("footer.php");
?>