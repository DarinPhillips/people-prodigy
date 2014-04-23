<?php
include_once("../session.php");

include_once("header.php");

class alert
{
	function succession_plan_alert($db_object,$common,$user_id)
	{
		
		$user_table=$common->prefix_table("user_table");
		
		$pos_qry="select position from $user_table where user_id='$user_id'";
			
			$pos_res=$db_object->get_a_line($pos_qry);
			
			$position=$pos_res[position];
			
			$users_under=$common->get_chain_below($position,$db_object,$twodarr);
			
			$user_under_id=$common->get_user_id($db_object,$users_under);
			
			$b=0;
		
			for($a=0;$a<count($user_under_id);$a++)
			{
				$ch_id=$user_under_id[$a][user_id];
				
				$ch_boss=$common->is_boss($db_object,$ch_id);
				
				if($ch_boss)
				{
					$users1[$b][user_id]=$ch_id;
					
					$users1[$b][username]=$common->name_display($db_object,$ch_id);
					
					$b++;
				}
			}
			
	}
}

$obj=new alert();

$obj->succession_plan_alert($db_object,$common,$user_id);
?>

