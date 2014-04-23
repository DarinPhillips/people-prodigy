<?php

/*---------------------------------------------
SCRIPT:update_unapproved_test.php
AUTHOR:info@chrisranjana.com	
UPDATED:17th Sept

DESCRIPTION:
This script updates the test which is in the unapproved status

---------------------------------------------*/
include("../session.php");
include("header.php");

class Update_unapproved
{
	
function displayQuestions($db_object,$common,$post_var,$user_id,$test_id,$default)
	{

	$q_cnt=$common->answerCount($db_object);


	while(list($kk,$vv)=@each($post_var))
		{
		$$kk=$vv;
		}
	if($user_id == 1)
	{
		if($test_mode != "approve")
		{
	$tests_table=$common->prefix_table("tests");
	$skill_table=$common->prefix_table("skills");
	$quest_table=$common->prefix_table("questions");	
	$ans_table = $common->prefix_table("answers");
		}
		else
		{
	$values["directreplace"]["test_mode"]="approve";
	$tests_table=$common->prefix_table("unapproved_tests");
	$skill_table=$common->prefix_table("skills");
	$quest_table=$common->prefix_table("unapproved_questions");	
	$ans_table = $common->prefix_table("unapproved_answers");
			
		}
	}
	else
	{
	$tests_table=$common->prefix_table("unapproved_tests");
	$skill_table=$common->prefix_table("skills");
	$quest_table=$common->prefix_table("unapproved_questions");	
	$ans_table = $common->prefix_table("unapproved_answers");
	}
	
	$mysql = "select test_name from $tests_table where test_id='$test_id'";
	$arr = $db_object->get_a_line($mysql);
	
	
	$fTestname=$arr["test_name"];
	
	$values["directreplace"]["fTestname"]=$fTestname;
	$values["directreplace"]["test_id"]=$test_id;


//===============Check if any hacker is intruding...

if($group_id !='')
{
	$assign_test_builder = $common->prefix_table('assign_test_builder');

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
	$xTemplate=$xPath."/templates/career/update_unapproved_test.html";
	$returncontent=$common->return_file_content($db_object,$xTemplate);
	

	//$returncontent = preg_replace("/{{(.*?)}}/e","$$1",$returncontent);
	
	$returncontent = $common->direct_replace($db_object,$returncontent,$values);
	
	


	$mysql = "select skill_id from $quest_table where test_id='$test_id' order by q_id";
	$skillarr = $db_object->get_a_line($mysql);
	$sel_skill_id = $skillarr["skill_id"];
	
	if($user_id !=1)
	{
	$mysql = "select skill_id,skill_name from $skill_table where skill_type = 't' and skill_id='$sel_skill_id'";
	$arr = $db_object->get_rsltset($mysql);
	}
	else
	{
	$mysql = "select skill_id,skill_name from $skill_table where skill_type = 't'";
	$arr = $db_object->get_rsltset($mysql);
	}
	
	if($arr != '')
	{
	$arr	= $common->conv_2Darray($db_object,$arr);
	
	$returncontent = $common->pulldown_replace($db_object,'<{skill_loopstart}>','<{skill_loopend}>',$returncontent,$arr,$sel_skill_id);
	}
	$fields = $common->return_fields($db_object,$tests_table);
	
	$mysql="select $fields from $tests_table where test_id='$test_id'";
	//echo "$mysql<br>";
	$db_data=$db_object->get_a_line($mysql);
	

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
	
	
	function showTest_interskill($db_object,$common,$post_var,$user_id,$default)
	{

	while(list($kk,$vv)=@each($post_var))
		{
		$$kk=$vv;
			
		}
	
	$xPath=$common->path;
	$xTemplate = $xPath."/templates/career/store_unapproved.html";
	$returncontent=$common->return_file_content($db_object,$xTemplate);


if($user_id == 1)
{
	if($test_mode != "approve")
	{
	$tests_table=$common->prefix_table("tests");
	$tablename=$common->prefix_table("skills");
	$quest_table=$common->prefix_table("questions");	
	$ans_table = $common->prefix_table("answers");
$mysql="select test_name,test_type,tot_question,user_id	from $tests_table where test_id='$test_id'";

	$db_data=$db_object->get_a_line($mysql);

	
	}
	else
	{
	$tests_table=$common->prefix_table("unapproved_tests");
	$tablename=$common->prefix_table("skills");
	$quest_table=$common->prefix_table("unapproved_questions");	
	$ans_table = $common->prefix_table("unapproved_answers");
	$assign_test_builder = $common->prefix_table("assign_test_builder");
$mysql="select test_name,test_type,tot_question,user_id,group_id from $tests_table where test_id='$test_id'";

	$db_data=$db_object->get_a_line($mysql);

	}
}
else
{
	$tests_table=$common->prefix_table("unapproved_tests");
	$tablename=$common->prefix_table("skills");
	$quest_table=$common->prefix_table("unapproved_questions");	
	$ans_table = $common->prefix_table("unapproved_answers");
	$assign_test_builder = $common->prefix_table("assign_test_builder");
$mysql="select test_name,test_type,tot_question,user_id,group_id from $tests_table where test_id='$test_id'";

	$db_data=$db_object->get_a_line($mysql);

}
	
	

	$fTestname = $db_data["test_name"];
	$tot_question = $db_data["tot_question"];	
	$test_type = $db_data["test_type"];
	$group_id = $db_data["group_id"];
	
	//echo "test type is $test_type";
		
	$values["directreplace"]["fTestname"]=$fTestname;
	$values["directreplace"]["test_id"]=$test_id;
	$values["directreplace"]["group_id"] = $group_id;
	$values["directreplace"]["test_mode"]=$test_mode;
	
if(($user_id == 1) || ($test_mode == "approve"))
{

	$mysql = "select skill_id,skill_name from $tablename where skill_type = '$test_type'";

	$skill_arr = $db_object->get_rsltset($mysql);
}
else
{

//*** TESTING ON PROCESS ***	$mysql = "select skill_id,skill_name from $tablename where skill_type = '$test_type'";

$mysql = "select $assign_test_builder.skill_id as skill_id,$tablename.skill_name as skill_name from $assign_test_builder,$tablename where $assign_test_builder.skill_id=$tablename.skill_id and $assign_test_builder.user_id='$user_id' and  $assign_test_builder.group_id ='$group_id' ";
//echo $mysql;
$skill_arr = $db_object->get_rsltset($mysql);
}
	
	

				
	$mysql = "select skill_id from $quest_table where test_id='$test_id' order by q_id";
	$s_arr = $db_object->get_single_column($mysql);
	
	if($skill_arr !='')
	{
	$skill_arr1	= $common->conv_2Darray($db_object,$skill_arr);
	}
	$q_cnt=$common->answerCount($db_object);


 	preg_match("/<{quest_loopstart}>(.*?)<{quest_loopend}>/s",$returncontent,$qmatch);
	$newqmatch = $qmatch[1];
	
		
	
	preg_match("/<{ans_loopstart}>(.*?)<{ans_loopend}>/s",$returncontent,$amatch);


	$str = "";

$fields = $common->return_fields($db_object,$quest_table);
	$mysql = "select $fields from $quest_table where test_id='$test_id' order by q_id";
			
	$q_arr = $db_object->get_rsltset($mysql);

		for($i=0;$i<$tot_question;$i++)//tot_question
			{
				
			$question = $q_arr[$i]["question"];
			$q_id = $q_arr[$i]["q_id"];
			$score = $q_arr[$i]["score"];
			
			$newamatch = $amatch[1];
			
			$str1 = "";
		
				for($j=0;$j<$q_cnt;$j++)
				{
					$fNo = $i+1;
			
					$fNo1 = $j;
					$fields = $common->return_fields($db_object,$ans_table);
					$mysql = "select $fields from $ans_table where q_id='$q_id' order by ans_id";	
					$tempans_arr = $db_object->get_rsltset($mysql);
				
					$oldanswer = $tempans_arr[$j]["answer"];
					$status = $tempans_arr[$j]["status"];
					$weightage = $tempans_arr[$j]["weightage"];
			
					if($oldanswer !="")
						{
						$answer = $oldanswer;
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

			$skill_id = $s_arr[$i];

			$newsmatch1 = $common->pulldown_replace($db_object,'<{skill_loopstart}>','<{skill_loopend}>',$finalmatch,$skill_arr1,$skill_id);

			$questions = preg_replace("/<{(.*?)}>/e","$$1",$newsmatch1);//$finalmatch
	
			$str .= $questions;

			}


	$returncontent = preg_replace("/<{quest_loopstart}>(.*?)<{quest_loopend}>/s",$str,$returncontent);
	
	$returncontent = $common->direct_replace($db_object,$returncontent,$values);

	echo $returncontent;
		
	}


}

$obj = new Update_unapproved;

/*
	while(list($kk,$vv)=@each($_POST))
		{
		$$kk=$vv;
		$post_var["$kk"]=$vv;
		
		}

	while(list($kk,$vv)=@each($_GET))
		{
		$$kk=$vv;
		$post_var["$kk"]=$vv;
		}

*/

if($fNext)
{
	while(list($kk,$vv)=@each($post_var))
		{
		$$kk=$vv;
		
		}
		$fTestname=trim($fTestname);
	
	if($user_id == 1)
	{
		if($test_mode != "approve")
		{
	$tests_table=$common->prefix_table("tests");

	$mysql = "update $tests_table set test_name='$fTestname',test_type='$fTesttype',tot_question='$fQuestion' where test_id='$test_id'"; 
	$db_object->insert($mysql);
		}
		else
		{
		$tests_table=$common->prefix_table("unapproved_tests");
		$mysql = "update $tests_table set test_name='$fTestname',test_type='$fTesttype',tot_question='$fQuestion',status='p' where test_id='$test_id'"; 
		
		$db_object->insert($mysql);
		}
	}
	else
	{
		$tests_table=$common->prefix_table("unapproved_tests");
		$mysql = "update $tests_table set test_name='$fTestname',test_type='$fTesttype',tot_question='$fQuestion',status='p' where test_id='$test_id'"; 
		$db_object->insert($mysql);
	}


		
		
		if($fTesttype == "t")
		{
		$obj->displayQuestions($db_object,$common,$post_var,$user_id,$test_id,$default);
		}
		else
		{
		
		$obj->showTest_interskill($db_object,$common,$post_var,$user_id,$default);
		
		}


}
else
{
	$common->showForm($db_object,$common,$post_var,$user_id,$error_msg);
}

include("footer.php");

?>
																