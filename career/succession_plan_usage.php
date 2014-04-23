<?php
include_once("../session.php");

include_once("header.php");

class plan
{
	function succession_plan($db_object,$common,$user_id,$gbl_date_format,$error_msg)
	{
	$update_plan=$common->prefix_table("update_plan");

	$user_table=$common->prefix_table("user_table");

	$position_designee1=$common->prefix_table("position_designee1");

	$position_designee2=$common->prefix_table("position_designee2");

	$update_plan=$common->prefix_table("update_plan");

	$deployment_plan=$common->prefix_table("deployment_plan");

	$path=$common->path;

	$xtemplate=$path."templates/career/succession_plan_usage.html";

	$file=$common->return_file_content($db_object,$xtemplate);

	preg_match("/<{boss_loopstart}>(.*?)<{boss_loopend}>/s",$file,$match);

	$match=$match[0];

	$pos_qry="select position from $user_table where user_id='$user_id'";

	$pos_res=$db_object->get_a_line($pos_qry);

	$pos=$pos_res[position];

	$chain_under=$common->get_chain_below($pos,$db_object,$twodarr);

	$users_under=$common->get_user_id($db_object,$chain_under);

	$a=0;
	if($user_id==1)
	{
	$users_under=array();
	
	$sql="select * from $user_table where user_id<>'$user_id'";
		
	$users_under=$db_object->get_rsltset($sql);
	}
	for($i=0;$i<count($users_under);$i++)
	{
	$user=$users_under[$i][user_id];

	$ch=$common->is_boss($db_object,$user);

	if($ch)
	{
	$boss_under[$a]=$user;

	$a++;
	}	
	}
	if(count($boss_under)==0)
	{
	echo $error_msg['cNoBossUnderThisAdmin'];
	}
	for($j=0;$j<count($boss_under);$j++)
	{
	$direct_reports=array();$count_res=array();
	
	$boss_id=$boss_under[$j];
	

	$boss_name=$common->name_display($db_object,$boss_id);

	$sql="select date_format(date,'$gbl_date_format') as date from $update_plan where user_id='$boss_id' order by date desc limit 1";
	
	$sql_res=$db_object->get_a_line($sql);
	
	$sql1="select date from $update_plan where user_id='$boss_id' order by date asc limit 1";

	$sql_res1=$db_object->get_a_line($sql);
	
	$date1=$sql_res1[date];

	$date2=$sql_res[date];

	$direct_reports=$common->return_direct_reports($db_object,$boss_id);

	if(count($direct_reports)>0)
	{
		$reports=@implode(",",$direct_reports);

		$report="(".$reports.")";

	$sql="select position from $user_table where user_id in $report";

	$pos_rs=$db_object->get_single_column($sql);

	$pos=@implode(",",$pos_rs);

	$pos1="(".$pos.")";
	
	$plan_sql="select plan_id from $deployment_plan where position in $pos1";
	
	$plan_res=$db_object->get_single_column($plan_sql);


	if($plan_res[0]!="")
	{
		$plan=@implode(",",$plan_res);

		$plan="(".$plan.")";

	$sql="select count($position_designee1.designated_user) from $position_designee1,$position_designee2

	where $position_designee1.plan_id=$position_designee2.plan_id and $position_designee1.plan_id in $plan";

	$count_res=$db_object->get_single_column($sql);

	$count=$count_res[0];

	if($count>0)
	{		
		$per=($count/count($direct_reports))*100;
	}
	else
	{
	$per=0;
	}

	}
	
	}
	else
	{
	$per=0;
	}

	$str.=preg_replace("/<{(.*?)}>/e","$$1",$match);

	}
	$file=preg_replace("/<{boss_loopstart}>(.*?)<{boss_loopend}>/s",$str,$file);

	$file=$common->direct_replace($db_object,$file,$xArray);

	echo $file;

	}
}

$obj=new plan();

$obj->succession_plan($db_object,$common,$user_id,$gbl_date_format,$error_msg);

include_once("footer.php");

?>
