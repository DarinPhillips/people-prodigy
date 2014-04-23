<?php
include("../session.php");
class Dashboardchange
{
	function change_dashboard_to_Admin($common,$db_object,$fuser_id,$user_id)
	{
		$user_table=$common->prefix_table("user_table");
		$selqry="select admin_id from $user_table where user_id='$fUser_id'";
		$admin_id=$db_object->get_a_line($selqry);
		if($user_id==1||$user_id==$admin_id["admin_id"])
		{

		 setcookie("viewasadmin",$fuser_id,0,"/");
		}
		else
		{
			echo "Trespassers will be prosecuted";
		}
	}
}
$obj= new Dashboardchange;
$yes=$common->is_admin($db_object,$user_id);
if($yes)
{
$obj->change_dashboard_to_Admin($common,$db_object,$fuser_id,$user_id);
}
else
{
echo "Trespassers will be prosecuted";
exit;
}
header('Location:../outer.php');

?>