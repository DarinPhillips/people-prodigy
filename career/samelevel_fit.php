<?php

include_once("../session.php");

class fit
{
	function samelevel_fit($db_object,$common,$image,$user_id,$model_id)
	{
		
		$model_factor_1=$common->prefix_table("model_factors_1");
		
		$model_name_table=$common->prefix_table("model_name_table");
		
		$career_goals=$common->prefix_table("career_goals");
		
		$model_percent_fit=$common->prefix_table("models_percent_fit");
		
		$sql="select same_level from $career_goals where user_id='$user_id' and interest='lot'";
			
		$res=$db_object->get_single_column($sql);

		if(count($res)>0)
		{
			$fam=@implode(",",$res);
			
			$family_ids="(".$fam.")";
			
			$sql="select model_id from $model_factor_1 where family in $family_ids";

			$sql_res=$db_object->get_single_column($sql);

			if($sql_res[0]!="")
			{
				$model_ids=@implode(",",$sql_res);
				
				$models="(".$model_ids.")";
				
				$qry="select avg(percent_fit) as percent from $model_percent_fit where model_id in $models
				
				and user_id ='$user_id'";
	
				$percent=$db_object->get_a_line($qry);
				
				$percent=$percent[0];
			}
		}
		else
		{
			$percent=0;
		}
			
		$total=100;
		
		$heads = array(
    array("Same Level", 3, "c"),  
    );
		$array=array($percent,$total);
			
		$vals=$image->return_Array($array);
		
		$image->init(150,150, $vals);
		
		$image->draw_heading($heads);
	
		$image->set_legend_percent();

		 $image->display($filename);

	}
}

$obj=new fit();

$obj->samelevel_fit($db_object,$common,$image,$user_id,$model_id);

?>
