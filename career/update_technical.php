<?php
/*---------------------------------------------
SCRIPT:update_technical.php
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


	
	
	$fields = $common->return_fields($db_object,$quest_table);
	$mysql = "select $fields from $quest_table where test_id = '$test_id' order by q_id";
	$q_arr = $db_object->get_rsltset($mysql);

	$q_keys = @array_keys($q_array);


	$mysql = "select skill_id from $quest_table where test_id='$test_id' group by skill_id";

	$oldskill_idarr=$db_object->get_single_column($mysql);


	$slevel_arr1 = @array_keys($slevel_arr);

	$mysql = "delete from $slevel_table where test_id='$test_id'";
	$db_object->insert($mysql);

	$sarr_keys = @array_keys($s_array);

	
	$sel_skills1 = @array_unique($sel_skills);

	$sel_skillkeys = @array_keys($sel_skills1);
	
	for($x=0;$x<count($sel_skillkeys);$x++)
	{


		$old_skillid = $sel_skillkeys[$x];
		
		$new_skill_id = $sel_skills1["$old_skillid"];
		
		$sel_skill_arr=$twod_slevel["$old_skillid"];
		
		list($un,$skillid,$level) = split("_",$slevel);
		
		$sel_skill_keyarr = @array_keys($sel_skill_arr);
		
		
		for($y=0;$y<count($sel_skill_keyarr);$y++)
		{

			$slkey = $sel_skill_keyarr[$y];
	
			list($un,$ref_skill_id,$level)=split("_",$slkey);
			$percent=$slevel_arr["$slkey"];
			
			$mysql = "insert into $slevel_table set test_id='$test_id',skill_id='$new_skill_id',level='$level',percent='$percent'";

			$db_object->insert($mysql);
		}
	}

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
		$xTemplate=$xPath."/templates/career/update_technical.html";//show_all_questions
		$returncontent=$common->return_file_content($db_object,$xTemplate);
		


		$tests_table=$common->prefix_table("temp_tests");
		$skills_table=$common->prefix_table("skills");
		$quest_table=$common->prefix_table("temp_questions");
		$config_table = $common->prefix_table("config");
		$t_skillpercent_table = $common->prefix_table("temp_skill_percent");
		$ans_table=$common->prefix_table("temp_answers");

		

		$mysql = "select test_name from $tests_table where test_id = '$test_id'";
		$test_arr = $db_object->get_a_line($mysql);

		$fTestname = $test_arr["test_name"];
		
		$values["directreplace"]["fTestname"]=$fTestname;
		$values["directreplace"]["test_id"]=$test_id;

	
	$returncontent = $common->direct_replace($db_object,$returncontent,$values);
	
	
		$mysql = "select test_type from $tests_table where test_id='$test_id'";
	
		$skilltype_arr = $db_object->get_a_line($mysql);
		

		$skill_type = $skilltype_arr["test_type"];

		

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
			
			if($user_id != 1)
			{
			$mysql = "select skill_id,skill_name from $skills_table where skill_type='$skill_type' and skill_id = '$skill_id'";
			}
			else
			{
			$mysql = "select skill_id,skill_name from $skills_table where skill_type='$skill_type'";	
			}
			$skill_arr = $db_object->get_rsltset($mysql);
	
			$skill_arr1	= $common->conv_2Darray($db_object,$skill_arr);
	
			$newsmatch1 = $common->pulldown_replace($db_object,'<{skill_loopstart}>','<{skill_loopend}>',$newsmatch,$skill_arr1,$skill_id);
		
			$fSno = $i;
			
		
			$mysql = "select q_id,question,score from $quest_table where skill_id='$skill_id' and test_id='$test_id'order by q_id";
			
			$quest_arr = $db_object->get_rsltset($mysql);
	



			//$fName = $skill_levels;
			$s_level = "";


			  $mysql = "select percent from $t_skillpercent_table where test_id='$test_id' and skill_id='$skill_id' order by level desc";
			  
			  $percent_arr = $db_object->get_single_column($mysql);

/////////////////////////////////
//the skill labels are taken from MULTIRATER APPRAISAL which will be assigned by the admin...

$skillraters_table = $common->prefix_table('skill_raters');
$mysql = "select count(*) as no_of_skills from $skillraters_table where skill_type='$skill_type' order by skill_type";
//echo $mysql;
$no_of_skills_arr = $db_object->get_a_line($mysql);
//print_r($no_of_skills_arr);
$no_of_skills = $no_of_skills_arr['no_of_skills'];

$mysql = "select rater_level_$default from $skillraters_table where skill_type='$skill_type'";
//echo $mysql;
$skilllevel_arr = $db_object->get_single_column($mysql);
//print_r($skilllevel_arr);

$no_skills1 = $no_of_skills - 1;
$fName = $no_of_skills;
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

		
/////////////////////////////////
			  
				for($j=0;$j<count($quest_arr);$j++)
				{
				
				$fNo = $j + 1;

				$newamatch = $amatch[1];

				$str1 = "";
				
				$q_id = $quest_arr[$j]["q_id"];
				$question = $quest_arr[$j]["question"];
				$score = $quest_arr[$j]["score"];

			
				$fields = $common->return_fields($db_object,$ans_table);
				$mysql = "select $fields from $ans_table where q_id='$q_id' order by ans_id";
			
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

function addTo_unapproved($db_object,$common,$post_var,$user_id,$error_msg,$default)
{
	include("header.php");


	while(list($kk,$vv)=@each($post_var))
		{

		$$kk=$vv;
	
		}


	$quest_table=$common->prefix_table("temp_questions");
	$test_table=$common->prefix_table("temp_tests");
	$ans_table=$common->prefix_table("temp_answers");
	$t_skillpercent_table=$common->prefix_table("temp_skill_percent");
	
	
	$mysql="select test_id,test_name,test_type,tot_question,user_id from $test_table where test_id='$test_id'";

	$temp_data=$db_object->get_a_line($mysql);
	
	$test_name = $temp_data["test_name"];
	$test_id = $temp_data["test_id"];

	$test_type = $temp_data["test_type"];
	$tot_question = $temp_data["tot_question"];
	
	$test_name=mysql_escape_string($test_name);

//If the user is admin directly approve the test...
	
	if($user_id!=1)
		{
	$un_quest_table=$common->prefix_table("unapproved_questions");
	$un_test_table=$common->prefix_table("unapproved_tests");
	$un_ans_table=$common->prefix_table("unapproved_answers");
	$un_slevel_table=$common->prefix_table("unapproved_skill_percent");
	
	$mysql = "insert into $un_test_table set test_id='',test_name='$test_name',test_type='$test_type',tot_question='$tot_question',user_id='$user_id',date=now()";

	$unapp_testid = $db_object->insert_data_id($mysql);
	
		}
	else
		{
	$un_quest_table=$common->prefix_table("questions");
	$un_test_table=$common->prefix_table("tests");
	$un_ans_table=$common->prefix_table("answers");
	$un_slevel_table=$common->prefix_table("skill_percent");
	
	$mysql = "insert into $un_test_table set test_id='',test_name='$test_name',test_type='$test_type',tot_question='$tot_question',user_id='$user_id',date=now(),ref_test_id='0'";  //$test_id this is changed since this comes from the temp table 

	$unapp_testid = $db_object->insert_data_id($mysql);
		}
	

	$fields = $common->return_fields($db_object,$quest_table);
	$mysql = "select $fields from $quest_table where test_id = '$test_id' order by q_id";

	$qarr = $db_object->get_rsltset($mysql);
	

	for($i=0;$i<count($qarr);$i++)
		{
		$qid = $qarr[$i]["q_id"];
		$test_id = $qarr[$i]["test_id"];
		$question = $qarr[$i]["question"];
		$score = $qarr[$i]["score"];
		$skill_id = $qarr[$i]["skill_id"];
		$user_id = $qarr[$i]["user_id"];
				
		$question=mysql_escape_string($question);
		
		$mysql = "insert into $un_quest_table set q_id='',test_id='$unapp_testid',question='$question',score='$score',skill_id='$skill_id'";
	
		$unapp_qid = $db_object->insert_data_id($mysql);

		$mysql = "insert into $un_ans_table (ans_id,q_id,answer,status,weightage) select '','$unapp_qid',answer,status,weightage from $ans_table where q_id='$qid' order by $ans_table.ans_id";
		$db_object->insert($mysql);


		//deleting the records from the temp_answers table...
		
		$mysql = "delete from $ans_table where q_id = '$qid'";
		$db_object->insert($mysql);
		
		}

	//deleting the records from the temp_tests table...
		
	$mysql = "delete from $test_table where test_id = '$test_id'";
	$db_object->insert($mysql);

	//deleting the records from the temp_questions table...
	
	$mysql = "delete from $quest_table where test_id='$test_id'";
	$db_object->insert($mysql);

	//deleting records from skill_percent table...
	
	$fields = $common->return_fields($db_object,$t_skillpercent_table);
	
	$mysql = "select $fields from $t_skillpercent_table where test_id='$test_id'";
	$s_level_arr = $db_object->get_rsltset($mysql);

	for($i=0;$i<count($s_level_arr);$i++)
		{
			
		$skill_id = $s_level_arr[$i]["skill_id"];
		$level = $s_level_arr[$i]["level"];
		$percent = $s_level_arr[$i]["percent"];

	
		$mysql = "insert into $un_slevel_table set sp_id='',test_id='$unapp_testid',skill_id='$skill_id',level='$level',percent='$percent'";

		$db_object->insert($mysql);
		
		}

	$mysql = "delete from $t_skillpercent_table where test_id='$test_id'";
	$db_object->insert($mysql);


	}

}

$obj = new showAll_questions;

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

if($later)
	{ 
		$test_table = $common->prefix_table("temp_tests");
		$quest_table = $common->prefix_table("temp_questions");
		$ans_table = $common->prefix_table("temp_answers");
		$skill_percent_table = $common->prefix_table("temp_skill_percent");
		
		$obj->editAllquestions($db_object,$common,$post_var,$user_id,$error_msg,$test_table,$quest_table,$ans_table,$skill_percent_table,$default);


		header("Location:front_panel.php");

	}

else if($approve)
	{
		$test_table = $common->prefix_table("temp_tests");
		$quest_table = $common->prefix_table("temp_questions");
		$ans_table = $common->prefix_table("temp_answers");
		$skill_percent_table = $common->prefix_table("temp_skill_percent");
		
	$obj->editAllquestions($db_object,$common,$post_var,$user_id,$error_msg,$test_table,$quest_table,$ans_table,$skill_percent_table,$default);

	$obj->addTo_unapproved($db_object,$common,$post_var,$user_id,$error_msg,$default);
	
	if($user_id!=1)
		{
		$message = $error_msg["cAddtest"];
 
		echo $message;

		$common->email_to_admin($db_object,$user_id);
		}
		else
		{
			$message = $error_msg["cApprovetest"];
 
		echo $message;

		}

	}
else if($testdrive)
	{
	include("header.php");
	
		$test_table = $common->prefix_table("temp_tests");
		$quest_table = $common->prefix_table("temp_questions");
		$ans_table = $common->prefix_table("temp_answers");
		$skill_percent_table = $common->prefix_table("temp_skill_percent");
	
		$obj->editAllquestions($db_object,$common,$post_var,$user_id,$error_msg,$test_table,$quest_table,$ans_table,$skill_percent_table,$default);
	
	
		$test_tablename = $common->prefix_table("temp_tests");
		$quest_tablename = $common->prefix_table("temp_questions");
		$ans_tablename = $common->prefix_table("temp_answers");
		$skill_percent_tablename = $common->prefix_table("temp_skill_percent");
		
		$post_var['test_mode'] = "temp";
		
	$common->take_test($db_object,$post_var,$user_id,$quest_tablename,$test_tablename,$ans_tablename,$skill_percent_tablename);
	
			

	}

else
	{
		
	$obj->showAllquestions($db_object,$common,$post_var,$user_id,$error_msg,$default);
	
	}



include("footer.php");