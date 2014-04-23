<?php
include("../session.php");
include("header.php");
class View_skill
{
  function view_skill1($common,$db_object,$user_id,$build_id,$user_id)
  {
	$path=$common->path;
	$xFile=$path."templates/career/view_allskills.html";
	$xTemplate=$common->return_file_content($db_object,$xFile);


	$objectives_table=$common->prefix_table("unapproved_objectives");
	$activities_table=$common->prefix_table("unapproved_activities");
	$skills_for_activities_table=$common->prefix_table("unapproved_skills_for_activities");
	
	preg_match("/<{skill_loopstart}>(.*?)<{skill_loopend}>/s",$xTemplate,$mat1);
	$replace=$mat1[1];
//	echo $replace;


	preg_match("/<{obj_loopstart}>(.*?)<{obj_loopend}>/s",$xTemplate,$matobj);
	$objreplace=$matobj[1];
	
	preg_match("/<{act_loopstart}>(.*?)<{act_loopend}>/s",$xTemplate,$matact);
	$actreplace=$matact[1];

	preg_match("/<{ski_loopstart}>(.*?)<{ski_loopend}>/s",$xTemplate,$matski);
	$skireplace=$matski[1];
	



$selqry="select obj_id,objective_name from $objectives_table where build_id='$build_id' order by obj_id";
$objectiveset=$db_object->get_rsltset($selqry);

for($i=0;$i<count($objectiveset);$i++)
{
	$objective=$objectiveset[$i]["objective_name"];
	$replaced.=preg_replace("/<{(.*?)}>/e","$$1",$objreplace);
	$temp_objid=$objectiveset[$i]["obj_id"];
	$selqry="select act_id,activity_name from $activities_table where obj_id='$temp_objid' order by act_id";
	$activityset=$db_object->get_rsltset($selqry);
//echo "act=$selqry<br>";
	for($j=0;$j<count($activityset);$j++)
	{
		$activity=$activityset[$j]["activity_name"];
		$replaced.=preg_replace("/<{(.*?)}>/e","$$1",$actreplace);

		$temp_act_id=$activityset[$j]["act_id"];
		$selqry="select skill_name from $skills_for_activities_table where act_id='$temp_act_id' order by ski_act_id";
		$skillset=$db_object->get_rsltset($selqry);
		
//echo "skill=$selqry<br>";		

		for($k=0;$k<count($skillset);$k++)
		{
			$skill=$skillset[$k]["skill_name"];
//echo "$skill<br>";
			$replaced.=preg_replace("/<{(.*?)}>/e","$$1",$skireplace);
		}
	}
}


$xTemplate=preg_replace("/<{skill_loopstart}>(.*?)<{skill_loopend}>/s",$replaced,$xTemplate);

$vals=array();

$xTemplate=$common->direct_replace($db_object,$xTemplate,$vals);
echo $xTemplate;
  } 
}
$obj1=new View_skill;
$obj1->view_skill1($common,$db_object,$user_id,$build_id,$user_id);
include("footer.php");
?>