<?php
include_once("../session.php");

include_once("header.php");

class nonapproved_appraisal
{
	function fix_nonapproved($db_object,$common,$user_id,$error_msg)
	{
		$assign_performance_appraisal=$common->prefix_table("assign_performance_appraisal");
		
		$user_table=$common->prefix_table("user_table");
		
		$sql="select $assign_performance_appraisal.status,$user_table.username,$assign_performance_appraisal.dummy_id, 
		
		$assign_performance_appraisal.submitted_on,$assign_performance_appraisal.rejection_date from $user_table,
		
		$assign_performance_appraisal where $assign_performance_appraisal.user_id=$user_table.user_id 
		
		and $assign_performance_appraisal.boss_user_id='$user_id' and $assign_performance_appraisal.check_status='c' and $assign_performance_appraisal.status<>'h'";
//
		
		$result1=$db_object->get_rsltset($sql);
	
		if($result1[0]=="")
		{
			echo $error_msg['cEmptyrecords'];
			
			include_once("footer.php");exit;
		}
		
		$c=0;
		
		for($i=0;$i<count($result1);$i++)
		{
			$ch_status=$result1[$i][status];
			
			if($ch_status!="h")
			{
				$result[$c]=$result1[$i];
				
				$c++;
			}
		}

		$xpath=$common->path;
		
		$xtemplate=$xpath."templates/performance/fix_nonapproved_appraisals.html";
		
		$file=$common->return_file_content($db_object,$xtemplate);
		
		preg_match("/<{appraisal_loopstart}>(.*?)<{appraisal_loopend}>/s",$file,$match);
		
		$match=$match[0];
		
		for($i=0;$i<count($result);$i++)
		{
			$dummy_id=$result[$i][dummy_id];
			
			$username=$result[$i][username];
			
			$submit_date=$result[$i][submitted_on];
			
			$reject_date=$result[$i][rejection_date];
			
			$str.=preg_replace("/<{(.*?)}>/e","$$1",$match);	
		}
		$file=preg_replace("/<{appraisal_loopstart}>(.*?)<{appraisal_loopend}>/s",$str,$file);
		
		$file=$common->direct_replace($db_object,$file,$xArray);
		
		echo $file;
	}
}

$obj=new nonapproved_appraisal();

$obj->fix_nonapproved($db_object,$common,$user_id,$error_msg);

include_once("footer.php");

?>
