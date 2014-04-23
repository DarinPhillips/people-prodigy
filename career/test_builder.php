<?php

/*---------------------------------------------
SCRIPT:test_builder.php
AUTHOR:info@chrisranjana.com	
UPDATED:4th Sept

DESCRIPTION:
This script uploads the skill sets of the person...

---------------------------------------------*/
include("../session.php");

class testBuilder
{

	function validateData($db_object,$common,$post_var,$error_msg,$user_id,$default)
	{

	while(list($kk,$vv)=@each($post_var))
		{
		$$kk=$vv;
		}
		
	if(trim($fTestname) == "")
	{
		$message.=$error_msg["cInvalidtestname"];
	}
	if($fTesttype == "")
	{
		$message.=$error_msg["cInvalidtesttype"];
	}
	if($fQuestion == "")
	{
		$message.=$error_msg["cInvalidqno"];

	}

	$c = $common->numeric_check($fQuestion);
	if($c == 1)
	{
	$message .= $error_msg["cNotnumber"];
	
	}
	
	return $message;
	}


	function storeTest_data($db_object,$common,$post_var,$returncontent,$user_id,$default)
	{
	
	while(list($kk,$vv)=@each($post_var))
		{
		$$kk=$vv;
		
		}

	
		
	//store TEST NAME, TEST TYPE, NO OF QUESTIONS in database

		
	$tot_question = $fQuestion;
	
	//insert the values into the tables...

	$tablename=$common->prefix_table("temp_tests");
	$mysql = "insert into $tablename set test_name = '$fTestname', test_type = '$fTesttype',tot_question = '$fQuestion',user_id='$user_id' , group_id = '$group_id'";

	$test_id = $db_object->insert_data_id($mysql); 

	
	return $test_id;
	
	
	}

	
	
}


$obj = new testBuilder;


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
		
	$message=$obj->validateData($db_object,$common,$post_var,$error_msg,$user_id,$default);
	
	if($message!="")
 		{
		
		include_once("header.php");
		echo $message;
		$common->showForm($db_object,$common,$post_var,$user_id,$error_msg);
		
		
		
		include_once("footer.php");		
 		}

 	else
 		{
			//determine which skill is tested...

			if($fTesttype == 'i')
			{
				
				$test_id = $obj->storeTest_data($db_object,$common,$post_var,$returncontent,$user_id,$default);

				header("Location:test_builder_questions.php?test_id=$test_id&group_id=$group_id");
 			}
			else
			{
				
				
				$test_id = $obj->storeTest_data($db_object,$common,$post_var,$returncontent,$user_id,$default);

				header("Location:technical_questions.php?test_id=$test_id&test_skill_id=$test_skill_id");
				
			}
		}

	}
else
	{
include_once("header.php");		
$common->showForm($db_object,$common,$post_var,$user_id,$error_msg,$fTest_type,$group_id);		
include_once("footer.php");		
	}



?>
