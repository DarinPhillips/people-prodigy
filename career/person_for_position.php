<?php
/*---------------------------------------------
SCRIPT:gaps_at_glance.php
AUTHOR:info@chrisranjana.com	
UPDATED:26th Dec

---------------------------------------------*/

include("../session.php");

include_once("header.php");
class modelviews
{
	function select_components($db_object,$common,$default,$user_id,$post_var,$modelid)
	{
		$xPath		= $common->path;
		$returncontent	= $xPath."/templates/career/person_for_position1.html";
		$returncontent	= $common->return_file_content($db_object,$returncontent);
		
			

	
		/*$values['designee1']= $desig1_id;
		$values['designee2']= $desig2_id;
		$values['modelid'] = $modelid;*/
		
		$modelid=3;
		
		$position = $common->prefix_table('position');
		$skills  = $common->prefix_table('skills');
		$user_table = $common->prefix_table('user_table');
		
//PRINTING OF POSITION NAME...
		$posid=1;
		
		$mysql = "select position_name from $position where pos_id = '$posid'";

		$posname_arr = $db_object->get_a_line($mysql);
		$posname = $posname_arr['position_name'];
		$values['posname'] = $posname;
		$values['posid'] = $posid;
		
//THE COOKIE CONTAINS THE USERS SELECTED IN THE PREVIOUS SCREEN...
		
		if($user_id==1)
		{
		$sql="select user_id from $user_table where user_id<>'$user_id'";
		
		$users=$db_object->get_single_column($sql);
		}
		else
		{
			$users=$common->employees_under_admin_boss($db_object,$user_id);
		}
		
		//$cookievalues = $_COOKIE['Usersindepth'];

		//$users = @explode(",",$cookievalues);
		
		/*$boss=is_boss($db_object,$user_id);
		
		$pos_qry="select position from $user_table where user_id='$user_id'";
		
		$pos_res=$db_object->get_a_line($pos_qry);
		
		$pos=$pos_res[position];
		
		$posres=$common->get_chain_below($pos,$db_object,$twodarr);
		
		$users*/



		for($i=0;$i<count($users);$i++)
		{
		$user_to_show = $users[$i];
		
		$userswithkeys = $common->gaps_at_a_glance($db_object,$user_to_show,$modelid);
		$userswithkeys_tech = $common->gaps_at_a_glance_technical($db_object,$user_to_show,$modelid);


		$mysql = "select username from $user_table where user_id = '$user_to_show'";
		$username_arr = $db_object->get_a_line($mysql);
		$username = $username_arr['username'];

		//SKILLS USER HAS...
			$vals_has[$i] = @array_merge($userswithkeys[3],$userswithkeys[4],$userswithkeys[5]);
		//SKILLS USER NEEDS...
			$vals_needs[$i] = @array_merge($userswithkeys[2],$userswithkeys[1]);
			
		//ALL SKILLS IN MODELS...
			$skills_all_in_models_arr = @array_merge($vals_has,$vals_needs);
			$skills_all_in_models = @implode("','",$skills_all_in_models_arr);
			
		//TECHNICAL SKILLS USER HAS...
			$vals_tech_has[$i] = @array_merge($userswithkeys_tech[3],$userswithkeys_tech[4],$userswithkeys_tech[5]);	
		//TECHNICAL SKILLS USER NEEDS...
			$vals_tech_needs[$i] = @array_merge($userswithkeys_tech[2],$userswithkeys_tech[1]);	
		//ALL TECHNICAL SKILLS IN MODELS...
			$skills_tech_all_in_models_arr = @array_merge($vals_tech_has,$vals_tech_needs);
			$skills_tech_all_in_models = @implode("','",$skills_tech_all_in_models_arr);
			
	
		
$mysql = "select distinct(skill_id) from other_raters_tech where rated_user = '$user_to_show' and skill_id not in ('$skills_tech_all_in_models')";
$extratech_arr[$i] = $db_object->get_single_column($mysql);		

$total[$i]=count($vals_has[$i])+count($vals_needs[$i])+count($vals_tech_has[$i])+count($vals_tech_needs[$i])+count($extraskills_arr[$i])+count($extratech_arr[$i]);
	}

		//LOOP CONTENTS OF THE HAS:NEEDS:EXTRAS...
	
preg_match("/<{gapsataglance_loopstart}>(.*?)<{gapsataglance_loopend}>/s",$returncontent,$gapsold);
$gaps_new = $gapsold[1];

preg_match("/<{gapsskillshas_loopstart}>(.*?)<{gapsskillshas_loopend}>/s",$gaps_new,$gapskillhas_old);
$gapskillhas_new = $gapskillhas_old[1];

preg_match("/<{gapsskillsneeds_loopstart}>(.*?)<{gapsskillsneeds_loopend}>/s",$gaps_new,$gapskillneeds_old);
$gapskillneeds_new = $gapskillneeds_old[1];

preg_match("/<{gapsskillsextras_loopstart}>(.*?)<{gapsskillsextras_loopend}>/s",$gaps_new,$gapskillextras_old);
$gapskillextras_new = $gapskillextras_old[1];

preg_match("/<{gapsskillshastech_loopstart}>(.*?)<{gapsskillshastech_loopend}>/s",$gaps_new,$gapskillhastech_old);
$gapskillhas_tech = $gapskillhastech_old[1];

preg_match("/<{gapsskillsneedstech_loopstart}>(.*?)<{gapsskillsneedstech_loopend}>/s",$gaps_new,$gapskillneedstech_old);
$gapskillneeds_tech = $gapskillneedstech_old[1];

preg_match("/<{gapsskillstechextras_loopstart}>(.*?)<{gapsskillstechextras_loopend}>/s",$gaps_new,$gapskillextratech_old);
$gapskillextras_tech = $gapskillextratech_old[1];
@arsort($total);
$keys=@array_keys($total);



	for($i=0;$i<20;$i++)
	{	
		$key=$keys[$i];
		
		$user=$users[$key];
		
		$username=$common->name_display($db_object,$user);

	
		$str6 = '';
		$str5 = '';
		$str4 = '';
		$str3 = '';		
		$str2 = '';
		$str1 = '';

		for($l=0;$l<count($vals_has[$key]);$l++)	
		{
			$skillid = $vals_has[$key][$l];
			
			$mysql = "select skill_name from $skills where skill_id = '$skillid'";
			$skillname_arr = $db_object->get_a_line($mysql);
			$skills_has = $skillname_arr['skill_name'];
			$str1 .= preg_replace("/<{(.*?)}>/e","$$1",$gapskillhas_new);
		}
		
		$gaps_new1 = preg_replace("/<{gapsskillshas_loopstart}>(.*?)<{gapsskillshas_loopend}>/s",$str1,$gaps_new);
		
		for($j=0;$j<count($vals_needs[$key]);$j++)
		{
			$skillsid = $vals_needs[$key][$j];
			$mysql = "select skill_name from $skills where skill_id = '$skillsid'";
			$skillname_arr = $db_object->get_a_line($mysql);

			$skills_needs = $skillname_arr['skill_name'];

			$str2 .= preg_replace("/<{(.*?)}>/e","$$1",$gapskillneeds_new);

		}


		$gaps_new2 = preg_replace("/<{gapsskillsneeds_loopstart}>(.*?)<{gapsskillsneeds_loopend}>/s",$str2,$gaps_new1);		
		//EXTRA SKILLS...
		for($k=0;$k<count($extraskills_arr[$key]);$k++)
		{
			$skillid_e = $extraskills_arr[$key][$k];
			$mysql = "select skill_name from $skills where skill_id = '$skillid_e'";
			$skillname_arr = $db_object->get_a_line($mysql);
			$skills_extras = $skillname_arr['skill_name'];
			$str3 .= preg_replace("/<{(.*?)}>/e","$$1",$gapskillextras_new);
		}
		
		$gaps_new3 = preg_replace("/<{gapsskillsextras_loopstart}>(.*?)<{gapsskillsextras_loopend}>/s",$str3,$gaps_new2);


//TECHNICAL SKILLS DISPLAY...
	
		for($a=0;$a<count($vals_tech_has[$key]);$a++)	
		{
			$skill_techid = $vals_tech_has[$key][$a];
			$mysql = "select skill_name from $skills where skill_id = '$skill_techid'";
			$skillname_arr = $db_object->get_a_line($mysql);
			$skills_has_tech = $skillname_arr['skill_name'];
			$str4 .= preg_replace("/<{(.*?)}>/e","$$1",$gapskillhas_tech);
		}
		$gaps_new4 = preg_replace("/<{gapsskillshastech_loopstart}>(.*?)<{gapsskillshastech_loopend}>/s",$str4,$gaps_new3);
	
		for($b=0;$b<count($vals_tech_needs[$key]);$b++)
		{
			$skill_tech_id = $vals_tech_needs[$key][$b]; 
			$mysql = "select skill_name from $skills where skill_id = '$skill_tech_id'";
			$skillname_arr = $db_object->get_a_line($mysql);
			$skills_needs_tech = $skillname_arr['skill_name'];
			$str5 .= preg_replace("/<{(.*?)}>/e","$$1",$gapskillneeds_tech);
		}
		$gaps_new5 = preg_replace("/<{gapsskillsneedstech_loopstart}>(.*?)<{gapsskillsneedstech_loopend}>/s",$str5,$gaps_new4);
		for($c=0;$c<count($extratech_arr[$key]);$c++)
		{
			$skilltechid = $extratech_arr[$key][$c];
			$mysql = "select skill_name from $skills where skill_id = '$skilltechid'";
			$skillname_arr = $db_object->get_a_line($mysql);
			$skills_extra_tech = $skillname_arr['skill_name'];
			$str6 .= preg_replace("/<{(.*?)}>/e","$$1",$gapskillextras_tech);
			
		}	
		$gaps_new6 = preg_replace("/<{gapsskillstechextras_loopstart}>(.*?)<{gapsskillstechextras_loopend}>/s",$str6,$gaps_new5);

		$gaps_new7 .= preg_replace("/<{(.*?)}>/e","$$1",$gaps_new6);
		


	}
		$returncontent = preg_replace("/<{gapsataglance_loopstart}>(.*?)<{gapsataglance_loopend}>/s",$gaps_new7,$returncontent);		
		
		$returncontent 	= $common->direct_replace($db_object,$returncontent,$values);
		echo $returncontent;
		
		

	}
	
	function show_models($db_object,$common,$user_id,$default)
	{
	$xPath=$common->path;
	$returncontent=$xPath."/templates/career/person_for_position.html";
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

$obj = new modelviews;


if($fSave)
{

$obj->save_designees($db_object,$common,$default,$user_id,$post_var);



//$message = $error_msg['cDesingneesaved'];
//echo $message;



}
else if($action==show)
{


$obj->select_components($db_object,$common,$default,$user_id,$post_var,$m_id);
}
else
{
	$obj->show_models($db_object,$common,$user_id,$default);


}

include_once("footer.php");


?>
