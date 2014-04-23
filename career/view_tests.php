<?php
include("../session.php");
include("header.php");
class View_tests
{
  function view_test($common,$db_object,$user_id,$gbl_skill_type,$error_msg)
  {
	$path=$common->path;
	$xFile=$path."templates/career/view_tests.html";
	$xTemplate=$common->return_file_content($common,$xFile);
	preg_match("/<{skill_loopstart}>(.*?)<{skill_loopend}>/s",$xTemplate,$mat);
	$replace=$mat[1];
	$unapproved_tests=$common->prefix_table("unapproved_tests");
	$unapproved_skill_percent=$common->prefix_table("unapproved_skill_percent");
/*
	
	$unapproved_tests=$common->prefix_table("tests");
	$unapproved_skill_percent=$common->prefix_table("skill_percent");
*/
	$skills=$common->prefix_table("skills");
	$user_table=$common->prefix_table("user_table");
	if($user_id!=1)
	{
	$selqry="select distinct($unapproved_tests.test_id),$unapproved_tests.test_name,$unapproved_tests.test_type,$user_table.username from $unapproved_tests,$user_table where  $unapproved_tests.user_id=$user_table.user_id and $user_table.admin_id='$user_id'";
	}
	else
	{
			$selqry="select distinct($unapproved_tests.test_id),$unapproved_tests.test_name,$unapproved_tests.test_type,$user_table.username from $unapproved_tests,$user_table where  $unapproved_tests.user_id=$user_table.user_id and $user_table.user_id<>'$user_id'";
	}
	$result1=$db_object->get_rsltset($selqry);

//echo $selqry;

//	$selqry="select distinct($unapproved_tests.test_id),$unapproved_skill_percent.skill_id,$unapproved_tests.test_name,$skills.skill_name,$unapproved_tests.user_id from $unapproved_skill_percent,$skills,$unapproved_tests,$user_table where $unapproved_skill_percent.skill_id=$skills.skill_id and $unapproved_skill_percent.test_id=$unapproved_tests.test_id and $user_table.user_id=$unapproved_tests.user_id and $user_table.admin_id='$user_id'";
//	$resultset=$db_object->get_rsltset($selqry);

preg_match("/<{skilltest_loopstart}>(.*?)<{skilltest_loopend}>/s",$replace,$mat1);
$re=$mat1[1];




	for($j=0;$j<count($result1);$j++)
	{
		$test_name=$result1[$j]["test_name"];
		$test_id=$result1[$j]["test_id"];
		$temp_type=$result1[$j]["test_type"];
		$user_name=$result1[$j]["username"];
		$test_type=$gbl_skill_type[$temp_type];
		$selqry="select distinct($unapproved_skill_percent.skill_id),$unapproved_skill_percent.skill_id,$skills.skill_name from $unapproved_skill_percent,$skills where $unapproved_skill_percent.skill_id=$skills.skill_id and test_id='$test_id'";
		$resultset=$db_object->get_rsltset($selqry);
		
		for($i=0;$i<count($resultset);$i++)
		{
			//---replaces the skill names alone for the inner table
			$skill_name=$resultset[$i]["skill_name"];
			$re1.=preg_replace("/<{(.*?)}>/e","$$1",$re);
		}


	$replace1=preg_replace("/<{skilltest_loopstart}>(.*?)<{skilltest_loopend}>/s",$re1,$replace);
	//after inner tables settlement outer table is governed
	
	$replaced.=preg_replace("/<{(.*?)}>/e","$$1",$replace1);
		$re1="";
	}
	
if($replaced=="")
{
	$replaced=$error_msg["cEmptyrecords"];
	echo $replaced;
	include_once("footer.php");
	exit;
}

$xTemplate=preg_replace("/<{skill_loopstart}>(.*?)<{skill_loopend}>/s",$replaced,$xTemplate);
$xTemplate=$common->direct_replace($db_object,$xTemplate,$vals);
echo $xTemplate;

   }

}
$viewobj=new View_tests;
$viewobj->view_test($common,$db_object,$user_id,$gbl_skill_type,$error_msg);
include("footer.php");
?>
