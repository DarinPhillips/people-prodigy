<?php
include("../session.php");
include("header.php");
class View_Admin_dashboard
{
	function display_dashboard($common,$db_object,$default,$user_id)
	{
		$path=$common->path;
		$xFile=$path."templates/core/view_pages_as_admin.html";
		$xTemplate=$common->return_file_content($db_object,$xFile);


		$user_table=$common->prefix_table("user_table");
		$admins_table=$common->prefix_table("admins");
		
		$selqry="select distinct($user_table.user_id),$user_table.username from $admins_table left join $user_table on $admins_table.user_id=$user_table.user_id";
		$userset=$db_object->get_rsltset($selqry);
		$values["user_loop"]=$userset;
$xTemplate=$common->simpleloopprocess($db_object,$xTemplate,$values);
$xTemplate=$common->direct_replace($db_object,$xTemplate,$vals);
		echo $xTemplate;
	}
}
$viewobj= new View_Admin_dashboard;
if($user_id==1)
{
	$viewobj->display_dashboard($common,$db_object,$default,$user_id);
}
else
{
	echo "Trespassers will be Prosecuted";
}

include("footer.php");

?>