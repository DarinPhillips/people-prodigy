<?php
include("../session.php");

include("header.php");

class update
{
	
	function showdepthchart($db_object,$common,$post_var,$user_id,$default,$gbl_met_value,$gbl_files,$modelid)
	{
		
		while(list($kk,$vv) = @each($post_var))
		{
			$$kk = $vv;
		}



	$xPath		= $common->path;
	$returncontent	= $xPath."/templates/career/depth_chart.html";
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
		
		//setcookie("Usersindepth",$allusers_full,0,"/");



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

	
	
	function update_plan($db_object,$common,$user_id,$error_msg)
	{
		$assign_succession_plan_sub=$common->prefix_table("assign_succession_plan_sub");
		
		$position=$common->prefix_table("position");
		
		$sql="select assigned_to,position,date_format(assigned_on,'%m.%d.%Y.%i:%s') as assigned_on,assigned_by from $assign_succession_plan_sub where assigned_to='$user_id'";

		
		$result=$db_object->get_rsltset($sql);
		
		$path=$common->path;
		
		$xtemplate=$path."templates/career/update_succession_plan.html";
		
		$file=$common->return_file_content($db_object,$xtemplate);
		
		$pattern="/<{user_loopstart}>(.*?)<{user_loopend}>/s";
		
		preg_match($pattern,$file,$match);
		
		$match=$match[0];
		
		for($i=0;$i<count($result);$i++)
		{
			$id=$result[$i][position];
			
			$qry="select position_name from $position where pos_id='$id'";
			
			$res=$db_object->get_a_line($qry);
						
			$name=$res[position_name];
			
			$date=$result[$i][assigned_on];
			
			$str.=preg_replace("/<{(.*?)}>/e","$$1",$match);
			
		}
		$file=preg_replace($pattern,$str,$file);
		
		$file=$common->direct_replace($db_object,$file,$xArray);
		
		echo $file;
	}
	//---------------------------------------
		
	function update_plan_admin($db_object,$common,$user_id,$error_msg)
	{
		$assign_succession_plan_sub=$common->prefix_table("assign_succession_plan_sub");
		
		$position=$common->prefix_table("position");

		$user_table=$common->prefix_table("user_table");
		
		//$sql="select assigned_to,position,date_format(assigned_on,'%m.%d.%Y.%i:%s') as assigned_on,assigned_by from $assign_succession_plan_sub where assigned_to='$user_id'";

$sql="select position from $user_table where user_id='$user_id'";

$sql_result=$db_object->get_a_line($sql);

$position_id=$sql_result[position];

		$sql="select position_name,pos_id from $position where pos_id<>'$position_id'";
	
		$result=$db_object->get_rsltset($sql);
		
		$path=$common->path;
		
		$xtemplate=$path."templates/career/update_succession_plan_admin.html";
		
		$file=$common->return_file_content($db_object,$xtemplate);
		
		$pattern="/<{user_loopstart}>(.*?)<{user_loopend}>/s";
		
		preg_match($pattern,$file,$match);
		
		$match=$match[0];
		
		for($i=0;$i<count($result);$i++)
		{
			$id=$result[$i][pos_id];
							
			$name=$result[$i][position_name];
						
			$str.=preg_replace("/<{(.*?)}>/e","$$1",$match);
			
		}
		$file=preg_replace($pattern,$str,$file);
		
		$file=$common->direct_replace($db_object,$file,$xArray);
		
		echo $file;
	}
	
	
	//---------------------------------------
	function update_succession_plan($db_object,$common,$user_id,$fPosition)
	{
		$deployment_plan=$common->prefix_table("deployment_plan");
		
		$position_designee1=$common->prefix_table("position_designee1");
		
		$position_designee2=$common->prefix_table("position_designee2");
		
		$sql="select created_user as user_id,designee1,designee2,plan_id from $deployment_plan where position='$fPosition'";
		
		$sql_result=$db_object->get_rsltset($sql);
	
		$a=0;$b=0;
		
		for($i=0;$i<count($sql_result);$i++)
		{
			$user=$sql_result[$i][user_id];
			
			$plan=$sql_result[$i][plan_id];
			
			$sql="select * from $position_designee1 where designated_user='$user' and plan_id='$plan'";

			$res=$db_object->get_rsltset($sql);
			
			if($res[0]!="")
			{
				$designee1[$a][user_id]=$user;
				
				$designee1[$a][type]=$sql_result[$i][designee1];
				
				$a++;
			}
			$sql1="select * from $position_designee2 where designated_user='$user_id' and plan_id='$plan'";
			
			$res1=$db_object->get_rsltset($sql1);
			
			if($res1[0]!="")
			{
				$designee2[$b][user_id]=$user;
				
				$designee2[$b][type]=$sql_result[$i][designee2];
				
				$b++;
			}
		}
		$a=0;$b=0;
		for($i=0;$i<count($designee1);$i++)
		{
			$type=$designee1[$i][type];
			
			if($type="employee")
			{
				$employee[$a]=$designee1[$i];
				
				$employee[$a][designee]="designee1";
				
				$a++;
			}
			if($type=="external")
			{
				$external[$b]=$designee1[$i];
				
				$external[$b][designee]="designee2";
				
				$b++;
			}
		}
		
		for($i=0;$i<count($designee2);$i++)
		{
			$type=$designee2[$i][type];
			
			if($type="employee")
			{
				$employee[$a]=$designee2[$i];
				
				$employee[$a][designee]="designee1";
				
				$a++;
			}
			if($type=="external")
			{
				$external[$b]=$designee2[$i];
				
				$external[$b][designee]="designee2";
				
				$b++;
			}
		}

		$path=$common->path;
		
		$xtemplate=$path."/templates/career/update_succession_plan2.html";
		
		$file=$common->return_file_content($db_object,$xtemplate);
		
		$pattern="/<{employee_loopstart}>(.*?)<{employee_loopend}>/s";
		
		$pattern1="/<{external_loopstart}>(.*?)<{external_loopend}>/s";
		
		preg_match($pattern,$file,$match);
		
		$match=$match[0];
		
		preg_match($pattern1,$file,$match1);
		
		$match1=$match1[0];

		for($i=0;$i<count($employee);$i++)
		{
			$emp_id=$employee[$i][user_id];
			
			$emp_name=$common->name_display($db_object,$emp_id);
			
			$design=$employee[$i][designee];
			
			if($design=="designee1")
			{
				$selected1="CHECKED";
			}
			else if($design="designee2")
			{
				$selected2="CHECKED";
			}
			$str.=preg_replace("/<{(.*?)}>/e","$$1",$match);
						
			
		}
		$file=preg_replace($pattern,$str,$file);

		for($i=0;$i<count($external);$i++)
		{
			$emp_id=$external[$i][user_id];
			
			$emp_name=$common->name_display($db_object,$emp_id);
			
			$design=$external[$i][designee];
			
			if($design=="designee1")
			{
				$selected1="CHECKED";
			}
			else if($design="designee2")
			{
				$selected2="CHECKED";
			}
			$str1.=preg_replace("/<{(.*?)}>/e","$$1",$match);
						
			
		}
		$file=preg_replace($pattern1,$str1,$file);
		
		$file=$common->direct_replace($db_object,$file,$xArray);
		
		echo $file;
		
		
		
		
	}
	function show_models($db_object,$common,$posid,$default,$user_id)
	{

	
		$xPath		= $common->path;
		$returncontent	= $xPath."/templates/career/update_succession_plan1.html";
		$returncontent	= $common->return_file_content($db_object,$returncontent);

		$model_factors_1 = $common->prefix_table('model_factors_1');
		$family_position = $common->prefix_table('family_position');
		$model_name_table= $common->prefix_table('model_name_table');
		$deployment_plan = $common->prefix_table('deployment_plan');

//STORE THE DATA REGARDING THE POSITION FOR WHICH THE SUCCESSION & DEPLOYMENT PLAN IS CREATED...
		
		$mysql = "select plan_id from $deployment_plan where position = '$posid'";
		$planid_arr = $db_object->get_a_line($mysql);		
		$planid = $planid_arr['plan_id'];
		if($planid == '')
		{
		$mysql = "insert into $deployment_plan set position = '$posid',
				created_user = '$user_id',
				created_date = now()";
		$db_object->insert($mysql);
		}
		else
		{
		$mysql = "update $deployment_plan set position = '$posid',
				created_user = '$user_id',
				created_date = now()
				where plan_id = '$planid'";	
		$db_object->insert($mysql);
		}
		
		
		
		


		//DETERMINE THE FAMILY OF THE SELECTED POSITION...
		$mysql = "select family_id from $family_position where position_id = '$posid'";
		$familypos_arr = $db_object->get_a_line($mysql);
		$familyofpos = $familypos_arr['family_id'];
		
		
		//THE POSITION IS OBTAINED FROM THE PREVIOUS SCREEN 
		//SHOW THE MODELS RELATING TO THAT POSITION...
		//SELECT THE MODELS THAT THIS PERSON IS CAPABLE OF VIEWING...
		//SELECT THE MODELS RELATING TO THAT POSITIONS' FAMILY...

		$userviewablemodels = $common->viewable_models($db_object,$user_id);
		
		$modelid_all = @implode("','",$userviewablemodels);
		
		if($modelid_all != '')
		{
		$mysql = "select model_id 
				from $model_factors_1 
				where family = '$familyofpos'
				and model_id in ('$modelid_all')";
		$models_arr = $db_object->get_single_column($mysql);
		$models_full = @implode("','",$models_arr);
		}

		if($models_full != '')	
		{
		$mysql = "select model_id,model_name from $model_name_table where model_id in ('$models_full')";
		$models_display_arr = $db_object->get_rsltset($mysql);
		}

		$multipleloopvalues['modelsview_loop'] = $models_display_arr;
		$values['posid'] = $posid;

		$returncontent 	= $common->direct_replace($db_object,$returncontent,$values);
		$returncontent = $common->multipleloop_replace($db_object,$returncontent,$multipleloopvalues,'');
		
		
		echo $returncontent;
	
	
	}
	
	
	//#############################################################33
	function show_documentactions($db_object,$common,$default,$user_id,$error_msg,$post_var,$modelid)
	{
		
		$xPath		= $common->path;
		$returncontent	= $xPath."/templates/career/update_succession_plan3.html";
		$returncontent	= $common->return_file_content($db_object,$returncontent);

		while(list($kk,$vv) = @each($post_var))
		{
			$$kk = $vv;
		}
		
		
		$position 		= $common->prefix_table("position");
		$position_designee1 	= $common->prefix_table('position_designee1');
		$position_designee2 	= $common->prefix_table('position_designee2');
		$user_table 		= $common->prefix_table('user_table');
		$skills 		= $common->prefix_table('skills');
		$model_skills 		= $common->prefix_table('model_skills');
		$deployment_plan 	= $common->prefix_table('deployment_plan');
		$approved_devbuilder	= $common->prefix_table('approved_devbuilder');
		
		$sql="select position from $user_table where user_id='$user_id'";
		
		$res=$db_object->get_a_line($sql);
		
		$pid=$res[position];
		$pid=43;
		$mysql = "select position_name from $position where pos_id = '$pid'";
		$pos_arr = $db_object->get_a_line($mysql);
		
		$position_name 		= $pos_arr['position_name'];
		$values['position_name']= $position_name;
		$values['pid'] 		= $pid;
		
//FIRST DESIGNEE FOR THAT POSITION...
		//$mysql = "select designated_user from $position_designee1 where position = $pid";
		$mysql = "select designated_user as designee1 
				from $position_designee1,$deployment_plan 
				where $position_designee1.plan_id = $deployment_plan.plan_id 
				and $deployment_plan.position='$pid'";

		$firstdes_arr = $db_object->get_a_line($mysql);
		
		$firstdesignee_user = $firstdes_arr['designee1'];
		$mysql = "select username from $user_table where user_id = '$firstdesignee_user'";
		$username_arr = $db_object->get_a_line($mysql);
		$firstdesignee_username = $username_arr['username'];
		
//SECOND DESIGNEE FOR THAT POSITION...

	$mysql = "select designated_user as designee2 
			from $position_designee2,$deployment_plan 
			where $position_designee2.plan_id = $deployment_plan.plan_id 
			and $deployment_plan.position='$pid'"; 

		$seconddes_arr = $db_object->get_a_line($mysql);
		
		$seconddesignee_user = $seconddes_arr['designee2'];
		$mysql = "select username from $user_table where user_id = '$seconddesignee_user'";
		$username_arr = $db_object->get_a_line($mysql);
		$seconddesignee_username = $username_arr['username'];

//CHECK IF THE FIRST DESIGNEE AND THE SECOND DESIGNEE SELECTED ARE EMPLOYEES 
//OR EXTERNAL CANDIDATES AND SHOW THE RELEVENT BOXES
		$mysql = "select user_type from $user_table where user_id = '$firstdesignee_user'";
		$type_arr = $db_object->get_a_line($mysql);
		$firstdes_type = $type_arr['user_type'];
		
		if($firstdes_type == 'employee')
		{
		$returncontent = preg_replace("/<{firstdesignee_employee_(.*?)}>/s","",$returncontent);
		$returncontent = preg_replace("/<{firstdesignee_external_start}>(.*?)<{firstdesignee_external_end}>/s","",$returncontent);
		}
		elseif($firstdes_type == 'external')
		{
		$returncontent = preg_replace("/<{firstdesignee_employee_start}>(.*?)<{firstdesignee_employee_end}>/s","",$returncontent);
		$returncontent = preg_replace("/<{firstdesignee_external_(.*?)}>/s","",$returncontent);
		}
		else
		{
		$returncontent = preg_replace("/<{firstdesignee_external_start}>(.*?)<{firstdesignee_external_end}>/s","",$returncontent);
		$returncontent = preg_replace("/<{firstdesignee_employee_start}>(.*?)<{firstdesignee_employee_end}>/s","",$returncontent);
		}		
		
		
		$mysql = "select user_type from $user_table where user_id = '$seconddesignee_user'";
		$type_arr = $db_object->get_a_line($mysql);
		$seconddes_type = $type_arr['user_type'];

		if($seconddes_type == 'employee')
		{
		$returncontent = preg_replace("/<{seconddesignee_employee_(.*?)}>/s","",$returncontent);
		$returncontent = preg_replace("/<{seconddesignee_external_start}>(.*?)<{seconddesignee_external_end}>/s","",$returncontent);
		}
		elseif($seconddes_type == 'external')
		{
		$returncontent = preg_replace("/<{seconddesignee_employee_start}>(.*?)<{seconddesignee_employee_end}>/s","",$returncontent);
		$returncontent = preg_replace("/<{seconddesignee_external_(.*?)}>/s","",$returncontent);
		}
		else
		{
		$returncontent = preg_replace("/<{seconddesignee_external_start}>(.*?)<{seconddesignee_external_end}>/s","",$returncontent);
		$returncontent = preg_replace("/<{seconddesignee_employee_start}>(.*?)<{seconddesignee_employee_end}>/s","",$returncontent);
		}

	

		$values['firstdesignee_username'] = $firstdesignee_username;
		$values['seconddesignee_username'] = $seconddesignee_username;

		//echo "firstdesignee user $firstdesignee_user";
		//echo "modelid $modelid";

		$inter_gaps_arr = $common->gaps_at_a_glance($db_object,$firstdesignee_user,$modelid);
		$tech_gaps_arr = $common->gaps_at_a_glance_technical($db_object,$firstdesignee_user,$modelid);


		
//FIRST DESIGNEE....
		preg_match("/<{largestgaps_loopstart}>(.*?)<{largestgaps_loopend}>/s",$returncontent,$largest_old);
		$largest_new = $largest_old[1];
			
		preg_match("/<{largestgapstech_loopstart}>(.*?)<{largestgapstech_loopend}>/s",$returncontent,$largesttech_old);
		$largesttech_new = $largesttech_old[1];
		
		preg_match("/<{othergaps_loopstart}>(.*?)<{othergaps_loopend}>/s",$returncontent,$othergap_old);
		$othergap_new = $othergap_old[1];
		
		preg_match("/<{othergapstech_loopstart}>(.*?)<{othergapstech_loopend}>/s",$returncontent,$othergaptech_old);
		$othergaptech_new = $othergaptech_old[1];
		
//CHECK IF THE SKILLS SHOWN IS IN THE PERSONS' LEARNING PLAN...
		$mod_l = md5("learning");
		
		
		$check=$common->is_module_purchased_check($db_object,$xPath,$mod_l);
		
//LARGEST GAPS...
		
		for($i=0;$i<count($inter_gaps_arr[1]);$i++)
		{
		
			$largestgaps_id = $inter_gaps_arr[1][$i];
			$mysql = "select skill_name from $skills where skill_id = '$largestgaps_id'";	
			$skillname_arr = $db_object->get_a_line($mysql);
			$largestgap_skillname = $skillname_arr['skill_name'];
			
//IF THE PERSON HAS THE SKILL IN HIS LEARNING PLAN, THEY ARE SELECTED...
			if($check)
			{
			$mysql = "select distinct(skill_id) from $approved_devbuilder where user_id = '$firstdesignee_user' and skill_id = '$largestgaps_id'";
			$skillpresent_arr = $db_object->get_a_line($mysql);		
			$skillpresent = $skillpresent_arr['skill_id'];
			if($largestgaps_id == $skillpresent)
				{
					$checkboxsel = "checked";
				}
			else
				{
					$checkboxsel = "";	
				}
			}

//IF ANOTHER BOSS OR ADMIN HAS ALREADY ASSIGNED SOME SKILLS FOR THIS POSITION...
			$mysql = "select skills_designee1.skill_id from position_designee1,skills_designee1 
				where position_designee1.plan_id = skills_designee1.plan_id
				and designated_user = '$firstdesignee_user'
				and skill_id = '$largestgaps_id'";
			$skills_in_plan_arr = $db_object->get_a_line($mysql);
			$skills_in_plan = $skills_in_plan_arr['skill_id'];
			if($skills_in_plan != '')
			{
				$checkboxsel = "checked";
			}
			else
			{
				$checkboxsel = "";	
			}
		
			$str .= preg_replace("/<{(.*?)}>/e","$$1",$largest_new);
					
		}
		
		$returncontent = preg_replace("/<{largestgaps_loopstart}>(.*?)<{largestgaps_loopend}>/s",$str,$returncontent);		

		for($i=0;$i<count($tech_gaps_arr[1]);$i++)
		{
			$largestgapstech_id = $tech_gaps_arr[1][$i];
			$mysql = "select skill_name from $skills where skill_id = '$largestgapstech_id'";	
			$skillname_arr = $db_object->get_a_line($mysql);
			$largestgaptech_skillname = $skillname_arr['skill_name'];
	
			if($check)
			{
			$mysql = "select distinct(skill_id) from $approved_devbuilder where user_id = '$firstdesignee_user' and skill_id = '$largestgapstech_id'";
			$skillpresent_arr = $db_object->get_a_line($mysql);		
			$skillpresent = $skillpresent_arr['skill_id'];
			if($largestgapstech_id == $skillpresent)
				{
					$checkboxsel = "checked";
				}
			else
				{
					$checkboxsel = "";	
				}
			}

//IF ANOTHER BOSS OR ADMIN HAS ALREADY ASSIGNED SOME SKILLS FOR THIS POSITION...
			$mysql = "select skills_designee1.skill_id from position_designee1,skills_designee1 
				where position_designee1.plan_id = skills_designee1.plan_id
				and designated_user = '$firstdesignee_user'
				and skill_id = '$largestgapstech_id'";
			
			$skills_in_plan_arr = $db_object->get_a_line($mysql);
			$skills_in_plan = $skills_in_plan_arr['skill_id'];
			if($skills_in_plan != '')
			{
				$checkboxsel = "checked";
			}
			else
			{
				$checkboxsel = "";	
			}


			
			$str1 .= preg_replace("/<{(.*?)}>/e","$$1",$largesttech_new);
			
			
		}

		$returncontent = preg_replace("/<{largestgapstech_loopstart}>(.*?)<{largestgapstech_loopend}>/s",$str1,$returncontent);		
		if((count($inter_gaps_arr[1])==0 or $str=="") and (count($tech_gaps_arr[1])==0 or $str1==""))
		{
			$values["notselected"]="No Skills Selected";
		}
		else
		{
			$values["notselected"]="";
		}
		
//OTHER GAPS...
		
		for($j=0;$j<count($inter_gaps_arr[2]);$j++)
		{
			$othergap_skillid = $inter_gaps_arr[2][$j];
			$mysql = "select skill_name from $skills where skill_id = '$othergap_skillid'";	
			$skillname_arr = $db_object->get_a_line($mysql);
			$othergap_skillname = $skillname_arr['skill_name'];
			
			if($check)
			{
			$mysql = "select distinct(skill_id) from $approved_devbuilder where user_id = '$firstdesignee_user' and skill_id = '$othergap_skillid'";
			$skillpresent_arr = $db_object->get_a_line($mysql);		
			$skillpresent = $skillpresent_arr['skill_id'];
			if($othergap_skillid == $skillpresent)
				{
					$checkboxsel = "checked";
				}
			else
				{
					$checkboxsel = "";	
				}
			}

//IF ANOTHER BOSS OR ADMIN HAS ALREADY ASSIGNED SOME SKILLS FOR THIS POSITION...
			$mysql = "select skills_designee1.skill_id from position_designee1,skills_designee1 
				where position_designee1.plan_id = skills_designee1.plan_id
				and designated_user = '$firstdesignee_user'
				and skill_id = '$othergap_skillid'";
			
			$skills_in_plan_arr = $db_object->get_a_line($mysql);
			$skills_in_plan = $skills_in_plan_arr['skill_id'];
			if($skills_in_plan != '')
			{
				$checkboxsel = "checked";
			}
			else
			{
				$checkboxsel = "";	
			}
			
			
				$str2 .= preg_replace("/<{(.*?)}>/e","$$1",$othergap_new);
			
			
		}
		
		$returncontent = preg_replace("/<{othergaps_loopstart}>(.*?)<{othergaps_loopend}>/s",$str2,$returncontent);		


		for($j=0;$j<count($tech_gaps_arr[2]);$j++)
		{
			$othergaptech_skillid = $tech_gaps_arr[2][$j];
			$mysql = "select skill_name from $skills where skill_id = '$othergaptech_skillid'";	
			$skillname_arr = $db_object->get_a_line($mysql);
			$othergaptech_skillname = $skillname_arr['skill_name'];
				
			if($check)
			{
			$mysql = "select distinct(skill_id) from $approved_devbuilder where user_id = '$firstdesignee_user' and skill_id = '$othergaptech_skillid'";
			$skillpresent_arr = $db_object->get_a_line($mysql);		
			$skillpresent = $skillpresent_arr['skill_id'];
			if($othergaptech_skillid == $skillpresent)
				{
					$checkboxsel = "checked";
				}
			else
				{
					$checkboxsel = "";	
				}
			}
			
//IF ANOTHER BOSS OR ADMIN HAS ALREADY ASSIGNED SOME SKILLS FOR THIS POSITION...
			$mysql = "select skills_designee1.skill_id from position_designee1,skills_designee1 
				where position_designee1.plan_id = skills_designee1.plan_id
				and designated_user = '$firstdesignee_user'
				and skill_id = '$othergaptech_skillid'";
			
			$skills_in_plan_arr = $db_object->get_a_line($mysql);
			$skills_in_plan = $skills_in_plan_arr['skill_id'];
			if($skills_in_plan != '')
			{
				$checkboxsel = "checked";
			}
			else
			{
				$checkboxsel = "";	
			}
			
			

				$str3 .= preg_replace("/<{(.*?)}>/e","$$1",$othergaptech_new);
			
			
		}


		$returncontent = preg_replace("/<{othergapstech_loopstart}>(.*?)<{othergapstech_loopend}>/s",$str3,$returncontent);		
		
		if((count($inter_gaps_arr[2])==0 or $str2=="") and (count($tech_gaps_arr[2])==0 or $str3==""))
		{
			$values[notselected1]="No Skill Selected";
		}
		else
		{

			$values[notselected1]="";
		}
		

//GAPS FOR SECOND DESIGNEE...
//LARGEST GAPS...	

		$inter_gaps2_arr = $common->gaps_at_a_glance($db_object,$seconddesignee_user,$modelid);
		$tech_gaps2_arr = $common->gaps_at_a_glance_technical($db_object,$seconddesignee_user,$modelid);
	

		preg_match("/<{largestgaps2_loopstart}>(.*?)<{largestgaps2_loopend}>/s",$returncontent,$largestgap2_old);
		$largestgap2_new = $largestgap2_old[1];
		
		preg_match("/<{largestgapstech2_loopstart}>(.*?)<{largestgapstech2_loopend}>/s",$returncontent,$largestgaptech2_old);
		$largestgaptech2_new = $largestgaptech2_old[1];
		




		for($i=0;$i<count($inter_gaps2_arr[1]);$i++)
		{
			$largestgaps2_id = $inter_gaps2_arr[1][$i];
			$mysql = "select skill_name from $skills where skill_id = '$largestgaps2_id'";	
			$skillname_arr = $db_object->get_a_line($mysql);
			$largestgap2_skillname = $skillname_arr['skill_name'];
			
			if($check)
			{
			$mysql = "select distinct(skill_id) from $approved_devbuilder where user_id = '$seconddesignee_user' and skill_id = '$largestgaps2_id'";
			$skillpresent_arr = $db_object->get_a_line($mysql);		
			$skillpresent = $skillpresent_arr['skill_id'];
			if($largestgaps2_id == $skillpresent)
				{
					$checkboxsel = "checked";
				}
			else
				{
					$checkboxsel = "";	
				}
			}

//IF ANOTHER BOSS OR ADMIN HAS ALREADY ASSIGNED SOME SKILLS FOR THIS POSITION...
			$mysql = "select skills_designee2.skill_id from position_designee2,skills_designee2 
				where position_designee2.plan_id = skills_designee2.plan_id
				and designated_user = '$seconddesignee_user'
				and skill_id = '$largestgaps2_id'";
			
			$skills_in_plan_arr = $db_object->get_a_line($mysql);
			$skills_in_plan = $skills_in_plan_arr['skill_id'];
			if($skills_in_plan != '')
			{
				$checkboxsel = "checked";
			}
			else
			{
				$checkboxsel = "";	
			}

			
			$str4 .= preg_replace("/<{(.*?)}>/e","$$1",$largestgap2_new);
			
			
		}
		
		$returncontent = preg_replace("/<{largestgaps2_loopstart}>(.*?)<{largestgaps2_loopend}>/s",$str4,$returncontent);


		for($i=0;$i<count($tech_gaps2_arr[1]);$i++)
		{
			$largestgapstech2_id = $tech_gaps2_arr[1][$i];
			$mysql = "select skill_name from $skills where skill_id = '$largestgapstech2_id'";
			$skillname_arr = $db_object->get_a_line($mysql);
			$largestgaptech2_skillname = $skillname_arr['skill_name'];
			
			if($check)
			{
			$mysql = "select distinct(skill_id) from $approved_devbuilder where user_id = '$seconddesignee_user' and skill_id = '$largestgapstech2_id'";
			$skillpresent_arr = $db_object->get_a_line($mysql);		
			$skillpresent = $skillpresent_arr['skill_id'];
			if($largestgapstech2_id == $skillpresent)
				{
					$checkboxsel = "checked";
				}
			else
				{
					$checkboxsel = "";	
				}
			}

//IF ANOTHER BOSS OR ADMIN HAS ALREADY ASSIGNED SOME SKILLS FOR THIS POSITION...
			$mysql = "select skills_designee2.skill_id from position_designee2,skills_designee2 
				where position_designee2.plan_id = skills_designee2.plan_id
				and designated_user = '$seconddesignee_user'
				and skill_id = '$largestgapstech2_id'";
			
			$skills_in_plan_arr = $db_object->get_a_line($mysql);
			$skills_in_plan = $skills_in_plan_arr['skill_id'];
			if($skills_in_plan != '')
			{
				$checkboxsel = "checked";
			}
			else
			{
				$checkboxsel = "";	
			}
			
			$str5 .= preg_replace("/<{(.*?)}>/e","$$1",$largestgaptech2_new);
			
		}

		$returncontent = preg_replace("/<{largestgapstech2_loopstart}>(.*?)<{largestgapstech2_loopend}>/s",$str5,$returncontent);
		if((count($inter_gaps2_arr[1])==0 or $str4=="") and (count($tech_gaps2_arr[1]==0) or $str5=""))
		{
			$values[notselected2]="No Skill Selected";
		}
		else
		{
			$values[notselected2]="";
		}
		
		
//OTHER GAPS...


		preg_match("/<{othergaps2_loopstart}>(.*?)<{othergaps2_loopend}>/s",$returncontent,$othergap2_old);
		$othergap2_new = $othergap2_old[1];
	
		preg_match("/<{othergapstech2_loopstart}>(.*?)<{othergapstech2_loopend}>/s",$returncontent,$othergaptech2_old);
		$othergaptech2_new = $othergaptech2_old[1];

		for($j=0;$j<count($inter_gaps2_arr[2]);$j++)
		{
			$othergap2_skillid = $inter_gaps2_arr[2][$j];
			$mysql = "select skill_name from $skills where skill_id = '$othergap2_skillid'";	
			$skillname_arr = $db_object->get_a_line($mysql);
			$othergap2_skillname = $skillname_arr['skill_name'];
			
			if($check)
			{
			$mysql = "select distinct(skill_id) from $approved_devbuilder where user_id = '$seconddesignee_user' and skill_id = '$othergap2_skillid'";
			$skillpresent_arr = $db_object->get_a_line($mysql);		
			$skillpresent = $skillpresent_arr['skill_id'];
			if($othergap2_skillid == $skillpresent)
				{
					$checkboxsel = "checked";
				}
			else
				{
					$checkboxsel = "";	
				}
			}

//IF ANOTHER BOSS OR ADMIN HAS ALREADY ASSIGNED SOME SKILLS FOR THIS POSITION...
			$mysql = "select skills_designee2.skill_id from position_designee2,skills_designee2 
				where position_designee2.plan_id = skills_designee2.plan_id
				and designated_user = '$seconddesignee_user'
				and skill_id = '$othergap2_skillid'";
			
			$skills_in_plan_arr = $db_object->get_a_line($mysql);
			$skills_in_plan = $skills_in_plan_arr['skill_id'];
			if($skills_in_plan != '')
			{
				$checkboxsel = "checked";
			}
			else
			{
				$checkboxsel = "";	
			}

			
			
			$str6 .= preg_replace("/<{(.*?)}>/e","$$1",$othergap2_new);
			
			
		}
		
		$returncontent = preg_replace("/<{othergaps2_loopstart}>(.*?)<{othergaps2_loopend}>/s",$str6,$returncontent);		

		
		for($j=0;$j<count($tech_gaps2_arr[2]);$j++)
		{
			$othergaptech2_skillid = $tech_gaps2_arr[2][$j];
			$mysql = "select skill_name from $skills where skill_id = '$othergaptech2_skillid'";	
			$skillname_arr = $db_object->get_a_line($mysql);
			$othergaptech2_skillname = $skillname_arr['skill_name'];
			
			if($check)
			{
			$mysql = "select distinct(skill_id) from $approved_devbuilder where user_id = '$seconddesignee_user' and skill_id = '$othergaptech2_skillid'";
			$skillpresent_arr = $db_object->get_a_line($mysql);		
			$skillpresent = $skillpresent_arr['skill_id'];
			if($othergaptech2_skillid == $skillpresent)
				{
					$checkboxsel = "checked";
				}
			else
				{
					$checkboxsel = "";	
				}
			}

//IF ANOTHER BOSS OR ADMIN HAS ALREADY ASSIGNED SOME SKILLS FOR THIS POSITION...
			$mysql = "select skills_designee2.skill_id from position_designee2,skills_designee2 
				where position_designee2.plan_id = skills_designee2.plan_id
				and designated_user = '$seconddesignee_user'
				and skill_id = '$othergaptech2_skillid'";
			
			$skills_in_plan_arr = $db_object->get_a_line($mysql);
			$skills_in_plan = $skills_in_plan_arr['skill_id'];
			if($skills_in_plan != '')
			{
				$checkboxsel = "checked";
			}
			else
			{
				$checkboxsel = "";	
			}
			
			$str7 .= preg_replace("/<{(.*?)}>/e","$$1",$othergaptech2_new);
			
			
		}

		$returncontent = preg_replace("/<{othergapstech2_loopstart}>(.*?)<{othergapstech2_loopend}>/s",$str7,$returncontent);		

		if((count($inter_gaps2_arr[2])==0 or $str6=="") and (count($tech_gaps2_arr[2])==0 or $str7==""))
		{
			$values[notselected3]="No Skill Selected";
		}
		else
		{
			$values[notselected3]="";
		}
	

//IF AN EXTERNAL HIRE WAS SELECTED ....
	
//approved_devbuilder is the table where the skills in the learning plan can be taken...
	

//HIGH PERFORMERS SKILLS DISPLAYED FOR EXTERNAL CANDIDATES (SAME SCRIPT USED TO DISPLAY 
	//BOTH FIRST DESIGNEE AND SECOND DESIGNEE)...

	$mysql = "select $model_skills.skill_id,skill_name from $skills,$model_skills
			where $skills.skill_id = $model_skills.skill_id
			and model_id = '$modelid' and skill_type = 'i'";
	$modelskills_arr = $db_object->get_rsltset($mysql);
	

	$mysql = "select $model_skills.skill_id,skill_name from $skills,$model_skills
			where $skills.skill_id = $model_skills.skill_id
			and model_id = '$modelid' and skill_type = 't'";
	$modelskillstech_arr = $db_object->get_rsltset($mysql);
	
	$multipleloopvalues['highperformerskills1_loop'] 	= $modelskills_arr;
	$multipleloopvalues['highperformerskills1tech_loop'] 	= $modelskillstech_arr;

 	$multipleloopvalues['highperformerskills2_loop'] 	= $modelskills_arr;
	$multipleloopvalues['highperformerskills2tech_loop'] 	= $modelskillstech_arr;


		$returncontent	= $common->multipleloop_replace($db_object,$returncontent,$multipleloopvalues,'');
		$values[model]=$modelid;

		$returncontent 	= $common->direct_replace($db_object,$returncontent,$values);
		echo $returncontent;
		
	}

function save_contents($db_object,$common,$default,$user_id,$error_msg,$post_var)
	{
		while(list($kk,$vv) = @each($post_var))
		{
			$$kk = $vv;
		
		if(ereg("^skills_designee1_",$kk))
			{
			$sid = ereg_replace("skills_designee1_","",$kk);
			$designee1skills[] = $sid;
			}
		if(ereg("^skills_designee2_",$kk))
			{
			$sid = ereg_replace("skills_designee2_","",$kk);
			$designee2skills[] = $sid;
			}
		
		}
		
		$deployment_plan 	= $common->prefix_table('deployment_plan');
		$skills_designee1 	= $common->prefix_table('skills_designee1');
		$skills_designee2 	= $common->prefix_table('skills_designee2');
		$assign_succession_plan_sub = $common->prefix_table('assign_succession_plan_sub');
		

//AFTER UPDATING THE POSITION'S SUCCESSION PLAN, CHANGE STATUS IN ASSIGN TABLE TO COMPLETED...
		$mysql = "update $assign_succession_plan_sub set status = 'y',updated_date=now() where assigned_to = '$user_id' and position = '$fPosid'";

		$db_object->insert($mysql);


		$mysql = "select plan_id from $deployment_plan where position = '$fPosid'";
		$planid_arr = $db_object->get_a_line($mysql);
		$plan_id = $planid_arr['plan_id'];
		
		$mysql = "select $skills_designee1.plan_id 
				from $deployment_plan,$skills_designee1
				where $skills_designee1.plan_id = $deployment_plan.plan_id
				and $deployment_plan.position = '$fPosid'";
			$checkplan_arr = $db_object->get_a_line($mysql);
			$checkplan_id = $checkplan_arr['plan_id'];
			
			if($checkplan_id != '')
			{
//DELETE THE PREVIOUS CONTENTS OF THE EXISTING PLAN...
			$mysql = "delete from $skills_designee1 where plan_id = '$plan_id'";
			$db_object->insert($mysql);
//DELETE THE TEXT IF PRESENT...
			$mysql = "update $deployment_plan set designee1_text = '' where plan_id = '$plan_id'";			
			$db_object->insert($mysql);

			}		


		for($i=0;$i<count($designee1skills);$i++)
		{
			$skill_req = $designee1skills[$i];
			
			
			
			$mysql = "insert into $skills_designee1 set plan_id = '$plan_id' ,
					skill_id = '$skill_req'";
			$db_object->insert($mysql);
			
			
			
		}
		
		$mysql = "select $skills_designee2.plan_id 
				from $deployment_plan,$skills_designee2
				where $skills_designee2.plan_id = $deployment_plan.plan_id
				and $deployment_plan.position = '$fPosid'";
			$checkplan2_arr = $db_object->get_a_line($mysql);
			$checkplan2_id = $checkplan2_arr['plan_id'];

			if($checkplan2_id != '')
			{
			$mysql = "delete from $skills_designee2 where plan_id = '$plan_id'";
			$db_object->insert($mysql);
//DELETE THE TEXT IF PRESENT...
			$mysql = "update $deployment_plan set designee2_text = '' where plan_id = '$plan_id'";			
			$db_object->insert($mysql);
		
			}


		for($j=0;$j<count($designee2skills);$j++)
		{
			$skill_req2 = $designee2skills[$j];
			
			
		 
			$mysql = "insert into $skills_designee2 set plan_id = '$plan_id' ,
					skill_id = '$skill_req2'";

			$db_object->insert($mysql);
			 


		}
		if($actionplan_designee1)
		{
			$mysql = "update $deployment_plan set designee1_text = '$actionplan_designee1'
					where plan_id = '$plan_id'";
			$db_object->insert($mysql);
		}	
		if($actionplan_designee2)
		{
			$mysql = "update $deployment_plan set designee2_text = '$actionplan_designee2'
					where plan_id = '$plan_id'";
			$db_object->insert($mysql);
		}
	

		



		
	}

	
	
}

$obj=new update();

if($fSave)
{
	$action="save";
}

switch($action)
{
	case NULL:

	if($user_id!=1)
	{
	$obj->update_plan($db_object,$common,$user_id,$error_msg);
	}
	else
	{
		$obj->update_plan_admin($db_object,$common,$user_id,$error_msg);
	}
	
	break;
	
	case "update":
	
	$obj->show_models($db_object,$common,$fPosition,$default,$user_id);
	
	break;
	
	case "show":
	
	//$obj->showdepthchart($db_object,$common,$post_var,$user_id,$default,$gbl_met_value,$gbl_files,$modelid);
	
	$obj->show_documentactions($db_object,$common,$default,$user_id,$error_msg,$post_var,$model_to_view);
	
	break;

	case "save":
	
	$obj->save_contents($db_object,$common,$default,$user_id,$error_msg,$post_var);
	
	break;
}

include_once("footer.php");

?>
