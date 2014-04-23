<?php
include_once("../session.php");

include_once("header.php");

class outstanding
{
	function outstanding_assignments($db_object,$common,$user_id,$error_msg)
	{
		$path=$common->path;
		
		$xtemplate=$path."templates/learning/outstanding_assignments.html";
		
		$file=$common->return_file_content($db_object,$xtemplate);
		
		$assign_solution_builder=$common->prefix_table("assign_solution_builder");
		
		$user_table=$common->prefix_table("user_table");
		
		$skills=$common->prefix_table("skills");
		
		$assign_solution_builder=$common->prefix_table("assign_solution_builder");
		
		$approved_devbuilder=$common->prefix_table("approved_devbuilder");
		
		$qry="select user_id from $user_table where admin_id='$user_id'";
		
		$users=$db_object->get_single_column($qry);	
		
		if(count($users)>0)
		{
			$users=@implode(",",$users);
			
			$users_set="(".$users.")";
			
			if($user_id!=1)
			{
			
				$sql="select $assign_solution_builder.skill_id,$assign_solution_builder.user_id,
				
				$assign_solution_builder.admin_id,$assign_solution_builder.date,$skills.skill_name from $assign_solution_builder,$skills where 
				
				$assign_solution_builder.skill_id=$skills.skill_id and user_id in $users_set order by date";
			}
			else
			{
				$sql="select user_id,$assign_solution_builder.skill_id,skill_name,admin_id,date from $assign_solution_builder,$skills where
				
				$assign_solution_builder.skill_id=$skills.skill_id order by date";
			}
		
			$result=$db_object->get_rsltset($sql);
					
			
		}
		
		if($result[0]=="")
		{
			echo $error_msg['cNoSolutionAssigned'];
			
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
			
			$assigned=$result[$i][admin_id];
			
			$assigned_by=$common->name_display($db_object,$assigned);
			
			$date=$result[$i][date];
			
			$skill_name=$result[$i][skill_name];
			
			$skill_id=$result[$i][skill_id];
			
			$qry="select $approved_devbuilder.status,email from $approved_devbuilder,
			
			$user_table where $approved_devbuilder.user_id=$user_table.user_id
			
			and $approved_devbuilder.user_id='$userid'
			
			and skill_id='$skill_id'";
			
			$res=$db_object->get_a_line($qry);
			
			$email=$res[email];
			
			if($res[status]=='a')
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

$obj->outstanding_assignments($db_object,$common,$user_id,$error_msg);

include_once("footer.php");

?>
