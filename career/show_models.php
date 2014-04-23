<?php

/*---------------------------------------------
SCRIPT:.php
AUTHOR:info@chrisranjana.com	
UPDATED:26th Dec

DESCRIPTION:
This script displays all the models for a particular position selected.

---------------------------------------------*/

include("../session.php");
include("header.php");
class displaymodels
{
function show_models($db_object,$common,$post_var,$default,$user_id)
	{

		while(list($kk,$vv) = @each($post_var))
		{
			$$kk = $vv;
		}
		//print_r($post_var);		
	
		$xPath		= $common->path;
		$returncontent	= $xPath."/templates/career/show_models.html";
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
}
$obj = new displaymodels;

$obj->show_models($db_object,$common,$post_var,$default,$user_id);
include_once('footer.php');
?>
