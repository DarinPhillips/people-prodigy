<?php

/*---------------------------------------------
SCRIPT:technical_questions.php
AUTHOR:info@chrisranjana.com	
UPDATED:4th Sept

DESCRIPTION:


---------------------------------------------*/
include("../session.php");
include("header.php");

$xPath=$common->path;


class Technicalquestions
{
function displayQuestions($db_object,$common,$post_var,$user_id,$test_id,$default)
	{

	$q_cnt=$common->answerCount($db_object);

	while(list($kk,$vv)=@each($post_var))
		{
		$$kk=$vv;
		}
	
	$tests_table=$common->prefix_table("temp_tests");
	$skill_table=$common->prefix_table("skills");
	$quest_table=$common->prefix_table("temp_questions");	
	$ans_table = $common->prefix_table("temp_answers");


	
	$mysql = "select test_name from $tests_table where test_id='$test_id'";
	$arr = $db_object->get_a_line($mysql);
	
	$fTestname=$arr["test_name"];


	$xPath=$common->path;
	$xTemplate=$xPath."/templates/career/technical_questions.html";
	$returncontent=$common->return_file_content($db_object,$xTemplate);
	

	//$returncontent = preg_replace("/{{(.*?)}}/e","$$1",$returncontent);
	
	$values["directreplace"]["fTestname"]=$fTestname;
	$values["directreplace"]["test_id"]=$test_id;
	//$values["directreplace"]["test_skill_id"]=$test_skill_id;
	
	$returncontent = $common->direct_replace($db_object,$returncontent,$values);
	

if($user_id != 1)
{
//***TESTING IN PROCESS*** $mysql = "select skill_id,skill_name from $skill_table where skill_type = 't'";	

	$mysql = "select skill_id,skill_name from $skill_table where skill_type = 't' and skill_id = '$test_skill_id'";
	$arr = $db_object->get_rsltset($mysql);

}
else
{
	$mysql = "select skill_id,skill_name from $skill_table where skill_type = 't'";	
	$arr = $db_object->get_rsltset($mysql);
}
	$mysql = "select skill_id from $quest_table where test_id='$test_id' order by q_id";
	$skillarr = $db_object->get_a_line($mysql);
	$sel_skill_id = $skillarr["skill_id"];
	
	
	
	$arr	= $common->conv_2Darray($db_object,$arr);
	
	$test_table=$common->prefix_table("temp_tests");

	$returncontent = $common->pulldown_replace($db_object,'<{skill_loopstart}>','<{skill_loopend}>',$returncontent,$arr,$sel_skill_id);
	
	$fields = $common->return_fields($db_object,$tests_table);
	$mysql="select $fields from $tests_table where test_id='$test_id'";
	
	$db_data=$db_object->get_a_line($mysql);
	
	

	//in $arr... all the skill names are present in 1D array...

	$tot_question = $db_data["tot_question"];	
	$fTestname = $db_data["test_name"];	

	//Questions Generated...
	

 	preg_match("/<{quest_loopstart}>(.*?)<{quest_loopend}>/s",$returncontent,$qmatch);
	$newqmatch = $qmatch[1];
	
	
	
		preg_match("/<{ans_loopstart}>(.*?)<{ans_loopend}>/s",$returncontent,$amatch);
		
		$fields = $common->return_fields($db_object,$quest_table);
		$mysql = "select $fields from $quest_table where test_id='$test_id' order by q_id";
		$quest_arr = $db_object->get_rsltset($mysql);
		
		$str = "";	
				
		for($i=0;$i<$tot_question;$i++)
		{
			$question = $quest_arr[$i]["question"];
			
			
			$score = $quest_arr[$i]["score"];
			$q_id = $quest_arr[$i]["q_id"];
		
			$newamatch = $amatch[1];

			$str1 = "";
			
			
			for($j=0;$j<$q_cnt;$j++)
			{
				$fNo = $i+1;
				
				$fNo1 = $j;	
				
				$fields = $common->return_fields($db_object,$ans_table);
				$mysql = "select $fields from $ans_table where q_id='$q_id' order by ans_id";
				
					$unapp_ans_arr = $db_object->get_rsltset($mysql);
					
				
					$oldanswer = $unapp_ans_arr[$j]["answer"];
					$status = $unapp_ans_arr[$j]["status"];
					$weightage = $unapp_ans_arr[$j]["weightage"];
			
					if($oldanswer !="")
						{
						$answer = $oldanswer;
						$answer=str_replace("$","&#36;",$answer);
						}
					else 
						{
						$answer = "";
						}
					if($status == "r")
						{
						$check = "checked";
						}
					else
						{
						$check = "";
						}
				
				
				
				
				
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

$obj = new Technicalquestions;
 
	$obj->displayQuestions($db_object,$common,$post_var,$user_id,$test_id,$default);

include("footer.php");

?>