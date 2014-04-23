<?php
/*---------------------------------------------
SCRIPT:show_unapproved.php
AUTHOR:info@chrisranjana.com	
UPDATED:17th Sept

DESCRIPTION:
This script displays all the questions entered for the unapproved tests.

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

	@array_unique($slevel_arr1);


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
	
	$fields = $common->return_fields($db_object,$quest_table);
	$mysql = "select $fields from $quest_table where test_id = '$test_id' order by q_id";
	$q_arr = $db_object->get_rsltset($mysql);

		$q_keys = @array_keys($q_array);


	$mysql = "select skill_id from $quest_table where test_id='$test_id' order by q_id";

	$oldskill_idarr=$db_object->get_single_column($mysql);


	$sarr_keys = @array_keys($s_array);

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
		$xTemplate=$xPath."/templates/career/show_unapproved.html";
		$returncontent=$common->return_file_content($db_object,$xTemplate);

		

		

	if($user_id == 1)
	{
		$tests_table=$common->prefix_table("tests");
		$skills_table=$common->prefix_table("skills");
		$quest_table=$common->prefix_table("questions");
		$config_table = $common->prefix_table("config");
		$t_skillpercent_table = $common->prefix_table("skill_percent");
		$ans_table=$common->prefix_table("answers");

		$mysql = "select ref_test_id from $tests_table where test_id = '$test_id'";
		$ref_arr = $db_object->get_a_line($mysql);
		
		$ref_test_id = $ref_arr['ref_test_id'];
		
		
	}
	else
	{
		$tests_table=$common->prefix_table("unapproved_tests");
		$skills_table=$common->prefix_table("skills");
		$quest_table=$common->prefix_table("unapproved_questions");
		$config_table = $common->prefix_table("config");
		$t_skillpercent_table = $common->prefix_table("unapproved_skill_percent");
		$ans_table=$common->prefix_table("unapproved_answers");
		$assign_test_builder = $common->prefix_table('assign_test_builder');

		$ref_test_id = $test_id;
	}
		

		$mysql = "select test_name from $tests_table where test_id = '$test_id'";
		$test_arr = $db_object->get_a_line($mysql);

		$fTestname = $test_arr["test_name"];
		
		$values["directreplace"]["fTestname"]=$fTestname;
		$values["directreplace"]["test_id"]=$test_id;
		$values["directreplace"]["test_mode"]=$test_mode;
		
	
		$returncontent = $common->direct_replace($db_object,$returncontent,$values);



		$mysql = "select test_type from $tests_table where test_id='$test_id'";

		$skilltype_arr = $db_object->get_a_line($mysql);
		
		$skill_type = $skilltype_arr["test_type"];

//===============Check if any hacker is intruding...


if(($skill_type=='i') && ($user_id != '1'))
{
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
}
//===============

		$mysql = "select skill_id from $quest_table where test_id='$test_id' group by skill_id";
		$s_arr = $db_object->get_single_column($mysql);
	
	




if($skill_type == 'i')
{
	if(($user_id == 1) || ($test_mode == "approve"))
	{
		$mysql = "select skill_id,skill_name from $skills_table where skill_type='$skill_type' order by skill_id";
		$skill_arr = $db_object->get_rsltset($mysql);		
	}
	else
	{
		

	
		

	$mysql = "select $assign_test_builder.skill_id as skill_id,$skills_table.skill_name as skill_name from $assign_test_builder,$skills_table where $assign_test_builder.skill_id=$skills_table.skill_id and $assign_test_builder.user_id='$user_id' and  $assign_test_builder.group_id ='$group_id' ";
	$skill_arr = $db_object->get_rsltset($mysql);
	}

}
elseif($skill_type== 't')
{
	if(($user_id == 1) || ($test_mode == "approve"))
	{
		$mysql = "select skill_id,skill_name from $skills_table where skill_type='$skill_type' order by skill_id";
		$skill_arr = $db_object->get_rsltset($mysql);		
	}
	else
	{
	$show_skill_id = $s_arr[0];

	$mysql = "select skill_id,skill_name from $skills_table where skill_type='$skill_type' and skill_id = '$show_skill_id' order by skill_id";
	$skill_arr = $db_object->get_rsltset($mysql);	
	}
}

	preg_match("/<{skillcontent_loopstart}>(.*?)<{skillcontent_loopend}>/s",$returncontent,$smatch);
	
	$newsmatch = $smatch[1];

	preg_match("/<{skilllevel_loopstart}>(.*?)<{skilllevel_loopend}>/s",$returncontent,$levelmatch);
	
	$newlevelmatch = $levelmatch[1];
	
	preg_match("/<{quest_loopstart}>(.*?)<{quest_loopend}>/s",$returncontent,$qmatch);
	
	$newqmatch = $qmatch[1];
	
	
	
	preg_match("/<{ans_loopstart}>(.*?)<{ans_loopend}>/s",$returncontent,$amatch);

				


	$mysql = "select skill_levels from $config_table";
	$skilllevel_arr = $db_object->get_a_line($mysql);

	$skill_levels = $skilllevel_arr["skill_levels"];
	


		for($i=0;$i<count($s_arr);$i++)
			{

			$str = "";
			
			$skill_id = $s_arr[$i];
			
			if($skill_arr !='')
			{
			$skill_arr1	= $common->conv_2Darray($db_object,$skill_arr);
			}
			
			$newsmatch1 = $common->pulldown_replace($db_object,'<{skill_loopstart}>','<{skill_loopend}>',$newsmatch,$skill_arr1,$skill_id);
		
			$fSno = $i;
			
		
			$mysql = "select q_id,question,score from $quest_table where skill_id='$skill_id' and test_id='$test_id' order by q_id";
			
			$quest_arr = $db_object->get_rsltset($mysql);
	

			
			$s_level = "";


			//$mysql = "select percent from $t_skillpercent_table where test_id='$ref_test_id' and skill_id='$skill_id' order by level desc";
			$mysql = "select percent from $t_skillpercent_table where test_id='$ref_test_id' and skill_id='$skill_id' order by level asc";
			
			$percent_arr = $db_object->get_single_column($mysql);

///////////////////////////
			
$skillraters_table = $common->prefix_table('skill_raters');
$mysql = "select count(*) as no_of_skills from $skillraters_table where skill_type='$skill_type' order by skill_type";

$no_of_skills_arr = $db_object->get_a_line($mysql);

$no_of_skills = $no_of_skills_arr['no_of_skills'];

$mysql = "select rater_level_$default from $skillraters_table where skill_type='$skill_type'";

$skilllevel_arr = $db_object->get_single_column($mysql);


$no_skills1 = $no_of_skills - 1;
$fName = $no_of_skills;

			for($x=0;$x<$no_of_skills;$x++)
			  {
			  	
			  $fSno = $x+1;
			  
			  $fName_level = $skilllevel_arr[$no_skills1];
			  
			  $percent = $percent_arr[$no_skills1];
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


$a=0;
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
		


}


$obj = new showAll_questions;
 
if($later)
	{
		if($user_id == 1)
		{
		$test_table = $common->prefix_table("tests");
		$quest_table = $common->prefix_table("questions");
		$ans_table = $common->prefix_table("answers");
		$skill_percent_table = $common->prefix_table("skill_percent");
		}
		else
		{
		$test_table = $common->prefix_table("unapproved_tests");
		$quest_table = $common->prefix_table("unapproved_questions");
		$ans_table = $common->prefix_table("unapproved_answers");
		$skill_percent_table = $common->prefix_table("unapproved_skill_percent");
		}
	$obj->editAllquestions($db_object,$common,$post_var,$user_id,$error_msg,$test_table,$quest_table,$ans_table,$skill_percent_table,$default);

	header("Location:front_panel.php");

	}

else if($approve)
	{
		include("header.php");
		
		if($user_id == 1)
		{
		$test_table = $common->prefix_table("tests");
		$quest_table = $common->prefix_table("questions");
		$ans_table = $common->prefix_table("answers");
		$skill_percent_table = $common->prefix_table("skill_percent");
		}
		else
		{

		$test_table = $common->prefix_table("unapproved_tests");
		$quest_table = $common->prefix_table("unapproved_questions");
		$ans_table = $common->prefix_table("unapproved_answers");
		$skill_percent_table = $common->prefix_table("unapproved_skill_percent");
		}



// if the user changes the contents of the existing test then it will be prompted to the admin for approval....

if($user_id !=1)
{
$mysql ="update $test_table set status = 'p' where test_id = '$test_id'";
$db_object->insert($mysql);
}

		
		$obj->editAllquestions($db_object,$common,$post_var,$user_id,$error_msg,$test_table,$quest_table,$ans_table,$skill_percent_table,$default);

//===
if($user_id == 1)
		{
		$message = $error_msg["cApprovetest"];
 
		echo $message;
		}
	else
		{
		$message = $error_msg["cAddtest"];
 
		echo $message;

		$common->email_to_admin($db_object,$user_id);	
		}
//===

		$common->email_to_admin($db_object,$user_id);

	}
else if($testdrive)
	{
	include("header.php");
	
	if($user_id == 1)
	{
		$test_table = $common->prefix_table("tests");
		$quest_table = $common->prefix_table("questions");
		$ans_table = $common->prefix_table("answers");
		$skill_percent_table = $common->prefix_table("skill_percent");
	}
	else
	{
		$test_table = $common->prefix_table("unapproved_tests");
		$quest_table = $common->prefix_table("unapproved_questions");
		$ans_table = $common->prefix_table("unapproved_answers");
		$skill_percent_table = $common->prefix_table("unapproved_skill_percent");
	}
		$obj->editAllquestions($db_object,$common,$post_var,$user_id,$error_msg,$test_table,$quest_table,$ans_table,$skill_percent_table,$default);
	
	if($user_id == 1)
	{
		$test_tablename = $common->prefix_table("tests");
		$quest_tablename = $common->prefix_table("questions");
		$ans_tablename = $common->prefix_table("answers");
		$skill_percent_tablename = $common->prefix_table("skill_percent");
	}
	else
	{
		$test_tablename = $common->prefix_table("unapproved_tests");
		$quest_tablename = $common->prefix_table("unapproved_questions");
		$ans_tablename = $common->prefix_table("unapproved_answers");
		$skill_percent_tablename = $common->prefix_table("unapproved_skill_percent");
	
	}
	
	$post_var["test_mode"] = "unapproved";
		
	$common->take_test($db_object,$post_var,$user_id,$quest_tablename,$test_tablename,$ans_tablename,$skill_percent_tablename);
	
			

	}

else
	{
	
$obj->showAllquestions($db_object,$common,$post_var,$user_id,$error_msg,$default);
	}



include("footer.php");
