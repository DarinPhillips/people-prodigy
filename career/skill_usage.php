<?php
/*---------------------------------------------
SCRIPT:skill_usage.php
AUTHOR:info@chrisranjana.com	
UPDATED:6th Jan

DESCRIPTION:
This script displays the usage of skills

---------------------------------------------*/
include('../session.php');
include('header.php');

class skillUsage
{
function show_screen1($db_object,$common,$user_id,$default)	
	{
		$xPath=$common->path;
		$returncontent=$xPath."/templates/career/skill_usage.html";
		$returncontent=$common->return_file_content($db_object,$returncontent);
		
		$skills = $common->prefix_table('skills');
			
		preg_match("/<{skill_loopstart}>(.*?)<{skill_loopend}>/s",$returncontent,$matchskillold);
		$matchskill = $matchskillold[1];
		
		$mysql = "select skill_id,skill_name from $skills where skill_name<>''";
		$skills_arr = $db_object->get_rsltset($mysql);
		$value['skill_loop'] = $skills_arr;
		$returncontent = $common->multipleloop_replace($db_object,$returncontent,$value,'');


		$returncontent = $common->direct_replace($db_object,$returncontent,$values);
		
		echo $returncontent;
	}
}
$obj = new skillUsage;
$obj->show_screen1($db_object,$common,$user_id,$default);

include('footer.php');
?>
