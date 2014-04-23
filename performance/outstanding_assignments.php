<?php
include_once("../session.php");

include_once("header.php");

class outstanding
{
	function outstanding_assignments($db_object,$common,$user_id,$error_msg,$gbl_date_format)
	{
		$path=$common->path;
		
		$xtemplate=$path."templates/performance/outstanding_assignments.html";
		
		$file=$common->return_file_content($db_object,$xtemplate);
		
		$assign_performance_appraisal=$common->prefix_table("assign_performance_appraisal");
		
		$user_table=$common->prefix_table("user_table");
					
		$qry="select user_id from $user_table where admin_id='$user_id'";
		
		$users=$db_object->get_single_column($qry);	
		
		if(count($users)>0)
		{
			$users=@implode(",",$users);
			
			$users_set="(".$users.")";
			
			if($user_id!=1)
			{
			
				$sql="select user_id,boss_user_id,date_format(date_added,'$gbl_date_format') as date_added,status from $assign_performance_appraisal where user_id in $users_set";
				
			}
			else
			{
				$sql="select user_id,boss_user_id,date_format(date_added,'$gbl_date_format') as date_added,status from $assign_performance_appraisal";
				
				
			}
		
			$result=$db_object->get_rsltset($sql);
					
			
		}
		
		if($result[0]=="")
		{
			echo $error_msg['cNoAppraisalAssigned'];
			
			include_once("footer.php");
			
			exit;
		}
		
		$pattern="/<{row_loopstart}>(.*?)<{row_loopend}>/s";
				
		preg_match($pattern,$file,$match);
		
		$match=$match[0];
		
		for($i=0;$i<count($result);$i++)
		{
			$userid=$result[$i][user_id];
			
			$username=$common->name_display($db_object,$userid);
			
			$assigned=$result[$i][boss_user_id];
			
			$assigned_by=$common->name_display($db_object,$assigned);
			
			$date=$result[$i][date_added];
						
			$sql="select email from $user_table where user_id='$assigned'";
			
			$res=$db_object->get_a_line($sql);
			
			$email=$res[email];
			
			$status=$result[$i][status];
			
			if($status!='h')
			{
				$highlight="class=TR";
				
			}
			else
			{
				$highlight="";
			}
			
			$str.=preg_replace("/<{(.*?)}>/e","$$1",$match);
		}

		$file=preg_replace($pattern,$str,$file);

		$file=$common->direct_replace($db_object,$file,$xArray);
		
		echo $file;
		
	}
}

$obj=new outstanding();

$obj->outstanding_assignments($db_object,$common,$user_id,$error_msg,$gbl_date_format);

include_once("footer.php");

?>
