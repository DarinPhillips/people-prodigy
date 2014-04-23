<?php
include("../session.php");
include("header.php");
class Test_Taken
{
	function display_statistics($common,$db_object,$user_id,$default,$error_msg)
	{
		$path=$common->path;
		$xFile=$path."templates/career/tests_taken.html";
		$xTemplate=$common->return_file_content($db_object,$xFile);
		$user_tests=$common->prefix_table("user_tests");
		$tests=$common->prefix_table("tests");
		$user_scores=$common->prefix_table("user_scores");
		$skills=$common->prefix_table("skills");
		$questions=$common->prefix_table("questions");
		
		$stflds=$common->return_fields($db_object,$user_scores);
		
/*		
$selqry="select distinct($user_scores.skill_id),$user_tests.user_id,
$tests.test_name,$user_scores.skill_id,$skills.skill_name from
$user_tests left join $tests on $user_tests.test_id=$tests.test_id
left join $user_scores on $user_tests.user_testid=$user_scores.user_testid
left join $skills on $user_scores.skill_id=$skills.skill_id";
*/

$selqry="select $tests.test_id,$tests.test_name,$user_tests.user_id,count($tests.test_id)as number from $tests left join $user_tests on $tests.test_id=$user_tests.test_id  group by test_id order by number";
$testset=$db_object->get_rsltset($selqry);



//$selqry="select distinct($questions.skill_id),$user_tests.test_id,$skills.skill_name from $user_tests left join $questions on $user_tests.test_id=$questions.test_id left join $skills on $questions.skill_id=$skills.skill_id";
$selqry="select distinct($questions.skill_id),$user_tests.test_id,$skills.skill_name from $user_tests,$questions,$skills where $user_tests.test_id=$questions.test_id and $questions.skill_id=$skills.skill_id";
$skill_set=$db_object->get_rsltset($selqry);




for($i=0;$i<count($skill_set);$i++)
{
	$skill_name=$skill_set[$i]["skill_name"];
	$test_id=$skill_set[$i]["test_id"];
	$testskill[$test_id][]=$skill_name;
}


preg_match("/<{test_loopstart}>(.*?)<{test_loopend}>/s",$xTemplate,$mat);
$replace=$mat[1];
for($i=0;$i<count($testset);$i++)
{
	$test_name=$testset[$i]["test_name"];
	$test_id=$testset[$i]["test_id"];
	for($j=0,$skill_name="";$j<count($testskill[$test_id]);$j++)
	{$skill_name.=$testskill[$test_id][$j]."<br>";}
	$number=$testset[$i]["number"];
	if($skill_name!="")
	{
	$replaced.=preg_replace("/<{(.*?)}>/e","$$1",$replace);
	}
}
if($replaced=="")
{
	echo $error_msg['cEmptyrecords'];
	
	include_once("footer.php");exit;
}
$xTemplate=preg_replace("/<{test_loopstart}>(.*?)<{test_loopend}>/s",$replaced,$xTemplate);
$xTemplate=$common->direct_replace($db_object,$xTemplate,$vasl);
		echo $xTemplate; 
	}
	
}
$tobj= new Test_Taken;
$tobj->display_statistics($common,$db_object,$user_id,$default,$error_msg);


include("footer.php");
?>
