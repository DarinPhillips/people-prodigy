<?php
/*---------------------------------------------
SCRIPT:position_model2.php
AUTHOR:info@chrisranjana.com	
UPDATED:17th Nov

DESCRIPTION:
This script displays the second step of position models created by admin.

---------------------------------------------*/
include("../session.php");


class pos_Model
{
	function qualify_components($db_object,$common,$post_var,$default,$gbl_skill_type,$user_id,$error_msg,$gbl_files,$mid)
	{
		$xPath		= $common->path;
		$returncontent	= $xPath."/templates/career/position_model2.html";
		$returncontent	= $common->return_file_content($db_object,$returncontent);

		$user_table  		= $common->prefix_table('user_table');
		$rater_label_relate 	= $common->prefix_table('rater_label_relate');
		$skill_raters 		= $common->prefix_table(skill_raters);
		$family 		= $common->prefix_table('family');
		$family_position 	= $common->prefix_table('family_position');
		$position 		= $common->prefix_table('position');
		$location_table		= $common->prefix_table('location_table');
		$employment_type 	= $common->prefix_table('employment_type');
		$opportunity_status 	= $common->prefix_table('opportunity_status');
		$skills 		= $common->prefix_table('skills');
		$user_eeo		= $common->prefix_table('user_eeo');
		
		while(list($kk,$vv) = @each($post_var))
		{
			$$kk = $vv;

		}
		 
//Components to be displayed in the last column...
		
		$fam_comp = @implode(",",$fFamilies);
		$boss_comp = @implode(",",$fBoss);
		$pos_comp = @implode(",",$fPositions);
		$users = @implode(",",$fEmpl);
		$loc_comp = @implode(",",$fLocations);
		$emptype_comp = @implode(",",$fEmpl_types);
		$level_comp = @implode(",",$fLevels);
		$eeo_comp = @implode(",",$fEeo);
		$i_skills = @implode(",",$fInter_skills);
		$t_skills = @implode(",",$fTech_skills);
		
$components = "families:$fam_comp||boss:$boss_comp||positions:$pos_comp||employees:$users||location:$loc_comp||employment_type:$emptype_comp||levels:$level_comp||eeo:$eeo_comp||iskills:$i_skills||tskills:$t_skills";
		

setcookie("ModelComponents",$components,0,"/");

include("header.php");
$values['model_id'] = $mid;
if($fRating_plan == 'per')
{
	$returncontent = preg_replace("/<{learning_rating_start}>(.*?)<{learning_rating_end}>/s","",$returncontent);
	$returncontent = preg_replace("/<{performance_rating_(.*?)}>/s","",$returncontent);	
}
elseif($fRating_plan == 'learn') 
{
	$returncontent = preg_replace("/<{performance_rating_start}>(.*?)<{performance_rating_end}>/s","",$returncontent);
	$returncontent = preg_replace("/<{learning_rating_(.*?)}>/s","",$returncontent);	
}



//FILTRATION OF THE FAMILIES BOX...
//=================================
//The Whole Check Is Done Only If The Families Box Is Selected In Some Way...
		
	if($fFamilies != '')
	{
		if(@in_array("All",$fFamilies))
		{
			
			$mysql = "select family_id from $family";
			$family_arr = $db_object->get_single_column($mysql);
			
			
		}
		if(@in_array("None",$fFamilies))
		{
			
			$family_arr = '';
		}
		elseif(!@in_array("All",$fFamilies) && !@in_array("None",$fFamilies))
		{
			
			$all_fam = @implode("','",$fFamilies);
			$mysql = "select family_id from $family where family_id in ('$all_fam')";
			$family_arr = $db_object->get_single_column($mysql);
		}

		
		if($family_arr != '')
		{
			$family_sel = @implode("','",$family_arr);

			$fam_subquery = "select position_id from $family_position where family_id in ('$family_sel')";
			$family_positions_arr = $db_object->get_single_column($fam_subquery);
	
			$family_positions = @implode("','",$family_positions_arr);

			$fam_subquery = "and pos_id in ('$family_positions')";
 	
		}
	}
	


//FILTRATION OF THE POSITIONS BOX...
//==================================


	if($fPositions != '')
	{
		if(@in_array("All",$fPositions))
		{
			
			$mysql = "select pos_id from $position";
			$position_arr = $db_object->get_single_column($mysql);
			
			
		}
		if(@in_array("None",$fPositions))
		{
			
			$position_arr = '';
		}
		elseif(!@in_array("All",$fPositions) && !@in_array("None",$fPositions))
		{
			
			$all_pos = @implode("','",$fPositions);
			
			$mysql = "select pos_id from $position where pos_id in ('$all_pos')";
			$position_arr = $db_object->get_single_column($mysql);
			
		}

		if($position_arr != '')
		{
			$position_sel = @implode("','",$position_arr);
		}
		
	}



// FILTRATION OF THE LOCATIONS ...
// ===============================



	if($fLocations != '')
	{
		if(@in_array("All",$fLocations))
		{
	
			$mysql = "select location_id from $location_table";
			$location_arr = $db_object->get_single_column($mysql);
						
			
		}
		if(@in_array("None",$fLocations))
		{
	
			$location_arr = '';
		}
		elseif(!@in_array("All",$fLocations) && !@in_array("None",$fLocations))
		{
	
			$all_loc = @implode("','",$fLocations);
	
			$mysql = "select location_id from $location_table where location_id in ('$all_loc');";
	
			$location_arr = $db_object->get_single_column($mysql);
		}

		
		if($location_arr != '')
		{
			$location_sel = @implode("','",$location_arr);

			$loc_subquery = "and location in ('$location_sel')";
	
	 
		}

	}
	


//FILTRATION OF THE LEVELS...
//===========================

	
	if($fLevels != '')
	{
		if(@in_array("All",$fLevels))
		{

			$levels_arr = $common->return_levels($db_object);
		}
		if(@in_array("None",$fLevels))
		{

			$levels_arr = '';
		}
		elseif(!@in_array("All",$fLevels) && !@in_array("None",$fLevels))
		{

		
			$levels_arr = $fLevels;
		}
		if($levels_arr != '')
		{
			$levels_sel = @implode("','",$levels_arr);

			$level_subquery = "and level_no in ('$levels_sel')";
		
		}
		
	}


//FIRST COLUMN FILTRATION...

$mysql = "select pos_id from $position where status = 'a' $fam_subquery $loc_subquery $level_subquery";
$first_column_arr = $db_object->get_single_column($mysql);

$first_column_pos = @implode("','",$first_column_arr);

$mysql = "select user_id from $user_table where position in ('$first_column_pos')";
$first_column_users_arr = $db_object->get_single_column($mysql);

$first_column_users = @implode("','",$first_column_users_arr);

if($fam_subquery != '' || $loc_subquery != '' || $level_subquery != '')
	{
	$first_column_query = "and user_id in ('$first_column_users')";
	}

	
//THE BOSS IS TAKEN INTO CONSIDERATION AND THEN SELECTIVE DIRECT REPORTS ARE SELECTED AND DISPLAYED IN THIS SCREEN...
//****************************************************************************************************		

	if($fBoss != '')
	{
		if(@in_array("All",$fBoss))              
		{
			$returncontent = preg_replace("/<{bosssel_loop(.*?)}>/s","",$returncontent);
			
			$position_id = 1;
//and boss_no in ('$family_positions')
  	
			$all_dirrep = $common->get_chain_below($position_id,$db_object,$twodarr);
			
			
			$all_directreports = @implode("','",$all_dirrep);

	
			$mysql = "select pos_id from $position where pos_id in ('$all_directreports')";
	
			$pos_of_all_arr = $db_object->get_single_column($mysql);
	
			$pos_of_all = @implode("','",$pos_of_all_arr);
			$mysql = "select user_id,username from $user_table where position in ('$pos_of_all')";
			$directreports_arr1 = $db_object->get_rsltset($mysql);
	
			
		}
		
		if(@in_array("None",$fBoss))
		{
	
			$returncontent = preg_replace("/<{bosssel_loopstart}>(.*?)<{bosssel_loopend}>/s","",$returncontent);
			$all_dirrep = '';
		}
		elseif(!@in_array("All",$fBoss) && !@in_array("None",$fBoss))
		{
			$returncontent = preg_replace("/<{bosssel_loop(.*?)}>/s","",$returncontent);
			
	
			$all_none_arr = array("All","None");
			$fBoss = @array_diff($fBoss,$all_none_arr);

//SPECIFIC DIRECT REPORTS DISPLAYED IN THE STEP 2...
//==================================================
		
		
		$allboss = @implode("','",$fBoss);
		
		$mysql = "select position from $user_table where user_id in ('$allboss')";
		$allbosspos_arr = $db_object->get_single_column($mysql);
		$allbosspos = @implode("','",$allbosspos_arr);
		$mysql = "select pos_id from $position where boss_no in ('$allbosspos')";
		
		$allposofdirep_arr = $db_object->get_single_column($mysql);
		
		$alldirreppos_old = @implode("','",$allposofdirep_arr);

//HERE WE SELECT THE USERS BELONGING TO THE PARTICULAR FAMILY ARE LISTED UNDER THE DIRECT REPORTS...
//PROVIDED THE FAMILY BOX IS NOT NULL
		
		if($family_sel != '')
		{
		$mysql = "select position_id from family_position where position_id in ('$alldirreppos_old') and family_id in ('$family_sel')";
		//echo $mysql;
		$pos_in_fam_arr = $db_object->get_single_column($mysql);
		
		$alldirreppos = @implode("','",$pos_in_fam_arr);
		if($alldirreppos != '')
		{
		$mysql = "select user_id,username from $user_table where position in ('$alldirreppos')";
		//echo "$mysql<br>";
		$directreports_arr1 = $db_object->get_rsltset($mysql);
		}
		}
		else
		{
		$mysql = "select user_id from $user_table where position in ('$alldirreppos')";
		$directreports_arr = $db_object->get_single_column($mysql);
		}
		}
		
		
	}

		$returncontent = preg_replace("/<{bosssel_loopstart}>(.*?)<{bosssel_loopend}>/s","",$returncontent);	
$values['dirrep_loop'] = $directreports_arr1;
		
$all_boss = @implode(",",$fBoss);

$values['all_boss']=$all_boss;


// FILTRATION OF THE EMPLOYEES SELECTED...
// =======================================

	if($fEmpl != '')
	{
		if(@in_array("All",$fEmpl))              
		{

			$mysql = "select user_id from $user_table";
			$users_arr = $db_object->get_single_column($mysql);
			
		}
		if(@in_array("None",$fEmpl))
		{

			$users_arr = '';
		}
		elseif(!@in_array("All",$fEmpl) && !@in_array("None",$fEmpl))
		{
		
			$user_full = @implode("','",$fEmpl);
			$mysql = "select user_id from $user_table where user_id in ('$user_full')";
			$users_arr = $db_object->get_single_column($mysql);
		}
		if($users_arr != '')
		{
			$users_sel = @implode("','",$users_arr);

			$users_subquery = "and user_id in ('$users_sel')";
		}

		
	}


// FILTRATION OF THE SELECTED EMPLOYMENT TYPES...
// ==============================================



	if($fEmpl_types != '')
	{
		if(@in_array("All",$fEmpl_types))              
		{

			$mysql = "select id from $employment_type";
			$employment_types_arr = $db_object->get_single_column($mysql);
			
		}
		if(@in_array("None",$fEmpl_types))
		{

			$employment_types_arr = '';
		}
		elseif(!@in_array("All",$fEmpl_types) && !@in_array("None",$fEmpl_types))
		{

			$empltype_full = @implode("','",$fEmpl_types);
			$mysql = "select id from $employment_type where id in ('$empltype_full')";
			$employment_types_arr = $db_object->get_single_column($mysql);
		}
		if($employment_types_arr != '')
		{
			$employment_types_sel = @implode("','",$employment_types_arr);

			$empl_types_subquery = "and employment_type in ('$employment_types_sel')";
	 
		}

	}


// FILTRATION OF THE SELECTED EEO STATUS ...
// =========================================

	if($fEeo != '')
	{
		if(@in_array("All",$fEeo))              
		{

			$mysql = "select eeo_id from $opportunity_status";

			$eeo_arr = $db_object->get_single_column($mysql);
			
		}
		if(@in_array("None",$fEeo))
		{

			$eeo_arr = '';
		}
		elseif(!@in_array("All",$fEeo) && !@in_array("None",$fEeo))
		{

			$eeo_full = @implode("','",$fEeo);
			$mysql = "select eeo_id from $opportunity_status where eeo_id in ('$eeo_full')";

			$eeo_arr = $db_object->get_single_column($mysql);
		}
		if($eeo_arr != '')
		{
			$eeo_sel = @implode("','",$eeo_arr);
			$mysql = "select distinct(user_id) from $user_eeo where tag_id in ('$eeo_sel')";
			$eeo_users_arr = $db_object->get_single_column($mysql);
			$eeo_users_all = @implode("','",$eeo_users_arr);
	
			$eeo_subquery = "and user_id in ('$eeo_users_all')";
		
		}
		
	}

// FILTRATION OF THE SELECTED INTERPERSONAL SKILLS...
// ==================================================

	if($fInter_skills != '')
	{
	
		if(@in_array("All",$fInter_skills))              
		{

			$mysql = "select skill_id from $skills where skill_type = 'i'";

			$interskills_arr = $db_object->get_single_column($mysql);
			
		}
		
		elseif(!@in_array("All",$fInter_skills))
		{

			$interskills_full = @implode("','",$fInter_skills);
			$mysql = "select skill_id from $skills where skill_type = 'i' and skill_id in ('$interskills_full')";

			$interskills_arr = $db_object->get_single_column($mysql);
		}
		
	}
	else
	{
		$mysql = "select skill_id from $skills where skill_type = 'i'";

			$interskills_arr = $db_object->get_single_column($mysql);
			
	}


// FILTRATION OF THE SELECTED TECHNICAL SKILLS...
// ==============================================
	if($fTech_skills != '')
	{
	
		if(@in_array("All",$fTech_skills))              
		{

			$mysql = "select skill_id from $skills where skill_type = 't'";

			$techskills_arr = $db_object->get_single_column($mysql);
			
		}
		
		elseif(!@in_array("All",$fTech_skills))
		{

			$techskills_full = @implode("','",$fTech_skills);
			$mysql = "select skill_id from $skills where skill_type = 't' and skill_id in ('$techskills_full')";

			$techskills_arr = $db_object->get_single_column($mysql);
		}
		
	}
	else
	{
		$mysql = "select skill_id from $skills where skill_type = 't'";
		$techskills_arr = $db_object->get_single_column($mysql);
			
	}



//SECOND COLUMN FILTRATION...

if($users_subquery == '' && $empl_types_subquery == '' && $eeo_subquery == '' && $first_column_query == '') 		//$dirrep_subquery == '' && 
{
	$model_users_arr = '';
}
elseif($users_subquery != '')
{
	$mysql = "select user_id from $user_table where user_type='employee' $users_subquery";
	$model_users_arr = $db_object->get_single_column($mysql);
	
}
else
{
	$mysql = "select user_id from $user_table where user_type='employee'
		$users_subquery
		$empl_types_subquery
		$eeo_subquery
		$first_column_query";

		$model_users_arr = $db_object->get_single_column($mysql);


}

$users_of_model = @implode(",",$model_users_arr);
$values['users_of_model']=$users_of_model;

$users_of_model = @implode(",",$model_users_arr);

if($interskills_arr != '')
{
	$interskills_sel = @implode("','",$interskills_arr);

	
}
if($techskills_arr != '')
{
	$techskills_sel = @implode("','",$techskills_arr);

}


$interskills = @implode(",",$interskills_arr);
$techskills = @implode(",",$techskills_arr);

$values['interskills'] = $interskills;
$values['techskills'] = $techskills;



//DISPLAY OF THE RATING LABELS TO BE CHOSEN...
//============================================	
//INTERPERSONAL...
		
	preg_match("/<{skillratinglabels_loopstart}>(.*?)<{skillratinglabels_loopend}>/s",$returncontent,$labelmatch);
	$newlabelmatch = $labelmatch[1];
	
	$mysql = "select $rater_label_relate.rater_labelno as label_id,$skill_raters.rater_level_$default as label_name 
				from $skill_raters,$rater_label_relate
				where $rater_label_relate.rater_id = $skill_raters.rater_id
				and $rater_label_relate.rater_type='i' order by label_id desc";
		

		$raters_arr = $db_object->get_rsltset($mysql);

		for($i=0;$i<count($raters_arr);$i++)
		{
			$label_id = $raters_arr[$i]['label_id'];
			$label_name = $raters_arr[$i]['label_name'];

			$str .= preg_replace("/<{(.*?)}>/e","$$1",$newlabelmatch);
			
			
		}

		$returncontent = preg_replace("/<{skillratinglabels_loopstart}>(.*?)<{skillratinglabels_loopend}>/s",$str,$returncontent);


//TECHNICAL LABELS...
		
		preg_match("/<{skillratingtech_loopstart}>(.*?)<{skillratingtech_loopend}>/s",$returncontent,$techlabelmatch);
		$newtechlabelmatch = $techlabelmatch[1];
		
		$mysql = "select $rater_label_relate.rater_labelno as label_id,$skill_raters.rater_level_$default as label_name 
				from $skill_raters,$rater_label_relate
				where $rater_label_relate.rater_id = $skill_raters.rater_id
				and $rater_label_relate.rater_type='t' order by label_id desc";
	
		$raters_tech_arr = $db_object->get_rsltset($mysql);
		
		for($i=0;$i<count($raters_tech_arr);$i++)
		{
			$label_id = $raters_tech_arr[$i]['label_id'];
			$label_name = $raters_tech_arr[$i]['label_name'];

			$str1 .= preg_replace("/<{(.*?)}>/e","$$1",$newtechlabelmatch);
			
		}
		
		$returncontent = preg_replace("/<{skillratingtech_loopstart}>(.*?)<{skillratingtech_loopend}>/s",$str1,$returncontent);
		
		$returncontent = $common->multipleloop_replace($db_object,$returncontent,$values,'');
		
		$returncontent = $common->direct_replace($db_object,$returncontent,$values);
		
		echo $returncontent;
	}


function save_components($db_object,$common,$post_var,$default,$gbl_skill_type,$user_id,$error_msg)
{
	while(list($kk,$vv) = @each($post_var))
	{
		$$kk = $vv;
	}
	//print_r($post_var);

	$model_table 		= $common->prefix_table('model_table');
	$model_factors_1 	= $common->prefix_table('model_factors_1');
	$model_factors_2 	= $common->prefix_table('model_factors_2');
	$model_factors_3 	= $common->prefix_table('model_factors_3');
	$model_factors_4 	= $common->prefix_table('model_factors_4');
	$model_factors_5 	= $common->prefix_table('model_factors_5');
	$model_factors_6 	= $common->prefix_table('model_factors_6');
	$model_factors_7 	= $common->prefix_table('model_factors_7');
	$model_factors_8 	= $common->prefix_table('model_factors_8');
	$model_factors_9 	= $common->prefix_table('model_factors_9');
	$model_factors_10 	= $common->prefix_table('model_factors_10');
	$family 		= $common->prefix_table('family');
	$user_table		= $common->prefix_table('user_table');
	$location_table 	= $common->prefix_table('location_table');
	$employment_type 	= $common->prefix_table('employment_type');
	$skills			= $common->prefix_table('skills');
	$user_eeo 		= $common->prefix_table('user_eeo');
	
	
//SAVE USERID TO MODEL TABLE AND GET THE ID WHICH WILL BE THE MODEL ID...
	$mysql = "insert into $model_table set user_id = '$user_id', model_created_on = now()";
	$cur_model_id = $db_object->insert_data_id($mysql);
	
	
	if($fFamilies != '')
	{
		if(@in_array("All",$fFamilies))
		{
			$mysql = "select family_id from $family";
			$fam_arr = $db_object->get_single_column($mysql);

		}
		if(@in_array("None",$fFamilies))
		{
			$fam_arr = '';			
		}
		elseif(!@in_array("All",$fFamilies) && !@in_array("None",$fFamilies))
		{
			$fam_arr = $fFamilies;
		}
		
		for($i=0;$i<count($fam_arr);$i++)
		{
			$mysql = "insert into $model_factors_1 set model_id = '$cur_model_id',family='$fam_arr[$i]'";
			$db_object->insert($mysql);
		}
		
		
	}
	if($fBoss != '')
	{
		if(@in_array("All",$fBoss))
		{

		$mysql = "select user_id from $user_table";

		$check_boss_arr = $db_object->get_single_column($mysql);

		$j=0;

		for($i=0;$i<count($check_boss_arr);$i++)
			{
				$check_if_boss = $check_boss_arr[$i];
				
				$boss_check = $common->is_boss($db_object,$check_if_boss);

				if($boss_check)
				{
					$boss_arr[$j] = $check_if_boss;
					$j++;
				}
			}
		}
		if(@in_array("None",$fBoss))
		{
			$boss_arr = '';
		}	
		elseif(!@in_array("All",$fBoss) && !@in_array("None",$fBoss))
		{
			$boss_arr = $fBoss;
		}
		for($i=0;$i<count($boss_arr);$i++)
		{
			$mysql = "insert into $model_factors_4 set model_id = '$cur_model_id',boss_id='$boss_arr[$i]'";
			$db_object->insert($mysql);
		}
	}
	if($fRating_plan == 'per')
	{
		$rating = "performance";
	}
	elseif($fRating_plan == 'learn')
	{
		$rating = "learning";	
	}
	$mysql = "insert into $model_factors_8 set model_id = '$cur_model_id',rating = '$rating'";
	$db_object->insert($mysql);
	
	if($fEmpl != '')
	{
		if(@in_array("All",$fEmpl))
		{
			$mysql = "select user_id from $user_table";
			$user_arr = $db_object->get_single_column($mysql);
			
		}
		if(@in_array("None",$fEmpl))
		{
			$user_arr='';	
		}
		elseif(!@in_array("All",$fEmpl) && !@in_array("None",$fEmpl))
		{
			$user_arr = $fEmpl;
		}
		for($i=0;$i<count($user_arr);$i++)
		{
			$mysql = "insert into $model_factors_5 set model_id = '$cur_model_id',employees='$user_arr[$i]'";
			$db_object->insert($mysql);
		}

	}
	if($fLocations != '')
	{
		if(@in_array("All",$fLocations))
		{
			$mysql = "select location_id from $location_table";
			$location_arr = $db_object->get_single_column($mysql);
				
		}
		if(@in_array("None",$fLocations))
		{
			$location_arr = '';
		}
		elseif(!@in_array("All",$fLocations) && !@in_array("None",$fLocations))
		{
			$location_arr = $fLocations;
		}
		for($i=0;$i<count($location_arr);$i++)
		{
			$mysql = "insert into $model_factors_2 set model_id = '$cur_model_id',location='$location_arr[$i]'";
			$db_object->insert($mysql);
		}
	}
	
	if($fEmpl_types != '')
	{
		if(@in_array("All",$fEmpl_types))
		{
			$mysql = "select id from $employment_type";
			$empltypes_arr = $db_object->get_single_column($mysql);
			
		}
		if(@in_array("None",$fEmpl_types))
		{
			$empltypes_arr = '';
		}
		elseif(!@in_array("All",$fEmpl_types) && !@in_array("None",$fEmpl_types))
		{
			$empltypes_arr = $fEmpl_types;
		}
		for($i=0;$i<count($empltypes_arr);$i++)
		{
			$mysql = "insert into $model_factors_6 set model_id = '$cur_model_id',emp_types='$empltypes_arr[$i]'";
			$db_object->insert($mysql);
		}
	}
	if($fInter_skills != '')
	{
		if(@in_array("All",$fInter_skills))
		{
			$mysql = "select skill_id from $skills where skill_type = 'i'";
			$interskills_arr = $db_object->get_single_column($mysql);
			
		}
		else
		{
			$interskills_arr = $fInter_skills;
		}
		for($i=0;$i<count($interskills_arr);$i++)
		{
			$mysql = "insert into $model_factors_9 set model_id = '$cur_model_id' , iskills = '$interskills_arr[$i]'";
			$db_object->insert($mysql);	
		}
	}
	if($fLevels != '')
	{
		if(@in_array("All",$fLevels))
		{
			$level_arr = $common->return_levels($db_object);
						
		}
		if(@in_array("None",$fLevels))
		{
			$level_arr = '';
		}
		elseif(!@in_array("All",$fLevels) && !@in_array("None",$fLevels))
		{
			$level_arr = $fLevels;
		}
		for($i=0;$i<count($level_arr);$i++)
		{
			$mysql = "insert into $model_factors_3 set model_id = '$cur_model_id' , levels = '$level_arr[$i]'";
			$db_object->insert($mysql);	
		}
	}
	if($fEeo != '')
	{
		if(@in_array("All",$fEeo))
		{
			$mysql = "select tag_id from $user_eeo";
			$eeo_arr = $db_object->get_single_column($mysql);
		}
		if(@in_array("None",$fEeo))
		{
			$eeo_arr = '';
		}
		elseif(!@in_array("All",$fEeo) && !@in_array("None",$fEeo))
		{
			$eeo_arr = $fEeo;	
		}
		for($i=0;$i<count($eeo_arr);$i++)
		{
			$mysql = "insert into $model_factors_7 set model_id = '$cur_model_id' , eeo_type = '$eeo_arr[$i]'";
			$db_object->insert($mysql);	
		}
		

	}
	if($fTech_skills != '')
	{
		if(@in_array("All",$fTech_skills))
		{
			$mysql = "select skill_id from $skills where skill_type = 't'";
			$techskills_arr = $db_object->get_single_column($mysql);
			
		}
		else
		{
			$techskills_arr = $fTech_skills;
		}
		for($i=0;$i<count($techskills_arr);$i++)
		{
			$mysql = "insert into $model_factors_10 set model_id = '$cur_model_id' , tskills = '$techskills_arr[$i]'";

			$db_object->insert($mysql);	
		}
	}	
 
return $cur_model_id;



}


} 	// END OF CLASS

$obj = new pos_Model;

if($fSubmit)
{
	$mid = $obj->save_components($db_object,$common,$post_var,$default,$gbl_skill_type,$user_id,$error_msg);
	$obj->qualify_components($db_object,$common,$post_var,$default,$gbl_skill_type,$user_id,$error_msg,$gbl_files,$mid);
}

include_once("footer.php");
?>
