<?php
/*---------------------------------------------
SCRIPT:approve.php
AUTHOR:info@chrisranjana.com	
UPDATED:17th Sept

DESCRIPTION:
This script displays all the questions entered for interpersonal skills.

---------------------------------------------*/
include("../session.php");

class showAll_questions
{

	

	function editAllquestions($db_object,$common,$post_var,$user_id,$error_msg,$test_table,$quest_table,$ans_table,$skill_percent_table,$default)
	{

	//update all the questions and delete and add new answers...

		while(list($kk,$vv)=@each($post_var))
			{

			$$kk=$vv;
			
			if(ereg("^fQuestion_",$kk))
				{
				$f_qid=ereg_replace("fQuestion_","",$kk);
				$q_array["$f_qid"] = $vv;
				//$q_array[] = $vv;
				}

			if(ereg("^fMarks_",$kk))
				{
				$f_qid=ereg_replace("fMarks_","",$kk);
				$m_array["$f_qid"] = $vv;
				}
	
			if(ereg("^fSkillname_",$kk))
				{
				$s_qid=ereg_replace("fSkillname_","",$kk);
				$s_array["$s_qid"] = $vv;
				$sel_skills["$s_qid"]=$vv;
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
					if($vv != "")
					{
					$slevel_arr[$kk] = $vv;	
					$twod_slevel["$slq"]["$kk"]=$vv;	
					}
				}
			
			
			}

	$quest_table=$common->prefix_table("$quest_table");
	$test_table=$common->prefix_table("$test_table");
	$ans_table=$common->prefix_table("$ans_table");
	
	$skills_table=$common->prefix_table("skills");
	$slevel_table=$common->prefix_table("$skill_percent_table");

	$slevel_arr1 = @array_keys($slevel_arr);

	$mysql = "delete from $slevel_table where test_id='$test_id'";
	
	$db_object->insert($mysql);
		

	
	$sel_skills1 = @array_unique($sel_skills);

	$sel_skillkeys = @array_keys($sel_skills1);
	
	for($x=0;$x<count($sel_skillkeys);$x++)
	{


		$old_skillid = $sel_skillkeys[$x];
		
		$new_skill_id = $sel_skills1["$old_skillid"];
		
		$sel_skill_arr=$twod_slevel["$old_skillid"];
		
		list($un,$skillid,$level) = split("_",$slevel);
		
		$sel_skill_keyarr=@array_keys($sel_skill_arr);
		
		
		for($y=0;$y<count($sel_skill_keyarr);$y++)
		{

			$slkey = $sel_skill_keyarr[$y];
	
			list($un,$ref_skill_id,$level)=split("_",$slkey);
			$percent=$slevel_arr["$slkey"];
			
			$mysql = "insert into $slevel_table set test_id='$test_id',skill_id='$new_skill_id',level='$level',percent='$percent'";
			$db_object->insert($mysql);
		}
	}
	
	
$fields = $common->return_fields($db_object,$quest_table);

	$mysql = "select $fields from $quest_table where test_id = '$test_id'";
	$q_arr = $db_object->get_rsltset($mysql);

	$q_keys = @array_keys($q_array);



	$mysql = "select skill_id from $quest_table where test_id='$test_id'";

	$oldskill_idarr=$db_object->get_single_column($mysql);


	$sarr_keys=@array_keys($s_array);

	for($z=0;$z<count($sarr_keys);$z++)
	{
	$old_skill_id=  $sarr_keys[$z];
	$new_skill_id = $s_array["$old_skill_id"];
	
	$mysql = "update $quest_table set skill_id='$new_skill_id' where test_id='$test_id' and skill_id='$old_skill_id'";
	$db_object->insert($mysql);
	}


		for($i=0;$i<count($q_arr);$i++)
			{
		
			$q_id = $q_arr[$i]["q_id"];

			$question = $q_array["$q_id"];
			$score = $m_array["$q_id"];
			
			$mysql = "update $quest_table set question='$question',score='$score' where q_id='$q_id'";
			$db_object->insert($mysql);

			
			$mysql = "delete from $ans_table where q_id='$q_id'";
			$db_object->insert($mysql);
			

			$ans_array1=$ans_array[$q_id];	
	
			$a_arr = @array_keys($ans_array1);
		

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


			
				$mysql = "insert into $ans_table set answer='$aval',status='$cval',weightage='$wval',q_id='$q_id'";
				$db_object->insert($mysql);

			
				}


			}


	}

	function showAllquestions($db_object,$common,$post_var,$user_id,$error_msg,$default)
	{

	include("header.php");

	while(list($kk,$vv)=@each($post_var))
		{

		$$kk=$vv;
	
		}
		$xPath=$common->path;
		$xTemplate=$xPath."/templates/career/approve.html";
		$returncontent=$common->return_file_content($db_object,$xTemplate);
		
		$tests_table=$common->prefix_table("unapproved_tests");
		$skills_table=$common->prefix_table("skills");
		$quest_table=$common->prefix_table("unapproved_questions");
		$config_table = $common->prefix_table("config");
		$t_skillpercent_table = $common->prefix_table("unapproved_skill_percent");
		$ans_table=$common->prefix_table("unapproved_answers");

		

		$mysql = "select test_name,user_id from $tests_table where test_id = '$test_id'";
		
		$test_arr = $db_object->get_a_line($mysql);

		$fTestname = $test_arr["test_name"];
		$test_user_id = $test_arr["user_id"];
		
		$values["directreplace"]["test_id"]=$test_id;
		$values["directreplace"]["fTestname"]=$fTestname;
		$values["directreplace"][test_user_id]=$test_user_id;
		$values["directreplace"]["test_mode"]=$test_mode;
		
		//$returncontent = preg_replace("/{{(.*?)}}/e","$$1",$returncontent);
		
		$returncontent = $common->direct_replace($db_object,$returncontent,$values);



	
	
		$mysql = "select test_type from $tests_table where test_id='$test_id'";
	
		$skilltype_arr = $db_object->get_a_line($mysql);
		

		$skill_type = $skilltype_arr["test_type"];

		$mysql = "select skill_id,skill_name from $skills_table where skill_type='$skill_type'";
		
		$skill_arr = $db_object->get_rsltset($mysql);
	


	preg_match("/<{skillcontent_loopstart}>(.*?)<{skillcontent_loopend}>/s",$returncontent,$smatch);
	
	$newsmatch = $smatch[1];

	preg_match("/<{skilllevel_loopstart}>(.*?)<{skilllevel_loopend}>/s",$returncontent,$levelmatch);
	
	$newlevelmatch = $levelmatch[1];
	
	preg_match("/<{quest_loopstart}>(.*?)<{quest_loopend}>/s",$returncontent,$qmatch);
	
	$newqmatch = $qmatch[1];
	
	
	
	preg_match("/<{ans_loopstart}>(.*?)<{ans_loopend}>/s",$returncontent,$amatch);

				
	$mysql = "select skill_id from $quest_table where test_id='$test_id' group by skill_id";

	$s_arr = $db_object->get_single_column($mysql);
	
	


	$mysql = "select skill_levels from $config_table";
	$skilllevel_arr = $db_object->get_a_line($mysql);

	$skill_levels = $skilllevel_arr["skill_levels"];
	


		for($i=0;$i<count($s_arr);$i++)
			{

			$str = "";
			
			$skill_id = $s_arr[$i];
		
			$skill_arr1	= $common->conv_2Darray($db_object,$skill_arr);
	
			$newsmatch1 = $common->pulldown_replace($db_object,'<{skill_loopstart}>','<{skill_loopend}>',$newsmatch,$skill_arr1,$skill_id);
		
			$fSno = $i;
			
		
			$mysql = "select q_id,question,score from $quest_table where skill_id='$skill_id' and test_id='$test_id' order by q_id";
		
			
			$quest_arr = $db_object->get_rsltset($mysql);
	



			$fName = $skill_levels;
			$s_level = "";


			  $mysql = "select percent from $t_skillpercent_table where test_id='$test_id' and skill_id='$skill_id' order by level desc";
			  
			  $percent_arr = $db_object->get_single_column($mysql);

////////////////////////
			  
			  
			  //the skill labels are taken from MULTIRATER APPRAISAL which will be assigned by the admin...

$skillraters_table = $common->prefix_table('skill_raters');
$mysql = "select count(*) as no_of_skills from $skillraters_table where skill_type='$skill_type' and type_name = 'n'order by skill_type";
//echo $mysql;
$no_of_skills_arr = $db_object->get_a_line($mysql);
//print_r($no_of_skills_arr);
$no_of_skills = $no_of_skills_arr['no_of_skills'];

$mysql = "select rater_level_$default from $skillraters_table where skill_type='$skill_type' and type_name = 'n'";
//echo $mysql;
$skilllevel_arr = $db_object->get_single_column($mysql);
//print_r($skilllevel_arr);

$no_skills1 = $no_of_skills - 1;
$fName = $no_of_skills;
			  
			  
			  
			  
////////////////////////			  
			for($x=0;$x<$no_of_skills;$x++)
			  {
			  $fSno = $x+1;
			  
			  $fName_level = $skilllevel_arr[$no_skills1];
			  
			  $percent = $percent_arr[$x];
	 		  $old = preg_replace("/<{(.*?)}>/e","$$1",$newlevelmatch);
			  $s_level .= $old;
			  
			  $no_skills1--;
			  
			  $fName--;
			  }

	
		
				for($j=0;$j<count($quest_arr);$j++)
				{
				
				$fNo = $j + 1;

				$newamatch = $amatch[1];

				$str1 = "";
				
				$q_id = $quest_arr[$j]["q_id"];
				$question = $quest_arr[$j]["question"];
				$score = $quest_arr[$j]["score"];

				$fields = $common->return_fields($db_object,$ans_table);
				
				$mysql = "select $fields from $ans_table where q_id='$q_id'";
			
				$ans_arr = $db_object->get_rsltset($mysql);	

				$q_cnt=$common->answerCount($db_object);



					for($k=0;$k<$q_cnt;$k++)
					{

					$fNo1 = $k;

					$ans_id = $ans_arr[$k]["ans_id"];	
					$answer = $ans_arr[$k]["answer"];
					
					$answer=str_replace("$","&#36;",$answer);
					
					$status = $ans_arr[$k]["status"];
					$weightage = $ans_arr[$k]["weightage"];

				
					if($status == "r")
						{
						$check = "checked";
						}
					else 
						{
						$check = "";
						}
					$fQid = $q_id;
					$ans = preg_replace("/<{(.*?)}>/e","$$1",$newamatch);
					$str1 .= $ans;

					}
			
			


				$finalmatch = preg_replace("/<{ans_loopstart}>(.*?)<{ans_loopend}>/s",$str1,$newqmatch);
		
	
				$questions = preg_replace("/<{(.*?)}>/e","$$1",$finalmatch);
	
				$str .= $questions;
			
			}



	$newsmatch1 = preg_replace("/<{quest_loopstart}>(.*?)<{quest_loopend}>/s",$str,$newsmatch1);

	$newsmatch1 = preg_replace("/<{skilllevel_loopstart}>(.*?)<{skilllevel_loopend}>/s",$s_level,$newsmatch1);
	
	$newsmatch1 = preg_replace("/<{(.*?)}>/e","$$1",$newsmatch1);

	$newsmatch_result.=$newsmatch1;

			}


	$returncontent = preg_replace("/<{skillcontent_loopstart}>(.*?)<{skillcontent_loopend}>/s",$newsmatch_result,$returncontent);


	echo $returncontent;
	
		}
		

function addToapproved($db_object,$common,$post_var,$user_id,$error_msg,$default)
{
	while(list($kk,$vv)=@each($post_var))
		{
		$$kk=$vv;
		}
	
	
	$tests = $common->prefix_table("tests");
	$questions = $common->prefix_table("questions");
	$answers = $common->prefix_table("answers");
	$skill_percent = $common->prefix_table("skill_percent");
	
	$unapp_test_table = $common->prefix_table("unapproved_tests");
	$unapp_quest_table = $common->prefix_table("unapproved_questions");
	$unapp_ans_table = $common->prefix_table("unapproved_answers");
	$unapp_skill_percent_table = $common->prefix_table("unapproved_skill_percent");

	$assign_test_builder = $common->prefix_table('assign_test_builder');

	$fields = $common->return_fields($db_object,$unapp_test_table);
	
	$mysql = "select $fields from $unapp_test_table where test_id='$test_id' and status='a'";
	$tests_arr = $db_object->get_a_line($mysql);

	$unapp_test_name = $tests_arr["test_name"];
	$unapp_test_id = $tests_arr["test_id"];
	$unapp_test_type = $tests_arr["test_type"];
	$unapp_tot_question = $tests_arr["tot_question"];
	$unapp_user_id = $tests_arr["user_id"];
	$unapp_test_name = mysql_escape_string($unapp_test_name);

//CHECK IF THE TEST IS ALREADY APPROVED BY THE ADMIN***
	//IF SO THEN DELETE THE OLD RECORDS AND INSERT THE NEW ONES***
	
	$mysql = "select ref_test_id from $tests";
	$ref_previous_arr = $db_object->get_single_column($mysql);

	for($i=0;$i<count($ref_previous_arr);$i++)
	{
	$ref_previous_id = $ref_previous_arr[$i];

		if($ref_previous_id == $unapp_test_id)
		{
		
		$mysql = "select test_id from $tests
				where test_name='$unapp_test_name'
				and test_type='$unapp_test_type'
				and tot_question='$unapp_tot_question'
				and user_id='$unapp_user_id'
				and ref_test_id = '$ref_previous_id'";

		$test_id_arr = $db_object->get_a_line($mysql);
	
		$existing_test_id = $test_id_arr['test_id'];

		$mysql = "select q_id from $questions where test_id = '$existing_test_id'";
		$qid_arr = $db_object->get_single_column($mysql);

		for($l=0;$l<count($qid_arr);$l++)
		{
			$q_id = $qid_arr[$l];
		
			$mysql = "delete from $answers where q_id = '$q_id'";
			$db_object->insert($mysql);

		
		}
	//before deleting the contents of the questions and answers table take the primary key id and then delete them accordingly since they all are interrelated...

		$mysql = "delete from $questions where test_id = '$existing_test_id'";
		$db_object->insert($mysql);

		$mysql = "delete from $skill_percent where test_id = '$existing_test_id'";
		$db_object->insert($mysql);

		$mysql = "delete from $tests where ref_test_id = '$unapp_test_id'";
		$db_object->insert($mysql);
	
		}

	}
	$unapp_test_name = mysql_escape_string($unapp_test_name); 

$mysql = "insert into $tests set test_id='',test_name='$unapp_test_name',test_type='$unapp_test_type',tot_question='$unapp_tot_question',user_id='$unapp_user_id',date=now(),ref_test_id='$unapp_test_id'";
$app_testid = $db_object->insert_data_id($mysql);


//update the assign test builder table so that the group name is not displayed in the alert board once the admin approves it...
	
	$mysql = "update $assign_test_builder set status = 'a' where user_id = '$unapp_user_id' and group_id = '$group_id'";
	$db_object->insert($mysql);
	
//===============Check if any hacker is intruding...
	
$assign_test_builder = $common->prefix_table('assign_test_builder');

$mysql = "select group_id from $assign_test_builder where user_id='$user_id'"; //** and status = 'p' CAN'T CHECK FOR THIS CONDITION SINCE THE USER MAY ALSO EDIT IT FROM THE EXISTING TABLES OPTION...
$group_arr = $db_object->get_single_column($mysql);

for($l=0;$l<count($group_arr);$l++)
{
	$check_group = $group_arr[$i];
		
	$check = @in_array("$group_id",$group_arr);
			if(!$check)
			{
				$message = $error_msg['cAlertHackertestbuilder'];
				echo $message;
				exit;
			
			}
}

//===============


	$fields = $common->return_fields($db_object,$unapp_quest_table);

	$mysql = "select $fields from $unapp_quest_table where test_id = '$unapp_test_id' order by q_id";
	
	$qarr = $db_object->get_rsltset($mysql);
	
	for($i=0;$i<count($qarr);$i++)
		{
		$qid = $qarr[$i]["q_id"];
		$unapp_test_id = $qarr[$i]["test_id"];
		$unapp_question = $qarr[$i]["question"];
		$unapp_score = $qarr[$i]["score"];
		$unapp_skill_id = $qarr[$i]["skill_id"];
		$unapp_user_id = $qarr[$i]["user_id"];

		$unapp_question = mysql_escape_string($unapp_question); 
		
		$mysql = "insert into $questions set q_id='',test_id='$app_testid',question='$unapp_question',score='$unapp_score',skill_id='$unapp_skill_id'";
		$app_qid = $db_object->insert_data_id($mysql);
	
		$mysql = "insert into $answers (ans_id,q_id,answer,status,weightage) select '','$app_qid',answer,status,weightage from $unapp_ans_table where q_id='$qid'  order by $unapp_ans_table.ans_id";
		$db_object->insert($mysql);

		}

$fields = $common->return_fields($db_object,$unapp_skill_percent_table);

$mysql = "select $fields from $unapp_skill_percent_table where test_id='$test_id' order by level";
$skillpercent_arr = $db_object->get_rsltset($mysql);

	for($i=0;$i<count($skillpercent_arr);$i++)
	{
		$test_id = $skillpercent_arr[$i]["test_id"];
		$skill_id = $skillpercent_arr[$i]["skill_id"];
		$level = $skillpercent_arr[$i]["level"];
		$percent = $skillpercent_arr[$i]["percent"];
	
		$mysql = "insert into $skill_percent set test_id='$app_testid',skill_id='$skill_id',level='$level',percent='$percent'";
		$db_object->insert($mysql);
	
	
	}
		
}

function delete_test($db_object,$common,$post_var,$user_id,$error_msg,$default)
{
	while(list($kk,$vv)=@each($post_var))
		{
		$$kk=$vv;
		}
		
		
	//======tables assigned...
		
	$unapp_test_table = $common->prefix_table("unapproved_tests");
	$unapp_quest_table = $common->prefix_table("unapproved_questions");
	$unapp_ans_table = $common->prefix_table("unapproved_answers");
	$unapp_skill_percent_table = $common->prefix_table("unapproved_skill_percent");

	$assign_test_builder = $common->prefix_table('assign_test_builder');
	
	$mysql = "delete from $assign_test_builder where group_id = '$group_id' and user_id = '$user_id'";
	$db_object->insert($mysql);
	
		
	$mysql = "delete from $unapp_test_table where test_id = '$test_id'";
	$db_object->insert($mysql);
	
//get qids of all the questions of this test...
	$mysql = "select q_id from $unapp_quest_table where test_id = '$test_id'";
	$qid_arr = $db_object->get_single_column($mysql);
	
	
	$qid_full = @implode("','",$qid_arr);
	
	if($qid_full != '')
	{
	$mysql = "delete from $unapp_ans_table where q_id in('$qid_full')";
	$db_object->insert($mysql);
	}
	
	$mysql = "delete from $unapp_quest_table where test_id = '$test_id'";
	$db_object->insert($mysql);

	$mysql = "delete from $unapp_skill_percent_table where test_id = '$test_id'";
	$db_object->insert($mysql);
	
	
	$message = $error_msg["cDeletetestwaitingforapproval"];
 	echo $message;
	
		
	
}


}
$obj = new showAll_questions;

 

if($later)
	{ 
		$test_table = $common->prefix_table("unapproved_tests");
		$quest_table = $common->prefix_table("unapproved_questions");
		$ans_table = $common->prefix_table("unapproved_answers");
		$skill_percent_table = $common->prefix_table("unapproved_skill_percent");
		
	$obj->editAllquestions($db_object,$common,$post_var,$user_id,$error_msg,$test_table,$quest_table,$ans_table,$skill_percent_table,$default);

	header("Location:front_panel.php");
	//show front panel.php
	}

else if($approve)
	{
		include_once("header.php");
		
		$test_table = $common->prefix_table("unapproved_tests");
		$quest_table = $common->prefix_table("unapproved_questions");
		$ans_table = $common->prefix_table("unapproved_answers");
		$skill_percent_table = $common->prefix_table("unapproved_skill_percent");
		
	$obj->editAllquestions($db_object,$common,$post_var,$user_id,$error_msg,$test_table,$quest_table,$ans_table,$skill_percent_table,$default);
	
	$mysql = "update $test_table set status='a' where test_id='$test_id'";
	$db_object->insert($mysql);
	
	$obj->addToapproved($db_object,$common,$post_var,$user_id,$error_msg,$default);
	
	$message = $error_msg["cApprovetest"];
 
	echo $message;

	}
elseif($del_test)
{
	include_once('header.php');
	$obj->delete_test($db_object,$common,$post_var,$user_id,$error_msg,$default);
	
}

else
	{
		
	$obj->showAllquestions($db_object,$common,$post_var,$user_id,$error_msg,$default);
	}



include("footer.php");