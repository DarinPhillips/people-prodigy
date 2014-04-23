<?php
include("../session.php");
class Dashboardchange
{
	function change_dashboard_to_Admin($common,$db_object,$fuser_id,$user_id)
	{
		if($user_id==1)
		{
		 setcookie("viewasadmin",$fuser_id,0,"/");
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
echo "TresPassers will be prosecuted";
exit;
}
header('Location:../outer.php');

?>