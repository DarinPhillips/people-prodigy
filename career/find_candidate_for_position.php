<?php
/*---------------------------------------------
SCRIPT:skill_usage_employee.php
AUTHOR:info@chrisranjana.com	
UPDATED:6th Jan

DESCRIPTION:
This script displays the no of clients with this skill.

---------------------------------------------*/
include('../session.php');
include('header.php');

class viewModels
{
function show_models($db_object,$common,$user_id,$default)
	{
	$xPath=$common->path;
	$returncontent=$xPath."/templates/career/show_models.html";
	$returncontent=$common->return_file_content($db_object,$returncontent);
	
	$model_table = $common->prefix_table('model_table');
	$model_name_table = $common->prefix_table('model_name_table');
	
	
	$all_viewable_models_arr = $common->viewable_models($db_object,$user_id);	
	
	$all_viewable_models = @implode("','",$all_viewable_models_arr);
	
	preg_match("/<{selfmodels_loopstart}>(.*?)<{selfmodels_loopend}>/s",$returncontent,$matchold);
	$match_new = $matchold[1];
	
//MODELS BUILT BY THIS ADMIN...	
		
	$mysql = "select model_id from $model_table where user_id = '$user_id' and model_id in ('$all_viewable_models')";
	$selfmodels_arr = $db_object->get_single_column($mysql);
	
	for($i=0;$i<count($selfmodels_arr);$i++)
	{
		$selfmodel = $selfmodels_arr[$i];	
		$models_displayedalready[] = $selfmodel;
		$mysql = "select model_name from $model_name_table where model_id = '$selfmodel'";
		$modelname_arr = $db_object->get_a_line($mysql);
		$models_self = $modelname_arr['model_name'];
		
		$str1 .= preg_replace("/<{(.*?)}>/e","$$1",$match_new);
		
	}
	$returncontent = preg_replace("/<{selfmodels_loopstart}>(.*?)<{selfmodels_loopend}>/s",$str1,$returncontent);


//OTHER MODELS THIS ADMIN or BOSS IS CAPABLE OF VIEWING...

//$models_displayedalready	CONTAINS THE MODELS ALREADY DISPLAYED IN SELF SECTION...
	 
$remaining_models_to_show = @array_diff($all_viewable_models_arr,$models_displayedalready);
	
	preg_match("/<{othermodels_loopstart}>(.*?)<{othermodels_loopend}>/s",$returncontent,$matchold1);
	$match1_new = $matchold1[1];
		
	for($j=0;$j<count($remaining_models_to_show);$j++)
	{
		$modelid_other = $remaining_models_to_show[$j];
		$mysql = "select model_name from $model_name_table where model_id = '$modelid_other'";
		$modelname_arr = $db_object->get_a_line($mysql);
		$models_others = $modelname_arr['model_name'];
		
		
		$str2 .= preg_replace("/<{(.*?)}>/e","$$1",$match1_new);
	}
	
	$returncontent = preg_replace("/<{othermodels_loopstart}>(.*?)<{othermodels_loopend}>/s",$str2,$returncontent);

	$returncontent = $common->direct_replace($db_object,$returncontent,$values);
	echo $returncontent;
	}
}
$obj = new viewModels;
$obj->show_models($db_object,$common,$user_id,$default);

include('footer.php');
?>
