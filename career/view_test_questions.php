<?php
include("../session.php");
include("header.php");
class View_question
{
	function display($common,$db_object,$user_id,$test_id)
	{
		$path=$common->path;
		$xFile=$path."templates/career/view_test_questions.html";
		$xTemplate=$common->return_file_content($db_object,$xFile);
	$unapproved_test=$common->prefix_table("unapproved_tests");
	$unapproved_questions=$common->prefix_table("unapproved_questions");
	$unapproved_answers=$common->prefix_table("unapproved_answers");


	$selqry="select $unapproved_questions.q_id,$unapproved_questions.question from
	$unapproved_questions,$unapproved_test
	where $unapproved_questions.test_id=$unapproved_test.test_id and
	$unapproved_test.test_id='$test_id'";
	$questionset=$db_object->get_rsltset($selqry);
//	print_r($questionset);
	


	
	preg_match("/<{question_loopstart}>(.*?)<{question_loopend}>/s",$xTemplate,$mat);
	$replace=$mat[1];
	preg_match("/<{ans_loopstart}>(.*?)<{ans_loopend}>/s",$replace,$mat1);
	$replace1=$mat1[1];

$ansint="select $unapproved_answers.answer as answer,$unapproved_answers.ans_id,$unapproved_answers.status,$unapproved_answers.q_id from $unapproved_answers where "; 


	for($j=0;$j<count($questionset);$j++)
	{
	$ques=$questionset[$j]["q_id"];
	$ansint.="q_id=$ques  or ";
	}
	$ansint=substr($ansint,0,-4);
	$answerset=$db_object->get_rsltset($ansint);

$newarray=$common->group_similar($answerset,"q_id","answer");
	for($i=0;$i<count($questionset);$i++)
	{
		$No=$i+1;
		$ques_no=$questionset[$i]["q_id"];
		$answer_name=$answerset[$ques_no];
		$ans_no=$ques_no;
		$ans_n=$No;
		$question_name=$questionset[$i]["question"];
//------------------error may occur because the the loop runs one time less than the total count
//----------to avoid the "correct" key value to encounter in the loop
		for($j=0;$j<count($newarray[$ques_no])-1;$j++)
		{
		$q_id=$ques_no;

		$answer_name=$newarray[$ques_no][$j];
		if($answer_name==$newarray[$ques_no]["correct"])
		{
			
			$checked="checked";
		}
		else
		{
			$checked="";
		}
		$replaced1.=preg_replace("/<{(.*?)}>/e","$$1",$replace1);
					
		}
		$repl=preg_replace("/<{ans_loopstart}>(.*?)<{ans_loopend}>/s",$replaced1,$replace);
		$replaced.=preg_replace("/<{(.*?)}>/e","$$1",$repl);
		$replaced1="";
		$checked="";
	}
	$xTemplate=preg_replace("/<{question_loopstart}>(.*?)<{question_loopend}>/s",$replaced,$xTemplate);
		
$xTemplate=$common->direct_replace($db_object,$xTemplate,$vals);
		echo $xTemplate;
	}
	
}
$qobj=new View_question;
$user_table=$common->prefix_table("user_table");
$unapproved_tests=$common->prefix_table("unapproved_tests");
$selqry="select $unapproved_tests.test_id from $unapproved_tests,$user_table where $user_table.user_id=$unapproved_tests.user_id and $user_table.admin_id='$user_id'";
$testcheck=$db_object->get_single_column($selqry);




if(@in_array($test_id,$testcheck) || $user_id==1)
{
$qobj->display($common,$db_object,$user_id,$test_id);
}
else
{
	echo "This test is not under your control";
}
include("footer.php");
?>
