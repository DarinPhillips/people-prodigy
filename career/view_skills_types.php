<?php
include("../session.php");
include("header.php");
class View_Skills1
{
  function view_all_skills1($common,$db_object,$user_id,$emp_id)
  {
	$path=$common->path;
	$xFile=$path."templates/career/view_skills_types.html";
	$xTemplate=$common->return_file_content($db_object,$xFile);

	$user_table=$common->prefix_table("user_table");
	$skill_builder=$common->prefix_table("unapproved_skill_builder");
	$position_table=$common->prefix_table("position");
	$selqry="select $skill_builder.build_id as build_id,$position_table.position_name  as position,$user_table.username as username from $skill_builder,$position_table,$user_table where $skill_builder.pos_id=$position_table.pos_id and $skill_builder.emp_id=$user_table.user_id and  $skill_builder.emp_id='$emp_id'";
	$build_id=$db_object->get_rsltset($selqry);
	preg_match("/<{emp_loopstart}>(.*?)<{emp_loopend}>/s",$xTemplate,$mat1);
	$replace1=$mat1[1];

	for($i=0;$i<count($build_id);$i++)
	{
			$username=$build_id[$i]["username"];
			$buildid=$build_id[$i]["build_id"];
			$position=$build_id[$i]["position"];
		$replaced.=preg_replace("/<{(.*?)}>/e","$$1",$replace1);
		
	}
	$xTemplate=preg_replace("/<{emp_loopstart}>(.*?)<{emp_loopend}>/s",$replaced,$xTemplate);
	$vals=array();
	$xTemplate=$common->direct_replace($db_object,$xTemplate,$vals);
	echo $xTemplate;

 }

}
$viewobj1= new View_Skills1;
$viewobj1->view_all_skills1($common,$db_object,$user_id,$emp_id);
include("footer.php");
?>