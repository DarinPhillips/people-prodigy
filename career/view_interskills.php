<?php
include("../session.php");
include("header.php");
class Inter_skill
{
  function view_interskills($common,$db_object,$user_id,$emp_id,$gbl_skill_type)
  {
	$path=$common->path;
$xFile=$path."templates/career/view_interskills.html";
$xTemplate=$common->return_file_content($db_object,$xFile);

$unapproved_skills=$common->prefix_table("unapproved_skills");

$selqry="select skill_name,skill_description,unskilled_desc,skill_type from $unapproved_skills where emp_id='$emp_id'";
$skillset=$db_object->get_rsltset($selqry);

preg_match("/<{skill_loopstart}>(.*?)<{skill_loopend}>/s",$xTemplate,$mat);
$replace=$mat[1];
for($i=0;$i<count($skillset);$i++)
{
	$skill_name=$skillset[$i]["skill_name"];
	$skill_description=$skillset[$i]["skill_description"];
	$unskilled_desc=$skillset[$i]["unskilled_desc"];
	$temp_skill_type=$skillset[$i]["skill_type"];
	$skill_type=$gbl_skill_type[$temp_skill_type];
	$replaced.=preg_replace("/{{(.*?)}}/e","$$1",$replace);

}
//echo $replaced;

$xTemplate=preg_replace("/<{skill_loopstart}>(.*?)<{skill_loopend}>/s",$replaced,$xTemplate);
$vals=array();
$xTemplate=$common->direct_replace($db_object,$xTemplate,$vals);


echo $xTemplate;


  }
}
$interobj=new Inter_skill;
$interobj->view_interskills($common,$db_object,$user_id,$emp_id,$gbl_skill_type);
include("footer.php");
?>


