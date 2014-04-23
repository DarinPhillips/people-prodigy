<?php
/*---------------------------------------------
SCRIPT: selected_techrating_skills.php
AUTHOR:info@chrisranjana.com	
UPDATED:23th Oct

DESCRIPTION:
This script displays alert for technical ratings...

---------------------------------------------*/
include("../session.php");
include_once("header.php");

class selectedTechRatings
{
	function show_selectedSkills($db_object,$common,$post_var,$user_id,$default,$error_msg)
	{
		while(list($kk,$vv)=@each($post_var))
		{
		$$kk=$vv;
		}
		
		$xPath=$common->path;
		$returncontent=$xPath."/templates/career/selected_techrating_skills.html";
		$returncontent=$common->return_file_content($db_object,$returncontent);
		
		$skills_table = $common->prefix_table('skills');
		$skillraters_table = $common->prefix_table('skill_raters');
		$appraisal_table = $common->prefix_table('appraisal');
		$temp_tech_rating    = $common->prefix_table('temp_tech_rating');
		
		
		$mysql = "select skill_id from $temp_tech_rating where rating_user = '$user_id'";

		$skills_selected_arr = $db_object->get_single_column($mysql);
		
		
		$skills_selected = @implode(",",$skills_selected_arr);

		
		if($skills_selected !='')
		{
		$whereclause = "where skill_id in ($skills_selected)";
		}
		else
		{
			echo $error_msg['cTechRatings_nonesel'];
			return;
		}
		$mysql = "select skill_id,skill_name from $skills_table $whereclause";

		$selected_arr = $db_object->get_rsltset($mysql); 
		


	preg_match("/<{selectedskills_loopstart}>(.*?)<{selectedskills_loopend}>/s",$returncontent,$skillmatch);
	$newskillmatch = $skillmatch[1];

	preg_match("/<{selectedlabel_loopstart}>(.*?)<{selectedlabel_loopend}>/s",$newskillmatch,$labelmatch);
	$newlabelmatch = $labelmatch[1];


		
		for($i=0;$i<count($selected_arr);$i++)
		{
			$selected_skill_id = $selected_arr[$i]['skill_id'];
			$selected_skill = $selected_arr[$i]['skill_name'];
			
			$mysql = "select $skillraters_table.rater_level_$default as rating_level
					from $skillraters_table,$temp_tech_rating 
					where $skillraters_table.rater_id = $temp_tech_rating.selfrating_labelid
					and $temp_tech_rating.skill_id='$selected_skill_id' 
					and $temp_tech_rating.rating_user='$user_id'";
					
		
			$rating_arr = $db_object->get_a_line($mysql);
			
			
			$rating_label = $rating_arr['rating_level'];
			
			$str = preg_replace("/<{(.*?)}>/e","$$1",$newlabelmatch);
			
			  
			$submatch = preg_replace("/<{selectedlabel_loopstart}>(.*?)<{selectedlabel_loopend}>/s",$str,$newskillmatch);
			$str2 .= preg_replace("/<{(.*?)}>/e","$$1",$submatch);
		}
		
		
		
		
	$returncontent = preg_replace("/<{selectedskills_loopstart}>(.*?)<{selectedskills_loopend}>/s",$str2,$returncontent);

	$returncontent = $common->direct_replace($db_object,$returncontent,$values);
		
		
		echo $returncontent;
	}
}
$obj = new selectedTechRatings;
$obj->show_selectedSkills($db_object,$common,$post_var,$user_id,$default,$error_msg);

//include_once('footer.php');
?>

