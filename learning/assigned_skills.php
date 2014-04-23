<?php
include("../session.php");

include("header.php");

class assigned_skills
{
	function show_assigned_skills($db_object,$common,$user_id,$error_msg)
	{
	$admin_id=$user_id;
	
	$path=$common->path;
	
	$xTemplate=$path."templates/learning/assigned_skills.html";
	
	$content=$common->return_file_content($db_object,$xTemplate);
		
	$user_table=$common->prefix_table("user_table");
	
	$skills=$common->prefix_table("skills");
	
	$assign_solution_builder=$common->prefix_table("assign_solution_builder");

	$approved_devbuilder=$common->prefix_table("approved_devbuilder");

	$qry="select $assign_solution_builder.skill_id,$assign_solution_builder.date,$assign_solution_builder.user_id,
	
	$skills.skill_name from $assign_solution_builder,$skills where admin_id='$user_id'
	
	and $assign_solution_builder.skill_id=$skills.skill_id  order by user_id";
	
	$qry_result=$db_object->get_rsltset($qry);
	
	
	
	if($qry_result[0]=="")
	{
		
		echo $error_msg[cNotassigned];
		
		include_once("footer.php");
		exit;
	}
	
	$j=0;
	
	for($i=0;$i<count($qry_result);$i++)
	{
		
		$skill_id=$qry_result[$i][skill_id];
		
		$user_id=$qry_result[$i][user_id];
		
		$sql="select pstatus from approved_devbuilder where skill_id='$skill_id' and 
		
		user_id='$user_id'";
		
		$sql_result=$db_object->get_a_line($sql);
		
		if($sql_result[pstatus]!='a')
		{
			$arr[$j]=$i;
			
			$j++;
		}
	}
	if($arr[0]=="")
	{
		echo $error_msg[cNounapprovedskills];
		
		include_once("footer.php");
		
		exit;
	}
	
	preg_match("/<{skill_loopstart}>(.*?)<{skill_loopend}>/s",$content,$match);
	
	$match=$match[0];
	
	$str="";
	
	
	for($i=0;$i<count($arr);$i++)
	{
				
		$admin=$common->name_display($db_object,$admin_id);
			
		$k=$arr[$i];
		
		$user_id=$qry_result[$k][user_id];
		
		$employee=$common->name_display($db_object,$user_id);
			
		$date=$qry_result[$k][date];
		
		$skill_name=$qry_result[$k][skill_name];
		
		$str.=preg_replace("/<{(.*?)}>/e","$$1",$match);
	}
	
	$content=preg_replace("/<{skill_loopstart}>(.*?)<{skill_loopend}>/s",$str,$content);
	
	$content=$common->direct_replace($db_object,$content,$array);
	
	echo $content;
	
	}
}

$obj=new assigned_skills();

$obj->show_assigned_skills($db_object,$common,$user_id,$error_msg);

include_once("footer.php");
?>
