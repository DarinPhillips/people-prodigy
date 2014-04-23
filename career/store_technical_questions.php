<?php
/*---------------------------------------------
SCRIPT:store_test_questions.php
AUTHOR:info@chrisranjana.com	
UPDATED:5th Sept

DESCRIPTION:
This script stores the technical questions to the database.

---------------------------------------------*/
include("../session.php");



class storeTechnical_questions
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




	$skills_table=$common->prefix_table("skills");
	$quest_table=$common->prefix_table("temp_questions");
	$test_table=$common->prefix_table("temp_tests");
	$ans_table=$common->prefix_table("temp_answers");

	
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
		

		$skill_id = $s_array[0];
			
			$mysql = "insert into $quest_table set test_id = '$test_id',question = '$val',score='$m_array[$i]',skill_id='$s_array[0]',user_id='$user_id'";
			
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
	//update the assign test builder table so that the skill name is not displayed in the alert board once the admin approves it...
	
		
	$assign_test_builder  = $common->prefix_table('assign_test_builder');	
	$mysql = "update $assign_test_builder set status = 'a' where user_id = '$user_id' and skill_id = '$skill_id'";
	//echo $mysql;
	$db_object->insert($mysql);



	}


	
}

$obj = new storeTechnical_questions;
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
	header("Location:update_technical.php?test_id=$test_id");
	}
else
	{
	header("Location:front_panel.php");
	}
?>
