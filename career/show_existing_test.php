<?php

/*---------------------------------------------
SCRIPT:show_existing_tables.php
AUTHOR:info@chrisranjana.com	
UPDATED:17th Sept

DESCRIPTION:
This script displays all the test questions & answers previously entered.

---------------------------------------------*/
include("../session.php");
include("header.php");


class showExisting_test
{
	function showTest_interskill($db_object,$common,$post_var,$user_id,$test_tablename,$quest_tablename,$ans_tablename,$default)
	{

	$q_cnt=$common->answerCount($db_object);

	while(list($kk,$vv)=@each($post_var))
		{
		$$kk=$vv;
			
		}
		
	$xPath=$common->path;
	$xTemplate = $xPath."/templates/career/show_existing_test.html";
	$returncontent=$common->return_file_content($db_object,$xTemplate);

	$tests_table=$common->prefix_table("$test_tablename");
	$tablename=$common->prefix_table("skills");
	$quest_table=$common->prefix_table("$quest_tablename");	
	$ans_table = $common->prefix_table("$ans_tablename");
	$assign_test_builder = $common->prefix_table('assign_test_builder');

//===============Check if any hacker is intruding...
	
$assign_test_builder = $common->prefix_table('assign_test_builder');

$mysql = "select group_id from $assign_test_builder where user_id='$user_id'";  //and status = 'p'
//echo $mysql;
$group_arr = $db_object->get_single_column($mysql);

if($group_id != '')
{
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

	$fields = $common->return_fields($db_object,$tests_table);
	
	$mysql="select $fields from $tests_table where test_id='$test_id' order by test_id";

	$db_data=$db_object->get_a_line($mysql);

	$fTestname = $db_data["test_name"];
	$tot_question = $db_data["tot_question"];
	$group_id = $db_data["group_id"];
	
	$test_type = $db_data["test_type"];
		
	$values["directreplace"]["fTestname"]=$fTestname;
	$values["directreplace"]["test_id"]=$test_id;
	$values["directreplace"]["group_id"]=$group_id;

if($user_id != 1)
{
	
//***TESTING IN PROCESS***	$mysql = "select skill_id,skill_name from $tablename where skill_type = '$test_type'";
	$mysql = "select $assign_test_builder.skill_id as skill_id,$tablename.skill_name as skill_name from $assign_test_builder,$tablename where $assign_test_builder.skill_id=$tablename.skill_id and $assign_test_builder.user_id='$user_id' and  $assign_test_builder.group_id ='$group_id' ";
	//echo $mysql;
	$skill_arr = $db_object->get_rsltset($mysql);
}
else
{
	$mysql = "select skill_id,skill_name from $tablename where skill_type = '$test_type'";
	$skill_arr = $db_object->get_rsltset($mysql);
}
				
	$mysql = "select skill_id from $quest_table where test_id='$test_id' order by q_id";
	$s_arr = $db_object->get_single_column($mysql);
	
	$skill_arr1	= $common->conv_2Darray($db_object,$skill_arr);

	
 	preg_match("/<{quest_loopstart}>(.*?)<{quest_loopend}>/s",$returncontent,$qmatch);
	$newqmatch = $qmatch[1];
	
		
	
	preg_match("/<{ans_loopstart}>(.*?)<{ans_loopend}>/s",$returncontent,$amatch);


	$str = "";

	$fields = $common->return_fields($db_object,$quest_table);
	$mysql = "select $fields from $quest_table where test_id='$test_id' order by q_id";
	$q_arr = $db_object->get_rsltset($mysql);

		for($i=0;$i<$tot_question;$i++)
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

	//$returncontent = preg_replace("/{{(.*?)}}/e","$$1",$returncontent);
	
	$returncontent = $common->direct_replace($db_object,$returncontent,$values);

	echo $returncontent;
		
	}


	

function showTest_technical($db_object,$common,$post_var,$user_id,$test_tablename,$quest_tablename,$ans_tablename,$default)
{
	while(list($kk,$vv)=@each($post_var))
		{
		$$kk=$vv;
			
		}
	
	$xPath=$common->path;
	$xTemplate = $xPath."/templates/career/technical_questions.html";
	$returncontent=$common->return_file_content($db_object,$xTemplate);



	$tests_table=$common->prefix_table("$test_tablename");
	$tablename=$common->prefix_table("skills");
	$quest_table=$common->prefix_table("$quest_tablename");	
	$ans_table = $common->prefix_table("$ans_tablename");

	$fields = $common->return_fields($db_object,$tests_table);
	$mysql="select $fields from $tests_table where test_id='$test_id'";

	$db_data=$db_object->get_a_line($mysql);

	$fTestname = $db_data["test_name"];
	$test_id = $db_data["test_id"];
	

	$tot_question = $db_data["tot_question"];	
	$test_type = $db_data["test_type"];
		
	$values["directreplace"]["fTestname"]=$fTestname;
	$values["directreplace"]["test_id"]=$test_id;


					
	$mysql = "select skill_id from $quest_table where test_id='$test_id' order by q_id";
	$s_arr = $db_object->get_a_line($mysql);

	$skillid = $s_arr["skill_id"];
	if($user_id !=1)
	{
	$mysql = "select skill_id,skill_name from $tablename where skill_type = '$test_type' and skill_id = '$skillid'";
	}
	else
	{
	$mysql = "select skill_id,skill_name from $tablename where skill_type = '$test_type'";
	}
	$skill_arr = $db_object->get_rsltset($mysql);
	
	$skill_arr1	= $common->conv_2Darray($db_object,$skill_arr);
	
	$returncontent = $common->pulldown_replace($db_object,'<{skill_loopstart}>','<{skill_loopend}>',$returncontent,$skill_arr1,$skillid);

	$q_cnt=$common->answerCount($db_object);


 	preg_match("/<{quest_loopstart}>(.*?)<{quest_loopend}>/s",$returncontent,$qmatch);
	$newqmatch = $qmatch[1];
	
		
	
	preg_match("/<{ans_loopstart}>(.*?)<{ans_loopend}>/s",$returncontent,$amatch);


	$str = "";

	$fields = $common->return_fields($db_object,$quest_table);
	$mysql = "select $fields from $quest_table where test_id='$test_id' order by q_id";
			
	$q_arr = $db_object->get_rsltset($mysql);

		for($i=0;$i<$tot_question;$i++)
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

					$oldanswer=str_replace("$","&#36;",$oldanswer);
			
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
		
			$questions = preg_replace("/<{(.*?)}>/e","$$1",$finalmatch);//$finalmatch
	
			$str .= $questions;

			}


	$returncontent = preg_replace("/<{quest_loopstart}>(.*?)<{quest_loopend}>/s",$str,$returncontent);

	$returncontent = $common->direct_replace($db_object,$returncontent,$values);

	echo $returncontent;
		
}


}

$obj = new showExisting_test;
 
	
if($fNext)
{

	if($test_id == 0)
	{
		$message = $error_msg["cTestunexists"];
		echo $message;
		$common->showForm($db_object,$common,$post_var,$user_id,$error_msg);
	}
	else
	{
		
		while(list($kk,$vv)=@each($post_var))
		{
		$$kk=$vv;
		
		}
	
		
		$test_tablename = $common->prefix_table("temp_tests");
		$quest_tablename = $common->prefix_table("temp_questions");
		$ans_tablename = $common->prefix_table("temp_answers");
		
		$fTestname=trim($fTestname);
				
		$mysql = "update $test_tablename set test_name='$fTestname',test_type='$fTesttype',tot_question='$fQuestion' where test_id='$test_id'"; 
		$db_object->insert($mysql);
		
		
		$mysql = "select test_type from $test_tablename where test_id='$test_id'";
		
		$testarr = $db_object->get_a_line($mysql);
		
		$test_type = $testarr["test_type"];
		
		if($test_type=="i")
			{
		$obj->showTest_interskill($db_object,$common,$post_var,$user_id,$test_tablename,$quest_tablename,$ans_tablename,$default);
			}
		else
			{
		$obj->showTest_technical($db_object,$common,$post_var,$user_id,$test_tablename,$quest_tablename,$ans_tablename,$default);
			}
	}
		
}


else
	{
	
$common->showForm($db_object,$common,$post_var,$user_id,$error_msg);		
	}

include("footer.php");
?>