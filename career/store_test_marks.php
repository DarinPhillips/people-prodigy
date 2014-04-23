<?php
/*---------------------------------------------
SCRIPT:store_test_marks.php
AUTHOR:info@chrisranjana.com	
UPDATED:30th Sept

DESCRIPTION:
This script stores the test marks.

---------------------------------------------*/
include("../session.php");
include_once("header.php");
class storeMarks
{
	function store_marks($db_object,$common,$post_var,$user_id,$default)
	{
		
	
		$xPath=$common->path;
		$returncontent=$xPath."/templates/career/store_test_marks.html";
		$returncontent=$common->return_file_content($db_object,$returncontent);
		
		//print_r($post_var);exit;
		
		$user_scores_table = $common->prefix_table("user_scores");
		$user_tests_table = $common->prefix_table("user_tests");
		$answers_table = $common->prefix_table("answers");
		$questions_table = $common->prefix_table("questions");
		$tests_table = $common->prefix_table("tests");
		$usertestgrade_table = $common->prefix_table("user_test_grade");		
		$skill_percent_table = $common->prefix_table("skill_percent");
		$skill_table = $common->prefix_table("skills");
			
	while(list($kk,$vv)=@each($post_var))
		{
		$$kk=$vv;
		
		if(ereg("^fCheck_",$vv))
			{
				
			$check_array[$vv] = $vv;
			}
		
		}


	preg_match("/<{result_loopstart}>(.*?)<{result_loopend}>/s",$returncontent,$res_match);
	
	$newres_match = $res_match[1];
	
//Change the test status to completed when the user has completed the test...
	
	$mysql = "update $user_tests_table set test_completed='y' where user_id='$user_id' and test_id = '$test_id'";
	$db_object->insert($mysql);
	$mysql="select sum(score) as score,skill_id from $questions_table where test_id='$test_id' group by skill_id";
	//echo $mysql;exit;
	$total_score=$db_object->get_rsltset($mysql);
	//print_r($total_score);exit;	
		
	$total_skill_score = $common->return_Keyedarray($total_score,'skill_id','score');

	//print_r($total_skill_score);exit;
	
	$c_arr = @array_keys($check_array);

	for($i=0;$i<count($c_arr);$i++)
		{
			
		$ckey=$c_arr[$i];
		$cval=$check_array["$ckey"];

		list($un,$qid,$aid)=split("_",$ckey,3);
		
		$mysql = "select skill_id from $questions_table where q_id='$qid'";
	
		$skillarr = $db_object->get_a_line($mysql);
	
		$skill_id = $skillarr["skill_id"];
		
		$mysql = "select percent from $skill_percent_table where skill_id='$skill_id'";
		$percentarr = $db_object->get_single_column($mysql);
	
		$mysql = "select status from $answers_table where ans_id='$aid'";
		$status_arr = $db_object->get_a_line($mysql);
		
		$mysql = "select score from $questions_table where q_id='$qid' and test_id='$test_id'";
		$score_arr = $db_object->get_a_line($mysql);
		
		$status = $status_arr["status"];

		$x = $score_arr["score"];

				if($status == "r")
					{
					$totalscore["$skill_id"] +=$x;	
					}
					if($status != "r")
					{
						$x = 0;
						
					}
		
		$mysql = "select user_testid from $user_tests_table where user_id='$user_id' and test_id = '$test_id'";
		//echo $mysql;exit;
		$arr = $db_object->get_a_line($mysql);
		$user_testid = $arr['user_testid'];			
		
		//store test result info into table...
		
		$mysql = "insert into $user_scores_table set user_testid = '$user_testid' , question_id = '$qid' , answer_id = '$aid' , mark_scored = '$x',skill_id='$skill_id'";
		//echo "$mysql<br>";exit;
		$db_object->insert($mysql);
					
		
		}
	
	
		
	
		$mysql="select $skill_table.skill_id,skill_name from $questions_table,$skill_table where $skill_table.skill_id =$questions_table.skill_id and  test_id='$test_id' group by skill_id";
		
		$sk_arr = $db_object->get_rsltset($mysql);
		
			$arr_keys=@array_keys($totalscore);
			
			for($r=0;$r<count($sk_arr);$r++)
				{
				$skill_id=$sk_arr[$r]["skill_id"];
			
				$skill_name = $sk_arr[$r]["skill_name"];
			
				$totalscore_new=$totalscore["$skill_id"];
				$skill_totalscore = $total_skill_score["$skill_id"];
			
				$score_percent = ($totalscore_new / $skill_totalscore) * 100;
				$score_percent = round($score_percent,2);
		
				$mysql = "select level from $skill_percent_table where test_id='$test_id' and skill_id='$skill_id' and percent >='$score_percent' order by percent asc  limit 0,1";

				//echo "mysql=$mysql<br>";exit;

				$levelarr = $db_object->get_a_line($mysql);
				
				if($levelarr[0] == "")
				{
					$mysql = "select level from $skill_percent_table where test_id='$test_id' and skill_id='$skill_id' and percent <='$score_percent' order by percent desc  limit 0,1";
				
					//echo "mysql=$mysql<br>";
					
					$levelarr = $db_object->get_a_line($mysql);

				}

				$level = $levelarr["level"];
				//exit;
/////////////////////
		$raterlabelrelate_table = $common->prefix_table('rater_label_relate');
		$skill_percent_table = $common->prefix_table('skill_percent');
		$skillraters_table = $common->prefix_table('skill_raters');
				
		$mysql = "select $raterlabelrelate_table.rater_id 
		from $raterlabelrelate_table,$skill_percent_table 
		where $skill_percent_table.level = $raterlabelrelate_table.rater_labelno 
		and $skill_percent_table.test_id='$test_id' 
		and $skill_percent_table.skill_id='$skill_id' 
		and $skill_percent_table.level='$level'";

//echo "$mysql<br>";
$labelrelate_arr = $db_object->get_a_line($mysql);
//print_r($labelrelate_arr);
$rater_id = $labelrelate_arr['rater_id'];

$mysql = "select rater_level_$default as level from $skillraters_table where rater_id = '$rater_id' and type_name='n'";
//echo "$mysql<br>";
$level_display_arr = $db_object->get_a_line($mysql);


$level_display = $level_display_arr['level'];
//echo "level display is $level_display";				
				
/////////////////////				
				
				if($totalscore_new == "")
				{
					$totalscore_new=0;
				}
				if($level== "")
				{
					$level="Fail";
				}
				
				$mysql = "insert into $usertestgrade_table set skill_id = '$skill_id',grade='$level',percentage='$score_percent',user_testid = '$user_testid'";
				//echo $mysql;
				$db_object->insert($mysql);
				
				
				$returncontent1 .= preg_replace("/<{(.*?)}>/e","$$1",$newres_match);
			
				
				}
				
		
		$returncontent = preg_replace("/<{result_loopstart}>(.*?)<{result_loopend}>/s",$returncontent1,$returncontent);
		
		$returncontent = $common->direct_replace($db_object,$returncontent,$values);
		
		
		echo $returncontent;
		
	}
}
$obj = new storeMarks;

//$post_var	= array_merge($_POST,$_GET);
	

 
$obj->store_marks($db_object,$common,$post_var,$user_id,$default);
 
include_once("footer.php");