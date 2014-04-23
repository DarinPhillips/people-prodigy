<?php

include_once("../session.php");

include_once("header.php");				

class assignments
{
	function outstanding_assignments($db_object,$common,$user_id,$error_msg,$gbl_date_format)
	{
		$assign_succession_plan_sub=$common->prefix_table("assign_succession_plan_sub");
					
		$user_table=$common->prefix_table("user_table");
		
		$position=$common->prefix_table("position");
		
		$ch_boss=$common->is_boss($db_object,$user_id);
		
		$ch_admin=$common->is_admin($db_object,$user_id);
		
		$pos_qry="select position from $user_table where user_id='$user_id'";
		
		$pos_res=$db_object->get_a_line($pos_qry);
		
		$pos=$pos_res[position];
		
		if($ch_boss==1)
		{
			$users_under=$common->get_chain_below($pos,$db_object,$twodarr);
			
			$users_under_set=$common->get_user_id($db_object,$users_under);
			
			for($i=0;$i<count($users_under_set);$i++)
			{
				$users_under_id[$i]=$users_under_set[$i][user_id];
			}
		
		}
		else
		{
			$users_under_id=array();
		}
		if($ch_admin==1)
		{
		
		$qry="select user_id from $user_table where admin_id='$user_id'";
		
		$users=$db_object->get_single_column($qry);	
		
		}
		else
		{
			$users=array();
		}
		
		$users=@array_merge($users,$users_under_id);
												
		if(count($users)>0)
		{
			$users=@implode(",",$users);
			
			$users_set="(".$users.")";
			
			if($user_id!=1)
			{
			
				$sql="select $assign_succession_plan_sub.status,assigned_to,position,date_format(assigned_on,'$gbl_date_format') as date,position_name from $assign_succession_plan_sub,$position
				
				where $position.pos_id=$assign_succession_plan_sub.position and assigned_to<>'$user_id' and
				
				assigned_to in $users_set ";
				 
				$result=$db_object->get_rsltset($sql);
				
			
			}
			else
			{
				$sql="select $assign_succession_plan_sub.status,assigned_to,position,date_format(assigned_on,'$gbl_date_format') as date,position_name from $assign_succession_plan_sub,$position
				
				 where $position.pos_id=$assign_succession_plan_sub.position and assigned_to<>'$user_id'";
				 
				 $result=$db_object->get_rsltset($sql);
				
				
			}
			
			
			
			
		}
			
		if($result[0]=="")
		{
			echo "Employees under you have not been assigned any plan";
			
			//echo $error_msg['cNoSkillBuilderAssigned'];
			
			include_once("footer.php");
			
			exit;
		}
		else
		{
			$path=$common->path;
			
			$xtemplate=$path."templates/career/outstanding_assignments_plan.html";
			
			$file=$common->return_file_content($db_object,$xtemplate);
			
			$pattern="/<{row_loopstart}>(.*?)<{row_loopend}>/s";
				
		preg_match($pattern,$file,$match);
		
		$match=$match[0];
	
		for($i=0;$i<count($result);$i++)
		{
			$userid=$result[$i][assigned_to];
		
			$username=$common->name_display($db_object,$userid);
					
			$date=$result[$i][date];
			
			$position_name=$result[$i][position_name];
				
			$sql="select email from $user_table where user_id='$userid'";
			
			$res=$db_object->get_a_line($sql);
			
			$email=$res[email];
			
			$pos_id=$result[$i][position_id];
			
			$status=$result[$i][status];
			
			if($status!="y")
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
}
$obj=new assignments();

$obj->outstanding_assignments($db_object,$common,$user_id,$error_msg,$gbl_date_format);


include_once('footer.php');
?>
