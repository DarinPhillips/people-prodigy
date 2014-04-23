<?php
include("../session.php");
class Opp_status
{
	function delete($common,$db_object,$eeo_id)
	{
		$opp_status=$common->prefix_table("opportunity_status");
		$user_eeo=$common->prefix_table("user_eeo");
		$temp_user_eeo=$common->prefix_table("temp_user_eeo");
		$delqry="delete from $opp_status where eeo_id='$eeo_id'";
		$db_object->insert($delqry);
		$delqry="delete from $user_eeo where eeo_id='$eeo_id'";
//		$db_object->insert($selqry);
		$delqry="delete from $temp_user_eeo where eeo_id='$eeo_id'";
//		$db_object->insert($selqry);		

	}
}
$obj34= new Opp_status;
$obj34->delete($common,$db_object,$eeo_id);
header('Location:display_eeo_status.php');
?>