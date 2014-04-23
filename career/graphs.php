<?php

include_once("../session.php");

class graphs
{
	function admin($db_object,$common,$user_id)
	{
		$path=$common->path;
		
		$xtemplate=$path."templates/career/admin_graphs.html";
		
		$file=$common->return_file_content($db_object,$xtemplate);
		
		echo $file;
	}
	function boss($db_object,$common,$user_id)
	{
		$path=$common->path;
		
		$xtemplate=$path."templates/career/boss_graphs.html";
		
		$file=$common->return_file_content($db_object,$xtemplate);
		
		echo $file;
	}
	function employee($db_object,$common,$user_id)
	{
		
		$path=$common->path;
		
		$xtemplate=$path."templates/career/employee_graphs.html";
		
		$file=$common->return_file_content($db_object,$xtemplate);
		
		$career_goals=$common->prefix_table("career_goals");
		
		$models_percent_fit=$common->prefix_table("models_percent_fit");
		
		$family_position=$common->prefix_table("family_position");
		
		$model_factors_1=$common->prefix_table("model_factors_1");
		
		$model_name_table=$common->prefix_table("model_name_table");
		
		$sql="select same_level from $career_goals where user_id='$user_id' and interest='lot'";
			
		$res=$db_object->get_single_column($sql);
		
		if(count($res)>0)
		{
			$fam=@implode(",",$res);
			
			$family_ids="(".$fam.")";
			
			$sql="select $model_factors_1.model_id,model_name,family from $model_name_table,$model_factors_1 where 
			
			$model_name_table.model_id=$model_factors_1.model_id and family in $family_ids";
			
			$sql_res=$db_object->get_rsltset($sql);
			
	preg_match("/<{model_loopstart}>(.*?)<{model_loopend}>/s",$file,$match);
			
	$match = $match[0];
	
	for($j=0;$j<count($sql_res);$j++)
		{
			$model_id=$sql_res[$j][model_id];

			$model_name=$sql_res[$j][model_name];
			
			$str .= preg_replace("/<{(.*?)}>/e","$$1",$match);
			
		}		

	$file = preg_replace("/<{model_loopstart}>(.*?)<{model_loopend}>/s",$str,$file);	
		}
		$file=$common->direct_replace($db_object,$file,$xArray);
		
		echo $file;
	}
}

$obj=new graphs();

switch($fAs)
{
	case "3":
	
	$obj->admin($db_object,$common,$user_id);
	
	break;
	
	case "2":
	
	$obj->boss($db_object,$common,$user_id);
	
	break;
	
	case "1":
	
	$obj->employee($db_object,$common,$user_id);
	
	break;
}
?>
