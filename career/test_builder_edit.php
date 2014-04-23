<?php
/*---------------------------------------------
SCRIPT:test_builder_edit.php
AUTHOR:info@chrisranjana.com	
UPDATED:15th Sept

DESCRIPTION:


---------------------------------------------*/
include("../session.php");
include_once("header.php");



class testBuilderedit
{
	function showTest($db_object,$common,$post_var,$user_id,$values,$default)
	{
		$xPath=$common->path;
		$xTemplate=$xPath."/templates/career/test_builder_edit.html";
		$returncontent=$common->return_file_content($db_object,$xTemplate);
		
		while(list($kk,$vv)=@each($post_var))
		{
		$$kk=$vv;
		}

		if($user_id==1)
		{
			
			
			if($test_mode == "approve")
			{
				$un_tests = $common->prefix_table("unapproved_tests");
			}
			else
			{
				$un_tests = $common->prefix_table("tests");
			}
			
		
		}
		else
		{
		$un_tests = $common->prefix_table("unapproved_tests");
		}
		
		$fields = $common->return_fields($db_object,$un_tests);
		$mysql = "select $fields from $un_tests where test_id = '$test_id'";

		$unapp_arr=$db_object->get_a_line($mysql);
	
		
			$test_id 	= $unapp_arr["test_id"];
			$fTestname 	= $unapp_arr["test_name"];
			$fTesttype 	= $unapp_arr["test_type"];;
			$fQuestion 	= $unapp_arr["tot_question"];
			$group_id  	= $unapp_arr["group_id"];
			$check_var	= $fTesttype."checked";
			$$check_var	= "checked";
			
			
	$values["directreplace"]["fTestname"]	= $fTestname;
	$values["directreplace"]["test_id"]		= $test_id;
	$values["directreplace"]["group_id"] 	= $group_id;
	$values["directreplace"]["fTesttype"]	= $fTesttype;
	$values["directreplace"]["fQuestion"]	= $fQuestion;
	$values["directreplace"][$check_var]	= $$check_var;
	$values["directreplace"]["test_mode"]	= $test_mode;
	$values["directreplace"]["user_id"] 	= $user_id;
			
			
		//$returncontent = preg_replace("/{{(.*?)}}/e","$$1",$returncontent);
			
		$returncontent = $common->direct_replace($db_object,$returncontent,$values);

		echo $returncontent;
		
	}
	function showTest_temp($db_object,$common,$post_var,$user_id,$values,$default)
	{
		$xPath=$common->path;
		$xTemplate=$xPath."/templates/career/test_builder_edit.html";
		$returncontent=$common->return_file_content($db_object,$xTemplate);
	
		while(list($kk,$vv)=@each($post_var))
		{
		$$kk=$vv;
		}


		$un_tests = $common->prefix_table("temp_tests");
		
		$fields = $common->return_fields($db_object,$un_tests);
		$mysql = "select $fields from $un_tests where test_id = '$test_id'";

		$unapp_arr=$db_object->get_a_line($mysql);
	
		
			$test_id = $unapp_arr["test_id"];
			$fTestname = $unapp_arr["test_name"];
			$fTesttype = $unapp_arr["test_type"];;
			$fQuestion = $unapp_arr["tot_question"];
			$group_id  = $unapp_arr["group_id"];
			$check_var=$fTesttype."checked";
			$$check_var="checked";
			
	$values["directreplace"]["fTestname"]	= $fTestname;
	$values["directreplace"]["test_id"]		= $test_id;
	$values["directreplace"]["fTesttype"]	= $fTesttype;
	$values["directreplace"]["fQuestion"]	= $fQuestion;
	$values["directreplace"][$check_var]	= $$check_var;
	$values["directreplace"]["user_id"] 	= $user_id;


			
		//$returncontent = preg_replace("/{{(.*?)}}/e","$$1",$returncontent);
			
		$returncontent = $common->direct_replace($db_object,$returncontent,$values);

		echo $returncontent;
	}
function deleteTest($db_object,$common,$post_var,$user_id,$default)
{

while(list($kk,$vv)=@each($post_var))
		{
		$$kk=$vv;
		
		}

	$tests_table=$common->prefix_table("temp_tests");
	$quest_table=$common->prefix_table("temp_questions");
	$ans_table=$common->prefix_table("temp_answers");
	$temp_skillpercent_table=$common->prefix_table("temp_skill_percent");


	$mysql = "delete from $tests_table where test_id='$test_id'";
	$db_object->insert($mysql);

	$mysql = "select q_id from $quest_table where test_id='$test_id'";
	$qid_arr = $db_object->get_single_column($mysql);
	
		for($i=0;$i<count($qid_arr);$i++)
			{
				
			$q_id = $qid_arr[$i];
			$mysql = "delete from $ans_table where q_id='$q_id'"; 
			$db_object->insert($mysql);
	
			}
	$mysql = "delete from $quest_table where test_id = '$test_id'";
	$db_object->insert($mysql);

}
}

$obj = new testBuilderedit;
 
//===============Check if any hacker is intruding...
	
$assign_test_builder = $common->prefix_table('assign_test_builder');

$mysql = "select group_id from $assign_test_builder where user_id='$user_id'"; // and status = 'p'
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
		
if($fChoose_unapptest)
{
	if($test_id == 0)
	{
		$message = $error_msg["cTestunexists"];
		echo $message;
		$common->showForm($db_object,$common,$post_var,$user_id,$error_msg);
	}
	else
	{
		$values["directreplace"]["form_action"]="update_unapproved_test";
		
		$obj->showTest($db_object,$common,$post_var,$user_id,$values,$default);
	}
}
else
{
	while(list($kk,$vv)=@each($post_var))
		{
		$$kk=$vv;
		}

	if($test_mode == "approve")
	{
		$values["directreplace"]["form_action"]="update_unapproved_test";
		
		$obj->showTest($db_object,$common,$post_var,$user_id,$values,$default);
	}
	
	elseif($fChoose_test)
	{
		if($test_id == 0)
		{
		$message = $error_msg["cTestunexists"];
		echo $message;
		$common->showForm($db_object,$common,$post_var,$user_id,$error_msg);
		}
		else
		{
		$values["directreplace"]["form_action"]="show_existing_test";
		$obj->showTest_temp($db_object,$common,$post_var,$user_id,$values,$default);
		}
	}
	elseif($fDelete_test)
	{
		if($test_id == 0)
		{
		$message = $error_msg["cTestunexists"];
		echo $message;
		$common->showForm($db_object,$common,$post_var,$user_id,$error_msg);
		}
		else
		{
		
		$obj->deleteTest($db_object,$common,$post_var,$user_id,$default);
		$message = $error_msg["cTestdeleted"];
		echo $message;
		$common->showForm($db_object,$common,$post_var,$user_id,$error_msg);
		}
	}
	
	else
	{
		$obj->showTest_temp($db_object,$common,$post_var,$user_id,$values,$default);	

	}

}
include_once("footer.php");
?>