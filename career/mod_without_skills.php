<?php
/*---------------------------------------------
SCRIPT:mod_without_skills.php
AUTHOR:info@chrisranjana.com	
UPDATED:Jan 8th

DESCRIPTION:
This script displays the models without any skills (either I or T).

---------------------------------------------*/
include("../session.php");
include_once("header.php");

class ModelsWithoutSkills
{
function show_models($db_object,$common,$post_var,$user_id,$default)
	{
		$xPath=$common->path;
		$returncontent=$xPath."/templates/career/mod_without_skills.html";
		$returncontent=$common->return_file_content($db_object,$returncontent);
		
		$model_skills  	= $common->prefix_table('model_skills');
		$skills		= $common->prefix_table('skills');
		$model_name_table=$common->prefix_table('model_name_table');
		
			
		$viewable_models_arr = $common->viewable_models($db_object,$user_id);
				
		for($i=0;$i<count($viewable_models_arr);$i++)
		{
			$modelid = $viewable_models_arr[$i];
			
			$mysql = "select skill_id from $model_skills where model_id = '$modelid'";
			$skills_present_arr = $db_object->get_single_column($mysql);
			
			$all_skills_in_model = @implode("','",$skills_present_arr);
			
//DETERMINE THE MODELS WITHOUT INTERPERSONAL SKILLS...

			$mysql = "select $skills.skill_id 
					from $model_skills, $skills 
					where $skills.skill_id = $model_skills.skill_id
					and $model_skills.model_id = '$modelid'
					and $skills.skill_type = 'i'
					and $model_skills.skill_id in('$all_skills_in_model')";
			$iskills_arr = $db_object->get_single_column($mysql);

			if($iskills_arr == '')
			{
				
				$model_without_iskill[] = $modelid;
			}
//DETERMINE THE MODELS WITHOUT TECHNICAL SKILLS...
			
			$mysql = "select $skills.skill_id 
					from $model_skills, $skills 
					where $skills.skill_id = $model_skills.skill_id
					and $model_skills.model_id = '$modelid'
					and $skills.skill_type = 't'
					and $model_skills.skill_id in('$all_skills_in_model')";
			$tskills_arr = $db_object->get_single_column($mysql);

			if($tskills_arr == '')
			{
				$model_without_tskill[] = $modelid;
			}	
			
		}
		if(count($model_without_iskill)==0)
		{
			$values[replace]="<tr><td class=code>No model Without interpersonal skill</td></tr>";
		}
		preg_match("/<{iskills_nil_loopstart}>(.*?)<{iskills_nil_loopend}>/s",$returncontent,$matchiskill_old);
		$matchiskill_new = $matchiskill_old[1];

		for($a=0;$a<count($model_without_iskill);$a++)
		{
			$modeltoshow = $model_without_iskill[$a];

			$mysql = "select model_name from $model_name_table where model_id = '$modeltoshow'";
			$modelname_arr = $db_object->get_a_line($mysql);
			$modelname =  $modelname_arr['model_name'];
			
			$stri .= preg_replace("/<{(.*?)}>/e","$$1",$matchiskill_new);
		}		
		$returncontent = preg_replace("/<{iskills_nil_loopstart}>(.*?)<{iskills_nil_loopend}>/s",$stri,$returncontent);
		
		if(count($model_without_tskill)==0)
		{
			$values[replace1]="<tr><td class=code>No model without technical skill</td></tr>";
		}
		
		preg_match("/<{tskills_nil_loopstart}>(.*?)<{tskills_nil_loopend}>/s",$returncontent,$matchtskill_old);
		$matchtskill_new = $matchtskill_old[1];

		for($b=0;$b<count($model_without_tskill);$b++)
		{
			$modeltoshow = $model_without_tskill[$b];

			$mysql = "select model_name from $model_name_table where model_id = '$modeltoshow'";
			$modelname_arr = $db_object->get_a_line($mysql);
			$modelname =  $modelname_arr['model_name'];
			
			$strt .= preg_replace("/<{(.*?)}>/e","$$1",$matchtskill_new);
		}		
		$returncontent = preg_replace("/<{tskills_nil_loopstart}>(.*?)<{tskills_nil_loopend}>/s",$strt,$returncontent);




		
		$returncontent = $common->direct_replace($db_object,$returncontent,$values);
		echo $returncontent;	
	}


function delete_model($db_object,$common,$post_var,$user_id)
{
	while(list($kk,$vv) = @each($post_var))
	{
		$$kk = $vv;
	}
	
	$model_components_1 	= $common->prefix_table('model_components_1');
	$model_components_2	= $common->prefix_table('model_components_2');
	$model_components_3	= $common->prefix_table('model_components_3');
	$model_components_4	= $common->prefix_table('model_components_4');
	$model_factors_1	= $common->prefix_table('model_factors_1');
	$model_factors_2	= $common->prefix_table('model_factors_2');
	$model_factors_3	= $common->prefix_table('model_factors_3');
	$model_factors_4	= $common->prefix_table('model_factors_4');
	$model_factors_5	= $common->prefix_table('model_factors_5');
	$model_factors_6	= $common->prefix_table('model_factors_6');
	$model_factors_7	= $common->prefix_table('model_factors_7');
	$model_factors_8	= $common->prefix_table('model_factors_8');
	$model_factors_9	= $common->prefix_table('model_factors_9');
	$model_factors_10	= $common->prefix_table('model_factors_10');
	$model_name_table	= $common->prefix_table('model_name_table');
	$model_skills		= $common->prefix_table('model_skills');
	$model_table		= $common->prefix_table('model_table');
	$model_view_1		= $common->prefix_table('model_view_1');
	$model_view_2		= $common->prefix_table('model_view_2');
	$model_percent_fit	= $common->prefix_table('models_percent_fit');
	
		
	$mysql = "delete from $model_components_1 where model_id = '$mid'";
	$db_object->insert($mysql);
	
	$mysql = "delete from $model_components_2 where model_id = '$mid'";
	$db_object->insert($mysql);
	
	$mysql = "delete from $model_components_3 where model_id = '$mid'";
	$db_object->insert($mysql);
	
	$mysql = "delete from $model_components_4 where model_id = '$mid'";
	$db_object->insert($mysql);
	
	$mysql = "delete from $model_factors_1 where model_id = '$mid'";
	$db_object->insert($mysql);
	
	$mysql = "delete from $model_factors_2 where model_id = '$mid'";
	$db_object->insert($mysql);

	$mysql = "delete from $model_factors_3 where model_id = '$mid'";
	$db_object->insert($mysql);
	
	$mysql = "delete from $model_factors_4 where model_id = '$mid'";
	$db_object->insert($mysql);

	$mysql = "delete from $model_factors_5 where model_id = '$mid'";
	$db_object->insert($mysql);

	$mysql = "delete from $model_factors_6 where model_id = '$mid'";
	$db_object->insert($mysql);
	
	$mysql = "delete from $model_factors_7 where model_id = '$mid'";
	$db_object->insert($mysql);

	$mysql = "delete from $model_factors_8 where model_id = '$mid'";
	$db_object->insert($mysql);
	
	$mysql = "delete from $model_factors_9 where model_id = '$mid'";
	$db_object->insert($mysql);
	
	$mysql = "delete from $model_factors_10 where model_id = '$mid'";
	$db_object->insert($mysql);
	
	$mysql = "delete from $model_name_table where model_id = '$mid'";
	$db_object->insert($mysql);
	
	$mysql = "delete from $model_skills where model_id = '$mid'";
	$db_object->insert($mysql);
	
	$mysql = "delete from $model_table where model_id = '$mid'";
	$db_object->insert($mysql);
	
	$mysql = "delete from $model_view_1 where model_id = '$mid'";
	$db_object->insert($mysql);
	
	$mysql = "delete from $model_view_2 where model_id = '$mid'";
	$db_object->insert($mysql);
	
	$mysql = "delete from $model_percent_fit where model_id = '$mid'";
	$db_object->insert($mysql);

	
}


}
$obj = new ModelsWithoutSkills;

if($mid != '')
{
$obj->delete_model($db_object,$common,$post_var,$user_id);
$message = $error_msg['cSuccessfulldel_of_models'];
echo $message;	
}

$obj->show_models($db_object,$common,$post_var,$user_id,$default);


include_once("footer.php");
?>

