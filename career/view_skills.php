<?php
include("../session.php");
include("header.php");
class View_Skills
{
  function view_all_skills($common,$db_object,$user_id,$error_msg)
  {
	$xPath		= $common->path;
	$returncontent	= $xPath."/templates/career/view_skills.html";
	$returncontent	= $common->return_file_content($db_object,$returncontent);
	
	$skills = $common->prefix_table('skills');	
	
	$mysql = "select skill_id,skill_name,
			skill_description,
			unskilled_desc,
			over_used,
			career_killer,
			compensator 
			from $skills where skill_type = 'i' and skill_name<>''";
	$skills_display_arr = $db_object->get_rsltset($mysql);
	
	$mulvals['skilldisplay_loop'] = $skills_display_arr;
	
	
	$mysql = "select skill_id,skill_name,
			skill_description
			from $skills where skill_type = 't' and skill_name<>''";
	$skills_displaytech_arr = $db_object->get_rsltset($mysql);

	$mulvals['skilldisplaytech_loop'] = $skills_displaytech_arr;
	$returncontent = $common->multipleloop_replace($db_object,$returncontent,$mulvals,0);
	
	$returncontent 	= $common->direct_replace($db_object,$returncontent,$values);
	
	echo $returncontent;
	 
  }

}
$viewobj= new View_Skills;
$viewobj->view_all_skills($common,$db_object,$user_id,$error_msg);
include("footer.php");
?>
