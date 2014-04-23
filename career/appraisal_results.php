<?php
/*---------------------------------------------
SCRIPT:appraisal_results.php
AUTHOR:info@chrisranjana.com	
UPDATED:31th Oct

DESCRIPTION:
This script displays the appraisal results.

---------------------------------------------*/
include("../session.php");
include_once("header.php");

class appraisalResults
{
	function show_results($db_object,$common,$post_var,$user_id,$default,$gbl_skill_categories)
	{


		$fields = '';

		while(list($kk,$vv) = @each($post_var))
		{
			$$kk = $vv;
			
			if(ereg("^fCat_",$kk))
			{
				$qid = ereg_replace("fCat_","",$kk);
				$category_arr[$qid] = $vv;
				
				if($qid == 'cat_title')
				{
					
				$fields[] .= "skill_name";
				
				$categoryname[] .= $gbl_skill_categories[$qid];
				
				}
				if($qid == 'cat_skill')
				{
					
				$fields[] .= "skill_description";
				$categoryname[] .= $gbl_skill_categories[$qid];
				}
				if($qid == 'cat_unskill')
				{
					
				$fields[] .= "unskilled_desc";
				$categoryname[] .= $gbl_skill_categories[$qid];
				}
				if($qid == 'cat_used')
				{
					
				$fields[] .= "over_used";
				$categoryname[] .= $gbl_skill_categories[$qid];
				}
				if($qid == 'cat_opp')
				{
					
				$fields[] .= "compensator";
				$categoryname[] .= $gbl_skill_categories[$qid];
				}
				if($qid == 'cat_ckill')
				{
					
				$fields[] .= "career_killer";
				$categoryname[] .= $gbl_skill_categories[$qid];
				}
				
				
			}
		if(ereg("^grp_id_",$kk))
			{
				$qid = ereg_replace("grp_id_","",$kk);
				$group_arr[$qid] = $qid;
				
			}
		if(ereg("^fTechGrp_",$kk))
			{
				$qid = ereg_replace("fTechGrp_","",$kk);
				$techgrp_arr[$qid] = $qid; 
				
			}
		}

		if($fEmpl_id == '')
		{
			$id_of_user = $user_id;
		}
		else
		{
			$id_of_user = $fEmpl_id;
		}


	$techgrp_full = @implode(",",$techgrp_arr);
	$values['techgrp_full'] = $techgrp_full;
	$values['id_of_user'] = $id_of_user;

//Determine the categories selected to view results from technical skills

if($fTechTitle == 'on')
{
	$field1 = ",skill_name";
	
}
if($fTechDef == 'on')
{
	$field2 = ",skill_description";
	
}
	

//Determine the groups selected to view results from interpersonal skills 
	
		$group_full			 = @implode(",",$group_arr);
		$values['group_full'] 	 = $group_full;

		$fields 		= @implode(",",$fields);

		$xPath		=$common->path;
		$returncontent	=$xPath."/templates/career/appraisal_results.html";
		$returncontent	=$common->return_file_content($db_object,$returncontent);
		
		$skills 		= $common->prefix_table('skills');
		$config 		= $common->prefix_table('config');
		$family 		= $common->prefix_table('family');
		$user_table 		= $common->prefix_table('user_table');		
		$tech_rating		= $common->prefix_table('tech_rating');
		$other_raters 		= $common->prefix_table('other_raters');
		$skill_raters 		= $common->prefix_table('skill_raters');
		$textqsort_rating 	= $common->prefix_table('textqsort_rating');
		$other_raters_tech	= $common->prefix_table('other_raters_tech');
		$rater_label_relate 	= $common->prefix_table('rater_label_relate');
		$model_skills 		= $common->prefix_table('model_skills');
		$model_name_table	= $common->prefix_table('model_name_table');
		$posmodel_colors 	= $common->prefix_table('posmodel_colors');
		$model_factors_1	= $common->prefix_table('model_factors_1');
		
//------------

		//$skill_raters 	= $common->prefix_table('skill_raters');
		//$skills 		= $common->prefix_table('skills');
		//$textqsort_rating 	= $common->prefix_table('textqsort_rating');
		//$rater_label_relate 	= $common->prefix_table('rater_label_relate');
		//$user_table 		= $common->prefix_table('user_table');
		//$model_main 		= $common->prefix_table('model_main');
		
		
//------------

//Inserting the system owner specified message on the introduction part...
		
		$mysql 		= "select appraisal_message from $config where id=1";
		$message_arr 	= $db_object->get_a_line($mysql);
		$message 		= $message_arr['appraisal_message'];

//display the username of the current employee....
		 
		$username 	= $common->name_display($db_object,$id_of_user);
			
//---------------------------------INTERPERSONAL RESUTLS START...
		
	preg_match("/<{skillresult_loopstart}>(.*?)<{skillresult_loopend}>/s",$returncontent,$matchres);
	$newmatch_res = $matchres[1];


//The definitions correspond to the definitions selected in the view appraisal set up screen...
//If the definitions are selected, they are displayed else the tag is nullified...


	$mysql 	= "select skill_id,$fields from $skills where skill_type = 'i'";
	$skills_arr = $db_object->get_rsltset($mysql);
	$str='';

	for($i=0;$i<count($skills_arr);$i++)
		{
		$skill_id 		= $skills_arr[$i]['skill_id'];
		$skill_name 	= $skills_arr[$i]['skill_name'];
		
		$skill_desc 	= $skills_arr[$i]['skill_description'];
		$skill_undesc 	= $skills_arr[$i]['unskilled_desc'];
		$skill_overused 	= $skills_arr[$i]['over_used'];
		$skill_car_killer = $skills_arr[$i]['career_killer'];
		$skill_compensator= $skills_arr[$i]['compensator'];
	
		if($skill_desc != '')
		{
		
		
			$newmatch_res = preg_replace("/<{skilldesc_(.*?)}>/s","",$newmatch_res);
		
			$skilledtitle = $gbl_skill_categories['cat_skill']; 
			$skilledtitle = strtoupper($skilledtitle);
		
		
		}

		else
		{
			$newmatch_res = preg_replace("/<{skilldesc_start}>(.*?)<{skilldesc_end}>/s","",$newmatch_res);		
		
		
		}

	
		if($skill_undesc != '')
		{ 
		
			$newmatch_res = preg_replace("/<{skillundesc_(.*?)}>/s","",$newmatch_res);
		
			$unskilledtitle = $gbl_skill_categories['cat_unskill'];
			$unskilledtitle = strtoupper($unskilledtitle);
		
	
		}
		else
		{
		
			$newmatch_res = preg_replace("/<{skillundesc_start}>(.*?)<{skillundesc_end}>/s","",$newmatch_res);		
		
		}
	
		if($skill_overused != '')
		{
			$newmatch_res = preg_replace("/<{skilloverused_(.*?)}>/s","",$newmatch_res);
		
			$skill_overusedtitle = $gbl_skill_categories['cat_used'];
			$skill_overusedtitle = strtoupper($skill_overusedtitle);
		
		}
		else
		{
			$newmatch_res = preg_replace("/<{skilloverused_start}>(.*?)<{skilloverused_end}>/s","",$newmatch_res);		
		
	 
		}
		if($skill_compensator !='')
		{
			$newmatch_res = preg_replace("/<{skillcomp_(.*?)}>/s","",$newmatch_res);
		
			$skill_comptitle = $gbl_skill_categories['cat_opp'];
			$skill_comptitle = strtoupper($skill_comptitle);
		
	 
		}
		else
		{
			$newmatch_res = preg_replace("/<{skillcomp_start}>(.*?)<{skillcomp_end}>/s","",$newmatch_res);		
	 
		}
		if($skill_car_killer != '')
		{
			$newmatch_res = preg_replace("/<{skillckiller_(.*?)}>/s","",$newmatch_res);
		
			$skill_killertitle = $gbl_skill_categories['cat_ckill'];
			$skill_killertitle = strtoupper($skill_killertitle);
		
	 
		}
		else
		{
			$newmatch_res = preg_replace("/<{skillckiller_start}>(.*?)<{skillckiller_end}>/s","",$newmatch_res);		
		
 
		}
	
 
	
		$str .= preg_replace("/<{(.*?)}>/e","$$1",$newmatch_res);
	
 
		}
	
	
	$returncontent = preg_replace("/<{skillresult_loopstart}>(.*?)<{skillresult_loopend}>/s",$str,$returncontent);

	
//Display of average of the skills (same as the graph except the average is shown)
//(AVG SHOWN ONLY FROM THE GROUPS SELECTED BY THE USER)
//****SELF IS NOT USED TO FIND THE AVERAGE AS PER SPEC****...		


//Check if this option of Show average rating's selected or not...
	
		if($fAvg_rating_1 == 'on')    	
		{
			$returncontent = preg_replace("/<{showskillaverage_(.*?)}>/s","",$returncontent);
		}
		else
		{
	
			$returncontent = preg_replace("/<{showskillaverage_start}>(.*?)<{showskillaverage_end}>/s","",$returncontent);
		}

	preg_match("/<{skillaverage_loopstart}>(.*?)<{skillaverage_loopend}>/s",$returncontent,$matchavg);
	$newavg_match = $matchavg[1];
	
	$mysql = "select skill_id,skill_name from $skills where skill_type = 'i'";
	$skills_arr = $db_object->get_rsltset($mysql);
	
 
 
	for($i=0;$i<count($skills_arr);$i++)
	{
	
		$skill_id = $skills_arr[$i]['skill_id'];
		$skill_name = $skills_arr[$i]['skill_name'];



		//Remove the self rated values from the array ...

		$self_arr = array("grp_self"=>"grp_self");

		$grp_arr = @array_diff($group_arr,$self_arr);

		$count_grps = count($grp_arr);
 
//if there are more than one person in the same group then find the average rating of that group...

		while(list($kk,$vv) = @each($grp_arr))
		{
			$mysql = "select $textqsort_rating.rater_label_no 
				from $textqsort_rating,$other_raters
				where $textqsort_rating.rated_user = $other_raters.cur_userid
				and $textqsort_rating.rater_id = $other_raters.rater_userid
				and $textqsort_rating.skill_id = '$skill_id'
				and other_raters.cur_userid = '$id_of_user' 
				and other_raters.group_belonging = '$kk'";  
	
			 
				$alllabels_arr = $db_object->get_single_column($mysql);
		
				$ratingcount = count($alllabels_arr);
				$totalrating = 0;
		 
				$label[$kk] = $alllabels_arr[0];//$kk
	
	
				//if there are more than one person rating from the same group then the average of all the raters are taken as the rating of that group...
	
				if($ratingcount > 1)
				{
					for($n=0;$n<count($alllabels_arr);$n++)
					{
						  
						$rating = $alllabels_arr[$n];
				
						$totalrating += $rating; 
				
					}
					$avg 		= $totalrating / $ratingcount;
			
					$label[$kk] = floor($avg); //$kk
			
			
				}
	
		
	
		}
		
		$total_labels = 0;
		  
		@reset($label);
		while(list($kk,$vv) = @each($label))	
		{
			
			$label_id = $label[$kk];  
			$total_labels += $label_id;
			
			
			
		}
	 
		if($count_grps !=0)
		{
		$avg_labels = $total_labels / $count_grps;
		}
		$avg_rating = round($avg_labels,2);
		 
		
		$highest[$skill_id] = $avg_rating;
	 
//query for overrated skill display...
		
		$mysql = "select rater_label_no from $textqsort_rating where rater_id = '$id_of_user' and rated_user = '$id_of_user' and skill_id = '$skill_id'";
		$self_rating_arr = $db_object->get_a_line($mysql);		

	 
		$self_rating[$skill_id] = $self_rating_arr['rater_label_no'];
		$allothers_avg_rating[$skill_id] = $avg_rating;	
		
	 	$str1 .= preg_replace("/<{(.*?)}>/e","$$1",$newavg_match);
		
		
	}

	$returncontent = preg_replace("/<{skillaverage_loopstart}>(.*?)<{skillaverage_loopend}>/s",$str1,$returncontent);






//Check if this option of Show skill OVERUSED rating's selected or not...
	
	if($fAvg_rating_2 == 'on')  		
	{	
		$returncontent = preg_replace("/<{showskillusedoften_(.*?)}>/s","",$returncontent);
	}
	else
	{	
		$returncontent = preg_replace("/<{showskillusedoften_start}>(.*?)<{showskillusedoften_end}>/s","",$returncontent);
	}
 
//Check if this option of Show CAREER KILLERS rating's selected or not...

if($fAvg_rating_3 == 'on')		
{
	
	$returncontent = preg_replace("/<{showskillkillers_(.*?)}>/s","",$returncontent);
	
 
	//Sorting the average to find the highest average of them all...
	
	$highest_val = $highest;
	@rsort($highest_val,SORT_NUMERIC);
	$highest_avg = $highest_val[0];
	
 
	
	$one_third_of_avg = $highest_avg/3;
	
	 
	while(list($key,$val) = @each($highest))
	{
		  
		if($highest[$key] <= $one_third_of_avg)
		{
			$killer_skills[] = $key;
			
		}
		
		
	}
	
	preg_match("/<{killerskilldisplay_loopstart}>(.*?)<{killerskilldisplay_loopend}>/s",$returncontent,$killerdisplaymatch);
	$newkillerdisplaymatch = $killerdisplaymatch[1];
	
 
	for($i=0;$i<count($killer_skills);$i++)
	{
		$skill_id = $killer_skills[$i];
		
	$mysql = "select skill_id,skill_name,career_killer from $skills where skill_id = $skill_id";
 
	$killer_skills_arr = $db_object->get_rsltset($mysql);
	
		for($j=0;$j<count($killer_skills_arr);$j++)
		{
			$skill_name 	 = $killer_skills_arr[$j]['skill_name'];
			$career_killer_def = $killer_skills_arr[$j]['career_killer'];
			
			$str_killer .= preg_replace("/<{(.*?)}>/e","$$1",$newkillerdisplaymatch);
			
		}
		
		
	
	}
	
	$returncontent = preg_replace("/<{killerskilldisplay_loopstart}>(.*?)<{killerskilldisplay_loopend}>/s",$str_killer,$returncontent);
	
}
else
{
	
	$returncontent = preg_replace("/<{showskillkillers_start}>(.*?)<{showskillkillers_end}>/s","",$returncontent);
	
}



//Check if the option OVER RATED SKILLS is selected ....

if($fAvg_rating_4 == 'on')		
{
	
	$returncontent = preg_replace("/<{showskilloverrated_(.*?)}>/s","",$returncontent);
	
	
//print_r($self_rating);

//Doesnot apply option is not used to find any of the rating averages or conclusions....
	
$mysql = "select rater_labelno from $rater_label_relate,$skill_raters
		where $skill_raters.rater_id = $rater_label_relate.rater_id 
		and $skill_raters.type_name = 'd' ";
$doesnt_apply_arr = $db_object->get_a_line($mysql);
 
$doesnt_apply[] = $doesnt_apply_arr['rater_labelno'];

	$self_rating = @array_diff($self_rating,$doesnt_apply);
	 
	
	preg_match("/<{skilldisplayoverrated_loopstart}>(.*?)<{skilldisplayoverrated_loopend}>/s",$returncontent,$overrated);
	$newoverrated_match = $overrated[1];
	
	while(list($kk,$vv) = @each($self_rating))
	{
		$self_rated_val 	= $self_rating[$kk];
		
		$one_third_self 	= $self_rated_val / 3;
		
		$allothers_avg_val= $allothers_avg_rating[$kk];
		
		if($allothers_avg_val < $one_third_self)
		{
			$overrated_skill[] = $kk;
			
			$myrating 		= $self_rated_val;
			$allothersrating  = $allothers_avg_val;
			
			$mysql = "select skill_name from $skills where skill_id = '$kk'";
			$skillname_arr = $db_object->get_a_line($mysql);
			
			$skill_name = $skillname_arr['skill_name'];
			
			$str_overrated .= preg_replace("/<{(.*?)}>/e","$$1",$newoverrated_match);
		}
		
		
		
	}
	
	
	$returncontent = preg_replace("/<{skilldisplayoverrated_loopstart}>(.*?)<{skilldisplayoverrated_loopend}>/s",$str_overrated,$returncontent);
	
 
	
}
else
{
	
	$returncontent = preg_replace("/<{showskilloverrated_start}>(.*?)<{showskilloverrated_end}>/s","",$returncontent);
	
}

//Check if the option UNDER RATED SKILLS is selected ....

if($fAvg_rating_5 == 'on')		
{
	
	$returncontent = preg_replace("/<{showskillunderrated_(.*?)}>/s","",$returncontent);
	
	preg_match("/<{skilldisplayunderrated_loopstart}>(.*?)<{skilldisplayunderrated_loopend}>/s",$returncontent,$matchunderrated);
	$newmatch_underrated = $matchunderrated[1];
	
 
	@reset($self_rating);
	
	
	//-------------------new
	
	
	while(list($kk,$vv) = @each($self_rating))
	{
		$self_rated_val 		= $self_rating[$kk];
		
		$allothers_avg_val 	= $allothers_avg_rating[$kk];
		
		$one_third_of_others 	= $allothers_avg_val / 3;
		
		
		if($self_rated_val < $one_third_of_others)
		{
			$overrated_skill[] = $kk;
			
			$myrating 		= $self_rated_val;
			$allothersrating 	= $allothers_avg_val;
			
			$mysql = "select skill_name from $skills where skill_id = '$kk'";
			$skillname_arr = $db_object->get_a_line($mysql);
			
			$skill_name = $skillname_arr['skill_name'];
			
			//$str_overrated .= preg_replace("/<{(.*?)}>/e","$$1",$newoverrated_match);
			
			$str_underrated .= preg_replace("/<{(.*?)}>/e","$$1",$newmatch_underrated);
		}
		
		
		
	}
		
		
		
		$returncontent = preg_replace("/<{skilldisplayunderrated_loopstart}>(.*?)<{skilldisplayunderrated_loopend}>/s",$str_underrated,$returncontent);
		
		

	

	
}
else
{
	
	$returncontent = preg_replace("/<{showskillunderrated_start}>(.*?)<{showskillunderrated_end}>/s","",$returncontent);
	
}

//skills to improve to be a high performing position...
if($fAvg_rating_imp_1 == 'on')
{
	$returncontent = preg_replace("/<{showskillimprovefirst_(.*?)}>/s","",$returncontent);
}
else
{
	$returncontent = preg_replace("/<{showskillimprovefirst_start}>(.*?)<{showskillimprovefirst_end}>/s","",$returncontent);
}


//--------------------------------------------------------------INTERPERSONAL RESULTS OVER...

//--------------------------------------------------------------TECHNICAL RESULTS START...

preg_match("/<{skillresulttech_loopstart}>(.*?)<{skillresulttech_loopend}>/s",$returncontent,$matchres_tech);
$newmatchres_tech = $matchres_tech[1];


//the field1 and field2 are obtained from the form array at the top...

$mysql = "select skill_id $field1 $field2 from $skills where skill_type='t'";
//echo $mysql;
$skilltech_arr = $db_object->get_rsltset($mysql);

for($i=0;$i<count($skilltech_arr);$i++)
{
	$skill_id 			= $skilltech_arr[$i]['skill_id']; 
	$skill_name 		= $skilltech_arr[$i]['skill_name'];
	$skill_description	= $skilltech_arr[$i]['skill_description'];	
	$str_tech 		     .= preg_replace("/<{(.*?)}>/e","$$1",$newmatchres_tech);
}

$returncontent = preg_replace("/<{skillresulttech_loopstart}>(.*?)<{skillresulttech_loopend}>/s",$str_tech,$returncontent);

//Average display....

//$techgrp_arr ==>contains the groups for which the results are to be displayed...
//print_r($techgrp_arr);exit;

$techgrp_arr1 =$techgrp_arr;

$self_arr = array("Self"=>"Self");
$techgrp_arr1 = @array_diff($techgrp_arr1,$self_arr);
//print_r($techgrp_arr1);exit;
$tech_all = @implode("','",$techgrp_arr1);

$subclause = "and group_name in('$tech_all')";
if($tech_all !='')
{
}
//echo $subclause;exit;

if($fTech_average == 'on')
{
	$returncontent = preg_replace("/<{skillaveragetechdisplay_(.*?)}>/s","",$returncontent);
	
	preg_match("/<{skillavg_loopstart}>(.*?)<{skillavg_loopend}>/s",$returncontent,$match_avg);
	$newmatch_avg = $match_avg[1];
	
 //The average is found without including self rating...

	for($i=0;$i<count($skilltech_arr);$i++)
	{
	$skill_id = $skilltech_arr[$i]['skill_id'];
	
	$mysql = "select $rater_label_relate.rater_labelno 
			from $other_raters_tech,$rater_label_relate 
			where $rater_label_relate.rater_id = $other_raters_tech.label_id
			and $other_raters_tech.rated_user = '$id_of_user'
			and skill_id ='$skill_id' $subclause";
		//echo "$mysql<br>";
			
	$label_arr = $db_object->get_single_column($mysql);
		
	$tech_tot = 0;
	
//if more than one person has rated from the same group, then find the average of them...	
	for($j=0;$j<count($label_arr);$j++)
	{
		$label_val = $label_arr[$j];
		$tech_tot += $label_val;
		
		
	}
		
		$count_tech = count($label_arr);
		
		if($count_tech !=0)
		{
		$avg_tech = $tech_tot / $count_tech;
		$avg_tech = round($avg_tech,2);
		}
		else
		{
			$avg_tech = $tech_tot;
		}
		
		
		$mysql 	= "select skill_name from $skills where skill_id = '$skill_id'";
		$skill_arr 	= $db_object->get_a_line($mysql);
		$skill_name = $skill_arr['skill_name'];
		
		//echo "$avg_tech<br>";
		$tech_avg_all[$skill_id] = $avg_tech; 
		
		$str_avg 	.= preg_replace("/<{(.*?)}>/e","$$1",$newmatch_avg);
		
		
		
	}
	
	$returncontent = preg_replace("/<{skillavg_loopstart}>(.*?)<{skillavg_loopend}>/s",$str_avg,$returncontent);
	
	
	
}
else
{
	$returncontent = preg_replace("/<{skillaveragetechdisplay_start}>(.*?)<{skillaveragetechdisplay_end}>/s","",$returncontent);
}


//if the Technical Overrated Option is selected...

if ($fTechrating_overrated == 'on')
{
	$returncontent = preg_replace("/<{skilloverrateddisplay_(.*?)}>/s","",$returncontent);
	
	//print_r($skilltech_arr);exit;
	
	preg_match("/<{skilloverratedvalues_loopstart}>(.*?)<{skilloverratedvalues_loopend}>/s",$returncontent,$match_overrated);
	$newmatch_overrated = $match_overrated[1];
	
	
	$tech_avg_all1 = $tech_avg_all;
	@rsort($tech_avg_all1,SORT_NUMERIC);
	$tech_highest_avg = $tech_avg_all1[0];
	
	
	for($i=0;$i<count($skilltech_arr);$i++)
	{
		$skill_id 	= $skilltech_arr[$i]['skill_id'];
		$skill_name = $skilltech_arr[$i]['skill_name'];
		
		$mysql = "select $rater_label_relate.rater_labelno  
				from $tech_rating,$rater_label_relate
				where $rater_label_relate.rater_id = $tech_rating.selfrating_labelid
				and $tech_rating.skill_id = '$skill_id'
				and $tech_rating.rating_user = '$id_of_user'";


	
		$rating_labels = $db_object->get_single_column($mysql);
	
		$self_rating_label = $rating_labels[0];
		
		$tech_highest_avg_eachskill = $tech_avg_all[$skill_id];
		
		$one_third_self_tech = $self_rating_label / 3;
		
		if($tech_highest_avg_eachskill < $one_third_self_tech)
		{
			$my_rating = $self_rating_label;
			$avg_rating = $tech_highest_avg_eachskill;
			$str_overrated .= preg_replace("/<{(.*?)}>/e","$$1",$newmatch_overrated);
		}
		
	}
	
		
	$returncontent = preg_replace("/<{skilloverratedvalues_loopstart}>(.*?)<{skilloverratedvalues_loopend}>/s",$str_overrated,$returncontent);
	
	
}
else
{
	$returncontent = preg_replace("/<{skilloverrateddisplay_start}>(.*?)<{skilloverrateddisplay_end}>/s","",$returncontent);
}

if($fTechrating_underrated == 'on')
{
	$returncontent = preg_replace("/<{skilltechunderrateddisplay_(.*?)}>/s","",$returncontent);
	
//=================================

	preg_match("/<{skilltechunderrated_loopstart}>(.*?)<{skilltechunderrated_loopend}>/s",$returncontent,$match_underrated);
	$newmatch_underrated = $match_underrated[1];
	
	
	$tech_avg_all1 = $tech_avg_all;
	@rsort($tech_avg_all1,SORT_NUMERIC);
	$tech_highest_avg = $tech_avg_all1[0];
	
	
	for($i=0;$i<count($skilltech_arr);$i++)
	{
		$skill_id 	= $skilltech_arr[$i]['skill_id'];
		$skill_name = $skilltech_arr[$i]['skill_name'];
		
		$mysql = "select $rater_label_relate.rater_labelno  
				from $tech_rating,$rater_label_relate
				where $rater_label_relate.rater_id = $tech_rating.selfrating_labelid
				and $tech_rating.skill_id = '$skill_id'
				and $tech_rating.rating_user = '$id_of_user'";


	
		$rating_labels = $db_object->get_single_column($mysql);
	
		$self_rating_label = $rating_labels[0];
		
		$tech_highest_avg_eachskill = $tech_avg_all[$skill_id];
		
		$one_third_other_tech = $tech_highest_avg_eachskill / 3;
		
		if($one_third_other_tech > $self_rating_label)
		{
			$my_rating = $self_rating_label;
			$avg_rating = $tech_highest_avg_eachskill;
			$str_underrated .= preg_replace("/<{(.*?)}>/e","$$1",$newmatch_underrated);
		}
		
	}
		$returncontent = preg_replace("/<{skilltechunderrated_loopstart}>(.*?)<{skilltechunderrated_loopend}>/s",$str_underrated,$returncontent);
//===================================
	
	
	
	
	
}
else
{
	$returncontent = preg_replace("/<{skilltechunderrateddisplay_start}>(.*?)<{skilltechunderrateddisplay_end}>/s","",$returncontent);
}


//--------------------------------------------------------------Technical results over...

//---------------------------JOB FAMILY MODELS DISPLAY INTERPERSONAL START 

preg_match("/<{jobfamilymodel1_loopstart}>(.*?)<{jobfamilymodel1_loopend}>/s",$returncontent,$jobfamoldmatch);
$jobfamnewmatch = $jobfamoldmatch[1];
$str_new = ''; 
$str = ''; 
	for($times=1;$times<=3;$times++)
	{	
		$fAvg_rval = "fAvg_rating_imp_"."$times";

		$fAvg_ratingval = $$fAvg_rval;
		$impr = "fImprove_"."$times";
		$fImpr = $$impr;
		

		if($fAvg_ratingval == 'on')
		{	
		
			$mysql = "select family_name from $family where family_id = '$fImpr'";
			//echo "$mysql<br>";
			$fam_arr = $db_object->get_a_line($mysql);
			$fam_name1 = $fam_arr['family_name'];
			
			$values['fam_name1']	= $fam_name1;

//----------------------------------------------------------------------------------------
			$mysql = "select model_id from $model_factors_1 where family = '$fImpr'";
			$modelid_arr = $db_object->get_a_line($mysql);

			$model_id = $modelid_arr['model_id'];

			if($model_id != '')
			{


				$mysql = "select $model_skills.skill_id,
					round(avg(rater_label_no)) as label_got,
					level_chosen as level_required
					from $model_skills,$textqsort_rating 
					where $model_skills.skill_id = $textqsort_rating.skill_id
					and $model_skills.model_id = '$model_id'
					and $textqsort_rating.rated_user = '$id_of_user'
					and $textqsort_rating.rater_type = 'i'
					group by $textqsort_rating.skill_id";

				
				$datareq_arr = $db_object->get_rsltset($mysql);


//CREATING THE REQUIRED DATA IN AN ARRAY...

				$newarray = array();


				for($i=0;$i<count($datareq_arr);$i++)
				{
	
					$label_got = $datareq_arr[$i]['label_got'];
					$level_required = $datareq_arr[$i]['level_required'];
					$skill_id = $datareq_arr[$i]['skill_id'];

					$newarray[$level_required][$label_got][]= $skill_id;
	
				}



				$mysql = "select count(*) as cnt from $skill_raters where skill_type = 'i'";
				$cnt_arr = $db_object->get_a_line($mysql);
				$cnt = $cnt_arr['cnt'];
	
				$mysql = "select rater_level_$default as rating_label ,rater_labelno from $skill_raters,$rater_label_relate where $skill_raters.rater_id = $rater_label_relate.rater_id and skill_type = 'i'";
				$raterlabel_arr = $db_object->get_rsltset($mysql);
	

//TABLE START POSITIONS
			preg_match("/<{trdisplay_start}>(.*?)<{tddisplay_start}>/s",$jobfamnewmatch,$trmatch_old);
			$trmatch = $trmatch_old[1];

	
//TR ENDING
			preg_match("/<{tddisplay_end}>(.*?)<{trdisplay_end}>/s",$jobfamnewmatch,$trendmatchold);
			$trendmatch = $trendmatchold[1];
	
			preg_match("/<{tddisplay_start}>(.*?)<{tddisplay_end}>/s",$jobfamnewmatch,$tdmatch_old);
			$tdmatch = $tdmatch_old[1];

//SKILLS TO DISPLAY INSIDE THE TD'S
			preg_match("/<{showallskills_loopstart}>(.*?)<{showallskills_loopend}>/s",$tdmatch,$skillsold);
			$skill_displaymatch = $skillsold[1];

//DISPLAY OF THE LABEL NAMES ... 
			preg_match("/<{labeldisplay_loopstart}>(.*?)<{labeldisplay_loopend}>/s",$jobfamnewmatch,$labelold);
			$labelmatch = $labelold[1];
	
			//preg_match("/<{positionlabeldisplay_loopstart}>(.*?)<{positionlabeldisplay_loopend}>/s",$returncontent,$poslabelold);
			//$poslabelmatch = $poslabelold[1];
	
//RETRIEVING THE COLORS FROM THE DATABASE...

				$mysql = "select key_1,key_2,key_3,key_4,key_5 from $posmodel_colors where posmodel_id = '1'";
				$color_arr = $db_object->get_a_line($mysql);
				//print_r($color_arr);

				$color1 = $color_arr['key_1'];
				$color2 = $color_arr['key_2'];
				$color3 = $color_arr['key_3'];
				$color4 = $color_arr['key_4'];
				$color5 = $color_arr['key_5'];
			
				$nrows=$cnt;
				$ncolumns=1;
				$percent_fit_inter = array();


				$no_of_skills_inter1 = 1;
				$no_of_skills_inter2 = 1;
				$no_of_skills_inter3 = 1;
				$no_of_skills_inter4 = 1;
				$no_of_skills_inter5 = 1;

$newstring = '';
$skill_str = '';
			for($i=$cnt;$i>0;$i--)
			{
				$mysql = "select rater_level_$default as rating_label
						from $skill_raters,$rater_label_relate
						where $skill_raters.rater_id = $rater_label_relate.rater_id
						and skill_type = 'i'
						and rater_labelno = $nrows";
				 	//echo "$mysql<br>";
					$label_arr = $db_object->get_a_line($mysql);
					$labelname = $label_arr['rating_label'];
			
			
			
				$newstring .= preg_replace("/<{(.*?)}>/e","$$1",$trmatch);

	
				for($j=$cnt;$j>0;$j--)
				{
 
				$skill_str="";
				$specific_color = '';
 
				for($l=0;$l<count($newarray[$nrows][$ncolumns]);$l++)
				{
			
					$skill_id_dis = $newarray[$nrows][$ncolumns][$l];
					$mysql = "select skill_name,skill_id from $skills where skill_id = '$skill_id_dis'";
					$skills_arr = $db_object->get_a_line($mysql);
					$skill_display = $skills_arr['skill_name'];
				
					$key_array[$keyid][] = $skill_display;


//COLOR DISPLAY				
					if($nrows == $cnt && $ncolumns == '1')
					{
						//echo "row $nrows and col $ncolumns <br>";
						$specific_color = $color1; //"red"
						$keyid = 1;
						$percent_fit_inter[$keyid] = $no_of_skills_inter1++;
					
					 
					}
				
					elseif($nrows == '1' && $ncolumns == $cnt)
					{
						//echo "row $nrows and col $ncolumns <br>";
						$specific_color = $color5; //"black"
						$keyid = 5;
						$percent_fit_inter[$keyid] = $no_of_skills_inter5++; 
					}
					elseif($nrows == $ncolumns)
					{
						$specific_color = $color3;  //"green"
						$keyid = 3;
						$percent_fit_inter[$keyid] = $no_of_skills_inter3++;
					}
					elseif($nrows < $cnt && $ncolumns >= $nrows)
					{
						$specific_color = $color4;  //"cyan"
						$keyid = 4;
						$percent_fit_inter[$keyid] = $no_of_skills_inter4++;
					}
					else //if($nrows == 1 && $ncolumns == 1)
					{
						$specific_color = $color2;  //"yellow"
						$keyid = 2;
						$percent_fit_inter[$keyid] = $no_of_skills_inter2++; 
					}
				
//COLOR DISPLAY END
							



				//echo "<a href=link.php>$skill_display</a> in row $nrows and col $ncolumns<br>";

				$skill_str .= preg_replace("/<{(.*?)}>/e","$$1",$skill_displaymatch);
			
				}
			
			 
			if(count($newarray[$nrows][$ncolumns]) < 1)
			{
								
				$skill_str = preg_replace("/<{showallskills_loopstart}>(.*?)<{showallskills_loopend}>/s","",$skill_str);
			
			}
		  
			$tdmatch1 = preg_replace("/<{showallskills_loopstart}>(.*?)<{showallskills_loopend}>/s",$skill_str,$tdmatch);
			
			if($j == 1) //$cnt-1
			{
			
				$mysql = "select rater_level_$default as rating_label
					from $skill_raters,$rater_label_relate
					where $skill_raters.rater_id = $rater_label_relate.rater_id
					and skill_type = 'i'
					and rater_labelno = $nrows";

				$label_arr = $db_object->get_a_line($mysql);
				$labelname = $label_arr['rating_label'];

				
//if it is the end of columns then add the last column and move on to the next row ...
		 
				$newstring .= preg_replace("/<{(.*?)}>/e","$$1",$tdmatch1);
				 
				$newstring .= preg_replace("/<{(.*?)}>/e","$$1",$trendmatch);
				
				$nrows--;
				$ncolumns=1;
				
			}
			else
			{
				//echo "<br><b>$ncolumns<br>";

		 		$mysql = "select rater_level_$default as rating_label
					from $skill_raters,$rater_label_relate
					where $skill_raters.rater_id = $rater_label_relate.rater_id
					and skill_type = 'i'
					and rater_labelno = $nrows";

				$label_arr = $db_object->get_a_line($mysql);
				$labelname = $label_arr['rating_label'];
				
				$newstring .= preg_replace("/<{(.*?)}>/e","$$1",$tdmatch1);
				
				$ncolumns++;
				
			}
			

			}


		}


//PERCENT FIT CALCULATION...

//print_r($percent_fit_inter);
	
$fit_1_inter = 0;
$fit_2_inter = 0;
$fit_3_inter = 0;
$fit_4_inter = 0;
$fit_5_inter = 0;

		$total_skills_fit_inter = 0;

		while(list($kk,$vv) = @each($percent_fit_inter))
		{
			if($kk == 1)
			{
				$fit_1_inter = $vv * 0;
			}
			if($kk == 2)
			{
				$fit_2_inter = $vv * 50;
			}
			if($kk == 3)
			{
				$fit_3_inter = $vv * 100;
			}
			if($kk == 4)
			{
				$fit_4_inter = $vv * 100;
			}
			if($kk == 5)
			{
				$fit_5_inter = $vv * 100;
			}

			$total_skills_fit_inter += $vv;

//echo "$total_skills_fit_inter<br>";

		}

		$fit_full_inter = $fit_1_inter + $fit_2_inter + $fit_3_inter + $fit_4_inter + $fit_5_inter;
//echo "$fit_full_inter<br>";
		if($fit_full_inter != 0 || $fit_full_inter != '')
		{
		$fit_inter = round(($fit_full_inter / $total_skills_fit_inter),2);
//echo "$fit_inter<br>";

		}
		 
	//KEY AND SKILL VALUES FOR THE INTERPERSONAL FIT PERCENTAGE...
	 
$label_str = '';
		$mysql = "select rater_level_$default as rating_label,rater_labelno from $skill_raters,$rater_label_relate where $skill_raters.rater_id = $rater_label_relate.rater_id and skill_type = 'i'";

		$label_myrating_arr = $db_object->get_rsltset($mysql);
		for($i=0;$i<count($label_myrating_arr);$i++)
		{
			$labelname = $label_myrating_arr[$i]['rating_label'];
			$labelid = $label_myrating_arr[$i]['rater_labelno'];
			$label_str .= preg_replace("/<{(.*?)}>/e","$$1",$labelmatch);
			//$poslabel_str .= preg_replace("/<{(.*?)}>/e","$$1",$poslabelmatch);
	
		}


		//DISPLAY OF THE MODEL NAME...

		$mysql = "select model_name from $model_name_table where model_id = '$model_id'";
		$modelname_arr = $db_object->get_a_line($mysql);
		$modelname = $modelname_arr['model_name'];
		$values['modelname'] = $modelname;



		$jobfam_match1 = preg_replace("/<{labeldisplay_loopstart}>(.*?)<{labeldisplay_loopend}>/s",$label_str,$jobfamnewmatch);
		//$returncontent = preg_replace	("/<{positionlabeldisplay_loopstart}>(.*?)<{positionlabeldisplay_loopend}>/s",$poslabel_str,$returncontent);	

		$jobfamnewmatch1 = preg_replace("/<{trdisplay_start}>(.*?)<{trdisplay_end}>/s",$newstring,$jobfam_match1);
		$str = preg_replace("/<{(.*?)}>/e","$$1",$jobfamnewmatch1); 
		$str_new .= $str; 

 
		}

		else
		{
		//$returncontent = preg_replace("/<{jobfamilymodel1_loopstart}>(.*?)<{jobfamilymodel1_loopend}>/s","",$returncontent);
		//echo "No Models Found [coded]";
		}


//----------------------------------------------------------------------------------------
		//$returncontent = preg_replace("/<{jobfamilymodel1_loop(.*?)}>/s","",$returncontent);		


		}
		else
		{
		$returncontent = preg_replace("/<{jobfamilymodel1_loopstart}>(.*?)<{jobfamilymodel1_loopend}>/s","",$returncontent);
		}

	}
$returncontent = preg_replace("/<{jobfamilymodel1_loopstart}>(.*?)<{jobfamilymodel1_loopend}>/s",$str_new,$returncontent);
  


//---------------------------JOB FAMILY MODELS DISPLAY INTERPERSONAL END 

//************************
//************************
//---------------------------JOB FAMILY MODELS DISPLAY TECHNICAL START 

preg_match("/<{jobfamilymodel1_tech_loopstart}>(.*?)<{jobfamilymodel1_tech_loopend}>/s",$returncontent,$jobfamoldmatch_tech);
$jobfamnewmatch_tech = $jobfamoldmatch_tech[1];
//echo $jobfamnewmatch_tech;exit;
$str_new_tech = ''; 
$str_tech = ''; 
	for($times=1;$times<=3;$times++)
	{	

		$fAvg_rval_tech = "fAvg_rating_imptech_"."$times";
		$fAvg_ratingval_tech = $$fAvg_rval_tech;
		$impr_tech = "fImprovetech_"."$times";
		$fImpr_tech = $$impr_tech;
		

		if($fAvg_ratingval_tech == 'on')
		{	
		
			$mysql = "select family_name from $family where family_id = '$fImpr_tech'";
			//echo "$mysql<br>";
			$fam_arr = $db_object->get_a_line($mysql);
			$fam_name1_tech = $fam_arr['family_name'];
			
			//$values['fam_name1_tech']	= $fam_name1_tech;

//----------------------------------------------------------------------------------------
			$mysql = "select model_id from $model_factors_1 where family = '$fImpr_tech'";
//echo "$mysql<br>";
			$modelid_arr = $db_object->get_a_line($mysql);

			$model_id_tech = $modelid_arr['model_id'];

			if($model_id_tech != '')
			{


			$mysql = "select $model_skills.skill_id as skill_id,
			round(avg(label_id)) as label_got,
			level_chosen as level_required
			from $other_raters_tech,$model_skills 
			where $model_skills.skill_id = $other_raters_tech.skill_id
			and $model_skills.model_id = '$model_id_tech'
			and $other_raters_tech.rated_user = '$id_of_user'
			group by $other_raters_tech.skill_id";	

			//	echo "$mysql<br><br>";
			$datareq_arr = $db_object->get_rsltset($mysql);

//print_r($datareq_arr);

//CREATING THE REQUIRED DATA IN AN ARRAY...

			$newarray_tech = array();


			for($i=0;$i<count($datareq_arr);$i++)
			{
				$label_got = $datareq_arr[$i]['label_got'];
				$level_required = $datareq_arr[$i]['level_required'];
				$skill_id = $datareq_arr[$i]['skill_id'];

				$newarray_tech[$level_required][$label_got][]= $skill_id;
			}




			$mysql = "select count(*) as cnt from $skill_raters where skill_type = 't'";
			$cnt_arr = $db_object->get_a_line($mysql);
				$cnt_tech = $cnt_arr['cnt'];
	
			$mysql = "select rater_level_$default as rating_label ,rater_labelno from $skill_raters,$rater_label_relate where $skill_raters.rater_id = $rater_label_relate.rater_id and skill_type = 't'";
			$raterlabel_arr = $db_object->get_rsltset($mysql);
	

//TABLE START POSITIONS
			preg_match("/<{trdisplay_tech_start}>(.*?)<{tddisplay_tech_start}>/s",$jobfamnewmatch_tech,$trmatch_old_tech);
			$trmatch_tech = $trmatch_old_tech[1];

//TR ENDING
			preg_match("/<{tddisplay_tech_end}>(.*?)<{trdisplay_tech_end}>/s",$jobfamnewmatch_tech,$trendmatchold_tech);
			$trendmatch_tech = $trendmatchold_tech[1];

			preg_match("/<{tddisplay_tech_start}>(.*?)<{tddisplay_tech_end}>/s",$jobfamnewmatch_tech,$tdmatch_old_tech);
			$tdmatch_tech = $tdmatch_old_tech[1];

//SKILLS TO DISPLAY INSIDE THE TD'S
			preg_match("/<{showallskills_tech_loopstart}>(.*?)<{showallskills_tech_loopend}>/s",$tdmatch_tech,$skillsold_tech);
			$skill_displaymatch_tech = $skillsold_tech[1];

//DISPLAY OF THE LABEL NAMES ... 
			preg_match("/<{labeldisplay_tech_loopstart}>(.*?)<{labeldisplay_tech_loopend}>/s",$jobfamnewmatch_tech,$labelold_tech);
			$labelmatch_tech = $labelold_tech[1];
	
			//preg_match("/<{positionlabeldisplay_loopstart}>(.*?)<{positionlabeldisplay_loopend}>/s",$returncontent,$poslabelold);
			//$poslabelmatch = $poslabelold[1];
	
//RETRIEVING THE COLORS FROM THE DATABASE...

			$mysql = "select key_1,key_2,key_3,key_4,key_5 from $posmodel_colors where posmodel_id = '1'";
			$color_arr = $db_object->get_a_line($mysql);
			//print_r($color_arr);

			$color1 = $color_arr['key_1'];
			$color2 = $color_arr['key_2'];
			$color3 = $color_arr['key_3'];
			$color4 = $color_arr['key_4'];
			$color5 = $color_arr['key_5'];
		
			$nrows=$cnt_tech;
			$ncolumns=1;
			$percent_fit_tech = array();


			$no_of_skills_tech1 = 1;
			$no_of_skills_tech2 = 1;
			$no_of_skills_tech3 = 1;
			$no_of_skills_tech4 = 1;
			$no_of_skills_tech5 = 1;

	$newstring_tech = '';
	$skill_str_tech = '';
//print_r($newarray_tech);
			for($i=$cnt_tech;$i>0;$i--)
			{
				$mysql = "select rater_level_$default as rating_label
						from $skill_raters,$rater_label_relate
						where $skill_raters.rater_id = $rater_label_relate.rater_id
						and skill_type = 't'
						and rater_labelno = $nrows";
			 	//echo "$mysql<br>";
				$label_arr = $db_object->get_a_line($mysql);
				$labelname_tech = $label_arr['rating_label'];
			
			
			
				$newstring_tech .= preg_replace("/<{(.*?)}>/e","$$1",$trmatch_tech);


				for($j=$cnt_tech;$j>0;$j--)
				{
 
				$skill_str_tech="";
				$specific_color_tech = '';

				for($l=0;$l<count($newarray_tech[$nrows][$ncolumns]);$l++)
				{
			
					$skill_id_dis_tech = $newarray_tech[$nrows][$ncolumns][$l];
					$mysql = "select skill_name,skill_id from $skills where skill_id = '$skill_id_dis_tech'";

					$skills_arr = $db_object->get_a_line($mysql);
					$skill_display_tech = $skills_arr['skill_name'];
				
					$key_array_tech[$keyid_tech][] = $skill_display_tech;


//COLOR DISPLAY				
					if($nrows == $cnt_tech && $ncolumns == '1')
					{
						//echo "row $nrows and col $ncolumns <br>";
						$specific_color_tech = $color1; //"red"
						$keyid_tech = 1;
						$percent_fit_tech[$keyid_tech] = $no_of_skills_tech1++;
					
					 
					}
				
					elseif($nrows == '1' && $ncolumns == $cnt_tech)
					{
						//echo "row $nrows and col $ncolumns <br>";
						$specific_color_tech = $color5; //"black"
						$keyid_tech = 5;
						$percent_fit_tech[$keyid_tech] = $no_of_skills_tech5++; 
					}
					elseif($nrows == $ncolumns)
					{
						$specific_color_tech = $color3;  //"green"
						$keyid_tech = 3;
						$percent_fit_tech[$keyid_tech] = $no_of_skills_tech3++;
					}
					elseif($nrows < $cnt_tech && $ncolumns >= $nrows)
					{
						$specific_color_tech = $color4;  //"cyan"
						$keyid_tech = 4;
						$percent_fit_tech[$keyid_tech] = $no_of_skills_tech4++;
					}
					else //if($nrows == 1 && $ncolumns == 1)
					{
						$specific_color_tech = $color2;  //"yellow"
						$keyid_tech = 2;
						$percent_fit_tech[$keyid_tech] = $no_of_skills_tech2++; 
					}
				
//COLOR DISPLAY END
							



				//echo "<a href=link.php>$skill_display</a> in row $nrows and col $ncolumns<br>";

				$skill_str_tech .= preg_replace("/<{(.*?)}>/e","$$1",$skill_displaymatch_tech);
			
				}
			
			 
			if(count($newarray_tech[$nrows][$ncolumns]) < 1)
			{
								
				$skill_str_tech = preg_replace("/<{showallskills_tech_loopstart}>(.*?)<{showallskills_tech_loopend}>/s","",$skill_str_tech);
			
			}
		  
			$tdmatch1_tech = preg_replace("/<{showallskills_tech_loopstart}>(.*?)<{showallskills_tech_loopend}>/s",$skill_str_tech,$tdmatch_tech);
			
			if($j == 1) //$cnt-1
			{
			
				$mysql = "select rater_level_$default as rating_label
					from $skill_raters,$rater_label_relate
					where $skill_raters.rater_id = $rater_label_relate.rater_id
					and skill_type = 't'
					and rater_labelno = $nrows";

				$label_arr = $db_object->get_a_line($mysql);
				$labelname_tech = $label_arr['rating_label'];

				
//if it is the end of columns then add the last column and move on to the next row ...
		 
				$newstring_tech .= preg_replace("/<{(.*?)}>/e","$$1",$tdmatch1_tech);
				 
				$newstring_tech .= preg_replace("/<{(.*?)}>/e","$$1",$trendmatch_tech);
				
				$nrows--;
				$ncolumns=1;
				
			}
			else
			{
				//echo "<br><b>$ncolumns<br>";

		 		$mysql = "select rater_level_$default as rating_label
					from $skill_raters,$rater_label_relate
					where $skill_raters.rater_id = $rater_label_relate.rater_id
					and skill_type = 't'
					and rater_labelno = $nrows";

				$label_arr = $db_object->get_a_line($mysql);
				$labelname_tech = $label_arr['rating_label'];
				
				$newstring_tech .= preg_replace("/<{(.*?)}>/e","$$1",$tdmatch1_tech);
				
				$ncolumns++;
				
			}
			

			}


		}


//PERCENT FIT CALCULATION...

//print_r($percent_fit_tech);
	
$fit_1_tech = 0;
$fit_2_tech = 0;
$fit_3_tech = 0;
$fit_4_tech = 0;
$fit_5_tech = 0;
$fit_tech = '';

		$total_skills_fit_tech = 0;

		while(list($kk,$vv) = @each($percent_fit_tech))
		{
			if($kk == 1)
			{
				$fit_1_tech = $vv * 0;
			}
			if($kk == 2)
			{
				$fit_2_tech = $vv * 50;
			}
			if($kk == 3)
			{
				$fit_3_tech = $vv * 100;
			}
			if($kk == 4)
			{
				$fit_4_tech = $vv * 100;
			}
			if($kk == 5)
			{
				$fit_5_tech = $vv * 100;
			}

			$total_skills_fit_tech += $vv;

//echo "$total_skills_fit_inter<br>";

		}

		$fit_full_tech = $fit_1_tech + $fit_2_tech + $fit_3_tech + $fit_4_tech + $fit_5_tech;
//echo "$fit_full_inter<br>";
		if($fit_full_tech != 0 || $fit_full_tech != '')
		{
		$fit_tech = round(($fit_full_tech / $total_skills_fit_tech),2);
//echo "$fit_inter<br>";

		}
		 
	//KEY AND SKILL VALUES FOR THE TECHNICAL FIT PERCENTAGE...
	 
$label_str_tech = '';
		$mysql = "select rater_level_$default as rating_label,rater_labelno from $skill_raters,$rater_label_relate where $skill_raters.rater_id = $rater_label_relate.rater_id and skill_type = 't'";

		$label_myrating_arr = $db_object->get_rsltset($mysql);
		for($i=0;$i<count($label_myrating_arr);$i++)
		{
			$labelname_tech_myrating = $label_myrating_arr[$i]['rating_label'];
			$labelid_tech = $label_myrating_arr[$i]['rater_labelno'];
			$label_str_tech .= preg_replace("/<{(.*?)}>/e","$$1",$labelmatch_tech);
			//$poslabel_str .= preg_replace("/<{(.*?)}>/e","$$1",$poslabelmatch);
	
		}


		//DISPLAY OF THE MODEL NAME...

		$mysql = "select model_name from $model_name_table where model_id = '$model_id'";
		$modelname_arr = $db_object->get_a_line($mysql);
		$modelname_tech = $modelname_arr['model_name'];
		//$values['modelname'] = $modelname;



		$jobfam_match1_tech = preg_replace("/<{labeldisplay_tech_loopstart}>(.*?)<{labeldisplay_tech_loopend}>/s",$label_str_tech,$jobfamnewmatch_tech);
		//$returncontent = preg_replace	("/<{positionlabeldisplay_loopstart}>(.*?)<{positionlabeldisplay_loopend}>/s",$poslabel_str,$returncontent);	

		$jobfamnewmatch1_tech = preg_replace("/<{trdisplay_tech_start}>(.*?)<{trdisplay_tech_end}>/s",$newstring_tech,$jobfam_match1_tech);
		$str_tech = preg_replace("/<{(.*?)}>/e","$$1",$jobfamnewmatch1_tech); 
		$str_new_tech .= $str_tech; 

 
		}

		else
		{
		//$returncontent = preg_replace("/<{jobfamilymodel1_loopstart}>(.*?)<{jobfamilymodel1_loopend}>/s","",$returncontent);
		//echo "No Models Found [coded]";
		}


//----------------------------------------------------------------------------------------
		//$returncontent = preg_replace("/<{jobfamilymodel1_loop(.*?)}>/s","",$returncontent);		


		}
		else
		{
		$returncontent = preg_replace("/<{jobfamilymodel1_tech_loopstart}>(.*?)<{jobfamilymodel1_tech_loopend}>/s","",$returncontent);
		}

	}
$returncontent = preg_replace("/<{jobfamilymodel1_tech_loopstart}>(.*?)<{jobfamilymodel1_tech_loopend}>/s",$str_new_tech,$returncontent);
  


//---------------------------JOB FAMILY MODELS DISPLAY END


//************************
//************************

 	
	$values['username'] = $username;
	$values['sys_ownermessage'] = $message;
	$returncontent = $common->direct_replace($db_object,$returncontent,$values);
	
	echo $returncontent;

		
	}  //end of function show_results()



function save_data($db_object,$common,$post_var,$user_id,$default,$error_msg)
{
	$models_percent_fit = $common->prefix_table('models_percent_fit');
	
	while(list($kk,$vv) = @each($post_var))
	{
		$$kk = $vv;
	}

	
	@reset($post_var);

	while(list($kk,$vv) = @each($post_var))
	{
		$$kk = $vv;

//PERCENT FIT OF THE PERSON FOR INTER & TECHNICAL SKILLS ONLY ARE SAVED TO DATABASE ON CLICK OF SAVE

		if(ereg("^fFit_",$kk))
		{
			if($fEmpl_id == '')
			{
				$id_of_user = $user_id;
			}
			else
			{
				$id_of_user = $fEmpl_id;
			}

			list($un,$skill_type,$modelid) = split("_",$kk);
			 
			$mysql = "select id from $models_percent_fit where user_id = '$id_of_user' and skill_type= '$skill_type' and model_id = '$modelid'";
			//echo $mysql;

			$idexists_arr = $db_object->get_single_column($mysql);

			if($idexists_arr != '')
			{
			$mysql = "delete from $models_percent_fit where user_id = '$id_of_user' and skill_type= '$skill_type' and model_id = '$modelid'";
			$db_object->insert($mysql);
			}
			
			$mysql = "insert into $models_percent_fit set user_id = '$id_of_user' ,skill_type='$skill_type' ,model_id = '$modelid', percent_fit = '$vv'";
			$db_object->insert($mysql);
			
			
				
		}
	}

$message = $error_msg['cModeldatasaved'];
echo $message;


	
}
 
} 	//end of class
$obj = new appraisalResults;


//$post_var = @array_merge($_POST,$_GET);

if($fSave)
{
//print_r($post_var);

$obj->save_data($db_object,$common,$post_var,$user_id,$default,$error_msg);
}
else
{
$obj->show_results($db_object,$common,$post_var,$user_id,$default,$gbl_skill_categories);
}
 
include_once('footer.php');
?>
