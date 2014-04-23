<?php
include_once("../session.php");

include_once("header.php");

class improvement
{
	function performance_improvement($db_object,$common,$user_id,$uid)
	{
		
		$path=$common->path;
		
		$setting=$common->prefix_table("performance_setting");
		
		$xtemplate=$path."/templates/performance/performance_improvement_plan.html";
		
		$array["name"]=$common->name_display($db_object,$uid);
		
		$content=$common->return_file_content($db_object,$xtemplate);
		
		$array["admin"]=$user_id;  //one who assigns
		
		$array["user_id"]=$uid;
		
		$content=$common->direct_replace($db_object,$content,$array);
		
		echo $content;
	}
	
	
	function submit_plan($db_object,$common,$fAssigned_by,$fAssigned_to,$fRequirement,$fConsequences,$fDate1,$default,$error_msg)
	{
		$plan=$common->prefix_table("plan");
		
		$user_table=$common->prefix_table("user_table");
		
		$performance_message=$common->prefix_table("performance_message");
		
		$current_date=date("Y-m-d H:i:s",time());
		
		$fDate1=$this->date_format($fDate1);
		
		$qry="insert into plan set employee_id='$fAssigned_to',added_by='$fAssigned_by',due_date='$fDate1',
		
		requirement='$fRequirement',consequences='$fConsequences',status='a',added_on='$current_date',check_status='n'";
		
		$db_object->insert($qry);
		
		$email_qry="select email,username from $user_table where user_id='$fAssigned_by'"; //
		
		$by_res=$db_object->get_a_line($email_qry);
		
		$by_mail=$by_res[email];
		
		$by_name=$by_res[username];
		
		$email_qry="select email,username from $user_table where user_id='$fAssigned_to'"; //user mail
		
		$to_res=$db_object->get_a_line($email_qry);
		
		$to_mail[0]=$to_res[email];
		
		$to_name[0]=$to_res[username];
		
		$email_qry="select email,username from $user_table where user_id='1'";  //user mail
		
		$admin_res=$db_object->get_a_line($email_qry);
		
		$to_mail[1]=$admin_res[email];
		
		$to_name[1]=$admin_res[username];
		
		$boss_id=$common->immediate_boss($db_object,$fAssigned_to);
		
		$emil_qry="select email,username from $user_table where user_id='$boss_id'"; //boss's mail
		
		$boss_res=$db_object->get_a_line($email_qry);
		
		$to_mail[2]=$boss_res[email];
		
		$to_name[2]=$boss_res[username];
		
		$to_mail[3]=$by_mail;
		
		$to_name[3]=$by_name;
		
		$mail_qry="select plan_subject_$default,plan_message_$default from $performance_message";
		
		$mail_res=$db_object->get_a_line($mail_qry);
		
		$subject=$mail_res[plan_subject_1];
		
		$message=$mail_res[plan_message_1];
		
		$plan_qry="select plan_id from $plan order by plan_id desc limit 1";
		
		$plan_res=$db_object->get_a_line($plan_qry);
		
		$plan_id=$plan_res[plan_id];
		
		for($i=0;$i<4;$i++)
		{
		
		$name=$common->name_display($db_object,$fAssigned_by);
		
		$click="http://www.cat45.com/Pms/performance/performance_improvement_plan.php?action=alert";
		
		$xarr[plan]=$plan_id;
		
		$click=$common->direct_replace($db_object,$click,$xarr);
		
		$xArray=array("user"=>$to_name[$i],"assignedby"=>$name,"assigned_to"=>$fAssigned_to,"click"=>$click);
		
		$message1=$common->direct_replace($db_object,$message,$xArray);
		
		$common->send_mail($to_mail[$i],$subject,$message1,$by_mail);
		}
		
		echo $error_msg[cPlanInformedtouser];
		
	}
	
	function show_alert($db_object,$common,$user_id)
	{
		$user_table=$common->prefix_table("user_table");
		
		$plan=$common->prefix_table("plan");
		
		$temp_plan_improvement=$common->prefix_table("temp_plan_improvement");
		
		$xpath=$common->path;
		
		$xTemplate=$xpath."/templates/performance/performance_improvement_plan_alert.html";
		
		$content=$common->return_file_content($db_object,$xTemplate);
		
		/*$qry="select plan_id from $temp_plan_improvement where employee_id='$user_id' group by plan_id";
		
		$res=$db_object->get_single_column($qry);
		
		if(count($res)>=1)
		{
			$planid=@implode(",",$res);
			
			$planid="(".$planid.")";
			
			$plan_clause=" and $plan.plan_id not in $planid";
		}
		else
		{
			$planid=$res[0];
			
			$planid="(".$planid.")";
			
			$plan_clause="";
			
		}*/
		$alert_qry="select $user_table.username,$plan.added_by,$plan.plan_id
		
		 from $plan,$user_table where $user_table.user_id=$plan.employee_id 
		 
		 and $plan.status='a' and $plan.check_status='n' and $plan.employee_id='$user_id'";
		 	 

		$alert_res=$db_object->get_rsltset($alert_qry);
		
		for($i=0;$i<count($alert_res);$i++)
		{
			$added_by=$alert_res[$i][added_by];
			
			$name_qry="select username from $user_table where user_id='$added_by'";
			
			$name_res=$db_object->get_a_line($name_qry);
			
			$alert_res[$i][addedby]=$name_res[username];
		}
		
		$values["alert_loop"]=$alert_res;
		
		$content=$common->simpleloopprocess($db_object,$content,$values);
		
		$content=$common->direct_replace($db_object,$content,$xArray);
		
		echo $content;
	}
	
	function date_format($date)
	{
		$date1=@explode("/",$date);
		
		$date=$date1[2]."-".$date1[0]."-".$date1[1];
		
		return($date);
	}

}
$obj=new improvement();


if($Submit)
{
	$action="submit";
}

switch($action)
{

case NULL:

	$obj->performance_improvement($db_object,$common,$user_id,$uid);
	
	break;
	
case "submit":

	
	$obj->submit_plan($db_object,$common,$fAssigned_by,$fAssigned_to,$fRequirement,$fConsequences,$fDate1,$default,$error_msg);

	break;
	
case "alert":

	$obj->show_alert($db_object,$common,$user_id);
	
	break;
	

}

include_once("footer.php");
?>
