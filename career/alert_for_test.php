<?php
/*---------------------------------------------
SCRIPT:alert_for_test.php
AUTHOR:info@chrisranjana.com	
UPDATED:30th Sept

DESCRIPTION:
This script displays alert for the tests.

---------------------------------------------*/
include("../session.php");
include_once("header.php");

class alertForTest
{
	function show_alert_screen($db_object,$common,$post_var,$gbl_test_mode,$gbl_skill_type,$default,$user_id)
	{
		while(list($kk,$vv)=@each($post_var))
		{

		$$kk=$vv;
	
		}
	
		$xPath=$common->path;
		$returncontent=$xPath."/templates/career/alert_for_test.html";
		$returncontent=$common->return_file_content($db_object,$returncontent);
		
		preg_match("/<{alert_start}>(.*?)<{alert_end}>/s",$returncontent,$matched);
		$replace=$matched[1];
		
		$appraisal_table = $common->prefix_table("appraisal");
		$fields = $common->return_fields($db_object,$appraisal_table);
		$mysql = "select $fields from $appraisal_table where user_id='$user_id' and test_mode = 'Test'";
		$detail_arr = $db_object->get_rsltset($mysql);
		

		$replaced = "";
		for($i=0;$i<count($detail_arr);$i++)
		{
			
			$test_mode = $detail_arr[$i]['test_mode'];
			
			$test_typevar = $detail_arr[$i]['test_type'];
			$test_type = $gbl_skill_type[$test_typevar];
			
			$user_id = $detail_arr[$i]['user_id'];
			
			if(($test_typevar == 'i') && (test_mode != 'Test'))
			{
			$returncontent=preg_replace("/<{interalert_(.*?)}>/s","",$returncontent);
				
			}
			
		
			if(($test_typevar == 't') && (test_mode != 'Test')) 
			{
				$returncontent=preg_replace("/<{techalert_(.*?)}>/s","",$returncontent);
			}
		
	
			$values[user_id] = $user_id;
			$values[test_mode]=$test_mode;
			$values[test_type]=$test_type;
			$values[test_typevar] = $test_typevar;
			

			$replaced .= $common->direct_replace($db_object,$replace,$values);
			
			
		}	
		
		$returncontent=preg_replace("/<{interalert_start}>(.*?)<{interalert_end}>/s","",$returncontent);	
		$returncontent=preg_replace("/<{techalert_start}>(.*?)<{techalert_end}>/s","",$returncontent);		
		
		
		
		$returncontent=preg_replace("/<{alert_start}>(.*?)<{alert_end}>/s",$replaced,$returncontent);
		$returncontent = $common->direct_replace($db_object,$returncontent,$values);
	
		echo $returncontent;
	}
		
}
$obj = new alertForTest;

//$post_var	= @array_merge($_POST,$_GET);
	

 
$obj->show_alert_screen($db_object,$common,$post_var,$gbl_test_mode,$gbl_skill_type,$default,$user_id);
 
include_once("footer.php");
?>