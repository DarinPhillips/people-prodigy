<?php
include_once("../session.php");

include_once("header.php");

class outstanding
{
	function outstanding_assignments($db_object,$common,$user_id,$error_msg)
	{
		$path=$common->path;
		
		$xtemplate=$path."templates/career/outstanding_assignments_tests.html";
		
		$file=$common->return_file_content($db_object,$xtemplate);
		
		$assign_test_builder=$common->prefix_table("assign_test_builder");
		
		$user_table=$common->prefix_table("user_table");
		
		$skills=$common->prefix_table("skills");
			
		$qry="select user_id from $user_table where admin_id='$user_id'";
		
		$users=$db_object->get_single_column($qry);	
		
		if(count($users)>0)
		{
			$users=@implode(",",$users);
			
			$users_set="(".$users.")";
			
			if($user_id!=1)
			{
			
				$sql="select admin_id,user_id,skill_name,date,status from $assign_test_builder,$skills where
				
				$assign_test_builder.skill_id=$skills.skill_id and user_id in $users_set";
			}
			else
			{
				$sql="select admin_id,user_id,skill_name,date,status from $assign_test_builder,$skills where
				
				$assign_test_builder.skill_id=$skills.skill_id";
			}
		
			$result=$db_object->get_rsltset($sql);
					
			
		}
		
		if($result[0]=="")
		{
			 echo "The employees under "; echo $error_msg['cNoTestAssigned'];
			
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
			
			$sql="select email from $user_table where user_id='$userid'";
			
			$res=$db_object->get_a_line($sql);
			
			$email=$res[email];
			
			$status=$result[$i][status];
			
			if($status!='a')
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
