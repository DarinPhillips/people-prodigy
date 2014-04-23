<?php
/*---------------------------------------------
SCRIPT:position_model.php
AUTHOR:info@chrisranjana.com	
UPDATED:14th Nov

DESCRIPTION:
This script displays the position models created by admin.

---------------------------------------------*/
include("../session.php");
include_once("header.php");

class position_model
{
	function select_components($db_object,$common,$default,$user_id,$post_var)
	{
		while(list($kk,$vv) = @each($post_var))
		{
			$$kk = $vv;
		}
	
		
		$xPath		= $common->path;
		$returncontent	= $xPath."/templates/career/position_model.html";
		$returncontent	= $common->return_file_content($db_object,$returncontent);


		$family 		= $common->prefix_table('family');
		$position 		= $common->prefix_table('position');
		$user_table 		= $common->prefix_table('user_table');
		$employment_type 	= $common->prefix_table('employment_type');
		$skills 		= $common->prefix_table('skills');
		$opportunity_status	= $common->prefix_table('opportunity_status');
		
		
// DISPLAY OF FAMILIES...
		$mysql 		= "select family_id,family_name from $family";
		$fam_arr 	= $db_object->get_rsltset($mysql);

		$values['family_loop'] = $fam_arr;
		
//IF THE FAMILY IS ALREADY SELECTED FROM OTHER LINKS, THEN THAT FAMILY IS SELECTED BY DEFAULT...
		if($f_id != '')
		{
			$sel_val[] = $f_id;
			$sel_arr['family_loop'] = $sel_val;
		}
		
// DISPLAY OF POSITIONS...
		$mysql 		= "select pos_id,position_name from $position";
		$position_arr 	= $db_object->get_rsltset($mysql);

		$values['position_loop'] = $position_arr;

// DISPLAY OF LOCATIONS...
		$location_arr 	= $common->return_location_for_display($db_object);

		preg_match("/<{location_loopstart}>(.*?)<{location_loopend}>/s",$returncontent,$match_loc);
		$newmatch_loc 	= $match_loc[1];
		
		while (list($key,$value) = @each($location_arr))
		{
			$loc_id 	= $location_arr[$key];
			$location_name 	= $location_arr[$value];
			$str           .= preg_replace("/<{(.*?)}>/e","$$1",$newmatch_loc);
		
		}
		
		$returncontent = preg_replace("/<{location_loopstart}>(.*?)<{location_loopend}>/s",$str,$returncontent);
		

// DISPLAY OF LEVELS...
		$level_arr = $common->return_levels($db_object);
		preg_match("/<{level_loopstart}>(.*?)<{level_loopend}>/s",$returncontent,$match_lev);
		$newmatch_lev = $match_lev[1];

		while(list($kk,$vv) = @each($level_arr))
		{
			$str1 .= preg_replace("/<{(.*?)}>/e","$$1",$newmatch_lev);
		}

		$returncontent = preg_replace("/<{level_loopstart}>(.*?)<{level_loopend}>/s",$str1,$returncontent);

// DISPLAY OF BOSSES...

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
		
		preg_match("/<{boss_loopstart}>(.*?)<{boss_loopend}>/s",$returncontent,$boss_old);
		$boss_new = $boss_old[1];
		
		while(list($kk,$vv) = @each($boss_arr))
		{
			$mysql = "select username from $user_table where user_id = $vv";
			$name_arr = $db_object->get_a_line($mysql);

			$boss_name = $name_arr['username'];
			$boss_id = $vv;
			
			$str2 .= preg_replace("/<{(.*?)}>/e","$$1",$boss_new);
			
			
		}
		$returncontent = preg_replace("/<{boss_loopstart}>(.*?)<{boss_loopend}>/s",$str2,$returncontent);

// DISPLAY OF ALL USERS...

		$loopname = "user_loop";
		$returncontent = $common->show_Username($db_object,$returncontent,$loopname);
		
// DISPLAY OF ALL EMPLOYMENT TYPES...

		$mysql = "select id , type_$default as type from employment_type";
		$emp_type_arr = $db_object->get_rsltset($mysql);

		$values['emptype_loop'] = $emp_type_arr;
		

// DISPLAY ALL TECHNICAL SKILLS...

		$mysql = "select skill_id,skill_name from $skills where skill_type = 't'";
		$skill_tech_arr = $db_object->get_rsltset($mysql);
		$values['skilltech_loop'] = $skill_tech_arr;

// DISPLAY OF INTERPERSONAL SKILLS...

		$mysql = "select skill_id,skill_name from $skills where skill_type = 'i'";
		$skill_inter_arr = $db_object->get_rsltset($mysql);
		$values['skillinter_loop'] = $skill_inter_arr;

// CHECK OF LEARNING MODULE (IF PURCHASED)...
		
	$returncontent = $common->is_module_purchased($db_object,$xPath,$returncontent,$common->lfvar);

// CHECK OF PERFORMANCE MODULE (IF PURCHASED)...

	$returncontent = $common->is_module_purchased($db_object,$xPath,$returncontent,$common->pfvar);

// DISPLAY OF EEO CLASSES...

	$mysql = "select eeo_id , tag from $opportunity_status";
	$eeo_arr = $db_object->get_rsltset($mysql);

	$values['eeomain_loop'] = $eeo_arr;

	
	$returncontent	= $common->multipleloop_replace($db_object,$returncontent,$values,$sel_arr);
			
	$returncontent = $common->direct_replace($db_object,$returncontent,$values);
	
	echo $returncontent;
	}
	
}
$obj = new position_model;

$obj->select_components($db_object,$common,$default,$user_id,$post_var);

include_once("footer.php");
?>
