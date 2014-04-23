<?php
/*===============================================================
    SCRIPT: dev_solution.php
    AUTHOR: chrisranjana.com
    UPDATED: 
    
    DESCRIPTION
     This deals with the analyses Developmental Solution.
===============================================================*/

include("../session.php");
include("header.php");

class plan_approval
{
	function send_plan_approval($common,$db_object,$fUser_id,$error_msg)
	{
	
	/*$approve_sql="select build_id,pstatus from approved_devbuilder where user_id='$fUser_id'";

	$approve_result=$db_object->get_rsltset($approve_sql);

	for($i=0;$i<count($approve_result);$i++)
	{
	
		$build_id=$approve_result[$i][build_id];

		$pstatus=$approve_result[$i][pstatus];

		if($pstatus != 'a')
		{
		
		$approve_plan_sql="update approved_devbuilder set pstatus='t' where build_id='$build_id' ";

		$db_object->insert($approve_plan_sql);	
		}

		
	}*/
	
	$approve_sql="select skill_id from approved_devbuilder where user_id='$fUser_id' and (pstatus='u' or pstatus='r')";
	
	$approve_result=$db_object->get_rsltset($approve_sql);
	
	$k=0;
	
	for($i=0;$i<count($approve_sql);$i++)
	{
		
		$skill[$k]=$approve_result[$i][skill_id];
		
		$k++;
		
	}
	for($i=0;$i<count($skill);$i++)
	{
		$skill=array_unique($skill);
		
	}
	for($i=0;$i<count($skill);$i++)
	{
		$skill=$skill[$i];
		
		$sql="update approved_devbuilder set pstatus='t' where user_id='$fUser_id' and skill_id='$skill'";
		
		
		$db_object->insert($sql);
	}

	echo $error_msg['cPlanSubmitted'];
	}

	function front_display($db_object,$common,$user_id)
	{
	$path=$common->path;

	$filename=$path."/templates/learning/front_panel.html";

	$filecontent=$common->return_file_content($db_object,$filename,$user_id);


	$yes=$common->is_admin($db_object,$user_id);
	if(isset($yes))
	{
	
	$filecontent=preg_replace("/<{(.*?)}>/s","",$filecontent);
	}
	else
	{
	
	$filecontent=preg_replace("/<{adminarea_loopstart}>(.*?)<{adminarea_loopend}>/s","",$filecontent);
	
	}
	$value=array();

	$filecontent=$common->direct_replace($db_object,$filecontent,$value);

	echo $filecontent;
	}

}
$plan_approval_obj=new plan_approval();

$fUser_id=$post_var["fUser_id"];
$learning->Save_finishlater($db_object,$common,$default,$fUser_id,$post_var,$gbl_freq_array);
$plan_approval_obj->send_plan_approval($common,$db_object,$fUser_id,$error_msg);

//$plan_approval_obj->front_display($db_object,$common,$fUser_id);

include_once("footer.php");

?>
