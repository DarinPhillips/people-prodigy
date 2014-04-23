<?php
/*---------------------------------------------
SCRIPT:position_model4.php
AUTHOR:info@chrisranjana.com	
UPDATED:27th Nov

DESCRIPTION:
This script displays the fourth step of position models created by admin.

---------------------------------------------*/
include("../session.php");


include_once("header.php");
class position_model
{
	function show_full_details($db_object,$common,$post_var,$default)
	{
		$xPath		= $common->path;
		$returncontent	= $xPath."/templates/career/position_model4.html";
		$returncontent	= $common->return_file_content($db_object,$returncontent);

		while(list($kk,$vv) = @each($post_var))
		{
			$$kk = $vv; 
			if(ereg("^fLevel_",$kk))
			{
				$skill = ereg_replace("fLevel_","",$kk);
				if($vv != '')
				{
					$level_chosen[$skill] = $vv;
				}
			}
		}
	
		$lev_cho = @implode(",",$level_chosen);
		$level_keys = @array_keys($level_chosen);

		$lev_key = @implode(",",$level_keys);

		$values['model_id'] = $model_id;
		$values['lev_key'] = $lev_key;
		$values['lev_cho'] = $lev_cho;
//===========================================================DISPLAY OF FACTORS SELECTED...
		$qualifications = $_COOKIE['Qualifications'];
		$components = $_COOKIE['ModelComponents'];

		$comp_sel = @explode("||",$components);

		while(list($kk,$vv) = @each($comp_sel))
		{
			if(ereg("^families:",$vv))
			{
			$f_qid=ereg_replace("families:","",$vv);
	
			if($f_qid != '')
				{
				$families[] = $f_qid;
				}
			}
			if(ereg("^boss:",$vv))
			{
			$b_qid=ereg_replace("boss:","",$vv);
	
			if($b_qid != '')
				{
				$boss[] = $b_qid;
				}
			}
			if(ereg("^positions:",$vv))
			{
			$p_qid=ereg_replace("positions:","",$vv);
	
			if($p_qid != '')
				{
				$positions[] = $p_qid;
				}
			}
			if(ereg("^employees:",$vv))
			{
			$e_qid=ereg_replace("employees:","",$vv);
	
			if($e_qid != '')
				{
				$employees[] = $e_qid;
				}
			}
	
			if(ereg("^location:",$vv))
			{
			$l_qid=ereg_replace("location:","",$vv);
	
			if($l_qid != '')
				{
				$location[] = $l_qid;
				}
			}
			if(ereg("^employment_type:",$vv))
			{
			$emptype_qid=ereg_replace("employment_type:","",$vv);
	
			if($emptype_qid != '')
				{
				$employment_type[] = $emptype_qid;
				}
			}
			if(ereg("^levels:",$vv))
			{
			$levels_qid=ereg_replace("levels:","",$vv);
	
			if($levels_qid != '')
				{
				$levels[] = $levels_qid;
				}
			}
			if(ereg("^eeo:",$vv))
			{
			$eeo_qid=ereg_replace("eeo:","",$vv);
	
			if($eeo_qid != '')
				{
				$eeo[] = $eeo_qid;
				}
			}
			if(ereg("^iskills:",$vv))
			{
			$iskills_qid=ereg_replace("iskills:","",$vv);
	
			if($tskills_qid != '')
				{
				$tskills[] = $tskills_qid;
				}
			}
			if(ereg("^tskills:",$vv))
			{
			$tskills_qid=ereg_replace("tskills:","",$vv);
	
			if($tskills_qid != '')
				{
				$tskills[] = $tskills_qid;
				}
			}
	
	
		}

		$family = $common->prefix_table('family');
		$location_table = $common->prefix_table('location_table');
		$employmenttype_table = $common->prefix_table('employment_type');

	if($families != '')
	{

		if(@in_array("All",$families))
		{
			$mysql = "select family_name from $family";
			$fam_arr = $db_object->get_single_column($mysql);
			//$fam = @implode(",",$arr);
		}
		elseif(@in_array("None",$families))
		{
			
			$fam_arr = '';
			$returncontent = preg_replace("/<{fam_start}>(.*?)<{fam_end}>/s","",$returncontent);
		}
		else
		{
			$families1=@explode(",",$families[0]);
			$fam=@implode("','",$families1);

			$fam="'".$fam."'";
			//$fam = @implode(",",$families1);
			$mysql = "select family_name from $family where family_id in ($fam)";
			//$mysql = "select family_name from $family where family_id in ($fam)";

			$fam_arr = $db_object->get_single_column($mysql);
		}
 
		$returncontent = preg_replace("/<{fam_(.*?)}>/s","",$returncontent);
		$families = @implode(",",$fam_arr);
		$values['famids']=$fam;
		$values['families']=$families;
	}

	$returncontent = preg_replace("/<{fam_start}>(.*?)<{fam_end}>/s","",$returncontent);

	if($levels != '')
	{
		preg_match("/<{leveldisplay_loopstart}>(.*?)<{leveldisplay_loopend}>/s",$returncontent,$levelmatch);
		$newlevelmatch = $levelmatch[1];
		
		if(@in_array("All",$levels))
		{
			$level_arr = $common->return_levels($db_object);
			//print_r($level_arr);
			$level_all = @implode(",",$level_arr);
			$level_arr = @explode(",",$level_all);
			
			for($i=0;$i<count($level_arr);$i++)
			{
				$lev_display = $level_arr[$i];
				$str .= preg_replace("/<{(.*?)}>/e","$$1",$newlevelmatch);
			}
			$returncontent = preg_replace("/<{leveldisplay_loopstart}>(.*?)<{leveldisplay_loopend}>/s",$str,$returncontent);
		}
		
		 
		elseif(@in_array("None",$levels))
		{
			$level_arr = '';
			$returncontent = preg_replace("/<{lev_start}>(.*?)<{lev_end}>/s","",$returncontent);

		}
		else
		{
			//print_r($levels);
			$l_sel = @explode(",",$levels[0]);
			//print_r($l_sel);
			for($i=0;$i<count($l_sel);$i++)
			{
				$lev_display = $l_sel[$i];
				$str .= preg_replace("/<{(.*?)}>/e","$$1",$newlevelmatch);
			}
			$returncontent = preg_replace("/<{leveldisplay_loopstart}>(.*?)<{leveldisplay_loopend}>/s",$str,$returncontent);
		}
		//print_r($level_arr);
		$returncontent = preg_replace("/<{lev_(.*?)}>/s","",$returncontent);
	}
	
	$returncontent = preg_replace("/<{lev_start}>(.*?)<{lev_end}>/s","",$returncontent);

	if($location != '')
	{
		
		if(@in_array("All",$location))
		{

		$returncontent = preg_replace("/<{loc_(.*?)}>/s","",$returncontent);
		
		$location_dis_arr = $common->return_location_for_display($db_object);
			
		preg_match("/<{locdisplay_loopstart}>(.*?)<{locdisplay_loopend}>/s",$returncontent,$match_loc);
		$newmatch_loc 	= $match_loc[1];
		
			while (list($key,$value) = @each($location_dis_arr))
			{
				$location_name 	= $location_dis_arr[$key];
				$strnew .= preg_replace("/<{(.*?)}>/e","$$1",$newmatch_loc);
			}
			
		$returncontent = preg_replace("/<{locdisplay_loopstart}>(.*?)<{locdisplay_loopend}>/s",$strnew,$returncontent);
		
		}
		elseif(@in_array("None",$location))
		{
			$returncontent = preg_replace("/<{loc_start}>(.*?)<{loc_end}>/s","",$returncontent);
		}
		else
		{
		preg_match("/<{locdisplay_loopstart}>(.*?)<{locdisplay_loopend}>/s",$returncontent,$match_loc);
		$newmatch_loc 	= $match_loc[1];
		
			$returncontent = preg_replace("/<{loc_(.*?)}>/s","",$returncontent);
			//print_r($location);
			$all_loc = @explode(",",$location[0]);
			//print_r($all_loc);
			for($i=0;$i<count($all_loc);$i++)
			{
				$locid = $all_loc[$i];
				
				$loc = $common->return_location_for_display($db_object,$locid);

				while(list($kk,$vv) = @each($loc))
				{
					//echo "key is $kk and val is $vv<br>";
					$location_name = $loc[$kk];
										
				}
				//echo "$location_name<br>";
				$strnew1 .= preg_replace("/<{(.*?)}>/e","$$1",$newmatch_loc);
				
			}
			//echo $strnew1;exit;
			$returncontent = preg_replace("/<{locdisplay_loopstart}>(.*?)<{locdisplay_loopend}>/s",$strnew1,$returncontent);
			//print_r($loc);
		}
		
	}
	$returncontent = preg_replace("/<{loc_start}>(.*?)<{loc_end}>/s","",$returncontent);
	if($employment_type != '')
	{
		//print_r($employment_type);
		$empltypes = @explode(",",$employment_type[0]);
		if(@in_array("All",$employment_type))
		{
			$returncontent = preg_replace("/<{empltype_(.*?)}>/s","",$returncontent);
			$mysql = "select id,type_$default as typename from $employmenttype_table";

			$empltype_arr = $db_object->get_rsltset($mysql);
			
			$values['employmenttype_loop'] = $empltype_arr;
			
		}
		elseif(@in_array("None",$employment_type))
		{
			
		}
		else
		{
			$etypes = @implode("','",$empltypes);
			
			$mysql = "select id,type_$default as typename from $employmenttype_table where id in ('$etypes')";

			$empltype_arr = $db_object->get_rsltset($mysql);
			$values['employmenttype_loop'] = $empltype_arr;
			$returncontent = preg_replace("/<{empltype_(.*?)}>/s","",$returncontent);
		}
		
	}
	$returncontent = preg_replace("/<{empltype_start}>(.*?)<{empltype_end}>/s","",$returncontent);
/*===========================================================location

$loc1 = $location[0];
$loc = @explode(",",$loc1);



preg_match("/<{<{location_start}>(.*?)<{location_end}>/s",$returncontent,$locmatchold);
$loc_match = $locmatchold[1];

for($i=0;$i<count($loc);$i++)
{
	$location = $loc[$i];
$x = $common->return_location_for_display($db_object,$location);
}
//print_r($x);exit;
while(list($kk,$vv) = @each($x))
{
	$loc .= preg_replace("/<{(.*?)}>/e","$$1",$loc_match);
}

$returncontent = preg_replace("/<{location_start}>(.*?)<{location_end}>/s",$loc,$returncontent);
===========================================================location  */


$qual_selected = @explode("||",$qualifications);
$qualification_selected = @implode(" ",$qual_selected);
$values['qualification_selected'] = $qualification_selected;

//=======================================================DISPLAY OF FACTORS SELECTED END.
		
		$level_arr = $common->return_levels($db_object);

		preg_match("/<{level_loopstart}>(.*?)<{level_loopend}>/s",$returncontent,$match_lev);
		$newmatch_lev = $match_lev[1];

		while(list($kk,$vv) = @each($level_arr))
		{
			$str1 .= preg_replace("/<{(.*?)}>/e","$$1",$newmatch_lev);
		}

		$returncontent = preg_replace("/<{level_loopstart}>(.*?)<{level_loopend}>/s",$str1,$returncontent);
		$returncontent = $common->direct_replace($db_object,$returncontent,$values);
		$returncontent	= $common->multipleloop_replace($db_object,$returncontent,$values,'');
		
		echo $returncontent;
	}
	function save_data($db_object,$common,$post_var,$default,$user_id)
	{
	//print_r($post_var);exit;
		while(list($kk,$vv) = @each($post_var))
		{

			$$kk = $vv;
			
			if(ereg("^fView_",$kk))
			{
				$lev = ereg_replace("fView_","",$kk);
				if($vv == 'on')
				{
					$level_view[$lev] = $lev; 
				}
			}
			if(ereg("^fViewsel_",$kk))
			{
				$lev1 = ereg_replace("fViewsel_","",$kk);
				if($vv == 'on')
				{
					$level_view1[$lev1] = "yes";
				}
				else
				{
					$level_view1[$lev1] = "no";
				}
			}
		}

	$model_name = $common->prefix_table('model_name_table');
	$model_view_1 = $common->prefix_table('model_view_1');
	$model_view_2 = $common->prefix_table('model_view_2');


	$mysql = "insert into $model_name set model_id = '$model_id' ,  model_name = '$fModelname'";
	$db_object->insert($mysql);


	while(list($key,$val) = @each($level_view))
	{

	$mysql = "insert into $model_view_1 set model_id = '$model_id' , levels_to_view = '$val'";
	$db_object->insert($mysql);

	}
//print_r($level_view1);

 
		$level1_keys = @array_keys($level_view1);
		if(@in_array("all",$level1_keys))
		{
			$all = 'yes';
		}
		else
		{
			$all = 'no';
		}
		if(@in_array("boss",$level1_keys))
		{
			$boss = 'yes';
		}
		else
		{
			$boss = 'no';
		}
		if(@in_array("admins",$level1_keys))
		{
			$admins = 'yes';
		}
		else
		{
			$admins = 'no';
		}
		if(@in_array("me",$level1_keys))
		{
			$me = 'yes';
		}
		else
		{
			$me = 'no';
		}
		
		$mysql = "insert into $model_view_2 set model_id = '$model_id', all1 = '$all', boss = '$boss' ,admins = '$admins' , me = '$me'";

		$db_object->insert($mysql);

		 
	}

function save_model_data($db_object,$common,$post_var,$default,$user_id)
{
	while(list($kk,$vv) = @each($post_var))
	{
		$$kk = $vv;

		if(ereg("^fLevel_",$kk))
		{
			list($un,$skill_id) = split("_",$kk);
			$level_arr[$skill_id] = $vv;
		}
		if(ereg("^weight_",$kk))
		{
			list($un,$skill_id) = split("_",$kk);
			$weight_arr[$skill_id] = $vv;
		}
		if(ereg("^recommended_",$kk))
		{
			list($un,$skill_id) = split("_",$kk);
			$recommended_arr[$skill_id] = $vv;
		}

	}
 
	$model_skills = $common->prefix_table('model_skills');


while(list($kk,$vv) = @each($level_arr))
{
	$level_chosen = $level_arr[$kk];
	$weight_chosen = $weight_arr[$kk];
	$recommended_chosen = $recommended_arr[$kk];
	
	$mysql = "insert into $model_skills set model_id = '$model_id' , skill_id = $kk , level_chosen = '$level_chosen' , weight = '$weight_chosen' , recommended_level = '$recommended_chosen'";
	//echo "$mysql<br>";	
	$db_object->insert($mysql);

}
 
}

}
$obj = new position_model;


if($fSave)
{
	 
	$obj->save_data($db_object,$common,$post_var,$default,$user_id);
	$message = $error_msg['cModelSaved'];
	echo $message;
}
else
{	
	$obj->save_model_data($db_object,$common,$post_var,$default,$user_id);
	
	$obj->show_full_details($db_object,$common,$post_var,$default);
}
include_once('footer.php');
?>
