<?php
include("../session.php");
include("header.php");
class Careeralert
{

function alert_display($common,$db_object,$user_id)
{
$path=$common->path;
$xFile=$path."templates/career/career_alerts.html";
$filecontent=$common->return_file_content($db_object,$xFile);
$value=array();

	$user_table=$common->prefix_table("user_table");
	$ratergroup_table = $common->prefix_table('rater_group');
	$otherraters_table = $common->prefix_table('other_raters');
	$tech_references = $common->prefix_table('tech_references');
	$appraisal  = $common->prefix_table('appraisal');
		
	
//if there is no alert for the user then nullify the link  "ALERT FOR MULTIRATER ASSESSMENT"...
//check for interpersonal rating alloted by admin....
	
	$mysql = "select test_mode from $appraisal where user_id = '$user_id' and test_type = 'i'";
	$test_inter_arr = $db_object->get_a_line($mysql);
	
	$test_mode_inter = $test_inter_arr['test_mode'];
	
	$mysql = "select test_mode from $appraisal where user_id = '$user_id' and test_type = 't'";
	$test_tech_arr = $db_object->get_a_line($mysql);
	
	$test_mode_tech = $test_tech_arr['test_mode'];
	
	if(($test_mode_inter == 360) || ($test_mode_tech == 360))
	{
		$filecontent = preg_replace("/<{multirater_(.*?)}>/s","",$filecontent);			

	}
	else
	{
		$filecontent=preg_replace("/<{multirater_start}>(.*?)<{multirater_end}>/s","",$filecontent);					
	}
	
	
	if(($test_mode_inter == 'Test') || ($test_mode_tech == 'Test'))
	{
		$filecontent = preg_replace("/<{testdisplay_(.*?)}>/s","",$filecontent);			

	}
	else
	{
		$filecontent=preg_replace("/<{testdisplay_start}>(.*?)<{testdisplay_end}>/s","",$filecontent);					
	}
//check for technical rating alloted by admin....
	

/*
	if($file)
	{
		$filecontent=preg_replace("/<{multirater_start}>(.*?)<{multirater_end}>/s","",$filecontent);			
	}
	else
	{
		$filecontent = preg_replace("/<{multirater_(.*?)}>/s","",$filecontent);		
	}

*/

//Check for any alerts for rating others...
//if no alerts are there for a person, then nullify the tag "ALERT FOR RATING OTHERS"...

//INTERPERSONAL...

$mysql = "select rater_id from $otherraters_table where rater_userid = '$user_id' and status = 'a'";
$ratingalert_arr =$db_object->get_single_column($mysql);

//TECHNICAL...

$mysql = "select ref_id from $tech_references where ref_userid = '$user_id' and status = 'a'";
$ratingtech_arr = $db_object->get_single_column($mysql);
    

	if(($ratingalert_arr == '') && ($ratingtech_arr == ''))
	{
	$filecontent=preg_replace("/<{alertforrating_start}>(.*?)<{alertforrating_end}>/s","",$filecontent);			
	}
	else
	{
	$filecontent = preg_replace("/<{alertforrating_(.*?)}>/s","",$filecontent);			
	}

    
	$selqry="select user_id from $user_table where admin_id='$user_id' limit 0,1";
	$user_is_admin=$db_object->get_a_line($selqry);
	$usersworker=$user_is_admin["user_id"];
	
	$user_is_admin=$common->is_admin($db_object,$user_id);

	

		if(!$user_is_admin)
		{
		$filecontent=preg_replace("/<{only_for_admins_area}>(.*?)<{only_for_admins_area}>/s","",$filecontent);
		}
		else
		{

		preg_match("/<{only_for_admins_area}>(.*?)<{only_for_admins_area}>/s",$filecontent,$mat);
		$replace=$mat[0];
		$replace=preg_replace("/<{(.*?)}>/s","",$replace);
		$filecontent=preg_replace("/<{only_for_admins_area}>(.*?)<{only_for_admins_area}>/s",$replace,$filecontent);
	
		}

$assign_test_table=$common->prefix_table("assign_test_builder");
$assign_skill_table=$common->prefix_table("assign_tech_skill_builder");

$selqry="select user_id from $assign_test_table where user_id='$user_id'";
$useridxistsa=$db_object->get_a_line($selqry);


//echo $user_id;
if($useridxistsa["user_id"]=="")
{
	$filecontent=preg_replace("/<{test_builderalertstart}>(.*?)<{test_builderalertend}>/s","",$filecontent);
		
	
}
$selqry="select user_id from $assign_skill_table where user_id='$user_id'";
$useridxistsb=$db_object->get_a_line($selqry);

if($useridxistsb["user_id"]=="")
{

	$filecontent=preg_replace("/<{skill_builderalertstart}>(.*?)<{skill_builderalertend}>/s","",$filecontent);
	
}

$filecontent=preg_replace("/<{test_builderalert(.*?)}>/s","",$filecontent);
$filecontent=preg_replace("/<{skill_builderalert(.*?)}>/s","",$filecontent);
		
	$filecontent=$common->direct_replace($db_object,$filecontent,$value);
	echo $filecontent;

}

}
$crobj= new Careeralert;
$crobj->alert_display($common,$db_object,$user_id);

include("footer.php");
?>