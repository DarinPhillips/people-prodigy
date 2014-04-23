<?php
include("../session.php");
class Change_To_UserMode
{
	function change_mode($common,$db_object,$user_id,$fEmployee_id,$error_msg)
	{
		$user_table=$common->prefix_table("user_table");
		$selqry="select admin_id from $user_table where user_id='$fEmployee_id'";
		$hismasters_id=$db_object->get_a_line($selqry);
		
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
		
		$ch_if_present=@in_array($fEmployee_id,$users);
		
		if($hismasters_id["admin_id"]==$user_id||$user_id==1 || $ch_if_present==1)
		{	
			setcookie("viewasadmin",$fEmployee_id,0,"/");
		}
		else
		{
			echo "Tresspassers will be prosecuted";
			exit;
		}
		
		
			
		
	}
	
}
$newobj= new Change_To_UserMode;
$newobj->change_mode($common,$db_object,$user_id,$fEmployee_id,$error_msg);
header('Location:../outer.php');
?>
