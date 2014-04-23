<?php
/*---------------------------------------------
SCRIPT:mobility_reports2.php
AUTHOR:info@chrisranjana.com	
UPDATED:9th Dec
DESCRIPTION:
This script displays the models and their comparisions in graph.
---------------------------------------------*/


include("../session.php");
include("header.php");


class mobility2
{
function show_mobility_reports($db_object,$common,$user_id,$default,$post_var)
{
	
	while(list($kk,$vv) = @each($post_var))
	{
		$$kk = $vv;
	}	

	$xPath		= $common->path;
	$returncontent	= $xPath."/templates/career/mobility_reports2.html";
	$returncontent	= $common->return_file_content($db_object,$returncontent);
		
	$models_percent_fit 	= $common->prefix_table('models_percent_fit');
	$model_main 		= $common->prefix_table('model_name_table');
	$models_view_1 		= $common->prefix_table('model_view_1');
	$models_view_2 		= $common->prefix_table('model_view_2');
	$user_table 		= $common->prefix_table('user_table');
	$position 		= $common->prefix_table('position');	
	$org_main 		= $common->prefix_table('org_main');
	$position      		= $common->prefix_table('position');
	$model_table		= $common->prefix_table('model_table');		
	$family_position	= $common->prefix_table('family_position'); 
	$model_factors_1	= $common->prefix_table('model_factors_1');       

	if($empl != '')
		{
		$id_of_user = $empl;
		}
	else
		{
		$id_of_user = $user_id;
		}

	
	 $mysql = "select username from $user_table where user_id = '$id_of_user'";
	 $username_mod_arr = $db_object->get_a_line($mysql);
	 $username_mod = $username_mod_arr['username'];	
	 $values['username_mod'] = $username_mod;

//CHECK WHICH WAY THE ORGANISATION HAS BUILT THE ORGANISATIONAL CHART 
//IF HIGHERORDER=YES THEN 8-7-..1
//ELSE 1-2-..8

	$mysql = "select higher_order from $org_main";
	$higherorder_arr = $db_object->get_a_line($mysql);	
	$higherorder = $higherorder_arr['higher_order'];

//DETERMINE WHAT ALL MODELS THIS PERSON IS CAPABLE OF VIEWING...
//DETERMINE THE LEVEL OF THE CURRENT PERSON...


	$mysql = "select level_no from $user_table,$position 
			where $user_table.position = $position.pos_id
			and $user_table.user_id = '$id_of_user'";
	$lev_arr = $db_object->get_a_line($mysql);
	$cur_level = $lev_arr['level_no'];

//DETERMINE THE MODELS WHICH THE LEVEL OF THE PERSON IS CAPABLE OF VIEWING...
	$mysql = "select model_id from $models_view_1 where levels_to_view = '$cur_level'";
	$model_view1_arr = $db_object->get_single_column($mysql);
	$models_viewable = @implode("','",$model_view1_arr);
	
	$check_boss 	= $common->is_boss($db_object,$id_of_user);
	$check_admin 	= $common->is_admin($db_object,$id_of_user);
	
//MODELS BOSSES CAN VIEW...	
	if($check_boss == 1)
		{
			$mysql = "select model_id from $models_view_2 where boss= 'yes'";
			$model_viewboss_arr = $db_object->get_single_column($mysql);
		
		}

//MODELS ADMINS CAN VIEW...	
	if($check_admin == 1)
		{
			$mysql = "select model_id from $models_view_2 where admins = 'yes'";
			$model_viewadmin_arr = $db_object->get_single_column($mysql);

		}

//MODELS ALL CAN VIEW...	
	$mysql = "select model_id from $models_view_2 where all1 = 'yes'";
	$model_viewall_arr = $db_object->get_single_column($mysql);

//MODELS SELF CAN ONLY VIEW...	
	$mysql = "select model_id from $models_view_2 where me = 'yes'";
	$checkwithself = $db_object->get_single_column($mysql);
	$checkself = @implode("','",$checkwithself);
	
	$mysql = "select model_id from $model_table where model_id in ('$checkself') and user_id = '$id_of_user'";
	$model_viewself_arr = $db_object->get_single_column($mysql);

	
//COMBINE ALL THE MODELS WHICH THE PERSON IS CAPABLE OF VIEWING...
	$all_viewable_models_arr 	= $model_view1_arr; 
	$all_models_one_can_view_arr_old= @array_merge($model_view1_arr,$model_viewboss_arr,$model_viewadmin_arr,$model_viewall_arr,$model_viewself_arr);
	$all_models_one_can_view_arr	= @array_unique($all_models_one_can_view_arr_old);
	$all_models_one_can_view	= @implode("','",$all_models_one_can_view_arr);

//DETERMINE WHAT ALL MODELS ARE BUILT ON FAMILY BASIS 
//ie(ONLY THE MODELS THAT WERE CREATED WITH FAMILIES AS ONE OF THE OPTION WILL BE CONSIDERED)...
//DETERMINE THE MODELS THAT FALL UNDER THE PERSONS' LEVEL AND FAMILY
//>>>>>>>>>>>MYLEVEL

//DETERMINE THE FAMILY OF THE PERSON...
	$mysql = "select position from $user_table where user_id = '$id_of_user'";
	$pos_arr = $db_object->get_a_line($mysql);	
	$position_of_user = $pos_arr['position'];
	
	$mysql = "select family_id from $family_position where position_id = '$position_of_user'";
	$family_of_user_arr = $db_object->get_a_line($mysql);
	$family_of_user = $family_of_user_arr['family_id'];
	
//CHECK IF THERE ARE ANY MODELS BUILT ON THESE FAMILIES, IF YES THEN SHOW THOSE MODELS	
	$mysql = "select model_id from $model_factors_1 where family = '$family_of_user' and model_id in ('$all_models_one_can_view') limit 0,$fModels_toshow";

	$models_mylevel_junk_arr = $db_object->get_single_column($mysql);

//ALL THE MODELS THAT WERE ASSIGNED FOR VIEWING TO ADMINS, BOSSES, ALL & SELF WILL ALSO BE SHOWN IN THE MYLEVEL MODELS...
	
	$models_mylevelall_arr 	= @array_merge($models_mylevel_junk_arr,$model_viewboss_arr,$model_viewadmin_arr,$model_viewall_arr,$model_viewself_arr);
	$models_mylevel_arr 	= @array_unique($models_mylevelall_arr);		
	$models_mylevel 	= @implode("','",$models_mylevel_arr);
	
//DETERMINE THE X AND Y VALUES FOR THE MODELS OF THE "MYLEVEL"
if($fMylevel == 'on')
{	
	
	if($fiskills == 'on')
	{
		$mysql = "select percent_fit,model_id,skill_type from $models_percent_fit where user_id = '$id_of_user' and skill_type='i' and percent_fit >= $ipercent and model_id in ('$models_mylevel') limit 0,$fModels_toshow";
		$ival_mylevel_arr = $db_object->get_rsltset($mysql);
	}
	
	if($ftskills == 'on')
	{

		$mysql = "select percent_fit,model_id,skill_type from $models_percent_fit where user_id = '$id_of_user' and skill_type='t' and percent_fit >= $tpercent and model_id in ('$models_mylevel') limit 0,$fModels_toshow";
		$tval_mylevel_arr = $db_object->get_rsltset($mysql);

	}
//-------------------------
	for($i=0;$i<count($ival_mylevel_arr);$i++)
	{
		$ipercentfit_mylevel = $ival_mylevel_arr[$i]['percent_fit'];
		$imodelid_mylevel = $ival_mylevel_arr[$i]['model_id'];
				

		for($j=0;$j<count($tval_mylevel_arr);$j++)
		{
			$tpercentfit_mylevel = $tval_mylevel_arr[$j]['percent_fit'];
			$tmodelid_mylevel = $tval_mylevel_arr[$j]['model_id'];
			
			if($imodelid_mylevel == $tmodelid_mylevel)
			{
			
			//$dataarray_mylevel[$ipercentfit_mylevel][$tpercentfit_mylevel] = $tmodelid_mylevel;
			 
				if($ipercentfit_mylevel != 0)
				{
				$xcoordinate_mylevel = round(($ipercentfit_mylevel * 3) + 65);
			
				}
				else
				{
				$xcoordinate_mylevel = 65;
				}

				if($tpercentfit_mylevel != 0)
				{
				$ycoordinate_mylevel = 325 - round(($tpercentfit_mylevel * 3));
			
				}
				else
				{
				$ycoordinate_mylevel = 325;	
				}
				

 				$x1_mylevel = $xcoordinate_mylevel;
				$y1_mylevel = $ycoordinate_mylevel;
				$xco_mylevel_arr[] = $x1_mylevel;
 				$yco_mylevel_arr[] = $y1_mylevel;
				

			
			}
			
		}
	}
}
$xco_mylevel = @implode(",",$xco_mylevel_arr);
$yco_mylevel = @implode(",",$yco_mylevel_arr);
$values['xco_mylevel']=$xco_mylevel;
$values['yco_mylevel']=$yco_mylevel;

//-------------------------	
//888888888888888888888888888888888888888888888888888888888888888888

//MODELS IN HIGHER LEVEL +1 LEVEL
	
//DETERMINE THE HIGER LEVEL TO THE CURRENT LEVEL...

	
	if($higherorder == yes)
	{
		$one_level_up = $cur_level + 1;
	}
	else
	{
		$one_level_up = $cur_level - 1;
	}
		
//DETERMINE THE MODELS IN +1 higher LEVEL...
	
//DETERMINE ALL THE POSITIONS IN THIS HIGER LEVELS
	$mysql = "select pos_id from $position where level_no = '$one_level_up'";
	$positions_in_higherlevel_arr = $db_object->get_single_column($mysql);
	
	$positions_in_higherlevel = @implode("','",$positions_in_higherlevel_arr);
		
//DETERMINE ALL THE FAMILIES IN THE HIGHER LEVEL
	$mysql = "select family_id from $family_position where position_id in ('$positions_in_higherlevel')";
	$families_inhigherlevel_arr = $db_object->get_single_column($mysql);

	$families_in_higher_level = @implode("','",$families_inhigherlevel_arr); 
//SHOW ALL THE MODELS IN THIS HIGER ORDER LEVEL	
	$mysql = "select model_id from $model_factors_1 where family in ('$families_in_higher_level') and model_id in ('$all_models_one_can_view') limit 0,$fModels_toshow";
	$models_in_higher_level_arr = $db_object->get_single_column($mysql);
	$models_in_higher_level = @implode("','",$models_in_higher_level_arr);

//DETERMINE THE X AND Y VALUES FOR THE MODELS OF THE "ONE LEVEL HIGHER"
if($f1level == 'on')
{	
	if($fiskills == 'on')
	{
		$mysql = "select percent_fit,model_id,skill_type from $models_percent_fit where user_id = '$id_of_user' and skill_type='i' and percent_fit >= $ipercent and model_id in ('$models_in_higher_level') limit 0,$fModels_toshow";
		$ival_1level_arr = $db_object->get_rsltset($mysql);
	}
	
	if($ftskills == 'on')
	{

		$mysql = "select percent_fit,model_id,skill_type from $models_percent_fit where user_id = '$id_of_user' and skill_type='t' and percent_fit >= $tpercent and model_id in ('$models_in_higher_level') limit 0,$fModels_toshow";
		$tval_1level_arr = $db_object->get_rsltset($mysql);

	}
//-------------------------
	for($i=0;$i<count($ival_1level_arr);$i++)
	{
		$ipercentfit_1level = $ival_1level_arr[$i]['percent_fit'];
		$imodelid_1level = $ival_1level_arr[$i]['model_id'];
				

		for($j=0;$j<count($tval_1level_arr);$j++)
		{
			$tpercentfit_1level = $tval_1level_arr[$j]['percent_fit'];
			$tmodelid_1level = $tval_1level_arr[$j]['model_id'];
			
			if($imodelid_1level == $tmodelid_1level)
			{
			
			//$dataarray_1level[$ipercentfit_1level][$tpercentfit_1level] = $tmodelid_1level;
			 
				if($ipercentfit_1level != 0)
				{
				$xcoordinate_1level = round(($ipercentfit_1level * 3) + 65);
			
				}
				else
				{
				$xcoordinate_1level = 65;
				}

				if($tpercentfit_1level != 0)
				{
				$ycoordinate_1level = 325 - round(($tpercentfit_1level * 3));
			
				}
				else
				{
				$ycoordinate_1level = 325;	
				}
				

 				$x1_1level = $xcoordinate_1level;
				$y1_1level = $ycoordinate_1level;
				$xco_1level_arr[] = $x1_1level;
 				$yco_1level_arr[] = $y1_1level;
				

			
			}
			
		}
	}
}
$xco_1level = @implode(",",$xco_1level_arr);
$yco_1level = @implode(",",$yco_1level_arr);
$values['xco_1level']=$xco_1level;
$values['yco_1level']=$yco_1level;

//-------------------------

//888888888888888888888888888888888888888888888888888888888888888888
//MODELS IN HIGHER LEVEL +2 LEVEL
	
//DETERMINE THE HIGER LEVEL +2 TO THE CURRENT LEVEL...
	
	if($higherorder == yes)
	{
		$two_level_up = $cur_level + 2;
	}
	else
	{
		$two_level_up = $cur_level - 2;
	}
		
//DETERMINE THE MODELS IN +2 higher LEVEL...
	
//DETERMINE ALL THE POSITIONS IN THIS HIGER LEVELS +2 ...

	$mysql = "select pos_id from $position where level_no = '$two_level_up'";
	$positions_in_higherleveltwo_arr = $db_object->get_single_column($mysql);
	
	$positions_in_higherlevel_two = @implode("','",$positions_in_higherleveltwo_arr);
		
//DETERMINE ALL THE FAMILIES IN THE HIGHER +2 LEVEL
	$mysql = "select family_id from $family_position where position_id in ('$positions_in_higherlevel_two')";
	$families_inhigherleveltwo_arr = $db_object->get_single_column($mysql);
	
	$families_in_higher_level_two = @implode("','",$families_inhigherleveltwo_arr); 
	
//SHOW ALL THE MODELS IN THIS HIGER +2 ORDER LEVEL
	$mysql = "select model_id from $model_factors_1 where family in ('$families_in_higher_level_two') and model_id in ('$all_models_one_can_view') limit 0,$fModels_toshow";
	$models_in_higher_level_two_arr = $db_object->get_single_column($mysql);
	
	$models_in_higher_level_two = @implode("','",$models_in_higher_level_two_arr);
if($f2level == 'on')
{	
	if($fiskills == 'on')
	{
		$mysql = "select percent_fit,model_id,skill_type from $models_percent_fit where user_id = '$id_of_user' and skill_type='i' and percent_fit >= $ipercent and model_id in ('$models_in_higher_level_two') limit 0,$fModels_toshow";
		$ival_2level_arr = $db_object->get_rsltset($mysql);
	}
	
	if($ftskills == 'on')
	{

		$mysql = "select percent_fit,model_id,skill_type from $models_percent_fit where user_id = '$id_of_user' and skill_type='t' and percent_fit >= $tpercent and model_id in ('$models_in_higher_level_two') limit 0,$fModels_toshow";
		$tval_2level_arr = $db_object->get_rsltset($mysql);

	}
//-------------------------
	for($i=0;$i<count($ival_2level_arr);$i++)
	{
		$ipercentfit_2level = $ival_2level_arr[$i]['percent_fit'];
		$imodelid_2level = $ival_2level_arr[$i]['model_id'];
				

		for($j=0;$j<count($tval_2level_arr);$j++)
		{
			$tpercentfit_2level = $tval_2level_arr[$j]['percent_fit'];
			$tmodelid_2level = $tval_2level_arr[$j]['model_id'];
			
			if($imodelid_2level == $tmodelid_2level)
			{
			
			//$dataarray_2level[$ipercentfit_2level][$tpercentfit_2level] = $tmodelid_2level;
			 
				if($ipercentfit_2level != 0)
				{
				$xcoordinate_2level = round(($ipercentfit_2level * 3) + 65);
			
				}
				else
				{
				$xcoordinate_2level = 65;
				}

				if($tpercentfit_2level != 0)
				{
				$ycoordinate_2level = 325 - round(($tpercentfit_2level * 3));
			
				}
				else
				{
				$ycoordinate_2level = 325;	
				}
				

 				$x1_2level = $xcoordinate_2level;
				$y1_2level = $ycoordinate_2level;
				$xco_2level_arr[] = $x1_2level;
 				$yco_2level_arr[] = $y1_2level;
				

			
			}
			
		}
	}
}
$xco_2level = @implode(",",$xco_2level_arr);
$yco_2level = @implode(",",$yco_2level_arr);
$values['xco_2level']=$xco_2level;
$values['yco_2level']=$yco_2level;

//-------------------------

//888888888888888888888888888888888888888888888888888888888888888888

//COMBINE ALL THE MODEL IDS TO ONE ARRAY SO THAT WE CAN FIND THE MAP LINKS TO SHOW IN THE CHART

$allmodels_arr = @array_merge($models_mylevel_arr,$models_in_higher_level_arr,$models_in_higher_level_two_arr);
$allmodels_unique_arr = @array_unique($allmodels_arr);

//DETERMINE THE PERCENTAGE FITS FOR THE MODELS OF THE PARTICULAR USER

	if($fiskills == 'on')
	{
		$mysql = "select percent_fit,model_id,skill_type from $models_percent_fit where user_id = '$id_of_user' and skill_type='i' and percent_fit >= $ipercent and model_id in ('$all_models_one_can_view')";
		$ival_arr = $db_object->get_rsltset($mysql);
	}
	
	if($ftskills == 'on')
	{

		$mysql = "select percent_fit,model_id,skill_type from $models_percent_fit where user_id = '$id_of_user' and skill_type='t' and percent_fit >= $tpercent and model_id in ('$all_models_one_can_view')";
		$tval_arr = $db_object->get_rsltset($mysql);

	}

	preg_match("/<{maplinks_loopstart}>(.*?)<{maplinks_loopend}>/s",$returncontent,$oldmap);
	$newmap = $oldmap[1];
	
	for($i=0;$i<count($ival_arr);$i++)
	{
		$ipercentfit = $ival_arr[$i]['percent_fit'];
		$imodelid = $ival_arr[$i]['model_id'];
				

		for($j=0;$j<count($tval_arr);$j++)
		{
			$tpercentfit = $tval_arr[$j]['percent_fit'];
			$tmodelid = $tval_arr[$j]['model_id'];
			
			if($imodelid == $tmodelid)
			{
			
			$dataarray[$ipercentfit][$tpercentfit] = $tmodelid;
			$mysql = "select model_name from $model_main where model_id = '$tmodelid' limit 0,$fModels_toshow";
			$modelname_arr = $db_object->get_a_line($mysql);
			$model_name = $modelname_arr['model_name'];
			 
				if($ipercentfit != 0)
				{
				$xcoordinate = round(($ipercentfit * 3) + 65);
			
				}
				else
				{
				$xcoordinate = 65;
				}

				if($tpercentfit != 0)
				{
				$ycoordinate = 325 - round(($tpercentfit * 3));
			
				}
				else
				{
				$ycoordinate = 325;	
				}
				

 				$x1 = $xcoordinate;
				$y1 = $ycoordinate;
				$x2 = $xcoordinate + 8;
				$y2 = $ycoordinate + 8;
				$all_coordinates = $x1.",".$y1.",".$x2.",".$y2;
 
				$str .= preg_replace("/<{(.*?)}>/e","$$1",$newmap);
			
			}
			
		}
	}
	$returncontent = preg_replace("/<{maplinks_loopstart}>(.*?)<{maplinks_loopend}>/s",$str,$returncontent);






	$returncontent = $common->direct_replace($db_object,$returncontent,$values);
	echo $returncontent;

}
 
}
$obj = new mobility2;
if($fSave)
{
 echo "to be saved";
}
else
{
	
$obj->show_mobility_reports($db_object,$common,$user_id,$default,$post_var);
}

include_once("footer.php");

?>
