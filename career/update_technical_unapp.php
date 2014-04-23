<?php
/*---------------------------------------------
SCRIPT:update_technical_unapp.php
AUTHOR:info@chrisranjana.com	
UPDATED:17th Sept

DESCRIPTION:
This script updates the technical skills based tests.

---------------------------------------------*/
include("../session.php");

class updateTech_questions
{

	function saveQuestions($db_object,$common,$post_var,$user_id,$error_msg,$default)
	{
	

	while(list($kk,$vv)=@each($post_var))
		{

		$$kk=$vv;

		if(ereg("^fQuestion_",$kk))
			{
			$q_array[$kk] = $vv;	
			
			}

		if(ereg("^fMarks_",$kk))
			{
			$m_array[] = $vv;
			}

		if(ereg("^fSkillname_",$kk))
			{
			$s_array[] = $vv;
			}

		if(ereg("^fAns_",$kk))
			{
		
			list($un,$qid,$aid) = split("_",$kk);
				{
				if($vv != "")
					{
					$ans_array[$qid][$kk] = $vv;
					}
	
				}
			}

		if(ereg("^fWeight_",$kk))
			{
				if($vv != "")
				{
				$weight_array[$kk] = $vv;
				}
			}

		if(ereg("^fCheck_",$kk))
			{
			if($vv == "r")
				{
				$check_array[$kk] = $vv;
				}
			}

		if(ereg("^fSkilllevel_",$kk))
				{
				list($un,$slq,$sla) = split("_",$kk);
				
				$slevel_arr[$kk] = $vv;	
				}
		}
if($user_id == 1)
{
	$skills_table=$common->prefix_table("skills");
	$quest_table=$common->prefix_table("questions");
	$test_table=$common->prefix_table("tests");
	$ans_table=$common->prefix_table("answers");
}
else
{
	$skills_table=$common->prefix_table("skills");
	$quest_table=$common->prefix_table("unapproved_questions");
	$test_table=$common->prefix_table("unapproved_tests");
	$ans_table=$common->prefix_table("unapproved_answers");
}
	
	//store questions into table...
		
	$mysql = "select q_id from $quest_table where test_id='$test_id'";

	$questionid_arr = $db_object->get_single_column($mysql);

	$mysql = "delete from $quest_table where test_id='$test_id'";
	$db_object->insert($mysql);
	
	$q_arr=@array_keys($q_array);

	for($i=0;$i<count($q_arr);$i++)
		{

		//for questions 

		$key=$q_arr[$i];
		$val=$q_array["$key"];

		list($un,$qid)=split("_",$key);
		

		
			
		$mysql = "insert into $quest_table set test_id = '$test_id',question = '$val',score='$m_array[$i]',skill_id='$s_array[0]'";
		$quest_id = $db_object->insert_data_id($mysql);
			
			
		$ans_array1=$ans_array[$qid];
			
			
		$a_arr = @array_keys($ans_array1);
		$c_arr = @array_keys($check_array);
			
		for($j=0;$j<count($a_arr);$j++)
			{
				
			$akey=$a_arr[$j];
			$aval=$ans_array1["$akey"];

					
			list($un,$qid)=split("_",$akey,2);
				
			$wkey ="fWeight_".$qid; 
				
			$wval=$weight_array["$wkey"];
				
			$ckey = "fCheck_".$qid;
			
			$cval=$check_array["$ckey"];
				
			if($cval=="")
				{
				$cval='w';
				}			
	
			if($wval == "")
				{
				$wval = 0;
				}

				for($k=0;$k<count($questionid_arr);$k++)
					{
					$question_id = $questionid_arr[$k];
				
					$mysql = "delete from $ans_table where q_id='$question_id'";
					$db_object->insert($mysql);
					}
			
			$mysql = "insert into $ans_table set q_id='$quest_id',answer='$aval',status='$cval',weightage='$wval'";
			$db_object->insert($mysql);
	
			}
			
		}

	}


	
}

$obj = new updateTech_questions;
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
if($fSave_later)
	{ 

	$obj->saveQuestions($db_object,$common,$post_var,$user_id,$error_msg,$default);
	header("Location:front_panel.php");


	}

else if($fGo_next)
	{

		$obj->saveQuestions($db_object,$common,$post_var,$user_id,$error_msg,$default);
			
			if($test_mode == "approve")
			{
			header("Location:approve_technical.php?test_id=$test_id&test_mode=$test_mode");
			}
			else
			{
			header("Location:show_unapproved.php?test_id=$test_id&test_mode=$test_mode");
			}
	}
else
	{
	header("Location:front_panel.php");
	}
?>
