<?php

/*---------------------------------------------
SCRIPT:test_builder_questions.php
AUTHOR:info@chrisranjana.com	
UPDATED:17th Sept

DESCRIPTION:
This script shows the screen for entering the Test Questions & answers.

---------------------------------------------*/
include("../session.php");
include("header.php");




class Testquestions
{
function displayQuestions($db_object,$common,$post_var,$user_id,$test_id,$default,$group_id,$error_msg)
	{

	$q_cnt=$common->answerCount($db_object);

	
	while(list($kk,$vv)=@each($post_var))
		{
		$$kk=$vv;
		}
	
	$tests_table=$common->prefix_table("temp_tests");
	$tablename=$common->prefix_table("skills");
	$assign_test_builder=$common->prefix_table("assign_test_builder");
	
	$mysql = "select test_name from $tests_table where test_id='$test_id'";
	$arr = $db_object->get_a_line($mysql);
	
	$fTestname=$arr["test_name"];

//===============Check if any hacker is intruding...
	
$assign_test_builder = $common->prefix_table('assign_test_builder');

if($user_id != 1)
{
	$mysql = "select group_id from $assign_test_builder where user_id='$user_id'";
	//echo $mysql;
	$group_arr = $db_object->get_single_column($mysql);

	for($l=0;$l<count($group_arr);$l++)
	{
		$check_group = $group_arr[$i];
		//echo "group id is $group_id<br>";
	
		$check = @in_array("$group_id",$group_arr);
				if(!$check)
				{
					$message = $error_msg['cAlertHackertestbuilder'];
					echo $message;
					exit;
			
				}
	}
}
//===============

	$xPath=$common->path;
	$xTemplate=$xPath."/templates/career/test_builder_questions.html";
	$returncontent=$common->return_file_content($db_object,$xTemplate);
	
	$values["directreplace"]["fTestname"]=$fTestname;
	$values["directreplace"]["test_id"]=$test_id;
	$values["directreplace"]["group_id"]=$group_id;
	
	//$returncontent = preg_replace("/{{(.*?)}}/e","$$1",$returncontent);
	$returncontent = $common->direct_replace($db_object,$returncontent,$values);

	if($group_id=="")
	{
		$selqry="select group_id from $assign_test_builder where user_id='$user_id'";
		$grp_id=$db_object->get_a_line($selqry);
		$group_id=$grp_id["group_id"];
		
	}
	

if($user_id != 1)
{
	
$mysql = "select $assign_test_builder.skill_id as skill_id,$tablename.skill_name as skill_name from $assign_test_builder,$tablename where $assign_test_builder.skill_id=$tablename.skill_id and $assign_test_builder.user_id='$user_id' and  $assign_test_builder.group_id ='$group_id' ";
//echo $mysql;
$arr = $db_object->get_rsltset($mysql);
	
}
else
{
	$mysql = "select skill_id,skill_name from $tablename where skill_type = 'i' ";
	$arr = $db_object->get_rsltset($mysql);
	
}

	if($arr !='')
	{
	$arr	= $common->conv_2Darray($db_object,$arr);
	$returncontent = $common->pulldown_replace($db_object,'<{skill_loopstart}>','<{skill_loopend}>',$returncontent,$arr,"");
	}
	$fields = $common->return_fields($db_object,$tests_table);
	$mysql="select $fields from $tests_table where test_id='$test_id'";
	$db_data=$db_object->get_a_line($mysql);
	
	

	//in $arr... all the skill names are present in 1D array...

	$tot_question = $db_data["tot_question"];	
	$fTestname = $db_data["test_name"];	
	
	//Questions Generated...
	

 	preg_match("/<{quest_loopstart}>(.*?)<{quest_loopend}>/s",$returncontent,$qmatch);
	$newqmatch = $qmatch[1];
	
	$str = "";	
	
		preg_match("/<{ans_loopstart}>(.*?)<{ans_loopend}>/s",$returncontent,$amatch);
		
		
		for($i=1;$i<=$tot_question;$i++)
		{
			
		
		//Answers Generated...
	
		
		$newamatch = $amatch[1];

			$str1 = "";
			
			for($j=0;$j<$q_cnt;$j++)
			{
				$fNo = $i;
				
				$fNo1 = $j;	
				
				$ans = preg_replace("/<{(.*?)}>/e","$$1",$newamatch);
				$str1 .= $ans;
			}
			
			
			$finalmatch = preg_replace("/<{ans_loopstart}>(.*?)<{ans_loopend}>/s",$str1,$newqmatch);
			
			$questions = preg_replace("/<{(.*?)}>/e","$$1",$finalmatch);
	
			$str .= $questions;

		}




		$returncontent = preg_replace("/<{quest_loopstart}>(.*?)<{quest_loopend}>/s",$str,$returncontent);
		
		
	
		echo $returncontent;

	}


}

$obj = new Testquestions;

 

$obj->displayQuestions($db_object,$common,$post_var,$user_id,$test_id,$default,$group_id,$error_msg);

include("footer.php");

?>