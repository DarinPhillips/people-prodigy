<?php
/*---------------------------------------------
SCRIPT:skill_usage_employee.php
AUTHOR:info@chrisranjana.com	
UPDATED:6th Jan

DESCRIPTION:
This script displays the usage of skills for employees

---------------------------------------------*/
include('../session.php');
include('header.php');

class skillUsageModels
{
function show_employees($db_object,$common,$post_var,$user_id,$default)
{
	while(list($kk,$vv) = @each($post_var))
	{
		$$kk = $vv;
	}

	$xPath=$common->path;
	$returncontent=$xPath."/templates/career/skill_usage_models.html";
	$returncontent=$common->return_file_content($db_object,$returncontent);
	
	$model_skills 	= $common->prefix_table('model_skills');
	$skills 	= $common->prefix_table('skills');
	
	$models_viewable = $common->viewable_models($db_object,$user_id);

//print_r($models_viewable);

	$total_models = @count($models_viewable);

	$models_all = @implode("','",$models_viewable);

	if($models_all != '')
	{
	$subqry = "and model_id in ('$models_all')";
	}
	for($i=0;$i<count($fSkills);$i++)
	{
		$skillid = $fSkills[$i];
		if($skillid != 0)
		{
		$mysql = "select count(distinct(model_id)) as model_req from $model_skills where skill_id = '$skillid' $subqry";
		$modelids_arr = $db_object->get_a_line($mysql);
		
		$modelids = $modelids_arr['model_req'];
		
		$unsorted_arr[$skillid] = $modelids;
		}
		
		
	}
	
	@arsort($unsorted_arr);
//print_r($unsorted_arr);
	
	preg_match("/<{modeldisplay_loopstart}>(.*?)<{modeldisplay_loopend}>/s",$returncontent,$matchold);
	$matchnew = $matchold[1];
	$strmodel = '';	
	while(list($key,$val) = @each($unsorted_arr))
	{
		$mysql = "select skill_name from $skills where skill_id = '$key'";
		$skillname_arr = $db_object->get_a_line($mysql);
		$skill_name = $skillname_arr['skill_name']; 
		
		$no_of_models = $val;
		$percent_models = ($no_of_models / $total_models ) * 100;
		
		$strmodel .= preg_replace("/<{(.*?)}>/e","$$1",$matchnew); 
		
	}	

	$returncontent = preg_replace("/<{modeldisplay_loopstart}>(.*?)<{modeldisplay_loopend}>/s",$strmodel,$returncontent);

	$returncontent = $common->direct_replace($db_object,$returncontent,$values);
	echo $returncontent;
}
		
}
$obj = new skillUsageModels;

$obj->show_employees($db_object,$common,$post_var,$user_id,$default);


include('footer.php');

?>
