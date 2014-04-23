<?php
include("../session.php");
include("header.php");
class Display_Employee_Test
{
	function display_test_details($common,$db_object,$test_id)
	{
		$path=$common->path;
		$xFile=$path."templates/career/view_employee_test.html";
		$xTemplate=$common->return_file_content($db_object,$xFile);

		$user_tests=$common->prefix_table("user_tests");
		$user_table=$common->prefix_table("user_table");

		$tflds=$common->return_fields($db_object,$user_tests);
		$selqry="select $tflds from $user_tests";
		$testdetails=$db_object->get_rsltset($selqry);
		$selqry="select distinct($user_table.user_id),$user_table.username,$user_table.email,$user_tests.test_taken_date from $user_tests,$user_table where $user_tests.user_id=$user_table.user_id and $user_tests.test_id='$test_id'";
		$testdetail=$db_object->get_rsltset($selqry);
		$value["test_loop"]=$testdetail;
		$xTemplate=$common->simpleloopprocess($db_object,$xTemplate,$value);
		$xTemplate=$common->direct_replace($db_object,$xTemplate,$vals);
		echo $xTemplate;
	}
}
$tobj = new Display_Employee_Test;
$tobj->display_test_details($common,$db_object,$test_id);
include("footer.php");
?>