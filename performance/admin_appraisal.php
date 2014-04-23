<?php
include_once("../session.php");

include_once("header.php");

class admin_appraisal
{
	function emp_appraisal($db_object,$common,$user_id,$fUser,$error_msg,$default)
	{
		$assign_performance_appraisal=$common->prefix_table("assign_performance_appraisal");
		
		$approved_selected_objective=$common->prefix_table("approved_selected_objective");
		
		$sql="select dummy_id,status from $assign_performance_appraisal where user_id='$fUser' and status<>'h'";
		
		$res1=$db_object->get_rsltset($sql);
		
		$c=0;
		
	for($i=0;$i<count($res1);$i++)
		{
			$ch_status=$res1[$i][status];
			
			if($ch_status!='h' || $ch_status=="null")
			{
			
				$res[$c]=$res1[$i];
				
				$c++;
			}
		}
	
		$qry="select o_id from $approved_selected_objective where user_id='$fUser'";
		
		$result=$db_object->get_single_column($qry);
		
		$name=$common->name_display($db_object,$fUser);
		
		if($result[0]=="")
		{
			echo $name;
			
			echo $error_msg['cNoObjective'];
			
			include_once("footer.php");
			
			exit;
		}
		
		if($res1[0]!="")
		{
			echo $error_msg['cPresentlyUnderAppraisal'];
			
			include_once("footer.php");
			
			exit;
		}
		else
		{
			$qry="insert into $assign_performance_appraisal set user_id='$fUser',boss_user_id='$user_id',
			
			check_status='n',date_added=now()";
			
			$db_object->insert($qry);
			
			$dummy_qry="select dummy_id from $assign_performance_appraisal where user_id='$fUser' and
			
			boss_user_id='$user_id'";
			
			$result=$db_object->get_a_line($dummy_qry);
			
			$dummy_id=$result[dummy_id];
			
			$action="show";
			
			include_once("complete_employee_appraisal.php");
			
		}
	}
}
$obj=new admin_appraisal();

$obj->emp_appraisal($db_object,$common,$user_id,$fUser,$error_msg,$default);

?>
