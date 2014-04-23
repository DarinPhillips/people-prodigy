<?php
/*---------------------------------------------
SCRIPT:calculate_marks.php
AUTHOR:info@chrisranjana.com	
UPDATED:9th Sept

DESCRIPTION:


---------------------------------------------*/
include("../session.php");
include("header.php");

class calculateMarks

{
	
function findResults($db_object,$common,$post_var,$user_id,$error_msg,$quest_tablename,$test_tablename,$ans_tablename,$skill_percent_tablename,$default)
{


	$quest_table=$common->prefix_table("$quest_tablename");
	$test_table=$common->prefix_table("$test_tablename");
	$ans_table=$common->prefix_table("$ans_tablename");
	$skill_percent_table=$common->prefix_table("$skill_percent_tablename");
	$skill_table = $common->prefix_table("skills");


$raterlabelrelate_table = $common->prefix_table('rater_label_relate');
$skillraters_table = $common->prefix_table('skill_raters');
				




	$xPath=$common->path;
	$xTemplate=$xPath."/templates/career/display_marks.html";
	$returncontent=$common->return_file_content($db_object,$xTemplate);
		
	
	while(list($kk,$vv)=@each($post_var))
		{
		$$kk=$vv;
		
		if(ereg("^fCheck_",$vv))
			{
				
			$check_array[$vv] = $vv;
			}
		
		}



		$mysql = "select test_name from $test_table where test_id = '$test_id'";

		$test_arr = $db_object->get_a_line($mysql);
		$fTestname = $test_arr["test_name"];

		$values["directreplace"]["fTestname"]=$fTestname;
		$values["directreplace"]["test_id"]=$test_id;
	
		
	preg_match("/<{result_loopstart}>(.*?)<{result_loopend}>/s",$returncontent,$res_match);
	
	$newres_match = $res_match[1];

	
	$mysql="select sum(score) as score,skill_id from $quest_table where test_id='$test_id' group by skill_id";
	$total_score=$db_object->get_rsltset($mysql);
		
		
	$total_skill_score = $common->return_Keyedarray($total_score,'skill_id','score');

	//print_r($total_skill_score);
	$c_arr = @array_keys($check_array);

	for($i=0;$i<count($c_arr);$i++)
		{
			
		$ckey=$c_arr[$i];
		$cval=$check_array["$ckey"];

		list($un,$qid,$aid)=split("_",$ckey,3);
		
		$mysql = "select skill_id from $quest_table where q_id='$qid'";
	
		$skillarr = $db_object->get_a_line($mysql);
	
		$skill_id = $skillarr["skill_id"];
		
		$mysql = "select percent from $skill_percent_table where skill_id='$skill_id'";
		$percentarr = $db_object->get_single_column($mysql);
	
		$mysql = "select status from $ans_table where ans_id='$aid'";
		$status_arr = $db_object->get_a_line($mysql);
		
		$mysql = "select score from $quest_table where q_id='$qid' and test_id='$test_id'";
		$score_arr = $db_object->get_a_line($mysql);
		
		$status = $status_arr["status"];

		$x = $score_arr["score"];

				if($status == "r")
					{
					$totalscore["$skill_id"] +=$x;	
					}
		
		
		}
	
	
	
		$mysql="select $skill_table.skill_id,skill_name from $quest_table,$skill_table where $skill_table.skill_id =$quest_table.skill_id and  test_id='$test_id' group by skill_id";
		$sk_arr = $db_object->get_rsltset($mysql);
		
			$arr_keys=@array_keys($totalscore);
			
			for($r=0;$r<count($sk_arr);$r++)
				{
				$skill_id=$sk_arr[$r]["skill_id"];
			
				$skill_name = $sk_arr[$r]["skill_name"];
			
				$totalscore_new=$totalscore["$skill_id"];
				$skill_totalscore = $total_skill_score["$skill_id"];

				if($skill_totalscore !=0)
				{
				$score_percent = ($totalscore_new / $skill_totalscore) * 100;
				}
				$score_percent = round($score_percent,2);
		
				$mysql = "select level from $skill_percent_table where test_id='$test_id' and skill_id='$skill_id' and percent >='$score_percent' order by percent asc  limit 0,1";

			//	echo "mysql=$mysql<br>";

				$levelarr = $db_object->get_a_line($mysql);
				
				if($levelarr[0] == "")
				{
					$mysql = "select level from $skill_percent_table where test_id='$test_id' and skill_id='$skill_id' and percent <='$score_percent' order by percent desc  limit 0,1";
				
				//	echo "mysql=$mysql<br>";
					$levelarr = $db_object->get_a_line($mysql);

				}

				$level = $levelarr["level"];
			
				///////////////////
				
$mysql = "select $raterlabelrelate_table.rater_id 
		from $raterlabelrelate_table,$skill_percent_table 
		where $skill_percent_table.level = $raterlabelrelate_table.rater_labelno 
		and $skill_percent_table.test_id='$test_id' 
		and $skill_percent_table.skill_id='$skill_id' 
		and $skill_percent_table.level='$level'";


$labelrelate_arr = $db_object->get_a_line($mysql);

$rater_id = $labelrelate_arr['rater_id'];

$mysql = "select rater_level_$default as level from $skillraters_table where rater_id = '$rater_id' and type_name='n'";
//echo $mysql;
$level_display_arr = $db_object->get_a_line($mysql);


$level_display = $level_display_arr['level'];


				///////////////////

				
				
				if($totalscore_new == "")
				{
					$totalscore_new=0;
				}
				if($level== "")
				{
					$level="Fail";
				}
				
				
				$returncontent1 .= preg_replace("/<{(.*?)}>/e","$$1",$newres_match);
			
				
				}
				
		
		$returncontent = preg_replace("/<{result_loopstart}>(.*?)<{result_loopend}>/s",$returncontent1,$returncontent);
		
		$returncontent = $common->direct_replace($db_object,$returncontent,$values);
		
		echo $returncontent;

	}
				
}

$obj = new calculateMarks;

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
if($fResult)
{
	
	if($user_id == 1)
	{
		if($test_mode == "temp")
		{
	
		$quest_tablename=$common->prefix_table("temp_questions");
		$test_tablename=$common->prefix_table("temp_tests");
		$ans_tablename=$common->prefix_table("temp_answers");
		$skill_percent_tablename=$common->prefix_table("temp_skill_percent");
		
		
	
		$obj->findResults($db_object,$common,$post_var,$user_id,$error_msg,$quest_tablename,$test_tablename,$ans_tablename,$skill_percent_tablename,$default);
		}
		elseif($test_mode == "unapproved")
		{
		$quest_tablename=$common->prefix_table("questions");
		$test_tablename=$common->prefix_table("tests");
		$ans_tablename=$common->prefix_table("answers");
		$skill_percent_tablename=$common->prefix_table("skill_percent");
	
		$obj->findResults($db_object,$common,$post_var,$user_id,$error_msg,$quest_tablename,$test_tablename,$ans_tablename,$skill_percent_tablename,$default);
	
		}
		else
		{
		$quest_tablename=$common->prefix_table("unapproved_questions");
		$test_tablename=$common->prefix_table("unapproved_tests");
		$ans_tablename=$common->prefix_table("unapproved_answers");
		$skill_percent_tablename=$common->prefix_table("unapproved_skill_percent");
		
	
		$obj->findResults($db_object,$common,$post_var,$user_id,$error_msg,$quest_tablename,$test_tablename,$ans_tablename,$skill_percent_tablename,$default);
		
		}
	}
	else
	{
		if($test_mode == "unapproved")
		{
		
		$quest_tablename=$common->prefix_table("unapproved_questions");
		$test_tablename=$common->prefix_table("unapproved_tests");
		$ans_tablename=$common->prefix_table("unapproved_answers");
		$skill_percent_tablename=$common->prefix_table("unapproved_skill_percent");
		
		$obj->findResults($db_object,$common,$post_var,$user_id,$error_msg,$quest_tablename,$test_tablename,$ans_tablename,$skill_percent_tablename,$default);
		}
		else
		{
		$quest_tablename=$common->prefix_table("temp_questions");
		$test_tablename=$common->prefix_table("temp_tests");
		$ans_tablename=$common->prefix_table("temp_answers");
		$skill_percent_tablename=$common->prefix_table("temp_skill_percent");
		
		$obj->findResults($db_object,$common,$post_var,$user_id,$error_msg,$quest_tablename,$test_tablename,$ans_tablename,$skill_percent_tablename,$default);
		
		}
	}
	
}

else
	{
	
$common->showForm($db_object,$common,$post_var,$user_id,$error_msg);		
	}

include_once("footer.php");
?>
