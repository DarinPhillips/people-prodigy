<?php
include("../session.php");
include("header.php");
class View_Boss_dashboard
{
	function display_dashboard($common,$db_object,$default,$user_id)
	{
		$path=$common->path;
		$xFile=$path."templates/core/view_pages_as_boss.html";
		$xTemplate=$common->return_file_content($db_object,$xFile);


		$user_table=$common->prefix_table("user_table");
		$position_table=$common->prefix_table("position");
		
		$selqry="select distinct(u1.user_id),u1.username from $user_table,$position_table,$user_table as u1,$position_table as p1 where $position_table.pos_id=$user_table.position and u1.position=$position_table.boss_no and p1.pos_id=u1.position order by u1.user_id";
		
		$userset=$db_object->get_rsltset($selqry);
		$values["user_loop"]=$userset;
$xTemplate=$common->simpleloopprocess($db_object,$xTemplate,$values);
$xTemplate=$common->direct_replace($db_object,$xTemplate,$vals);
		echo $xTemplate;
	}
}
$viewobj= new View_Boss_dashboard;
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