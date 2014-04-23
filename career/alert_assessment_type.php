<?php
/*---------------------------------------------
SCRIPT:alert_assessment_type.php
AUTHOR:info@chrisranjana.com	
UPDATED:30th Sept

---------------------------------------------*/
include("../session.php");
include_once("header.php");

class alertForAssessment
{
	function show_assessments($db_object,$common,$post_var,$user_id)
	{
		$xPath=$common->path;
		$returncontent=$xPath."/templates/career/alert_assessment_type.html";
		$returncontent=$common->return_file_content($db_object,$returncontent);
		
		
//Check if there is any rejected offers for any interpersonal or technical ratings....
//if there is a rejected offer display them else nullify the link....
		
		$other_raters = $common->prefix_table('other_raters');
		$mysql = "select rater_id from $other_raters where cur_userid = '$user_id' and status = 'r'";
		$rejected_inter_arr = $db_object->get_single_column($mysql);
		
		$tech_references = $common->prefix_table('tech_references');
		$mysql = "select ref_id from $tech_references where user_to_rate = '$user_id' and status = 'r'";
		$rejected_tech_arr = $db_object->get_single_column($mysql);
		
		if(($rejected_inter_arr == '') && ($rejected_tech_arr == ''))
		{
			$returncontent=preg_replace("/<{rejectedoffersdisplay_start}>(.*?)<{rejectedoffersdisplay_end}>/s","",$returncontent);							

		}
		else
		{
			$returncontent=preg_replace("/<{rejectedoffersdisplay_(.*?)}>/s","",$returncontent);	
		}
		
		
		
		$appraisal_table = $common->prefix_table("appraisal");
		$fields = $common->return_fields($db_object,$appraisal_table);
		$mysql = "select $fields from $appraisal_table where user_id='$user_id' and test_mode = '360'";
		
		$detail_arr = $db_object->get_rsltset($mysql);
		
		
		for($i=0;$i<count($detail_arr);$i++)
		{
			
			$test_mode = $detail_arr[$i]['test_mode'];
			
			$test_typevar = $detail_arr[$i]['test_type'];
			$test_type = $gbl_skill_type[$test_typevar];
			
			$user_id = $detail_arr[$i]['user_id'];
			
			if(($test_typevar == 'i') && (test_mode != '360'))
			{
			$returncontent=preg_replace("/<{interappraisal_(.*?)}>/s","",$returncontent);
				
			}
			
		
			if(($test_typevar == 't') && (test_mode != '360')) 
			{
				$returncontent=preg_replace("/<{techappraisal_(.*?)}>/s","",$returncontent);
			}
		
	
			$values[user_id] = $user_id;
			$values[test_mode]=$test_mode;
			$values[test_type]=$test_type;
			$values[test_typevar] = $test_typevar;
			

			//$replaced .= $common->direct_replace($db_object,$replace,$values);
			
			
		}	
		
		$returncontent=preg_replace("/<{interappraisal_start}>(.*?)<{interappraisal_end}>/s","",$returncontent);	
		$returncontent=preg_replace("/<{techappraisal_start}>(.*?)<{techappraisal_end}>/s","",$returncontent);		
		
		
		
		
		$returncontent = $common->direct_replace($db_object,$returncontent,$values);
	
		
		echo $returncontent;
	}
		
}
$obj = new alertForAssessment;

$obj->show_assessments($db_object,$common,$post_var,$user_id);

include_once('footer.php');
?>
