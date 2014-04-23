<?php

/*---------------------------------------------
SCRIPT:career_goals.php
AUTHOR:info@chrisranjana.com	
UPDATED:13th Dec

DESCRIPTION:
This script sets the career goals for the users

---------------------------------------------*/

include("../session.php");


class depthchart
{
function showdepthchart($db_object,$common,$post_var,$user_id,$default,$gbl_met_value,$gbl_files)
	{
		while(list($kk,$vv) = @each($post_var))
		{
			$$kk = $vv;
		}


	$xPath		= $common->path;
	$returncontent	= $xPath."templates/career/depth_chart.html";
	$returncontent	= $common->return_file_content($db_object,$returncontent);
	
	$position 		= $common->prefix_table('position');
	$career_goals   	= $common->prefix_table('career_goals');
	$org_main		= $common->prefix_table('org_main');
	$user_table		= $common->prefix_table('user_table');
	$position		= $common->prefix_table('position');	
	$family_position	= $common->prefix_table('family_position');
	$user_eeo		= $common->prefix_table('user_eeo');
	$opportunity_status	= $common->prefix_table('opportunity_status');
	$models_percent_fit 	= $common->prefix_table('models_percent_fit');
	$employment_type	= $common->prefix_table('employment_type');

//PERFORMANCE WHAT & HOW DONE VALUES TAKEN FROM THE FOLLOWING TABLE...

	$approved_selected_objective 	= $common->prefix_table('approved_selected_objective');
	$approveduser_objective 	= $common->prefix_table('approveduser_objective');
	$approved_feedback 		= $common->prefix_table('approved_feedback');
	$config_table			= $common->prefix_table('config');
	$rating_table			= $common->prefix_table('rating');	


	$mysql 			= "select position_name from $position where pos_id = '$posid'";
	$posname_arr 		= $db_object->get_a_line($mysql);
	$posname 		= $posname_arr['position_name'];
	$values['posname'] 	= $posname;
	$values['posid'] 	= $posid;
	$values['modelid'] 	= $model_to_view;

	$components 		= $_COOKIE['successionplans'];		
	$comp_sel 		= @explode("||",$components);

	while(list($kk,$vv) = @each($comp_sel))
		{
	 
			if(ereg("^matchperpos:",$vv))
			{
			$mppos = ereg_replace("matchperpos:","",$vv);
	
			if($mppos != '')
				{
				$match_per_pos = $mppos;
				}
			}
			 
			if(ereg("^lh1:",$vv))
			{
			$lev1high = ereg_replace("lh1:","",$vv);
	
			if($lev1high != '')
				{
				$one_level_higher = $lev1high;
				}
			}

			 
			if(ereg("^lsame:",$vv))
			{
			$levsame = ereg_replace("lsame:","",$vv);
	
			if($levsame != '')
				{
				$same_level = $levsame;
				}
			}

		 
			if(ereg("^ll1:",$vv))
			{
			$lev1low = ereg_replace("ll1:","",$vv);
	
			if($lev1low != '')
				{
				$one_level_lower = $lev1low;
				}
			}

			 
			if(ereg("^ll2:",$vv))
			{
			$lev2low = ereg_replace("ll2:","",$vv);
	
			if($lev2low != '')
				{
				$two_level_lower = $lev2low;
				}
			}
					
			 
			if(ereg("^ilot:",$vv))
			{
			$int_lot = ereg_replace("ilot:","",$vv);
	
			if($int_lot != '')
				{
				$interest_lot = $int_lot;
				}
			}

			 
			if(ereg("^isome:",$vv))
			{
			$int_some = ereg_replace("isome:","",$vv);
	
			if($int_some != '')
				{
				$interest_some = $int_some;
				}
			}
 
			if(ereg("^ino:",$vv))
			{
			$int_no = ereg_replace("ino:","",$vv);
	
			if($int_no != '')
				{
				$interest_no = $int_no;
				}
			}
			
			 
			if(ereg("^ilplan:",$vv))
			{
			$int_lplan = ereg_replace("ilplan:","",$vv);
	
			if($int_lplan != '')
				{
				$interest_lplan = $int_lplan;
				}
			}
 
			if(ereg("^etags:",$vv))
			{
			$etags_all = ereg_replace("etags:","",$vv);
	
			if($etags_all != '')
				{
				$etags_arr = @explode(",",$etags_all);
				}
			}
			if(ereg("^etypes:",$vv))
			{
			$emptypes_all = ereg_replace("etypes:","",$vv);
	
			if($emptypes_all != '')
				{
				$emptypes_arr = @explode(",",$emptypes_all);
				}
			}
			
		}

//echo $match_per_pos;exit;
//echo $one_level_higher;exit;
//echo $same_level;exit;
//echo $one_level_lower;exit;
//echo $two_level_lower;exit;
//echo $interest_lot;exit;
//echo $interest_some;exit;
//echo $interest_no;exit;
//echo $interest_lplan;exit;
//print_r($etags_arr);exit;
//print_r($emptypes_arr);exit;

 

//FIND THE FAMILY OF THE CURRENT POSITION...


	$mysql = "select family_id from $family_position where position_id = '$posid'";
	$fam_pos_arr = $db_object->get_a_line($mysql);
	$family_of_this_position = $fam_pos_arr['family_id'];


//CHECK WHICH WAY THE ORGANISATION HAS BUILT THE ORGANISATIONAL CHART 
//IF HIGHERORDER=YES THEN 8-7-..1
//ELSE 1-2-..8

	$mysql = "select higher_order from $org_main";
	$higherorder_arr = $db_object->get_a_line($mysql);	
	$higherorder = $higherorder_arr['higher_order'];

//DETERMINE WHAT ALL MODELS THIS PERSON IS CAPABLE OF VIEWING...
//DETERMINE THE LEVEL OF THE CURRENT POSITION...

	$mysql = "select level_no from $position where $position.pos_id = '$posid'";
	$lev_arr = $db_object->get_a_line($mysql);

	$cur_level = $lev_arr['level_no'];
	
//SELECT EMPLOYEES ONE LEVEL HIGHER THAN THE CURRENT POSITION SELECTED FROM THE PREVIOUS SCREEEN

	if($higherorder == 'yes')
		{
			$higher_level1 = $cur_level + 1;
			$lower_level1 = $cur_level - 1;
			$lower_level2 = $cur_level - 2;
		}
	else
		{
			$higher_level1 = $cur_level - 1;
			$lower_level1 = $cur_level + 1;
			$lower_level2 = $cur_level + 2;
		}	
	

//THIS IS THE NO OF EMPLOYEES TO SHOW PER POSITION...
/*------------------------------------------------------------------------------------

//$match_per_pos

$count_of_filters = 0;	

	if($two_level_lower == 'on')
	{
		$count_of_filters++;
	}

	if($one_level_lower == 'on')
	{
		$count_of_filters++;
	}

	if($same_level == 'on')
	{
		$count_of_filters++;
	}
	
	if($one_level_higher == 'on')
	{
		$count_of_filters++;
	}


$no_of_employees_to_show = floor($match_per_pos / $count_of_filters);

//echo "Matches per Position $match_per_pos <br>";
//echo "Total Levels selected = $count_of_filters <br>";
//echo "Employees to show for each level $no_of_employees_to_show <br>";
//$remaining_no = $match_per_pos - ($no_of_employees_to_show * $count_of_filters);
//echo "Remaining No is $remaining_no<br>";


if(($no_of_employees_to_show * $count_of_filters) != $match_per_pos)
{
$remaining_no = $match_per_pos - ($no_of_employees_to_show * $count_of_filters);
} 

//echo $remaining_no;exit;

------------------------------------------------------------------------------*/
	

//EMPLOYEES ONE LEVEL HIGHER...	

if($one_level_higher == 'on')
	{
	$mysql = "select pos_id from $position where level_no = $higher_level1";
	$pos_of_higher_level_arr = $db_object->get_single_column($mysql);	

	$positions_higherlevel = @implode("','",$pos_of_higher_level_arr);
	if($positions_higherlevel != '')
		{
			$mysql = "select user_id from $user_table where position in ('$positions_higherlevel')";
			$users_higherlevel = $db_object->get_single_column($mysql);
			
		}
	}


//EMPLOYEES AT THE SAME LEVEL AS THE POSITION...
if($same_level == on)
	{
	$mysql = "select pos_id from $position where level_no = '$cur_level'";	
	$pos_of_same_level_arr = $db_object->get_single_column($mysql);
	
	$positions_samelevel = @implode("','",$pos_of_same_level_arr);	
	if($positions_samelevel != '')
		{
			$mysql = "select user_id from $user_table where position in ('$positions_samelevel')";
			$users_samelevel = $db_object->get_single_column($mysql);
		}	
	}
//EMPLOYEES AT ONE LEVEL LOWER THAN THE CURRENT POSITION...
if($one_level_lower == 'on')
	{
	$mysql = "select pos_id from $position where level_no = '$lower_level1'";
	$pos_of_low1_level_arr = $db_object->get_single_column($mysql);
	
	$positions_low1level = @implode("','",$pos_of_low1_level_arr);	
	if($positions_low1level != '')
		{
			$mysql = "select user_id from $user_table where position in ('$positions_low1level')";
			$users_low1level = $db_object->get_single_column($mysql);
	
		}
	}

//EMPLOYEES AT TWO LEVEL LOWER THAN THE CURRENT POSITION...
if($two_level_lower == 'on')
	{
	$mysql = "select pos_id from $position where level_no = '$lower_level2'";
	$pos_of_low2_level_arr = $db_object->get_single_column($mysql);
	
	$positions_low2level = @implode("','",$pos_of_low2_level_arr);	
	if($positions_low2level != '')
		{
			$mysql = "select user_id from $user_table where position in ('$positions_low2level')";
			$users_low2level = $db_object->get_single_column($mysql);
	
		}
	}

/*=========================

print_r($users_higherlevel);
echo "<br><br>";
print_r($users_samelevel);
echo "<br><br>";
print_r($users_low1level);
echo "<br><br>";
print_r($users_low2level);

=========================*/

//ALL EMPLOYEES SELECTED FROM THE FIRST SELECTION PROCESS (LEVELS)...
$levels_users_junk_arr = @array_merge($users_higherlevel,$users_samelevel,$users_low1level,$users_low2level);
$levels_users_arr = @array_unique($levels_users_junk_arr);

//**************ALL EMPLOYEES (FILTERED FROM THE FIRST SELECTION)IN A STRING*********//
//-----------------------------------------------------------------------------------//
		$levels_users = @implode("','",$levels_users_arr);


//FILTER THE USERS WHO HAVE EXPRESSED ANY INTEREST OR NO INTEREST IN THEIR CAREER GOALS...
if($interest_lot == 'on')
	{
		if($levels_users != '')
		{
			$mysql = "select user_id from $career_goals 
				where user_id in ('$levels_users') 
				and interest = 'lot' 
				and (onelevel_low = '$family_of_this_position' 
				or same_level = '$family_of_this_position' 
				or onelevel_up = '$family_of_this_position' 
				or twolevel_up = '$family_of_this_position')";

			$lot_of_interest_arr = $db_object->get_single_column($mysql);
			
		}
	}

if($interest_some == 'on')
	{
		if($levels_users != '')
		{
			$mysql = "select user_id from $career_goals 
				where user_id in ('$levels_users') 
				and interest = 'some' 
				and (onelevel_low = '$family_of_this_position' 
				or same_level = '$family_of_this_position' 
				or onelevel_up = '$family_of_this_position' 
				or twolevel_up = '$family_of_this_position')";

			$some_interest_arr = $db_object->get_single_column($mysql);
		
		}
	
		
	}

if($interest_no == 'on')
	{
//DISPLAY THE USERS WHO HAVE NOT EXPRESSED ANY INTEREST IN THEIR CAREER GOALS
//FOR THIS FIRST TAKE ALL THE USERS WHO HAVE EXPRESSED ANY INTEREST IN THE POSITION TO BE FILLED...

	$mysql = "select distinct(user_id) from $career_goals where user_id in ('$levels_users') and (onelevel_low = '$family_of_this_position' or same_level = '$family_of_this_position' or onelevel_up = '$family_of_this_position' or twolevel_up = '$family_of_this_position')";
	$users_in_careergoals_arr = $db_object->get_single_column($mysql);

//ALL THE SELECTED USERS WHO HAVE EXPRESSED SOME INTEREST IN THE FAMILY REQ
	$users_in_careergoals = @implode("','",$users_in_careergoals_arr);	

//NOW REMOVE ALL THE OTHER USERS FROM THE LIST OF USERS...

	$users_with_no_interest_arr = @array_diff($levels_users_arr,$users_in_careergoals_arr);
	
	}

//SELECT USERS WHO HAVE THE POSITION IN THEIR LEARNING PLAN...
$userswithpos_in_learnplan_junk_arr = $common->users_with_pos_in_learningplan($db_object,$posid);

//FILTER THOSE USERS CONSIDERED FROM THE PREVIOUS SCREEN...
$users_with_pos_in_learnplan_arr = @array_intersect($userswithpos_in_learnplan_junk_arr,$levels_users_arr);



$interest_users_junk_arr = @array_merge($lot_of_interest_arr,$some_interest_arr,$users_with_no_interest_arr,$users_with_pos_in_learnplan_arr);

$interest_users_arr = @array_unique($interest_users_junk_arr);

//***************USERS WITH & WITHOUT INTERESTS IN THEIR CAREER GOALS*************//
//--------------------------------------------------------------------------------//
		$interest_users = @implode("','",$interest_users_arr);



/*
echo "People with lot of interest";
print_r($lot_of_interest_arr);
echo "<br><br>";
echo "People with some interest";
print_r($some_interest_arr);
echo "<br><br>";
echo "All users";
print_r($interest_users_arr);exit;

*/
//HIGHLIGHTING USERS WITH EEO TAGS...
	
	$tagids = @implode("','",$etags_arr);
	
	//***************USERS WITH SELECTED EEO TAGS*************//
	//--------------------------------------------------------//

	$mysql = "select distinct(user_id) from $user_eeo where tag_id in ('$tagids') and user_id in ('$interest_users')";	
	$usereeo_arr = $db_object->get_single_column($mysql);



//FILTRATION OF USERS BASED ON EMPLOYMENT TYPES...
	
	$empltypes = @implode("','",$emptypes_arr);


	//***************USERS WITH SELECTED EMPLOYMENT TYPES*************//
	//----------------------------------------------------------------//

	$mysql = "select user_id from $user_table where employment_type in ('$empltypes') and user_id in ('$interest_users')";

	$empltypes_users_arr = $db_object->get_single_column($mysql);
	
	
	
	
//******************USERS COMPLETED ALL THE FILTERING****************
//===================================================================
		
		$allusers_old_arr = $empltypes_users_arr;	

//SHOW ONLY THE # OF EMPLOYEES SPECIFIED BY THE BOSS OR ADMIN IN THE FIRST SCREEN...

	if(count($allusers_old_arr) > $match_per_pos)
	{
		for($i=0;$i<$match_per_pos;$i++)		
		{
			$allusers_arr[] = $allusers_old_arr[$i];
			
		}	
	}
	else
	{
		$allusers_arr = $allusers_old_arr;
	}

		$allusers_full = @implode(",",$allusers_arr);
		setcookie("Usersindepth",$allusers_full,0,"/");

include("header.php");

//FIND THE WHAT DONE AND HOW DONE PERCENTAGES IN PERFORMANCE MODULE ONLY IF THE MODULE IS PURCHASED...

$returncontent = $common->is_module_purchased($db_object,$xPath,$returncontent,$common->pfvar);



//DISPLAY OF ALL CHARECTERISTICS ON SCREEN...

	preg_match("/<{depthchartdisplay_loopstart}>(.*?)<{depthchartdisplay_loopend}>/s",$returncontent,$displaymatchold);
	$depthdisplay = $displaymatchold[1];
	
	for($i=0;$i<count($allusers_arr);$i++)
	{
//DISPLAY OF THE USERNAMES ON THE DEPTH CHART...
	
	$employeeid = $allusers_arr[$i];
	$mysql = "select username from $user_table where user_id = '$employeeid'";
	$username_arr = $db_object->get_a_line($mysql);

//IF THE USER IS THE CURRENT HOLDER OF THE POSITION FILLING, THEN THAT USER IS BOLD...
	
//FIND THE POSITION OF THE USER DISPLAYED...
	$mysql = "select position from $user_table where user_id = '$employeeid'";		
	$posofemployee_arr = $db_object->get_a_line($mysql);
	
	$position_of_employee = $posofemployee_arr['position'];
	
	if($position_of_employee == $posid)
	{
		$boldstart = "<B>";
		$boldend = "</B>";
	}
	else
	{
		$boldstart = "";
		$boldend = "";
	}
			
	


//DISPLAY OF THE LEVELS ON THE DEPTH CHART...		
	$mysql = "select level_no from $user_table , $position where $user_table.position = $position.pos_id and user_id = '$employeeid'";
	$level_arr = $db_object->get_a_line($mysql);
	
	$level_emp = $level_arr['level_no'];

	$level_display = '';

	if($level_emp == $cur_level)
	{
		$level_display = "0";
	}
	if($level_emp == $higher_level1)
	{
		$level_display = "+1";
	}
	if($level_emp == $lower_level1)
	{
		$level_display = "-1";
	}
	if($level_emp == $lower_level2)
	{
		$level_display = "-2";
	}
		

//DISPLAY OF EEO TAGS...

		$mysql = "select tag from $user_eeo,$opportunity_status 
				where $user_eeo.tag_id = $opportunity_status.eeo_id
				and user_id = '$employeeid' 
				and tag_id in ('$tagids')";

		$tags_arr = $db_object->get_single_column($mysql);
		if($tags_arr != '')
		{
		//$tags_display = @implode(",",$tags_arr);
		$tags_display = count($tags_arr);
		}
		else
		{
		$tags_display = 0;
		}

		$username = $username_arr['username'];
	
//DISPLAY OF INTERPERSONAL AND TECHNICAL MODEL FIT PERCENTAGES...
//CHECK IF THE USER HAS THE PERCENTAGE FIT FOR THE MODEL REQUIRED...



		
		//$model_to_view;
		$mysql = "select percent_fit from $models_percent_fit 
				where skill_type = 'i' 
				and user_id = '$employeeid'
				and model_id = '$model_to_view'";
		//echo "$mysql<br>";	
		$percent_i_fit_arr = $db_object->get_a_line($mysql);
		$modelipercentfit = $percent_i_fit_arr['percent_fit']; 
		
		$mysql = "select percent_fit from $models_percent_fit 
				where skill_type = 't' 
				and user_id = '$employeeid'
				and model_id = '$model_to_view'";
		
		$percent_t_fit_arr = $db_object->get_a_line($mysql);
		$modeltpercentfit = $percent_t_fit_arr['percent_fit']; 




//CALCULATION OF THE WHAT DONE AND HOW DONE PERCENTAGES OF PERFORMANCE MODULE...
//===============================================================================

//from Config
		$boss=0;
		$conqry = "select person_affected from $config_table";
		$conres = $db_object->get_a_line($conqry);

		$noofperson = $conres['person_affected'];
		$boss = 1;	
//Total rater is 4 (without self),noofperson(the raters we have selected) + boss (boss's rating)
		$totalperson = $noofperson + $boss;	
	//from rating
		$ratqry = "select rval from $rating_table where rval='$gbl_met_value'";
		$ratres = $db_object->get_a_line($ratqry);
		$r_val = $ratres['rval'];

	//met expectation value;
		$metexpectation = $r_val * $totalperson;

	$mysql = "select sl_id,o_id,committed_no,percent from $approved_selected_objective 
			where user_id='$employeeid' and status='A' order by sl_id";

	$selres = $db_object->get_rsltset($mysql);

	$count_of_objectives = 0;
	$what_full = 0;
	$how_full = 0;
	$what = 0;
	$how = 0;

	for($j=0;$j<count($selres);$j++)
	{
		$o_id = $selres[$j]['o_id'];

		$get = $common->get_fullfilled($db_object,$o_id,$employeeid,$dates);

			if($get[Cfulfill]!='')
			{ 
			$what_full += round($get[Cfulfill],2);
			}
			else
			{
			$what_full += 0;
			}
			
		
			//get all  metrics for the given o_id
			$oqry = "select met_id from $approveduser_objective where o_id='$o_id' and 
				user_id='$employeeid'";
			$ores = $db_object->get_a_line($oqry);
			$met_id = $ores['met_id'];
			$mqry = "select o_id from $approveduser_objective where met_id='$met_id' and 
				user_id='$employeeid'";
			$mres = $db_object->get_single_column($mqry);
			$aver  = count($mres);
			$oid = @implode("','",$mres);

			$Ratervalue = "select sum(r_id) from $approved_feedback where o_id in ('$oid')
					 and user_id='$employeeid' and status<>'1' and status<>'2'";
			$Resvalue = $db_object->get_single_column($Ratervalue);
			$actual = $Resvalue[0];
			$actual = @($actual/$aver);

		//calculation for met expectation value
			$expected = @($actual/$metexpectation);
			$expected = $expected * 100;

			$how_full  += round($expected,2);
			$count_of_objectives++;
		
	}

//FIND THE PERCENTAGE OF ALL THE WHAT AND HOW OF ALL THE OBJECTIVES...
	if($count_of_objectives != 0)
	{
	$how = round(($how_full / $count_of_objectives),2);
	$what = round(($what_full / $count_of_objectives),2);
	}
	else
	{
	$what = 0;
	$how = 0;
	}

	

//===============================================================================
//DISPLAY OF INTERESTS...


//IF NOT THEN CHECK IF THAT FAMILY IS IN THE CAREER GOALS

	$mysql = "select interest from $career_goals where (onelevel_low = '$family_of_this_position' or same_level = '$family_of_this_position' or onelevel_up = '$family_of_this_position' or twolevel_up = '$family_of_this_position') and user_id = '$employeeid'";
	$interest_arr = $db_object->get_a_line($mysql);
	$interest = $interest_arr['interest'];
	
	if($interest == '')
	{
		$interest = "Not Indicated";
	}
	//CHECK IF THE POSITION IS IN THE LEARNING PLAN...
	$users_with_pos_in_learning_plan_arr = $common->users_with_pos_in_learningplan($db_object,$posid);

	for($k=0;$k<count($users_with_pos_in_learning_plan_arr);$k++)
	{
		$user = $users_with_pos_in_learning_plan_arr[$k];
		if($user == $employeeid)
		{
			$interest = "Developmental";
		}
		
		
	}
	
	
	

		$str .= preg_replace("/<{(.*?)}>/e","$$1",$depthdisplay);
	}		
	
	$returncontent = preg_replace("/<{depthchartdisplay_loopstart}>(.*?)<{depthchartdisplay_loopend}>/s",$str,$returncontent);


//EXTERNAL HIRE DISPLAY START (DISPLAYED ONLY IF EXTERNAL CANDIDATE SELECTED IN FIRST SCREEN)

$mysql = "select id from $employment_type where external_candidate = 'yes'";
$emplexternal_arr = $db_object->get_a_line($mysql);
$emplexternal = $emplexternal_arr['id'];
if(@in_array($emplexternal,$emptypes_arr))
{

preg_match("/<{externalchartdisplay_loopstart}>(.*?)<{externalchartdisplay_loopend}>/s",$returncontent,$extold);
$extnew = $extold[1];

$mysql = "select user_id, username from $user_table where user_type = 'external'";
$externaluser_arr = $db_object->get_rsltset($mysql);
for($l=0;$l<count($externaluser_arr);$l++)
{
$user_extid = $externaluser_arr[$l]['user_id'];
$user_extname = $externaluser_arr[$l]['username'];

		$mysql = "select percent_fit from $models_percent_fit 
				where skill_type = 'i' 
				and user_id = '$user_extid'
				and model_id = '$model_to_view'";
		//echo "$mysql<br>";	
		$percent_i_fit_arr = $db_object->get_a_line($mysql);
		$modelipercentfit_ext = $percent_i_fit_arr['percent_fit']; 
		
		$mysql = "select percent_fit from $models_percent_fit 
				where skill_type = 't' 
				and user_id = '$user_extid'
				and model_id = '$model_to_view'";
		
		$percent_t_fit_arr = $db_object->get_a_line($mysql);
		$modeltpercentfit_ext = $percent_t_fit_arr['percent_fit']; 





$str_ext .= preg_replace("/<{(.*?)}>/e","$$1",$extnew);

}
$returncontent = preg_replace("/<{externalchartdisplay_loopstart}>(.*?)<{externalchartdisplay_loopend}>/s",$str_ext,$returncontent);

//EXTERNAL HIRE DISPLAY END.
}
else
{
$returncontent = preg_replace("/<{externalchartdisplay_loopstart}>(.*?)<{externalchartdisplay_loopend}>/s","",$returncontent);
}
	
	$returncontent 	= $common->direct_replace($db_object,$returncontent,$values);
	echo $returncontent;

	}
}
$obj = new depthchart;

$obj->showdepthchart($db_object,$common,$post_var,$user_id,$default,$gbl_met_value,$gbl_files);
include("footer.php");
?>
