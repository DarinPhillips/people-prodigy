<?php
/*---------------------------------------------
SCRIPT:emp_without_iskill.php
AUTHOR:info@chrisranjana.com	
UPDATED:6th Jan

DESCRIPTION:
This script displays the employees without any interpersonal skills
---------------------------------------------*/

include('../session.php');
include('header.php');

class emp_WithoutSkill
{
function show_screen($db_object,$common,$post_var,$default,$user_id)
	{
	$xPath=$common->path;
	$returncontent=$xPath."/templates/career/emp_without_tskill.html";
	$returncontent=$common->return_file_content($db_object,$returncontent);
		
	$user_table  	= $common->prefix_table('user_table');
	$skills 	= $common->prefix_table('skills');
	
	$other_raters_tech = $common->prefix_table('other_raters_tech');
	
	$mysql = "select skill_id from $skills";
	$skills_arr = $db_object->get_single_column($mysql);
	$skills_all = @implode("','",$skills_arr);
	
	
	if($user_id != '1')
	{
	$mysql = "select user_id from $user_table where admin_id = '$user_id'";
	}
	if($user_id == '1')
	{
	$mysql = "select user_id from $user_table where user_id <> '1'";
	}
	
	$user_underadmin_arr = $db_object->get_single_column($mysql);
	
//CHECKING IF A USER DOES NOT HAVE EVEN ONE INTERPERSONAL SKILL WITH THEM...
	
 	for($i=0;$i<count($user_underadmin_arr);$i++)
	{
		$user_underadmin = $user_underadmin_arr[$i];
		//$mysql = "select distinct(rated_user) as user_rated from $textqsort_rating where skill_id in ('$skills_all') and rated_user = '$user_underadmin'";
		$mysql = "select distinct(rated_user)as user_rated from $other_raters_tech where skill_id in ('$skills_all') and rated_user = '$user_underadmin'";
		//echo $mysql;exit;
		$user_skill_arr = $db_object->get_a_line($mysql);
		$user_skill = $user_skill_arr['user_rated'];
		if($user_skill == '')
		{
		
			$mysql = "select distinct(user_id) as userid from user_tests,user_test_grade 
					where user_tests.user_testid = user_test_grade.user_testid
					and test_type = 't'
					and skill_id in ('$skills_all') and user_id = '$user_underadmin'";
			$test_arr = $db_object->get_a_line($mysql);
			$test_user = $test_arr['userid'];
			if($test_user == '')
				{
				$user_withoutiskill[] = $user_underadmin;
				}
			
		} 
	}
	
//DISPLAYING THE USERS WITHOUT EVEN ONE TECHNICAL SKILL WITH THEM (HAVENT' RATED YET)

	preg_match("/<{empwithoutskill_loopstart}>(.*?)<{empwithoutskill_loopend}>/s",$returncontent,$matchold);
	$matchnew = $matchold[1];
	$str = '';	

	for($j=0;$j<count($user_withoutiskill);$j++)
	{
		$user_dis = $user_withoutiskill[$j];
		$mysql = "select username,email from $user_table where user_id = '$user_dis'";
		$user_arr = $db_object->get_a_line($mysql);
		$user_display = $user_arr['username'];
		$email_user = $user_arr['email'];
		$boss_dis = $common->immediate_boss($db_object,$user_dis);
		$mysql = "select username,email from $user_table where user_id = '$boss_dis'";
		$boss_arr = $db_object->get_a_line($mysql);
		$boss_display = $boss_arr['username'];
		$email_boss = $boss_arr['email'];

		$str .= preg_replace("/<{(.*?)}>/e","$$1",$matchnew); 
			
		
	}
	$returncontent = preg_replace("/<{empwithoutskill_loopstart}>(.*?)<{empwithoutskill_loopend}>/s",$str,$returncontent);

	$returncontent = $common->direct_replace($db_object,$returncontent,$values);
	echo $returncontent;
	
	}
}

$obj = new emp_WithoutSkill;

$obj->show_screen($db_object,$common,$post_var,$default,$user_id);

include('footer.php');
?>
