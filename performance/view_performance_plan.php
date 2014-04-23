<?php
include_once("../session.php");

include_once("header.php");

class view_plan
{
	function view_performance_plan($db_object,$common,$user_id,$error_msg)
	{
		$plan=$common->prefix_table("plan");
		
		$qry="select * from $plan where employee_id='$user_id' and check_status='c'";
		
		$result=$db_object->get_rsltset($qry);
		
		if(count($result)==0)
		{
			echo $error_msg['cEmptyrecords'];
			
			include_once("footer.php");exit;
		}
		
		$xpath=$common->path;
		
		$xTemplate=$xpath."templates/performance/view_performance_plan.html";
		
		$content=$common->return_file_content($db_object,$xTemplate);
		
		preg_match("/<{plan_loopstart}>(.*?)<{plan_loopend}>/s",$content,$match);
		
		$match=$match[0];
		
		for($i=0;$i<count($result);$i++)
		{
			$assigned_by=$result[$i][added_by];
			
			$name=$common->name_display($db_object,$assigned_by);
			
			$date=$result[$i][added_on];
			
			$date=$this->date_display($date);
			
			$plan_id=$result[$i][plan_id];
			
			$str.=preg_replace("/<{(.*?)}>/e","$$1",$match);
		}
		
		$content=preg_replace("/<{plan_loopstart}>(.*?)<{plan_loopend}>/s",$str,$content);
		
		$content=$common->direct_replace($db_object,$content,$xArray);
		
		echo $content;
	}
	function show_plan($db_object,$common,$plan_id)
	{
		
		$path=$common->path;
		
		$setting=$common->prefix_table("performance_setting");
		
		$plan=$common->prefix_table("plan");
		
		$xtemplate=$path."templates/performance/view_performance_improvement_plan.html";
		
		$content=$common->return_file_content($db_object,$xtemplate);
		
		$qry="select * from $plan where plan_id='$plan_id'";
		
		$result=$db_object->get_a_line($qry);
		
		$array["requirement"]=$result[requirement];
		
		$array["consequences"]=$result[consequences];
		
		$due_date=$this->changedate_display($result[due_date]);
		
		$array["date"]=$due_date;
		
		$array["name"]=$common->name_display($db_object,$result[employee_id]);
		
		$content=$common->return_file_content($db_object,$xtemplate);
		
		$content=$common->direct_replace($db_object,$content,$array);
		
		echo $content;
	}
	
	function show_reports_plan($db_object,$common,$user_id,$error_msg)
	{
		$plan=$common->prefix_table("plan");
		
		$user_table=$common->prefix_table("user_table");		
		
		$id=$common->employees_under_admin_boss($db_object,$user_id);
		
		
		
		if(count($id)>1)
		{
			$id=@implode(",",$id);
			
			$id="(".$id.")";
		}
		
		$qry="select * from $plan where employee_id in $id";
		
		$result=$db_object->get_rsltset($qry);
		
		if(count($result)==0)
		{
			echo $error_msg['cEmptyrecords'];
			include_once("footer.php");exit;
		}
		
		$xpath=$common->path;
		
		$xTemplate=$xpath."templates/performance/view_reports_performance_plan.html";
		
		$content=$common->return_file_content($db_object,$xTemplate);
		
		preg_match("/<{plan_loopstart}>(.*?)<{plan_loopend}>/s",$content,$match);
		
		$match=$match[0];
		
		for($i=0;$i<count($result);$i++)
		{
			$assigned_to=$result[$i][employee_id];
			
			$name=$common->name_display($db_object,$assigned_to);
			
			$date1=$result[$i][added_on];
			
			$date1=$this->date_display($date1);
			
			$date2=$result[$i][due_date];
			
			$date2=$this->date_display($date2);
			
			$plan_id=$result[$i][plan_id];
			
			$str.=preg_replace("/<{(.*?)}>/e","$$1",$match);
		}
		
		$content=preg_replace("/<{plan_loopstart}>(.*?)<{plan_loopend}>/s",$str,$content);
		
		$content=$common->direct_replace($db_object,$content,$xArray);
		
		echo $content;
	}

	
	function changedate_display($date)
	{
	list($year,$month,$date)=explode("-",$date);

	$newdate=$month.'/'.$date.'/'.$year;

	return ($newdate);

	}
	
	function date_display($date)
	{
	
		$date1=@explode(" ",$date);
		
		$date2=@explode("-",$date1[0]);
		
		$date3=@explode(":",$date1[1]);
		
		if($date3[0]!="")
		{
		
			$date=$date2[1].".".$date2[2].".".$date2[0].".".$date3[0].":".$date3[1];
		}
		else
		{
			$date=$date2[1].".".$date2[2].".".$date2[0];
		}
		
		return($date);
	}
	
}
$obj=new view_plan();

switch($action)
{
case NULL:

	$obj->view_performance_plan($db_object,$common,$user_id,$error_msg);
	
	break;
	
case "show":
	
	$obj->show_plan($db_object,$common,$plan_id);
	
	break;
	
case "boss":

	
$obj->show_reports_plan($db_object,$common,$user_id,$error_msg);
	
	break;

}
include_once("footer.php");
?>
