<?php
/*---------------------------------------------
SCRIPT:appraisal.php
AUTHOR:info@chrisranjana.com	
UPDATED:17th Sept

DESCRIPTION:
This script displays all the information for the tests.

---------------------------------------------*/
include("../session.php");
include_once("header.php");

class writeTest
{
	function showAll_tests($db_object,$common,$post_var,$user_id)
	{
		while(list($kk,$vv)=@each($post_var))
		{
		$$kk=$vv;
		}
		
		
		$xPath=$common->path;
		$returncontent=$xPath."/templates/career/write_test.html";
		$returncontent=$common->return_file_content($db_object,$returncontent);
		
		//print_r($post_var);
	
		$tests_table = $common->prefix_table("tests");
		$usertest_table = $common->prefix_table("user_tests");	
		$skills_for_rating = $common->prefix_table('skills_for_rating');
		$questions = $common->prefix_table('questions');
		$skills = $common->prefix_table('skills');
		
		//echo "userid is $user_id<br>";


		$mysql="select test_id from $usertest_table where user_id='$user_id' and test_completed='y'";

		$a_writtentests=$db_object->get_single_column($mysql);


		if($a_writtentests[0]!="")
		{

		$written_ids=@implode(",",$a_writtentests);

		$testclause =" and $tests_table.test_id not  in ($written_ids)  ";

		}



		
		//$mysql=" select $tests_table.test_name , $tests_table.test_id
		//from $tests_table where $tests_table.test_type='$test_type' $testclause";  //


$mysql = "select $skills_for_rating.skill_id
		from $skills_for_rating,$skills
		where $skills_for_rating.skill_id = $skills.skill_id
		and usr_id = '$user_id'
		and $skills_for_rating.skill_type = '$test_type'";
		
$skills_sel_arr = $db_object->get_single_column($mysql);

$skills_sel = @implode("','",$skills_sel_arr);

		$mysql = "select distinct($tests_table.test_id), $tests_table.test_name
				from $tests_table,$questions
				where questions.skill_id in ('$skills_sel')
				and $questions.test_id = $tests_table.test_id
				and $tests_table.test_type = '$test_type'
				$testclause";
				
				//echo "$mysql<br>";

		$test_arr = $db_object->get_rsltset($mysql);
		//print_r($test_arr);
		$values['testnames_loop'] = $test_arr;
		$returncontent	= $common->multipleloop_replace($db_object,$returncontent,$values,'');
		$values['test_type'] = $test_type;
		$returncontent = $common->direct_replace($db_object,$returncontent,$values);
	
		echo $returncontent;
		
		
	}
	function write_test($db_object,$common,$post_var,$user_id,$quest_tablename,$test_tablename,$ans_tablename,$skill_percent_tablename)
	{
		while(list($kk,$vv)=@each($post_var))
		{
		$$kk=$vv;
		}
		
		$xPath=$common->path;
		$returncontent=$xPath."/templates/career/user_write_test.html";
	
		$returncontent=$common->return_file_content($db_object,$returncontent);

		$quest_table=$common->prefix_table("$quest_tablename");
		$test_table=$common->prefix_table("$test_tablename");
		$ans_table=$common->prefix_table("$ans_tablename");
		$skill_percent = $common->prefix_table("$skill_percent_tablename");
	
		$mysql = "select test_type,test_name from $test_table where test_id='$test_id'";

		$testtype_arr = $db_object->get_a_line($mysql);
		
		$test_type = $testtype_arr["test_type"]; 
		$fTestname = $testtype_arr["test_name"];

		$values["directreplace"]["fTestname"]=$fTestname;
		$values["directreplace"]["test_id"]=$test_id;
		$values["directreplace"]["test_type"]=$test_type;
		$values["directreplace"]["test_mode"]=$test_mode;

	$returncontent = $common->direct_replace($db_object,$returncontent,$values);
	
	

	$mysql = "select * from $quest_table where test_id='$test_id' order by q_id "; 
	$questions_arr = $db_object->get_rsltset($mysql);

	preg_match("/<{quest_loopstart}>(.*?)<{quest_loopend}>/s",$returncontent,$qmatch);
	$newqmatch = $qmatch[1];
	
	$str = "";	

	preg_match("/<{ans_loopstart}>(.*?)<{ans_loopend}>/s",$returncontent,$amatch);
		

	for($i=0;$i<count($questions_arr);$i++)
		{
		$No = $i + 1;
		$newamatch = $amatch[1];
		
		
		
		$str1 = "";
		
		$q_id = $questions_arr[$i]["q_id"];
		$question = $questions_arr[$i]["question"];
		$status = $questions_arr[$i]["status"];
		$skill_id = $questions_arr[$i]["skill_id"];
		
		$mysql = "select * from $ans_table where q_id='$q_id' order by ans_id";
		//echo $mysql;
		$ans_arr = $db_object->get_rsltset($mysql);



			for($j=0;$j<count($ans_arr);$j++)
			{
			
			$ans_id = $ans_arr[$j]["ans_id"];
			$answer = $ans_arr[$j]["answer"];
			
			$answer=str_replace("$","&#36;",$answer);
					
			$ans = preg_replace("/<{(.*?)}>/e","$$1",$newamatch);

			$str1 .= $ans;
			
			}

		$finalmatch = preg_replace("/<{ans_loopstart}>(.*?)<{ans_loopend}>/s",$str1,$newqmatch);
		
		$questions = preg_replace("/<{(.*?)}>/e","$$1",$finalmatch);
	
		$str .= $questions;

		}
	$returncontent = preg_replace("/<{quest_loopstart}>(.*?)<{quest_loopend}>/s",$str,$returncontent);
		


	echo $returncontent;


		
	}
}
$obj = new writeTest;
//$post_var	= array_merge($_POST,$_GET);
//print_r($post_var);

if($fSubmit)
{
	$test_tablename = $common->prefix_table("tests");
	$quest_tablename = $common->prefix_table("questions");
	$ans_tablename = $common->prefix_table("answers");
	$skill_percent_tablename = $common->prefix_table("skill_percent");
		
	$user_test_table = $common->prefix_table("user_tests");
	//print_r($post_var);
	$mysql = "insert into $user_test_table set user_id='$user_id',test_id = '$test_id' , test_type='$test_type',test_taken_date = now()";
	//echo $mysql;exit;
	$db_object->insert($mysql);
	
	
	$obj->write_test($db_object,$common,$post_var,$user_id,$quest_tablename,$test_tablename,$ans_tablename,$skill_percent_tablename);
}

else
{
$obj->showAll_tests($db_object,$common,$post_var,$user_id);
}
include_once("footer.php");
?>