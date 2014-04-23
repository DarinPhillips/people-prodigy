<?php
/*---------------------------------------------
SCRIPT:model_match.php
AUTHOR:info@chrisranjana.com	
UPDATED:Jan 8th

DESCRIPTION:
This script displays the model match with employees

---------------------------------------------*/
include("../session.php");
include_once("header.php");

class ModelMatch
{
function show_models($db_object,$common,$post_var,$user_id,$default,$modelfit,$error_msg)
	{
		while(list($kk,$vv) = @each($post_var))
		{	
		$$kk = $vv;
		}
		
		$xPath=$common->path;
		$returncontent=$xPath."/templates/career/model_match.html";
		$returncontent=$common->return_file_content($db_object,$returncontent);
		
		$user_table = $common->prefix_table('user_table');
		$position_table = $common->prefix_table('position');
		$models_percent_fit=$common->prefix_table("models_percent_fit");
		$model_name_table=$common->prefix_table("model_name_table");
		
		
		
		$values['modelfit'] = $modelfit;
		
		$all_viewable_models_arr = $common->viewable_models($db_object,$user_id);
		
		//print_r($all_viewable_models_arr);
		
//DISPLAY ALL EMPLOYEES UNDER THIS ADMIN...
		if($user_id!=1)
		{
			$mysql = "select user_id from $user_table where admin_id='$user_id'";
		}
		else
		{
			$mysql = "select $user_table.user_id 
				from $user_table,$position_table 
				where $user_table.position=$position_table.pos_id 
				and ($user_table.position<>NULL or $user_table.position<>0) 
				and $user_table.user_id!=1 
				order by $position_table.level_no desc";			

		}
	
		$user_underadmin_arr = $db_object->get_single_column($mysql);
		
		if(count($user_underadmin_arr)>0)
		{
			$users=@implode(",",$user_underadmin_arr);
			
			$users_id="(".$users.")";
			
		$sql="select model_id from $models_percent_fit where user_id in $users_id and percent_fit<'$modelfit' group by model_id";
		
		$result=$db_object->get_single_column($sql);
		
		}
		
		
		preg_match("/<{model_loopstart}>(.*?)<{model_loopend}>/s",$returncontent,$match);
		
	$match = $match[0];
if(count($result)==0)
{
	echo $error_msg['cEmptyrecords'];
	
	include_once("footer.php");exit;
}
	for($i=0;$i<count($result);$i++)
	{
		$model_id=$result[$i];

		$mysql = "select model_name from $model_name_table where model_id='$model_id'";
		
		$model_res=$db_object->get_a_line($mysql);
		
		$model_name=$model_res[model_name];
		
		$replace.= preg_replace("/<{(.*?)}>/e","$$1",$match);
		
		
	}	
	$returncontent = preg_replace("/<{model_loopstart}>(.*?)<{model_loopend}>/s",$replace,$returncontent);

		$returncontent = $common->direct_replace($db_object,$returncontent,$values);
		
		echo $returncontent;
		
	}
}
$obj = new ModelMatch;
$obj->show_models($db_object,$common,$post_var,$user_id,$default,$modelfit,$error_msg);
include_once("footer.php");
?>
