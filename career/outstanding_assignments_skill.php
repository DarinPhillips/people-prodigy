<?php
include_once("../session.php");

include_once("header.php");

class assignments
{
	function outstanding_assignments($db_object,$common,$user_id,$error_msg,$gbl_date_format)
	{
		$assign_tech_skill_builder=$common->prefix_table("assign_tech_skill_builder");
		
		$user_table=$common->prefix_table("user_table");
		
		$position=$common->prefix_table("position");
		
		$skill_builder=$common->prefix_table("skill_builder");
		
		$assign_tech_skill_builder=$common->prefix_table("assign_tech_skill_builder");
		
		$qry="select user_id from $user_table where admin_id='$user_id'";
		
		$users=$db_object->get_single_column($qry);	
		
		if(count($users)>0)
		{
			$users=@implode(",",$users);
			
			$users_set="(".$users.")";
			
			if($user_id!=1)
			{
			
				$sql="select $assign_tech_skill_builder.status,user_id,admin_id,date_format(date,'$gbl_date_format') as date,position_id,position_name from $assign_tech_skill_builder,$position
				
				where $position.pos_id=$assign_tech_skill_builder.position_id and
				
				user_id in $users_set";
				
				$result=$db_object->get_rsltset($sql);
				
				//$sql="select user_id,
			}
			else
			{
				$sql="select $assign_tech_skill_builder.status,user_id,admin_id,date_format(date,'$gbl_date_format') as date,position_id,position_name from $assign_tech_skill_builder,$position
				
				 where $position.pos_id=$assign_tech_skill_builder.position_id";
				 
				 $result=$db_object->get_rsltset($sql);
				
			}
			
			
			
			
		}
		
		if($result[0]=="")
		{
			echo $error_msg['cNoSkillBuilderAssigned'];
			
			include_once("footer.php");
			
			exit;
		}
		else
		{
			$path=$common->path;
			
			$xtemplate=$path."templates/career/outstanding_assignments_skill.html";
			
			$file=$common->return_file_content($db_object,$xtemplate);
			
			$pattern="/<{row_loopstart}>(.*?)<{row_loopend}>/s";
				
		preg_match($pattern,$file,$match);
		
		$match=$match[0];
		
		for($i=0;$i<count($result);$i++)
		{
			$userid=$result[$i][user_id];
			
			$username=$common->name_display($db_object,$userid);
					
			$date=$result[$i][date];
			
			$position_name=$result[$i][position_name];
				
			$sql="select email from $user_table where user_id='$userid'";
			
			$res=$db_object->get_a_line($sql);
			
			$email=$res[email];
			
			$pos_id=$result[$i][position_id];
			
			$status=$result[$i][status];
			
			if($status!="h")
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

include_once("footer.php");
?>
