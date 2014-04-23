<?php
/*---------------------------------------------
SCRIPT:careergoals_usage.php
AUTHOR:info@chrisranjana.com	
UPDATED:7th Jan

DESCRIPTION:
This script shows the career goals usage.

---------------------------------------------*/
include("../session.php");
include("header.php");

class careergoalsUsage
{
function show_screen($db_object,$common,$user_id,$default,$post_var)
	{
		while(list($kk,$vv) = @each($post_var))
		{
		$$kk = $vv;
		}
	$xPath		= $common->path;
	$returncontent	= $xPath."/templates/career/careergoals_usage.html";
	$returncontent	= $common->return_file_content($db_object,$returncontent);
	
	$family 	= $common->prefix_table('family');
	$career_goals 	= $common->prefix_table('career_goals');

	
	$mysql = "select family_id,family_name from $family";
	$family_arr = $db_object->get_rsltset($mysql);
	
	preg_match("/<{careergoals_loopstart}>(.*?)<{careergoals_loopend}>/s",$returncontent,$matchold);
	$matchnew = $matchold[1];
	for($i=0;$i<count($family_arr);$i++)
	{
		$family_name = $family_arr[$i]['family_name'];
		$family_id = $family_arr[$i]['family_id'];
		$mysql = "select count(*) as cnt_fam from career_goals where 
			(onelevel_low = '$family_id' or same_level = '$family_id' or onelevel_up = '$family_id' or twolevel_up = '$family_id')";
		$fam_cnt_arr = $db_object->get_a_line($mysql);
		$fam_cnt = $fam_cnt_arr['cnt_fam'];
		
		
		$str .= preg_replace("/<{(.*?)}>/e","$$1",$matchnew);
	}
	$returncontent = preg_replace("/<{careergoals_loopstart}>(.*?)<{careergoals_loopend}>/s",$str,$returncontent);

	
	
	$returncontent = $common->direct_replace($db_object,$returncontent,$values);	
	echo $returncontent;
	}
}
$obj = new careergoalsUsage;
$obj->show_screen($db_object,$common,$user_id,$default,$post_var);
include_once('footer.php');
?>
