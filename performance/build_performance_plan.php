<?php
include_once("../session.php");

include_once("header.php");

class performance_plan
{
	function build_plan($db_object,$common,$plan_id)
	{
		
		$plan=$common->prefix_table("plan");
		
		$user_table=$common->prefix_table("user_table");
		
		$performance_setting=$common->prefix_table("performance_setting");
		
		$temp_plan_improvement=$common->prefix_table("temp_plan_improvement");
		
		$unapproved_plan_improvement=$common->prefix_table("unapproved_plan_improvement");
		
		$plan_improvement=$common->prefix_table("plan_improvement");
		
			$path=$common->path;
				
			$xTemplate=$path."/templates/performance/build_performance_plan.html";
		
			$content=$common->return_file_content($db_object,$xTemplate);
		
		$sql="select plan_id from $temp_plan_improvement where plan_id='$plan_id'";
		
		$sql_result=$db_object->get_single_column($sql);
				
		$plan_qry="select * from $plan where plan_id='$plan_id'";
		
		$plan_result=$db_object->get_a_line($plan_qry);
		
		if($sql_result[0]=="")
		{
			$action="plan";    //To just show the form
		}
		else
		{
			$action="temp";    //if the user has already done something in the form
		}
		$if_qry="select status from $plan_improvement where plan_id='$plan_id'";
		
		$if_res=$db_object->get_single_column($if_qry);
		
		
		if($if_res[0]=="a")
			{
				$content=preg_replace("/<{if_(.*?)}>/s","",$content);

			}
			else
			{
				$content=preg_replace("/<{if_loopstart}>(.*?)<{if_loopend}>/s","",$content);				
			}
		
		switch($action)
		{
			case "plan":
			
		
			$qry="select $plan.requirement,$plan.consequences,$user_table.username,$plan.employee_id
			
			from $plan,$user_table where $user_table.user_id=$plan.employee_id and $plan.plan_id='$plan_id'";
		
			$result=$db_object->get_a_line($qry);
		
			$emp_id=$result[employee_id];
					
			preg_match("/<{row_loopstart}>(.*?)<{row_loopend}>/s",$content,$match);
			
			$match=$match[0];
		
			$qry="select plan_no from $performance_setting";
		
			$res=$db_object->get_a_line($qry);
		
			for($j=0;$j<$res[plan_no];$j++)
			{
			
				$i=$j+1;
				
				$str.=preg_replace("/<{(.*?)}>/e","$$1",$match);
				
			}
		
		
			$content=preg_replace("/<{row_loopstart}>(.*?)<{row_loopend}>/s",$str,$content);
		
			$result[plan_id]=$plan_id;
			
			$result[employee_id]=$emp_id;
			
			$result[username]=$result[username];
			
			$result[requirement]=$plan_result[requirement];
			
			$result[consequences]=$plan_result[consequences];
			
		
			$result[date1]=$this->changedate_display($plan_result[due_date]);
			
			
			$content=$common->direct_replace($db_object,$content,$result);
			
			break;
			
			case "temp":
			
			$qry="select $temp_plan_improvement.plan_text,$temp_plan_improvement.plan_date,$user_table.username,$temp_plan_improvement.employee_id
			
			 from $temp_plan_improvement,$user_table where $temp_plan_improvement.plan_id='$plan_id'
			 
			 and $temp_plan_improvement.employee_id=$user_table.user_id";
			
			$result=$db_object->get_rsltset($qry);
			
			$emp_id=$result[0][employee_id];
					
			$username=$result[0][username];
			
						
			$qry="select plan_no from $performance_setting";
		
			$res=$db_object->get_a_line($qry);
			
			preg_match("/<{row_loopstart}>(.*?)<{row_loopend}>/s",$content,$match);

			$match=$match[0];
				
			for($j=0;$j<count($result);$j++)
			{
			
				$i=$j+1;
				
				$text=$result[$j][plan_text];
				
				$date=$result[$j][plan_date];
				
				
				if($date=="0000-00-00")
				{
					
					$date="";
				}
				else
				{
					$date=$this->changedate_display($date);
				}
				
				$str.=preg_replace("/<{(.*?)}>/e","$$1",$match);
				
			}
		
		
			$content=preg_replace("/<{row_loopstart}>(.*?)<{row_loopend}>/s",$str,$content);
		
			$result[plan_id]=$plan_id;
			
			$result[employee_id]=$emp_id;
			
			$result[username]=$username;
			
			$result[requirement]=$plan_result[requirement];
			
			$result[consequences]=$plan_result[consequences];
			
		
			$result[date1]=$this->changedate_display($plan_result[due_date]);
			
			$content=$common->direct_replace($db_object,$content,$result);
			
			break;	
		}
		echo $content;
		
				
	}
	
	function save_plan($db_object,$common,$plan_id,$_POST,$error_msg)
	{
		
		$performance_setting=$common->prefix_table("performance_setting");
		
		$temp_plan_improvement=$common->prefix_table("temp_plan_improvement");
		
		$plan_improvement=$common->prefix_table("plan_improvement");
		
		$unapproved_plan_improvement=$common->prefix_table("unapproved_plan_improvement");
		
		$plan=$common->prefix_table("plan");
		
		$qry="select plan_no from $performance_setting";
		
		$res=$db_object->get_a_line($qry);
		
		$sql="update $plan set check_status='c' where plan_id='$plan_id'";
		
		$db_object->insert($sql);
		
		$employee_id=$_POST["employee_id"];
		
		$keys=@array_keys($_POST);
		
		$i=0;
		
		$c_qry="select * from $temp_plan_improvement where plan_id='$plan_id'";
		
		$c_res=$db_object->get_a_line($c_qry);
		
		$imp_qry="select imp_id from $temp_plan_improvement where plan_id='$plan_id' ORDER BY imp_id asc LIMIT 1";
		
		$imp_res=$db_object->get_a_line($imp_qry);
		
		$imp_id=$imp_res[imp_id];
		
		while($i<=$res[plan_no])
		{
			
			$key=$keys[$i];
			
			$action=$_POST[$key];
			
			$i++;
			
			$key=$keys[$i];
			
			$date=$_POST[$key];
			
			$date=$this->date_format($date);
			
			if($c_res[0] =="")
			{
			
				if($employee_id==1)
				{
				$sql="insert into $plan_improvement set plan_id='$plan_id',plan_text='$action',
			
			plan_date='$date',employee_id='$employee_id'";
				}
				else
				{
			$sql="insert into $temp_plan_improvement set plan_id='$plan_id',plan_text='$action',
			
			plan_date='$date',employee_id='$employee_id'";
				}
			
			$db_object->insert($sql);
			}
			else
			{
				if($employee_id==1)
				{
					$sql="update $plan_improvement set plan_text='$action',
			
			plan_date='$date',employee_id='$employee_id' where imp_id='$imp_id'";
				}
				else
				{
			$sql="update $temp_plan_improvement set plan_text='$action',
			
			plan_date='$date',employee_id='$employee_id' where imp_id='$imp_id'";
				}
			
			$db_object->insert($sql);
			
			$imp_id++;
			}
			
			$i++;
						
		}
		
		echo $error_msg['cInformationSaved'];
		
	}
	
	function send_for_approval($db_object,$common,$_POST,$error_msg)
	{
		
		$performance_setting=$common->prefix_table("performance_setting");
		
		$plan_improvement=$common->prefix_table("plan_improvement");
		
		$temp_plan_improvement=$common->prefix_table("temp_plan_improvement");
		
		$unapproved_plan_improvement=$common->prefix_table("unapproved_plan_improvement");
		
		$plan=$common->prefix_table("plan");
		
		$qry="select plan_no from $performance_setting";
		
		$res=$db_object->get_a_line($qry);
		
		$i=0;
		
		$employee_id=$_POST["employee_id"];
		
		$plan_id=$_POST["plan_id"];
		
		$sql="update $plan set check_status='c' where plan_id='$plan_id'";
		
		$db_object->insert($sql);
		
		$keys=@array_keys($_POST);
		
		$qry="select imp_id from $temp_plan_improvement where plan_id='$plan_id'ORDER BY imp_id asc LIMIT 1 ";
			
		$result=$db_object->get_a_line($qry);
		
		$t_imp_id=$result[imp_id];
			
			$qry1="select imp_id from $unapproved_plan_improvement where plan_id='$plan_id'ORDER BY imp_id asc LIMIT 1 ";
			
			$result1=$db_object->get_a_line($qry1);
			
			$imp_id=$result1[imp_id];
		
		while($i<=$res[plan_no])
		{
			
			$key=$keys[$i];
			
			$action=$_POST[$key];
			
			$i++;
			
			$key=$keys[$i];
			
			$date=$_POST[$key];
			
			$date=$this->date_format($date);
			
					
			if($result[0]=="")
			{
			
		$qry1="insert into $temp_plan_improvement set plan_id='$plan_id',plan_text='$action',
			
		plan_date='$date',employee_id='$employee_id'";
		

		
		$db_object->insert($qry1);
			}
			else
			{
			$qry1="update $temp_plan_improvement set plan_id='$plan_id',plan_text='$action',
			
		plan_date='$date',employee_id='$employee_id' where imp_id='$t_imp_id'";
		

		
		$t_imp_id++;
		
		$db_object->insert($qry1);
			}
			
			if($result1[0]=="")
			{
			
		$sql="insert into $unapproved_plan_improvement set plan_id='$plan_id',plan_text='$action',
			
		plan_date='$date',employee_id='$employee_id',status='u'";
		

		
		$db_object->insert($sql);
			}
			else
			{
		$sql="update $unapproved_plan_improvement set plan_id='$plan_id',plan_text='$action',
			
		plan_date='$date',employee_id='$employee_id',status='u' where imp_id='$imp_id'";
		

		
		$db_object->insert($sql);
		
		$imp_id++;
			}
			$i++;
		}
		echo $error_msg['cPlanSentforApproval'];
	}
	
	function date_format($date)
	{
		$date1=@explode("/",$date);
		
		$date=$date1[2]."-".$date1[0]."-".$date1[1];
		
		return($date);
	}


	function changedate_display($date)
	{
	list($year,$month,$date)=explode("-",$date);

	//$newdate="";

	$newdate=$month.'/'.$date.'/'.$year;


	return ($newdate);

	}
}

$obj=new performance_plan();

if($fSave)
{
	$action="save";
	
}
if($fSubmit)
{
	$action="approve";
}
if($fPlan_id)
{
	$plan_id=$fPlan_id;
}
switch($action)
{
	case NULL:

		$obj->build_plan($db_object,$common,$plan_id);
		
		break;
		
	case "save":
	
		$obj->save_plan($db_object,$common,$plan_id,$_POST,$error_msg);
		
		break;
		
	case "approve":
		
		$obj->send_for_approval($db_object,$common,$_POST,$error_msg);
		
		break;
}
		
include_once("footer.php");
?>
