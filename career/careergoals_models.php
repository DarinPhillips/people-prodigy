<?php
/*---------------------------------------------
SCRIPT:careergoals_models.php
AUTHOR:info@chrisranjana.com	
UPDATED:19th Dec
DESCRIPTION:
This script displays the models and their comparisions in graph.
---------------------------------------------*/


include("../session.php");
include("header.php");
class modelsdisplay
{
function show_models($db_object,$common,$post_var,$user_id)
	{
		while(list($kk,$vv) = @each($post_var))
			{
			$$kk = $vv;
			}	


	$xPath		= $common->path;
	$returncontent	= $xPath."/templates/career/careergoals_models.html";
	$returncontent	= $common->return_file_content($db_object,$returncontent);
	
	$user_table = $common->prefix_table('user_table');
	$model_factors_1 = $common->prefix_table('model_factors_1');
	$model_name_table = $common->prefix_table('model_name_table');


	$mysql = "select username from $user_table where user_id = '$user_id'";
	$username_arr = $db_object->get_a_line($mysql);
	$username = $username_arr['username'];
	$values['user_id'] = $user_id;
	$values['username'] = $username;




//ALL THE MODELS THAT THIS PERSON CAN VIEW IS FOUND OUT..
	$allviewablemodels_arr = $common->viewable_models($db_object,$user_id);
	
	$allviewablemodels = @implode("','",$allviewablemodels_arr);

//FIND WHAT ALL MODELS ARE RELATED TO THE REQUIRED FAMILY ID "FID"
	if($allviewablemodels != '')
	{	
	$mysql = "select model_id from $model_factors_1 where family = $fid and  model_id in ('$allviewablemodels')";
	$models_arr = $db_object->get_single_column($mysql);
	}

	$models_all = @implode("','",$models_arr);
	if($models_all != '')
	{
	$mysql = "select model_id , model_name from $model_name_table where model_id in ('$models_all')";
	$modeldisplay_arr = $db_object->get_rsltset($mysql);
	}	
	$multipleloop_values['modeldisplay_loop'] = $modeldisplay_arr;

	$returncontent	= $common->multipleloop_replace($db_object,$returncontent,$multipleloop_values,'');
	$returncontent = $common->direct_replace($db_object,$returncontent,$values);
	echo $returncontent;	
	}
}
$obj = new modelsdisplay;
//IF THE ADMIN VIEW ANY EMPLOYEES MODELS.

if($uid)
{
	$user_id=$uid;
}

$obj->show_models($db_object,$common,$post_var,$user_id);



include("footer.php");
?>
