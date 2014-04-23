<?php
/*---------------------------------------------
SCRIPT:appraisal.php
AUTHOR:info@chrisranjana.com	
UPDATED:17th Sept

DESCRIPTION:
This script displays all the information for the tests.

---------------------------------------------*/
include("../session.php");
include_once("header.php");

class appraisal
{
	function show_appraisal_screen($db_object,$common,$post_var,$gbl_test_mode,$gbl_skill_type,$default,$user_id)
	{
		while(list($kk,$vv)=@each($post_var))
		{

		$$kk=$vv;
	
		}
	
		$xPath=$common->path;
		$xTemplate=$xPath."/templates/career/appraisal.html";
		$returncontent=$common->return_file_content($db_object,$xTemplate);
		
		$user_table = $common->prefix_table("user_table");
		$position_table = $common->prefix_table("position");
		$skills = $common->prefix_table('skills');
		$questions = $common->prefix_table('questions');
		$skills_for_rating = $common->prefix_table('skills_for_rating');

//============================================
//Nov 20
//the select menu that is used to select the skills for rating 
//are to be created dynamically according to the test mode selected ...

//for tests....
		$mysql = "select distinct(skill_id) from $questions";
		$skills_with_test_arr = $db_object->get_single_column($mysql);
		$skills_full = @implode("','",$skills_with_test_arr);
		
		$mysql = "select skill_id, skill_name from $skills where skill_id in ('$skills_full') and skill_type='i'";
 
		$skills_toshow_arr = $db_object->get_rsltset($mysql);
			
		for($i=0;$i<count($skills_toshow_arr);$i++)
		{
			$name_of_skills[] = $skills_toshow_arr[$i]['skill_name'];
			$id_of_skills[] = $skills_toshow_arr[$i]['skill_id'];
			
		}
		
		$is_name = @implode("','",$name_of_skills);
		$is_id 	 = @implode("','",$id_of_skills);

		$arr .= "itestid = Array ('$is_id');\n";
		
		$arr .= "itestname = Array ('$is_name');\n";
		
		$returncontent=preg_replace("/<{loopstart}>(.*?)<{loopend}>/s",$arr,$returncontent);
		
		$mysql = "select skill_id , skill_name from $skills where skill_type='i'";
		$skillsfull_arr = $db_object->get_rsltset($mysql);
		
		for($i=0;$i<count($skillsfull_arr);$i++)
		{
			$name_of_skills1[] = $skillsfull_arr[$i]['skill_name'];
			$id_of_skills1[] = $skillsfull_arr[$i]['skill_id'];
			
		}
		
		$is_name360 = @implode("','",$name_of_skills1);
		$is_id360 	 = @implode("','",$id_of_skills1);

		$arr360 .= "itestid360 = Array ('$is_id360');\n";
		
		$arr360 .= "itestname360 = Array ('$is_name360');\n";
		
		$returncontent=preg_replace("/<{360loopstart}>(.*?)<{360loopend}>/s",$arr360,$returncontent);
		
		$mysql = "select skill_id, skill_name from $skills where skill_id in ('$skills_full') and skill_type='t'";
		//echo $mysql;
		$tskills_toshow_arr = $db_object->get_rsltset($mysql);

		for($i=0;$i<count($tskills_toshow_arr);$i++)
		{
			$name_of_tskills[] = $tskills_toshow_arr[$i]['skill_name'];
			$id_of_tskills[] = $tskills_toshow_arr[$i]['skill_id'];
			
		}

		$ts_name = @implode("','",$name_of_tskills);
		$ts_id 	 = @implode("','",$id_of_tskills);

		$arrttest .= "ttestid = Array ('$ts_id');\n";
		
		$arrttest .= "ttestname = Array ('$ts_name');\n";
		
		$returncontent=preg_replace("/<{techtloopstart}>(.*?)<{techtloopend}>/s",$arrttest,$returncontent);

		$mysql = "select skill_id , skill_name from $skills where skill_type='t'";
		$skillsfullt_arr = $db_object->get_rsltset($mysql);
		
		for($i=0;$i<count($skillsfullt_arr);$i++)
		{
			$name_of_tskills1[] = $skillsfullt_arr[$i]['skill_name'];
			$id_of_tskills1[] = $skillsfullt_arr[$i]['skill_id'];
			
		}
		
		$ts_name360 = @implode("','",$name_of_tskills1);
		$ts_id360 	 = @implode("','",$id_of_tskills1);

		$arrt360 .= "ttestid360 = Array ('$ts_id360');\n";
		
		$arrt360 .= "ttestname360 = Array ('$ts_name360');\n";
		
		$returncontent=preg_replace("/<{tech360loopstart}>(.*?)<{tech360loopend}>/s",$arrt360,$returncontent);
		
		
//============================================

//THE APPRAISAL ARE ASSIGNED TO EMPLOYEES BY ADMINS...
		
		//===  $loopname = "user_loop";
		//===  $returncontent = $common->show_Username($db_object,$returncontent,$loopname);

		if($user_id != '1')
		{
		$mysql = "select user_id,username from $user_table where admin_id ='$user_id'";
		$users_to_show_arr = $db_object->get_rsltset($mysql);

			for($i=0;$i<count($users_to_show_arr);$i++)
			{
				$user_underadmin = $users_to_show_arr[$i]['user_id'];
				$check_if_boss = $common->is_boss($db_object,$user_underadmin);
				if($check_if_boss)
				{
					$boss_underadmin_arr[] = $user_underadmin;
				}
			}

		}
		if($user_id == '1')
		{
		$mysql = "select user_id,username from $user_table where user_id <> '1'";
		$users_to_show_arr = $db_object->get_rsltset($mysql);

			for($i=0;$i<count($users_to_show_arr);$i++)
			{
				$user_underadmin = $users_to_show_arr[$i]['user_id'];
				$check_if_boss = $common->is_boss($db_object,$user_underadmin);
				if($check_if_boss)
				{
					$boss_underadmin_arr[] = $user_underadmin;
				}
			}
	
		}

		
		preg_match("/<{user_loopstart}>(.*?)<{user_loopend}>/s",$returncontent,$useroldmatch);
		$usernewmatch = $useroldmatch[1];
			
		for($i=0;$i<count($users_to_show_arr);$i++)
		{
			$user_id = $users_to_show_arr[$i]['user_id'];
			$username = $users_to_show_arr[$i]['username'];
			$str_users .= preg_replace("/<{(.*?)}>/e","$$1",$usernewmatch);
		}
		
		$returncontent = preg_replace("/<{user_loopstart}>(.*?)<{user_loopend}>/s",$str_users,$returncontent);
		
		//print_r($boss_underadmin_arr);
		
		preg_match("/<{boss_loopstart}>(.*?)<{boss_loopend}>/s",$returncontent,$bossoldmatch);
		$bossnewmatch = $bossoldmatch[1];
		
		for($j=0;$j<count($boss_underadmin_arr);$j++)
		{
			$boss_id = $boss_underadmin_arr[$j];
			$mysql = "select username from $user_table where user_id = '$boss_id'";
			$bossname_arr = $db_object->get_a_line($mysql);
			$username = $bossname_arr['username'];
			$strboss .= preg_replace("/<{(.*?)}>/e","$$1",$bossnewmatch);
		}
		$returncontent = preg_replace("/<{boss_loopstart}>(.*?)<{boss_loopend}>/s",$strboss,$returncontent);
		
/*--------------
		$mysql = "select user_id as boss_id,username from $user_table";
		$boss_arr = $db_object->get_rsltset($mysql);
	
		
		$j = 0;
		
		for($i=0;$i<count($boss_arr);$i++)
		{
			$check_if_boss = $boss_arr[$i]['boss_id'];
			
			$boss_check = $common->is_boss($db_object,$check_if_boss);
			
			if($boss_check)
			{
				
			
				$boss_arr1[$j] = $boss_arr[$i];
				$j++;
			}
		}
				
			for($i=0;$i<count($boss_arr1);$i++)
			{
			$user_id = $boss_arr1[$i]['boss_id'];
			$boss_arr1[$i]['username'] = $common->name_display($db_object,$user_id);

			}
		
		$values['boss_loop'] = $boss_arr1;
-------------*/
 
	preg_match("/<{skilltype_loopstart}>(.*?)<{skilltype_loopend}>/s",$returncontent,$skillmatch);
	$newskillmatch = $skillmatch[1];

 
	@reset($gbl_skill_type);
	for($j=0;$j<count($gbl_skill_type);$j++)
	{
				 
		while(list($skey,$sval) = @each($gbl_skill_type))
		{
			
			$type = $skey;
			$skill_name = $sval;

//DISPLAY OF SKILLS TO CHOOSE TO RATE...

		$mysql = "select skill_id,skill_name from $skills where skill_type='$type'";
	 
		$skills_arr = $db_object->get_rsltset($mysql);

	 
	$newskillmatch1	= $common->multipleloop_replace($db_object,$newskillmatch,$value,'');
	 	
		$str1 .= preg_replace("/<{(.*?)}>/e","$$1",$newskillmatch1);
				
		}
		
	
	}

	
	$returncontent = preg_replace("/<{skilltype_loopstart}>(.*?)<{skilltype_loopend}>/s",$str1,$returncontent);
	
	$returncontent	= $common->multipleloop_replace($db_object,$returncontent,$values,'');
		
	$returncontent = $common->direct_replace($db_object,$returncontent,$values);
		
	echo $returncontent;
	}
	
	
}

$obj = new appraisal;

$obj->show_appraisal_screen($db_object,$common,$post_var,$gbl_test_mode,$gbl_skill_type,$default,$user_id);

include_once("footer.php");
?>
