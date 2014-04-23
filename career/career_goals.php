<?php
/*---------------------------------------------
SCRIPT:career_goals.php
AUTHOR:info@chrisranjana.com	
UPDATED:13th Dec

DESCRIPTION:
This script sets the career goals for the users

---------------------------------------------*/
include("../session.php");
include("header.php");

class careergoals
{
function show_screen($db_object,$common,$post_var,$user_id,$fEmpl_id)
	{
		while(list($kk,$vv) = @each($post_var))
		{
		$$kk = $vv;
		}	

	$xPath		= $common->path;
	$returncontent	= $xPath."/templates/career/career_goals.html";
	$returncontent	= $common->return_file_content($db_object,$returncontent);


	$config 	= $common->prefix_table('config');
	$user_table 	= $common->prefix_table('user_table');
	$family_position= $common->prefix_table('family_position');
	$family		= $common->prefix_table('family');
	$position	= $common->prefix_table('position');
	$org_main	= $common->prefix_table('org_main');
	$career_goals 	= $common->prefix_table('career_goals');

	
	if($fEmpl_id != '')
	{
		$id_of_user = $fEmpl_id;
	}
	else
	{
		$id_of_user = $user_id;
	}
		

	$mysql = "select username from $user_table where user_id = '$id_of_user'";
	$username_arr = $db_object->get_a_line($mysql);
	$username = $username_arr['username'];
	$values['username'] = $username;
	
//Selected Values taken from database for "lot" of Interest...

	$mysql = "select onelevel_low,same_level,onelevel_up,twolevel_up from $career_goals where interest = 'lot' and user_id = '$id_of_user'";
	$lot_arr = $db_object->get_a_line($mysql);

	$famsel_onelow_lot['family_id'] = $lot_arr['onelevel_low'];
	$famsel_same_lot['family_id'] = $lot_arr['same_level'];
	$famsel_oneup_lot['family_id'] = $lot_arr['onelevel_up'];
	$famsel_twoup_lot['family_id'] = $lot_arr['twolevel_up'];
	
//Selected Values taken from database for "some" Interest...
		
	$mysql = "select onelevel_low,same_level,onelevel_up,twolevel_up from $career_goals where interest = 'some' and user_id = '$id_of_user'";
	$some_arr = $db_object->get_a_line($mysql);

	$famsel_onelow_some['family_id'] = $some_arr['onelevel_low'];
	$famsel_same_some['family_id'] = $some_arr['same_level'];
	$famsel_oneup_some['family_id'] = $some_arr['onelevel_up'];
	$famsel_twoup_some['family_id'] = $some_arr['twolevel_up'];



//SYSTEM OWNER DEFINED TEXT DISPLAYED...
	$mysql 		= "select career_goals_message from $config where id = 1";
	$cgoal_mess_arr = $db_object->get_a_line($mysql);
	$Careergoalmessage= $cgoal_mess_arr['career_goals_message'];
	$values['Careergoalmessage'] = $Careergoalmessage; 	


//DISPLAY OF THE FAMILIES IN THE DROP DOWN BOXES...
//FIRST DETERMINE THE PERSONS' POSITION...
	$mysql = "select position from $user_table where user_id = '$id_of_user'";
	$user_position_arr = $db_object->get_a_line($mysql);
	$position_of_user = $user_position_arr['position'];

//DETERMINE THE LEVEL OF THE USER...
	$mysql = "select level_no from $position where pos_id = '$position_of_user'";
	$level_arr = $db_object->get_a_line($mysql);
	$level_of_user = $level_arr['level_no'];

//DETERMINE THE POSITIONS OF THE LEVEL...	
	$mysql = "select pos_id from $position where level_no = '$level_of_user'";
	$pos_of_level_arr = $db_object->get_single_column($mysql);
	$pos_of_level = @implode("','",$pos_of_level_arr);

//DETERMINE THE FAMILIES OF THOSE POSITIONS...
	$mysql = "select distinct(family_id) from $family_position where position_id in ('$pos_of_level')";	
	$family_of_user_level_arr = $db_object->get_single_column($mysql);

	$family_of_user_level = @implode("','",$family_of_user_level_arr);

//DISPLAY THOSE FAMILIES IN THE DROP DOWN BOXES...
	$mysql = "select family_id,family_name from $family where family_id in ('$family_of_user_level')";
	$familydisplay_arr = $db_object->get_rsltset($mysql);

	


//DISPLAY THE FAMILIES ONE LEVEL BELOW THE USERS' LEVEL...
	
//CHECK WHICH WAY THE ORGANISATION HAS BUILT THE ORGANISATIONAL CHART 
//IF HIGHERORDER=YES THEN 8-7-..1
//ELSE 1-2-..8

	$mysql = "select higher_order from $org_main";
	$higherorder_arr = $db_object->get_a_line($mysql);	
	$higherorder = $higherorder_arr['higher_order'];

	if($higherorder == 'yes')
	{
		$onelevel_below = $level_of_user - 1;
		$onelevel_above = $level_of_user + 1; 
		$twolevel_above = $level_of_user + 2;
	}
	else
	{
		$onelevel_below = $level_of_user + 1;
		$onelevel_above = $level_of_user - 1;
		$twolevel_above = $level_of_user - 2;
	}
	
//DETERMINE THE POSITIONS OF THAT LEVEL...
	$mysql = "select pos_id from $position where level_no = '$onelevel_below'";	
	$pos_onelevelbelow_arr = $db_object->get_single_column($mysql);
	$pos_onelevelbelow = @implode("','",$pos_onelevelbelow_arr);

//DETERMINE THE FAMILIES OF THOSE POSITIONS...
	$mysql = "select family_id from $family_position where position_id in ('$pos_onelevelbelow')";
	$fam_onelevelbelow_arr = $db_object->get_single_column($mysql);
	$fam_onelevelbelow = @implode("','",$fam_onelevelbelow_arr);
	
//DISPLAY THOSE FAMILIES
	$mysql = "select family_id, family_name from $family where family_id in ('$fam_onelevelbelow')";
	$family_display_onelevelbelow_arr = $db_object->get_rsltset($mysql);
	



//DISPLAY THE FAMILIES ONE LEVEL ABOVE THE USERS' LEVEL...
//--------------------------------------------------------	
//DETERMINE THE POSITIONS OF THAT LEVEL...
	$mysql = "select pos_id from $position where level_no = '$onelevel_above'";	
	$pos_onelevelabove_arr = $db_object->get_single_column($mysql);
	$pos_onelevelabove = @implode("','",$pos_onelevelabove_arr);

//DETERMINE THE FAMILIES OF THOSE POSITIONS...
	$mysql = "select family_id from $family_position where position_id in ('$pos_onelevelabove')";
	$fam_onelevelabove_arr = $db_object->get_single_column($mysql);
	$fam_onelevelabove = @implode("','",$fam_onelevelabove_arr);
	
//DISPLAY THOSE FAMILIES
	$mysql = "select family_id, family_name from $family where family_id in ('$fam_onelevelabove')";
	$family_display_onelevelabove_arr = $db_object->get_rsltset($mysql);
	

	



//DISPLAY THE FAMILIES TWO LEVEL ABOVE THE USERS' LEVEL...
//--------------------------------------------------------	
//DETERMINE THE POSITIONS OF THAT LEVEL...
	$mysql = "select pos_id from $position where level_no = '$twolevel_above'";	
	$pos_twolevelabove_arr = $db_object->get_single_column($mysql);
	$pos_twolevelabove = @implode("','",$pos_twolevelabove_arr);

//DETERMINE THE FAMILIES OF THOSE POSITIONS...
	$mysql = "select family_id from $family_position where position_id in ('$pos_twolevelabove')";
	$fam_twolevelabove_arr = $db_object->get_single_column($mysql);
	$fam_twolevelabove = @implode("','",$fam_twolevelabove_arr);
	
//DISPLAY THOSE FAMILIES
	$mysql = "select family_id, family_name from $family where family_id in ('$fam_twolevelabove')";
	$family_display_twolevelabove_arr = $db_object->get_rsltset($mysql);

/*=================	
	$famsel_onelow_lot['family_id'] = $lot_arr['onelevel_low'];
	$famsel_same_lot['family_id'] = $lot_arr['same_level'];
	$famsel_oneup_lot['family_id'] = $lot_arr['onelevel_up'];
	$famsel_twoup_lot['family_id'] = $lot_arr['twolevel_up'];



	$famsel_onelow_some['family_id'] = $some_arr['onelevel_low'];
	$famsel_same_some['family_id'] = $some_arr['same_level'];
	$famsel_oneup_some['family_id'] = $some_arr['onelevel_up'];
	$famsel_twoup_some['family_id'] = $some_arr['twolevel_up'];

==================*/
//SELECTED VALUES ARRAY...
	$sel_values['famdisplay_onebelow1_loop'] 		= $famsel_onelow_lot;
	$sel_values['familydisplaysame1_loop'] 			= $famsel_same_lot;
	$sel_values['famdisplay_oneabove1_loop'] 		= $famsel_oneup_lot;
	$sel_values['famdisplay_twoabove1_loop'] 		= $famsel_twoup_lot;
	
	$sel_values['famdisplay_onebelow2_loop'] 		= $famsel_onelow_some;
	$sel_values['familydisplaysame2_loop'] 			= $famsel_same_some;
	$sel_values['famdisplay_oneabove2_loop'] 		= $famsel_oneup_some;
	$sel_values['famdisplay_twoabove2_loop'] 		= $famsel_twoup_some;
	

//LOOP REPLACE ARRAY...
	$multipleloop_values['familydisplaysame1_loop']   = $familydisplay_arr;
	$multipleloop_values['familydisplaysame2_loop']   = $familydisplay_arr;
	
	$multipleloop_values['famdisplay_onebelow1_loop'] = $family_display_onelevelbelow_arr;
	$multipleloop_values['famdisplay_onebelow2_loop'] = $family_display_onelevelbelow_arr;

	$multipleloop_values['famdisplay_oneabove1_loop'] = $family_display_onelevelabove_arr;
	$multipleloop_values['famdisplay_oneabove2_loop'] = $family_display_onelevelabove_arr;
	
	$multipleloop_values['famdisplay_twoabove1_loop'] = $family_display_twolevelabove_arr;
	$multipleloop_values['famdisplay_twoabove2_loop'] = $family_display_twolevelabove_arr;
	
if($fEmpl_id != '')
	{
		$returncontent = preg_replace("/<{visiblecheck_start}>(.*?)<{visiblecheck_end}>/s","",$returncontent);
	}
else
	{
		$returncontent = preg_replace("/<{visiblecheck_(.*?)}>/s","",$returncontent);
	}


	$returncontent	= $common->multipleloop_replace($db_object,$returncontent,$multipleloop_values,$sel_values);
	$values[uid]=$fEmpl_id;
	$returncontent 	= $common->direct_replace($db_object,$returncontent,$values);
	echo $returncontent;	
	
	}



function save_data($db_object,$common,$post_var,$user_id)
{


	while(list($kk,$vv) = @each($post_var))
	{
		$$kk = $vv;
	
	}

	$career_goals = $common->prefix_table('career_goals');

//DELETE THE DATAS IF THE USER HAS ALREADY ENTERED SOME DATAS...
	$mysql = "delete from $career_goals where user_id = '$id_of_user'";
	$db_object->insert($mysql);

	$mysql = "insert into $career_goals set 
			user_id='$id_of_user', 
			interest = 'lot',
			onelevel_low = $low1level_lot,
			same_level = $samelevel_lot,
			onelevel_up = $above1level_lot,
			twolevel_up = $above2level_lot";
	$db_object->insert($mysql);

	$mysql = "insert into $career_goals set 
				user_id='$id_of_user', 
				interest = 'some',
				onelevel_low = $low1level_some,
				same_level = $samelevel_some,
				onelevel_up = $above1level_some,
				twolevel_up = $above2level_some";
	$db_object->insert($mysql);	

}


}
$obj = new careergoals;


if($fSave)
{

$obj->save_data($db_object,$common,$post_var,$user_id);
$message = $error_msg['cCareergoalsupdated'];
echo $message;
}

$obj->show_screen($db_object,$common,$post_var,$user_id,$fEmpl_id);
include_once('footer.php');
?>
