<?php
include("../session.php");
include("header.php");
class Display_Employees
{
	function display_employee($common,$db_object,$error_msg,$user_id,$date_format)
	{
		$path=$common->path;
		$xFile=$path."templates/performance/employee_without_approved_obj.html";
		$xTemplate=$common->return_file_content($db_object,$xFile);

		$user_table=$common->prefix_table("user_table");
		$position_table=$common->prefix_table("position");
		$unapproveduser_objective=$common->prefix_table("unapproveduser_objective");
		$performance_alert=$common->prefix_table("performance_alert");
		

$selqry="select position from $user_table where user_id='$user_id'";
$userposition=$db_object->get_a_line($selqry);
$temp_pos=$userposition["position"];

$directreports= array();
$directreports=$common->get_chain_below($temp_pos,$db_object,$twodim);

//Lists the User from perfroamce alert table so that the USers are haev the unapproved Objectives


$arr_cnt=count($directreports);

if(!is_array($directreports))
	{
		$directreports= array();
	}
$directreports[$arr_cnt] = $temp_pos;


$pos=@implode("','",$directreports);


$selqry="select distinct($user_table.user_id),$user_table.username,
date_format($performance_alert.submit_date,'$date_format') as date,user1.username as bossname,user1.email,user1.user_id as id from
$user_table left join $performance_alert  on $user_table.user_id=$performance_alert.user_id
left join $position_table on $user_table.position=$position_table.pos_id
left join $user_table as  user1 on user1.position=$position_table.boss_no
where $performance_alert.user_id is not null  and $performance_alert.boss_id in ('$pos') group by $performance_alert.user_id";



$employeeset=$db_object->get_rsltset($selqry);

	for($i=0;$i<count($employeeset);$i++)
	{		
		$bossid = $employeeset[$i]['id'];

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
	}
}
$empobj= new Display_Employees;
$empobj->display_employee($common,$db_object,$error_msg,$user_id,$gbl_date_format);

include("footer.php");

?>
