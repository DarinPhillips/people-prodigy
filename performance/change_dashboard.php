<?php
include("../session.php");
class Change_To_UserMode
{
	function change_mode($common,$db_object,$user_id,$fEmployee_id,$error_msg)
	{
		$user_table=$common->prefix_table("user_table");
		$selqry="select admin_id from $user_table where user_id='$fEmployee_id'";
		$hismasters_id=$db_object->get_a_line($selqry);

		if($hismasters_id["admin_id"]==$user_id||$user_id==1)
		{	
			setcookie("viewasadmin",$fEmployee_id,0,"/");
		}
		else
		{
			echo $error_msg["cSorrynotallowedhere"];
			exit;
		}
		
		
			
		
	}
	
}
$newobj= new Change_To_UserMode;
$newobj->change_mode($common,$db_object,$user_id,$fEmployee_id,$error_msg);
header('Location:../outer.php');
?>
