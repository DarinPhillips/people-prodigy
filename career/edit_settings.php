<?php
include("../session.php");
include("header.php");
class Admin
{
 function edit_display($common,$db_object,$form_array)
 {
	$xFile="../templates/career/edit_settings.html";
	$xTemplate=$common->return_file_content($db_object,$xFile);
	$config_table=$common->prefix_table("config");
	$qry="select objectives,activities,skills,delimiter,skill_levels,answer_count,no_at_level,challenges,appraisal_message,career_goals_message from $config_table";
	$set=$db_object->get_a_line($qry);
	$values["directreplace"]["objectives"]=$set["objectives"];
	$values["directreplace"]["activities"]=$set["activities"];
	$values["directreplace"]["skills"]=$set["skills"];
	$values["directreplace"]["delimiter"]=$set["delimiter"];
	$values["directreplace"]["skill_levels"]=$set["skill_levels"];
	$values["directreplace"]["answer_count"]=$set["answer_count"];
	$values["directreplace"]["no_at_level"]=$set["no_at_level"];
	$values["directreplace"]["challenges"]=$set["challenges"];
	$values["directreplace"]["appraisal_message"]=$set["appraisal_message"];
	$values["directreplace"]["careergoal_message"]=$set["career_goals_message"];

	$xTemplate=$common->direct_replace($db_object,$xTemplate,$values);
	echo $xTemplate;
 }

  function update_settings($common,$db_object,$form_array)
  {

    	while(list($kk,$vv)=each($form_array))
  	{
  		$$kk=$vv;
  	}
if($fObjectives=="" ||$fChallenges=="" ||$fActivities==""||$fSkills==""||$fDelimiter==""||$fSkill_levels==""||$fAns_count==""||$fNo=="" || $fAppr_txt == "" || $fCareergl_txt == "")
{
	echo  "Some fields are entered as Null";
}
else
{
 	$config_table=$common->prefix_table("config");
  	$selqry="select id from $config_table where id=1";
  	$idrslt=$db_object->get_a_line($selqry);
    	if($idrslt["id"]!=1)
  	{
  	$updqry="insert into $config_table set id=1,objectives='$fObjectives',activities='$fActivities',skills='$fSkills',delimiter='$fDelimiter',skill_levels='$fSkill_levels',answer_count='$fAns_count',no_at_level='$fNo',challenges='$fChallenges',appraisal_message = '$fAppr_txt',career_goals_message = '$fCareergl_txt'";
  	}
  	else
  	{
  	$updqry="update $config_table set objectives='$fObjectives',activities='$fActivities',skills='$fSkills',delimiter='$fDelimiter',skill_levels='$fSkill_levels',answer_count='$fAns_count',no_at_level='$fNo',challenges='$fChallenges',appraisal_message = '$fAppr_txt',career_goals_message = '$fCareergl_txt' where id=1";
  	}
  	$db_object->insert($updqry);
}
  	
  }
  	
 

}
$adobj=new Admin;
if($submit)
{
	$adobj->update_settings($common,$db_object,$_POST);
}
if($user_id==1)
{
$adobj->edit_display($common,$db_object,$_POST);
}
else
{
echo $error_msg["cNoPermission"];
}
include("footer.php");
?>
