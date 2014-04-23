<?php
/*---------------------------------------------
SCRIPT:fam_without_model.php
AUTHOR:info@chrisranjana.com	
UPDATED:Jan 8th

DESCRIPTION:
This script displays the families without models.

---------------------------------------------*/
include("../session.php");
include_once("header.php");
class familyWithoutModel
{
function show_families($db_object,$common,$post_var,$user_id,$default)
	{
		while(list($kk,$vv) = @each($post_var))
		{
		$$kk = $vv;
		}

		$xPath=$common->path;
		$returncontent=$xPath."/templates/career/fam_without_model.html";
		$returncontent=$common->return_file_content($db_object,$returncontent);

		$user_table  	= $common->prefix_table('user_table');
		$position   	= $common->prefix_table('position');
		$family_position= $common->prefix_table('family_position');
		$model_factors_1= $common->prefix_table('model_factors_1');
		$family_table	= $common->prefix_table('family');
		
		$mysql = "select position from $user_table where admin_id = '$user_id'";
		$position_arr = $db_object->get_single_column($mysql);
		
//DETERMINE ALL THE FAMILIES TRUE FOR THE POSITIONS UNDER THIS ADMIN...
		
		for($i=0;$i<count($position_arr);$i++)
		{
			$posid = $position_arr[$i];
			$mysql = "select family_id from $family_position where position_id = '$posid'";
			$fam_arr = $db_object->get_single_column($mysql);
			
			for($j=0;$j<count($fam_arr);$j++)
			{
				$families_arr[] = $fam_arr[$j];
			}
			
		}

//SELECT ALL MODELS THIS PERSON IS CAPABLE OF VIEWING...
		
		$viewable_models_arr = $common->viewable_models($db_object,$user_id);
		$models_viewable_all = @implode("','",$viewable_models_arr);
	

		//echo "Families under him ";
		//print_r($families_arr);
		//echo "<br> Models He can view";	
		//print_r($viewable_models_arr);


		if($models_viewable_all != '')
		{
			for($k=0;$k<count($families_arr);$k++)
			{
				$family = $families_arr[$k];
				$mysql = "select model_id from $model_factors_1 where family = '$family' and model_id in ('$models_viewable_all')";
				$fam_withmodel_arr = $db_object->get_a_line($mysql);
				$fam_withmodel = $fam_withmodel_arr['model_id'];
				if($fam_withmodel == '')
				{
					$fam_without_model[] = $family;
				}
				
			
			
			}
		}
		else
		{
			$fam_without_model = $families_arr;
		}
		//print_r($fam_without_model);
		
		preg_match("/<{displayfamnames_loopstart}>(.*?)<{displayfamnames_loopend}>/s",$returncontent,$matchfam_old);
		$matchfam_new = $matchfam_old[1];
		
		for($l=0;$l<count($fam_without_model);$l++)	
		{
			$fam_id = $fam_without_model[$l];
			$mysql = "select family_name from $family_table where family_id = '$fam_id'";
			$fam_name_arr = $db_object->get_a_line($mysql);
			$fam_name = $fam_name_arr['family_name'];
			$strfam .= preg_replace("/<{(.*?)}>/e","$$1",$matchfam_new);	
		}
		
		$returncontent = preg_replace("/<{displayfamnames_loopstart}>(.*?)<{displayfamnames_loopend}>/s",$strfam,$returncontent);

		if(!count($fam_without_model) > 0 )
		{
		$returncontent = preg_replace("/<{shownull_(.*?)}>/s","",$returncontent);
		$returncontent = preg_replace("/<{showfamilies_start}>(.*?)<{showfamilies_end}>/s","",$returncontent);
		}
		else
		{
		$returncontent = preg_replace("/<{showfamilies_(.*?)}>/s","",$returncontent);
		$returncontent = preg_replace("/<{shownull_start}>(.*?)<{shownull_end}>/s","",$returncontent);
		}

		$returncontent = $common->direct_replace($db_object,$returncontent,$values);
		echo $returncontent;
	}
}
$obj = new familyWithoutModel;
$obj->show_families($db_object,$common,$post_var,$user_id,$default);
include_once("footer.php");
?>

