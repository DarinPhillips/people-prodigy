<?php
/*---------------------------------------------
SCRIPT:skill_usage_employee.php
AUTHOR:info@chrisranjana.com	
UPDATED:6th Jan

DESCRIPTION:
This script displays the no of clients with this skill.

---------------------------------------------*/
include('../session.php');
include('header.php');

class skillUsageEmployee
{
function show_employees($db_object,$common,$post_var,$user_id,$default)
{
	while(list($kk,$vv) = @each($post_var))
	{
		$$kk = $vv;
	}

	$xPath=$common->path;
	$returncontent=$xPath."/templates/career/skill_usage_employee.html";
	$returncontent=$common->return_file_content($db_object,$returncontent);
		
	$textqsort_rating  	= $common->prefix_table('textqsort_rating');
	$other_raters_tech 	= $common->prefix_table('other_raters_tech');
	$skills		   	= $common->prefix_table('skills');
	$user_table		= $common->prefix_table('user_table');
	
	//print_r($post_var);	
	//print_r($fSkills);
	//$fSkills = @array	

//SELECT THE USERS WHO FALL UNDER THE ADMINISTRATION OF THIS USER...

	if($user_id != '1')
	{
	$mysql = "select user_id from $user_table where admin_id = '$user_id'";
	}
	if($user_id == '1')
	{
	$mysql = "select user_id from $user_table where user_id <> '1'";
	}
	
	$user_underadmin_arr = $db_object->get_single_column($mysql);
	
	$user_underadmin_all = @implode("','",$user_underadmin_arr);		

	$total_clients = @count($user_underadmin_arr);
		
	
	preg_match("/<{skilldisplay_loopstart}>(.*?)<{skilldisplay_loopend}>/s",$returncontent,$matchold);
	$matchnew = $matchold[1];
	if($user_underadmin_all != '')
	{	 
	$subqry = "and rated_user in ('$user_underadmin_all')";
	}

	$str = '';
	for($i=0;$i<count($fSkills);$i++)
	{
		
		$s_id = $fSkills[$i];
		if($s_id != 0)
		{
		$mysql = "select skill_type from $skills where skill_id = '$s_id'";
		$skilltype_arr = $db_object->get_a_line($mysql);
		$skilltype = $skilltype_arr['skill_type'];
		$skill_name = $skilltype_arr['skill_name'];

		if($skilltype == 'i')
		{
		$mysql = "select count(distinct(rated_user)) as user_rated from $textqsort_rating where skill_id = '$s_id' $subqry";

		}
		if($skilltype == 't')
		{
		$mysql = "select count(distinct(rated_user)) as user_rated from $other_raters_tech where skill_id = '$s_id' $subqry";
		}

		$no_of_clients_arr 	= $db_object->get_a_line($mysql);
		$no_of_clients 		= $no_of_clients_arr['user_rated'];
		$unsorted_arr[$s_id] 	= $no_of_clients;
		
		}

	}
	
	@arsort($unsorted_arr);

//DISPLAYING IN DESCENDING ORDER...

	while(list($key,$val) = @each($unsorted_arr))	
	{	
		$skillid = $key;
		
		$mysql = "select skill_name from $skills where skill_id = '$skillid'";
		$skillname_arr = $db_object->get_a_line($mysql);
		$skill_name = $skillname_arr['skill_name'];
		$no_of_clients = $val;
		$percent_clients = ($no_of_clients / $total_clients) * 100;

		$str .= preg_replace("/<{(.*?)}>/e","$$1",$matchnew);
	}
	
	
	$returncontent = preg_replace("/<{skilldisplay_loopstart}>(.*?)<{skilldisplay_loopend}>/s",$str,$returncontent);
		

	$returncontent = $common->direct_replace($db_object,$returncontent,$values);
	echo $returncontent;
}
		
}
$obj = new skillUsageEmployee;

$obj->show_employees($db_object,$common,$post_var,$user_id,$default);


include('footer.php');

?>
