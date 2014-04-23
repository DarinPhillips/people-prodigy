<?php
/*---------------------------------------------
SCRIPT:careergoals_detail.php
AUTHOR:info@chrisranjana.com	
UPDATED:7th Jan

DESCRIPTION:
This script shows the career goals usage.

---------------------------------------------*/
include("../session.php");
include("header.php");

class careergoalsDetails
{
function show_details($db_object,$common,$post_var,$user_id,$default,$error_msg)
	{
		while(list($kk,$vv) = @each($post_var))	
		{
			$$kk = $vv;	
		}
	$xPath		= $common->path;
	$returncontent	= $xPath."/templates/career/careergoals_detail.html";
	$returncontent	= $common->return_file_content($db_object,$returncontent);
	
	$family = $common->prefix_table('family');
	$career_goals = $common->prefix_table('career_goals');
	$user_table = $common->prefix_table('user_table');
	
	
	$mysql = "select family_name from $family where family_id = '$f_id'";
	$fam_name_arr = $db_object->get_a_line($mysql);
	$family_name = $fam_name_arr['family_name'];
	$values['family_name'] = $family_name;
	
	$mysql = "select  user_id  from $career_goals where 
		(onelevel_low = '$f_id' or same_level = '$f_id' or onelevel_up = '$f_id' or twolevel_up = '$f_id') and user_id<>'0'";
	$userwithfam_arr = $db_object->get_single_column($mysql);
	
	if(count($userwithfam_arr)==0)
	{
		echo $error_msg['cEmptyrecords'];
		
		include_once("footer.php");exit;
	}

	$fields_of_fam = $common->return_fields($db_object,$career_goals);
	$fields_of_fam_arr = @explode(",",$fields_of_fam);
	preg_match("/<{careergoaldetails_loopstart}>(.*?)<{careergoaldetails_loopend}>/s",$returncontent,$matchold);
	$matchnew = $matchold[1];
	
	
	for($i=0;$i<count($userwithfam_arr);$i++)
	{

		$userwithfam = $userwithfam_arr[$i];
		$mysql = "select username from $user_table where user_id = '$userwithfam'";
		$employeename_arr = $db_object->get_a_line($mysql);
		$employee = $employeename_arr['username'];
		
		
		$mysql = "select field('$f_id',$fields_of_fam) as fieldid from $career_goals where user_id = '$userwithfam' order by fieldid desc";

		$index_field_arr = $db_object->get_a_line($mysql);

		$field_id = $index_field_arr['fieldid'];

		
		$fieldname = $fields_of_fam_arr[$field_id-1];
		
		if($fieldname == 'onelevel_low')
		{
			$level = '-1';
		}
		if($fieldname == 'same_level')
		{
			$level = '0';
		}
		if($fieldname == 'onelevel_up')
		{
			$level = '+1';
		}
		if($fieldname == 'twolevel_up')
		{
			$level = '+2';
		}
		
		
		$str .= preg_replace("/<{(.*?)}>/e","$$1",$matchnew);
		
	}
	$returncontent = preg_replace("/<{careergoaldetails_loopstart}>(.*?)<{careergoaldetails_loopend}>/s",$str,$returncontent);

		
	
	$returncontent = $common->direct_replace($db_object,$returncontent,$values);	
	echo $returncontent;	

	}
}
$obj = new careergoalsDetails;
$obj->show_details($db_object,$common,$post_var,$user_id,$default,$error_msg);

include("footer.php");
?>
