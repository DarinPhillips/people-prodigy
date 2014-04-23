<?php
/*---------------------------------------------
SCRIPT:position_model3.php
AUTHOR:info@chrisranjana.com	
UPDATED:17th Nov

DESCRIPTION:
This script displays the third step of position models created by admin.

---------------------------------------------*/
include("../session.php");

class position_model
{
	function show_models($db_object,$common,$post_var,$user_id,$default,$error_msg,$gbl_files)
	{
		$xPath		= $common->path;
		$returncontent	= $xPath."/templates/career/position_model3.html";
		$returncontent	= $common->return_file_content($db_object,$returncontent);
		
		
		while(list($kk,$vv) = @each ($post_var))
		{
			$$kk = $vv;
		
			if(ereg("^fLabel_",$kk))
			{
					
				list($un,$qid,$aid) = split("_",$kk);

				$label_arr[$qid][] = $aid;
			}
		}



		$label_for_model 		= $common->prefix_table('label_for_model');
		$skills  			= $common->prefix_table('skills');
		$rater_label_relate 		= $common->prefix_table('rater_label_relate');
		$user_table 			= $common->prefix_table('user_table');

		$approved_devbuilder 		= $common->prefix_table('approved_devbuilder');
		$assign_solution_builder 	= $common->prefix_table('assign_solution_builder');
		$learning_feedback_results 	= $common->prefix_table('learning_feedback_results');
		$approved_selected_objective 	= $common->prefix_table('approved_selected_objective');
		$approved_feedback 		= $common->prefix_table('approved_feedback');


//To store as cookie...
		
		if($fDiff_per == 'top')
		{
			$diff = "Top";
			$percent_val = $fTop_val;
		}
		elseif($fDiff_per == 'bottom')
		{
			$diff = "Bottom";
			$percent_val = $fBottom_val;
		}
		if($fDiff_learn == 'top')
		{
			$diff = "Top";
			$percent_val = $fImp_val;
		}
		elseif($fDiff_learn == 'bottom')
		{
			$diff = "Bottom";
			$percent_val = $fWhatdone_val;
		}
		
$qualifications = "$diff||$percent_val";

setcookie("Qualifications",$qualifications,0,"/");

include_once("header.php");
$values['model_id'] = $model_id;

if($fdir_rep == 'sel_direct')
{
	$model_users_arr = $fDir_reports;
}
elseif($fdir_rep == 'all')
{
	

	$all_boss = @explode(",",$all_boss);
	if(@in_array("All",$all_boss))              
		{
			
			$position_id = 1;
			$all_dirrep = $common->get_chain_below($position_id,$db_object,$twodarr);

			$all_users_sel = @implode("','",$all_dirrep);
			
			$mysql = "select user_id from $user_table where position in ('$all_users_sel')";
			$model_users_arr = $db_object->get_single_column($mysql);
			
		}
	elseif(@in_array("None",$all_boss))
		{
			
			
		}
	elseif(!@in_array("All",$all_boss) && !@in_array("None",$all_boss))
		{
			 
			$all_boss_arr = $all_boss;

			while(list($kk,$vv) = @each($all_boss_arr))
			{
				if($vv!= '')
				{
					$mysql = "select position from $user_table where user_id = $vv";
					$pos_arr = $db_object->get_a_line($mysql);
					$position_id = $pos_arr['position'];
				}
			
				$all_dirrep = $common->get_chain_below($position_id,$db_object,$twodarr);
					
			}
		 
			$all_users_sel = @implode("','",$all_dirrep);
			
			$mysql = "select user_id from $user_table where position in ('$all_users_sel')";
			$model_users_arr = $db_object->get_single_column($mysql);
			
		}
}
else
{
$model_users_arr = @explode(",",$users_of_model);
}
//THE USERS SELECTED ARE $model_users_arr

		
		$interskills_arr = @explode(",",$interskills);
		
		$techskills_arr = @explode(",",$techskills);
		
		$this->store_rating_values($db_object,$common,$model_users_arr,$interskills_arr,$techskills_arr);

//DIFFERENTIATORS FROM THE PERFORMANCE PLAN...
//============================================

		//$mysql = "select count(distinct(user_id)) as user_count from $label_for_model";
		//$arr = $db_object->get_a_line($mysql);
		//$user_count = $arr['user_count'];
		
		
		if($fDiff_per == 'top')
		{
			$diff = $fTop_val;
			$differentiators = $diff / 100;

	$mysql = "select count(distinct($approved_selected_objective.user_id)) cntuid
		from $approved_selected_objective,$approved_feedback 
		where $approved_selected_objective.user_id = $approved_feedback.user_id";
		$uidcnt_arr = $db_object->get_a_line($mysql);
		$user_count = $uidcnt_arr['cntuid'];


			
		$limitval = $differentiators * $user_count;

	$mysql = "select $approved_selected_objective.user_id,
		(sum(idelivered) / sum(committed_no) * 100 ) as percent_performance
		from $approved_selected_objective,$approved_feedback
		where $approved_selected_objective.user_id = $approved_feedback.raters_id
		and $approved_feedback.user_id = $approved_feedback.raters_id
		and $approved_selected_objective.o_id = $approved_feedback.o_id
		group by $approved_selected_objective.user_id 
		order by percent_performance desc limit 0,$limitval";
		//echo "$mysql<br>";
	$user_cnt_arr = $db_object->get_single_column($mysql);
	//print_r($user_cnt_arr);
	$users_to_include = @implode("','",$user_cnt_arr);	
		
					
		}
		if($fDiff_per == 'bottom')
		{
			$diff = $fBottom_val;
			$differentiators = $diff / 100;

		$mysql = "select count(distinct($approved_selected_objective.user_id)) cntuid
			from $approved_selected_objective,$approved_feedback 
			where $approved_selected_objective.user_id = $approved_feedback.user_id";
		$uidcnt_arr = $db_object->get_a_line($mysql);
		$user_count = $uidcnt_arr['cntuid'];
		$limitval = $differentiators * $user_count;

		$mysql = "select $approved_selected_objective.user_id,
		(sum(idelivered) / sum(committed_no) * 100 ) as percent_performance
		from $approved_selected_objective,$approved_feedback
		where $approved_selected_objective.user_id = $approved_feedback.raters_id
		and $approved_feedback.user_id = $approved_feedback.raters_id
		and $approved_selected_objective.o_id = $approved_feedback.o_id
		group by $approved_selected_objective.user_id 
		order by percent_performance asc limit 0,$limitval";


		$user_cnt_arr = $db_object->get_single_column($mysql);
		
		//print_r($user_cnt_arr);
		$users_to_include = @implode("','",$user_cnt_arr);	

		}
		
		$label_i = @implode("','",$label_arr[i]);
		$inter_labels = $label_arr['i'];

		$label_t = @implode("','",$label_arr[t]);
		$tech_labels = $label_arr['t'];

//DIFFERENTIATORS FROM THE LEARNING PLAN...
//============================================		

		if($fDiff_learn == 'top')
		{
			$diff_learn = $fImp_val;
			$improvement = $diff_learn / 100;
			
			$mysql = "select count(rated_id) as cnt from $learning_feedback_results where status = '1'";
			$usercnt_arr = $db_object->get_a_line($mysql);
			$user_count = $usercnt_arr['cnt'];

			$limitval_learn = $improvement * $user_count;
			
			$limitval_learn1=@explode(".",$limitval_learn);
			
			$limitval_learn=$limitval_learn1[0];
			
			if($limitval_learn==0)
			$limitval_learn=1;
			
			$mysql = "select distinct(rated_id) from $learning_feedback_results where value='2' and status = '1' limit 0,$limitval_learn";
			
			$learn_arr = $db_object->get_single_column($mysql);
			
			$users_to_include = @implode("','",$learn_arr);	
			
		}
		if($fDiff_learn == 'bottom')
		{
			$dif_learn = $fWhatdone_val;
			$whatdone = $dif_learn / 100;

			
		$mysql = "select count(distinct(user_id)) as user_count
			from $approved_devbuilder
			where cdate <> '0000-00-00'
			and completed_date <> '0000-00-00'";
		
		$usercnt_arr = $db_object->get_a_line($mysql);
		$user_count = $usercnt_arr['user_count'];
		$limitval_learn = $whatdone * $user_count;


//THE DIFFERENCE BETWEEN THE START DATE AND THE USER SAID DATE AND THE ACTUAL
//DATE COMPLETED IS FOUND AND THE PERCENTAGE OF LAG IS FOUND OUT. THE LEAST
//THE LAG THE BETTER THE PERCENTAGE VALUE.
		
		
		$mysql = "select approved_devbuilder.user_id,100 - sum((to_days(approved_devbuilder.cdate) - to_days(assign_solution_builder.plan_approved_date)) /
		(to_days(approved_devbuilder.completed_date) - to_days(assign_solution_builder.plan_approved_date)) * 100) as days_total_taken,
		approved_devbuilder.skill_id,plan_approved_date,cdate,completed_date 
		from approved_devbuilder , assign_solution_builder
		where approved_devbuilder.user_id = assign_solution_builder.user_id
		and approved_devbuilder.skill_id = assign_solution_builder.skill_id
		and approved_devbuilder.completed_date <> '0000-00-00'
		and approved_devbuilder.cdate <> '0000-00-00' group by user_id order by days_total_taken desc limit 0,$limitval_learn" ;
		
	
	
		$whatdone_arr = $db_object->get_single_column($mysql);	

		$users_to_include = @implode("','",$whatdone_arr);

		}
		
		

//echo $users_to_include;


//CHECK AND SHOW ALERT IF ANY OR NO USERS HAVE COMPLETED THE PROCESS OF RATING...
	//(DONE IN JAVASCRIPT IN TEMPLATE)

		$mysql = "select distinct(user_id) from label_for_model";
		$usrcnt_arr = $db_object->get_single_column($mysql);

		if($usrcnt_arr != '')
		{
			$usrcnt = @implode(",",$usrcnt_arr);
			$temp_arr = @explode("','",$users_to_include);
			$usrtoinc = @implode(",",$temp_arr);
			
			//echo "Users with rating values $usrcnt <br>";
			//echo "Users requested for rating $usrtoinc <br>";

			$values['usrcnt'] = $usrcnt;
			$values['usrtoinc'] = $usrtoinc;

			$usrcnt_arr = @explode(",",$usrcnt);
			//print_r($usrcnt_arr);exit;
			$usrtoinc_arr = @explode(",",$usrtoinc);
			$arr1 = @array_intersect($usrcnt_arr,$usrtoinc_arr);
			//print_r($usrtoinc_arr);exit;
			
			$cnt_arr1 = count($arr1);
			 
			if(count($arr1) == 0)
			{
			 
				$values['none'] = "none";
			}
			if(count($usrtoinc_arr)+1 != $cnt_arr1)
			{
				$values['some'] = "some";
			}
			

		}


		
//Interpersonal

		$mysql = "select user_id,sum(label_no) as label_total from $label_for_model where skill_type = 'i' and user_id in ('$users_to_include') group by user_id order by  label_total desc ";    //limit 0,'$limitval'
		
		$users_sel_arr = $db_object->get_single_column($mysql);



		$users_all = @implode("','",$users_sel_arr);
		$cnt_users = count($users_sel_arr);
		$cnt_users -= 1;	
	

		for($i=0;$i<count($inter_labels);$i++)
		{
			$label_val = $inter_labels[$i];
	
			$mysql = "select skill_id ,count(skill_id) as cm, user_id   from $label_for_model 
				where user_id  in ('$users_all') 
				and label_no in ('$label_val')
				group by skill_id having cm > $cnt_users
				order by skill_id,label_no desc";
				
			
				$skills_common_arr = $db_object->get_single_column($mysql);

				$skills_comm .= @implode(",",$skills_common_arr);
				$skills_comm .= ",";
		
		}
		$skills_comm = substr($skills_comm,0,-1);

		$skills_interpersonal = @explode(",",$skills_comm);

		$skills_id = @implode("','",$skills_interpersonal);

		$mysql = "select skill_id , skill_name from $skills where skill_id in ('$skills_id') and skill_type = 'i'";

		$skills_arr = $db_object->get_rsltset($mysql);


		$mysql = "select max(rater_labelno) as max_label from $rater_label_relate where rater_type = 'i' ";
		$max_label_arr = $db_object->get_a_line($mysql);
		$max_label = $max_label_arr['max_label'];

		preg_match("/<{skills_loopstart}>(.*?)<{skills_loopend}>/s",$returncontent,$skillmatch);
		$newskillmatch = $skillmatch[1];
		
		for($i=0;$i<count($skills_arr);$i++)
		{
			$skill_id = $skills_arr[$i]['skill_id'];
			$skill_name = $skills_arr[$i]['skill_name'];
			
			$mysql = "select avg(label_no) as avg from label_for_model where skill_id = '$skill_id' and skill_type = 'i' group by skill_id";
			$avg_label_arr = $db_object->get_a_line($mysql);

			$avg_label = $avg_label_arr['avg'];

			$weight = ( $avg_label / $max_label ) * 100;
			$weight = round($weight,2);

			$recommended = round(($weight * $max_label) / 100);  //the max_label here alone refers to the total no of possible rating which is also the highest possible rating...
				
			
			$str .= preg_replace("/<{(.*?)}>/e","$$1",$newskillmatch);
			
		}
		$returncontent = preg_replace("/<{skills_loopstart}>(.*?)<{skills_loopend}>/s",$str,$returncontent);


//Technical



		
		$mysql = "select  user_id,sum(label_no) as label_total from $label_for_model
			where skill_type = 't' and user_id in ('$users_to_include')
			group by user_id order by  label_total desc";   //limit 0,$limitval

		$users_selt_arr = $db_object->get_single_column($mysql);
		$userst_all = @implode("','",$users_selt_arr);
		$cnt_userst = count($userst_sel_arr);
		$cnt_userst -= 1;	


		for($j=0;$j<count($tech_labels);$j++)
		{
			$techlabel_val = $tech_labels[$j];
	
			$mysql = "select skill_id ,count(skill_id) as cm, user_id   from $label_for_model 
				where user_id  in ('$userst_all') 
				and label_no in ('$techlabel_val')
				and skill_type = 't'
				group by skill_id having cm > $cnt_userst
				order by skill_id,label_no desc";

				$techskills_common_arr = $db_object->get_single_column($mysql);

				$techskills_comm .= @implode(",",$techskills_common_arr);
				$techskills_comm .= ",";
		
		
		}
		$techskills_comm = substr($techskills_comm,0,-1);
		$skills_technical = @explode(",",$techskills_comm);

		$techskills_id = @implode("','",$skills_technical);

		$mysql = "select skill_id , skill_name from $skills where skill_id in ('$techskills_id') and skill_type = 't'";
		$techskills_arr = $db_object->get_rsltset($mysql);

		$mysql = "select max(rater_labelno) as max_label from $rater_label_relate where rater_type = 't' ";
		$max_label_arr = $db_object->get_a_line($mysql);

		$max_label_t = $max_label_arr['max_label'];
		
		preg_match("/<{skillstech_loopstart}>(.*?)<{skillstech_loopend}>/s",$returncontent,$techskillmatch);
		$newtechskillmatch = $techskillmatch[1];
		
		for($l=0;$l<count($techskills_arr);$l++)
		{
			$skill_id_t = $techskills_arr[$l]['skill_id'];
			$skill_name_t = $techskills_arr[$l]['skill_name'];
			
			$mysql = "select avg(label_no) as avg from label_for_model where skill_id = '$skill_id_t' and skill_type = 't' group by skill_id";
			$avg_label_arr = $db_object->get_a_line($mysql);

			$avg_label_t = $avg_label_arr['avg'];

			$weight_t = ( $avg_label_t / $max_label_t ) * 100;
			$weight_t = round($weight_t,2);

			$recommended_t = round(($weight_t * $max_label_t) / 100);  //the max_label here alone refers to the total no of possible rating which is also the highest possible rating...
				
			$str1 .= preg_replace("/<{(.*?)}>/e","$$1",$newtechskillmatch);
			
		}
		$returncontent = preg_replace("/<{skillstech_loopstart}>(.*?)<{skillstech_loopend}>/s",$str1,$returncontent);
		
		
		$returncontent = $common->direct_replace($db_object,$returncontent,$values);
		echo $returncontent;

	}	//END OF FUNCTION()

	function store_rating_values($db_object,$common,$model_users_arr,$interskills_arr,$techskills_arr)
	{
		//the users selected for creating the model...


		$textqsort_rating = $common->prefix_table('textqsort_rating');
		$label_for_model = $common->prefix_table('label_for_model');
		$other_raters_tech = $common->prefix_table('other_raters_tech');

//DELETE DATA THAT PREVIOUSLY EXISTS...
		
		$mysql = "delete from $label_for_model";
		$db_object->insert($mysql);

		$interskills = @implode("','",$interskills_arr);
		$techskills = @implode("','",$techskills_arr);
		
//ALL THE USERS WHO ARE SELECTED AS COMPONENTS ARE STORED IN THE LABEL_FOR_MODEL TABLE...
		
		for($i=0;$i<count($model_users_arr);$i++)
		{
			$user = $model_users_arr[$i];

			$mysql = "select distinct(skill_id) from $textqsort_rating where rated_user = '$user' and skill_id in ('$interskills')";
			$skills_arr = $db_object->get_single_column($mysql);
		
		
			for($j=0;$j<count($skills_arr);$j++)
			{
				$skill_id = $skills_arr[$j];
				$mysql = "select round(avg(rater_label_no)) as label_no from $textqsort_rating where skill_id = '$skill_id' and rated_user = '$user'";
				$label_arr = $db_object->get_a_line($mysql);
			
				$label_no = $label_arr['label_no'];
			
				$mysql = "insert into $label_for_model set user_id = '$user' , skill_id = '$skill_id' , label_no = '$label_no' , skill_type = 'i'";
				$db_object->insert($mysql);
			
			}
			$mysql = "select distinct(skill_id) from $other_raters_tech where rated_user = '$user' and skill_id in ('$techskills')";

			$techskills_arr = $db_object->get_single_column($mysql);

			for($l=0;$l<count($techskills_arr);$l++)
			{
				$techskill_id = $techskills_arr[$l];
				$mysql = "select round(avg(label_id)) as tech_labelno from $other_raters_tech where skill_id = '$techskill_id' and rated_user = '$user'";
				$techlabel_arr = $db_object->get_a_line($mysql);

				$techlabel_no = $techlabel_arr['tech_labelno'];
				
				$mysql = "insert into $label_for_model set user_id = '$user' , skill_id = '$techskill_id' , label_no = '$techlabel_no' , skill_type = 't'";

				$db_object->insert($mysql);
			}
			
			
		}



}	// END OF FUNCTION ()

function save_data($db_object,$common,$post_var,$user_id,$default,$error_msg)
{
	while(list($kk,$vv) = @each($post_var))
	{
		$$kk = $vv;
	
		if(ereg("^fLabel_",$kk))
		{
			list($un,$qid,$aid) = split("_",$kk);
			$label_arr[$qid][] = $aid;
		}
		
	}
	//print_r($post_var);
	
	$model_components_1 = $common->prefix_table('model_components_1');
	$model_components_2 = $common->prefix_table('model_components_2');
	$model_components_3 = $common->prefix_table('model_components_3');	
	$model_components_4 = $common->prefix_table('model_components_4');	
	

	if($fDiff_per == 'top')
		{
			$diff = "Performance";
			$value_t_b = "Top";
			$percent_val = $fTop_val;
		}
		elseif($fDiff_per == 'bottom')
		{
			$diff = "Performance";
			$value_t_b = "Bottom";
			$percent_val = $fBottom_val;
		}

	if($fDiff_learn == 'top')
		{
			$diff = "Learning";
			$value_t_b = "Top";
			$percent_val = $fImp_val;
		}
		elseif($fDiff_learn == 'bottom')
		{
			$diff = "Learning";
			$value_t_b = "Bottom";
			$percent_val = $fWhatdone_val;
		}


	$mysql = "insert into $model_components_1 set model_id = '$model_id' , differentiator_type = '$diff' , top_bottom = '$value_t_b' , percent_each = '$percent_val'";
	$db_object->insert($mysql);
	
	//print_r($label_arr);
	
		for($i=0;$i<count($label_arr[i]);$i++)
			{
			$ival = $label_arr[i][$i];
			$mysql = "insert into $model_components_2 set model_id = '$model_id' , i_rated_as = '$ival'";		
			$db_object->insert($mysql);
			}
		
		for($i=0;$i<count($label_arr[t]);$i++)
			{
			$tval = $label_arr[t][$i];
			$mysql = "insert into $model_components_3 set model_id = '$model_id' , t_rated_as = '$tval'";		
			$db_object->insert($mysql);
			}
		

	if($fdir_rep == 'sel_direct')
	{
		$model_sel_users = "dirrep";
	}
	elseif($fdir_rep == 'all')
	{
		$model_sel_users = "all";

	}
		
		
	$mysql = "insert into $model_components_4 set model_id = '$model_id' , sel_users = '$model_sel_users'";
	$db_object->insert($mysql);
	
	 

//print_r($post_var);
//exit;
		
		
}

	
}	//END OF CLASS

$obj = new position_model;
$obj->save_data($db_object,$common,$post_var,$user_id,$default,$error_msg);
$obj->show_models($db_object,$common,$post_var,$user_id,$default,$error_msg,$gbl_files);


include_once("footer.php");
?>
