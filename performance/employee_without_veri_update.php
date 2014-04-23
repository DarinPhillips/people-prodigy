<?php
include("../session.php");
include("header.php");
class Without_verified_Updates
{
	function without_verified_update($common,$db_object,$default,$error_msg,$user_id,$date_format)
	{
		$path=$common->path;
		$xFile=$path."templates/performance/employee_without_veri_update.html";
		$xTemplate=$common->return_file_content($db_object,$xFile);

		$user_table=$common->prefix_table("user_table");
		$position_table=$common->prefix_table("position");
		$approved_feedback=$common->prefix_table("approved_feedback");
$selqry="select position from $user_table where user_id='$user_id'";
$userposition=$db_object->get_a_line($selqry);
$temp_pos=$userposition["position"];

$directreportspos=$common->get_chain_below($temp_pos,$db_object,$twodim);
$directreportsname=$common->get_user_id($db_object,$directreportspos);

$userids=$user_id;
for($i=0;$i<count($directreportsname);$i++)
{
	$userids.=",".$directreportsname[$i]["user_id"];
}

//$selqry="select $approved_feedback.user_id,$user_table.username from $approved_feedback  left join $user_table on $approved_feedback.user_id=$user_table.user_id left join

//--date of submission has to be updated
//--this displays the whole chain of command in wihch they have dont have the verified updates

$selqry="select distinct($user_table.user_id),$user_table.username,$approved_feedback.boss_id,
date_format($approved_feedback.approved_date,'$date_format') as date,user1.username as bossname,user1.email from
$user_table left join $approved_feedback  on $user_table.user_id=$approved_feedback.user_id
left join $position_table on $user_table.position=$position_table.pos_id
left join $user_table as  user1 on user1.position=$position_table.boss_no
where $approved_feedback.status=1 and $approved_feedback.active='A' and $approved_feedback.vaccept is NULL   and $approved_feedback.boss_id in ($userids)";
$employeeset=$db_object->get_rsltset($selqry);




	for($i=0;$i<count($employeeset);$i++)
	{		
		$bossid = $employeeset[$i]['boss_id'];
		if($bossid==$user_id)
		{
			$employeeset[$i]['email_prefix']="<!--";
			$employeeset[$i]['email_suffix']="-->";
			
			//$xTemplate = preg_replace("/<!--mailto_start-->(.*?)<!--mailto_end-->/s","",$xTemplate);
		}
			
	}


$values["emp_loop"]=$employeeset;

if(!isset($employeeset))
{
	echo $error_msg["cEmptyrecords"];
}
else
{
	$xTemplate=$common->simpleloopprocess($db_object,$xTemplate,$values);

	$xTemplate=$common->direct_replace($db_object,$xTemplate,$vals);
	echo $xTemplate;
}
}//end function
}//edn class
$vobj= new Without_verified_Updates;
$vobj->without_verified_update($common,$db_object,$defualt,$error_msg,$user_id,$gbl_date_format);

include("footer.php");
?>
