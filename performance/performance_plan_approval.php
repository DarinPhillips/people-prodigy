<?
include_once("../session.php");
include_once("header.php");

class approval
{
	
	function show_approval($db_object,$common,$user_id)
	{
		$plan=$common->prefix_table("plan");
		
		$plan_improvement=$common->prefix_table("plan_improvement");
		
		$user_table=$common->prefix_table("user_table");
		
		$unapproved_plan_improvement=$common->prefix_table("unapproved_plan_improvement");
		
		$id=$common->employees_under_admin_boss($db_object,$user_id);
		
		if(count($id)>1)
		{
			$id=@implode(",",$id);
			
			$id="(".$id.")";
		}
		else
		{
			$id="(".$id[0].")";
		}
		
		$qry="select $unapproved_plan_improvement.employee_id,$unapproved_plan_improvement.plan_id,$user_table.username
		
		from $unapproved_plan_improvement,$user_table where $unapproved_plan_improvement.status='u' and 
		
		$unapproved_plan_improvement.employee_id=$user_table.user_id and 
		
		$unapproved_plan_improvement.employee_id in $id group by $unapproved_plan_improvement.plan_id";
		
		$result=$db_object->get_rsltset($qry);
		
		$path=$common->path;
		
		$xTemplate=$path."/templates/performance/performance_plan_approval_alert.html";
		
		$content=$common->return_file_content($db_object,$xTemplate);
		
		$values["alert_loop"]=$result;
		
		$content=$common->simpleloopprocess($db_object,$content,$values);
		
		$content=$common->direct_replace($db_object,$content,$array);
		
		echo $content;
	}
	
	function show_form($db_object,$common,$plan_id)
	{
	
		$plan_improvement=$common->prefix_table("plan_improvement");
		
		$user_table=$common->prefix_table("user_table");
		
		$performance_setting=$common->prefix_table("performance_setting");
		
		$plan=$common->prefix_table("plan");
		
		$unapproved_plan_improvement=$common->prefix_table("unapproved_plan_improvement");
		
		$qry="select $unapproved_plan_improvement.plan_text,$unapproved_plan_improvement.plan_date,$unapproved_plan_improvement.employee_id,$user_table.username,$unapproved_plan_improvement.employee_id
			
			 from $unapproved_plan_improvement,$user_table where $unapproved_plan_improvement.plan_id='$plan_id'
			 
			 and $unapproved_plan_improvement.employee_id=$user_table.user_id";
			 

			
		$result=$db_object->get_rsltset($qry);
		
		$qry="select * from $plan where plan_id='$plan_id'";
		
		$res1=$db_object->get_a_line($qry);
		
		$path=$common->path;
		
		$xTemplate=$path."/templates/performance/performance_plan_approval.html";
		
		$content=$common->return_file_content($db_object,$xTemplate);
		
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
				
				
				if($date!="0000-00-00")
				{
							
				$date=$this->changedate_display($date);
				}
				else
				{
					$date="";
				}
				
				$str.=preg_replace("/<{(.*?)}>/e","$$1",$match);
				
			}
		
		
			$content=preg_replace("/<{row_loopstart}>(.*?)<{row_loopend}>/s",$str,$content);
		
			$result[plan_id]=$plan_id;
			
			$result[employee_id]=$emp_id;
			
			$result[username]=$username;
			
			$result[requirement]=$res1[requirement];
			
			$result[consequences]=$res1[consequences];
			
			$result[date]=$this->changedate_display($res1[due_date]);
			
			$content=$common->direct_replace($db_object,$content,$result);
			
			echo $content;
			
		
	}
	
	function make_changes($db_object,$common,$_POST,$error_msg)
	{
		$user_table=$common->prefix_table("user_table");
		
		$performance_setting=$common->prefix_table("performance_setting");
		
		$plan_improvement=$common->prefix_table("plan_improvement");
		
		$temp_plan_improvement=$common->prefix_table("temp_plan_improvement");
		
		$unapproved_plan_improvement=$common->prefix_table("unapproved_plan_improvement");
		
		$qry="select plan_no from $performance_setting";
		
		$res=$db_object->get_a_line($qry);
		
		$qry1="select $unapproved_plan_improvement.plan_text,$unapproved_plan_improvement.plan_date,$unapproved_plan_improvement.employee_id,$user_table.username,$unapproved_plan_improvement.employee_id
			
			 from $unapproved_plan_improvement,$user_table where $unapproved_plan_improvement.plan_id='$plan_id'
			 
			 and $unapproved_plan_improvement.employee_id=$user_table.user_id";
			 
			
			
		$result1=$db_object->get_rsltset($qry1);
		
		$employee_id=$_POST["employee_id"];
		
		$plan_id=$_POST["plan_id"];
		
		$keys=@array_keys($_POST);
		
		$i=0;
			
		$imp_qry="select imp_id from $unapproved_plan_improvement where plan_id='$plan_id' ORDER BY imp_id asc LIMIT 1";
		
		$imp_res=$db_object->get_a_line($imp_qry);
		
		$t_imp_qry="select imp_id from $temp_plan_improvement where plan_id='$plan_id' ORDER BY imp_id asc LIMIT 1";
		
		$t_imp_res=$db_object->get_a_line($t_imp_qry);
		
		$t_imp_id=$t_imp_res[imp_id];
		
		$imp_id=$imp_res[imp_id];
		
		while($i<=$res[plan_no])
		{
			
			$key=$keys[$i];
			
			$action=$_POST[$key];
			
			$i++;
			
			$key=$keys[$i];
			
			$date=$_POST[$key];
			
			$date=$this->date_format($date);
			
				
			$sql="update $unapproved_plan_improvement set plan_text='$action',
			
			plan_date='$date',employee_id='$employee_id' where imp_id='$imp_id'";
			
			$db_object->insert($sql);
			
		
			
			$imp_id++;
			
		
			
			
			$i++;
			

			
		}
		echo $error_msg['cInformationSaved'];
	}
	
	function approve($db_object,$common,$_POST,$default,$error_msg)
	{
		
		$performance_setting=$common->prefix_table("performance_setting");
		
		$plan_improvement=$common->prefix_table("plan_improvement");
		
		$temp_plan_improvement=$common->prefix_table("temp_plan_improvement");
		
		$plan=$common->prefix_table("plan");
		
		$user_table=$common->prefix_table("user_table");
		
		$performance_message=$common->prefix_table("performance_message");
		
		$unapproved_plan_improvement=$common->prefix_table("unapproved_plan_improvement");
		
		$qry="select plan_no from $performance_setting";
		
		$res=$db_object->get_a_line($qry);
		
		$employee_id=$_POST["employee_id"];
		
		$plan_id=$_POST["plan_id"];
		
		$keys=@array_keys($_POST);
		
		$i=0;
		
		$app_qry="select imp_id from $plan_improvement where plan_id='$plan_id' ORDER BY imp_id asc LIMIT 1";
		
		$app_res=$db_object->get_single_column($app_qry);
		
		$imp=$app_res[0];
		
		$imp_qry="select imp_id from $unapproved_plan_improvement where plan_id='$plan_id' ORDER BY imp_id asc LIMIT 1";
		
		$imp_res=$db_object->get_a_line($imp_qry);
		
		$imp_id=$imp_res[imp_id];
		
		
		$t_imp_qry="select imp_id from $temp_plan_improvement where plan_id='$plan_id' ORDER BY imp_id asc LIMIT 1";
		
		$t_imp_res=$db_object->get_a_line($t_imp_qry);
		
		$t_imp_id=$t_imp_res[imp_id];
		
		$j=0;
		
		while($i<=$res[plan_no])
		{
			
			$key=$keys[$i];
			
			$action=$_POST[$key];
			
			$i++;
			
			$key=$keys[$i];
			
			$date=$_POST[$key];
			
			$date=$this->date_format($date);
			

			$sql="update $unapproved_plan_improvement set plan_text='$action',
			
			plan_date='$date',employee_id='$employee_id',status='a' where imp_id='$imp_id'";
			

			$db_object->insert($sql);
			
			$sql1="update $temp_plan_improvement set plan_text='$action',
			
			plan_date='$date',employee_id='$employee_id' where imp_id='$t_imp_id'";
			
			$db_object->insert($sql1);
			
			if($app_res[0]=="")
			{
			$qry="insert into $plan_improvement set plan_text='$action',plan_id='$plan_id',
			
			plan_date='$date',employee_id='$employee_id',status='a'";
			

			
			$db_object->insert($qry);
			}
			else
			{
				
			$qry1="update $plan_improvement set plan_text='$action',
			
			plan_date='$date',employee_id='$employee_id',status='a' where imp_id='$imp'";
			

			
			$db_object->insert($qry1);
			
			$j++;
			}
			
			
			$imp_id++;
			
			$imp++;
			
			$t_imp_id++;
			
			$i++;
			
		}
		
		//----------------------------MAILS--------------------------
		$qry="select * from $plan where plan_id='$plan_id'";
		
		$res=$db_object->get_a_line($qry);
		
		$fAssigned_by=$res[added_by];
		
		$fAssigned_to=$res[employee_id];
		
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
		
		$mail_qry="select plan_approved_subject_$default,plan_approved_message_$default from $performance_message";
		
		$mail_res=$db_object->get_a_line($mail_qry);
		
		$subject=$mail_res[plan_approved_subject_1];
		
		$message=$mail_res[plan_approved_message_1];
				
		for($i=0;$i<4;$i++)
		{		
	
		$click="http://www.cat45.com/Pms/performance/view_performance_plan.php";
		
		$xarr[plan]=$plan_id;
		
		$click=$common->direct_replace($db_object,$click,$xarr);
		
		$xArray=array("user"=>$to_name[$i],"click"=>$click);
			
		$message1=$common->direct_replace($db_object,$message,$xArray);
				
		$to=$to_mail[$i];
		
	
		
		$common->send_mail($to,$subject,$message1,$by_mail);
		}
		
		echo $error_msg[cApprovalInformedtouser];
		
		
		
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

$obj=new approval();

if($fSave)
{
	$action="save";
	
}
if($fApprove)
{
	$action="approve";
}

switch($action)
{
	case "alert":
	
	$obj->show_approval($db_object,$common,$user_id);
	
	break;
	
	case "show":
	
	$obj->show_form($db_object,$common,$plan_id);
	
	break;
	
	case "save":
	
	$obj->make_changes($db_object,$common,$_POST,$error_msg);
	
	break;
	
	case "approve":
	
$obj->approve($db_object,$common,$_POST,$default,$error_msg);
	
	break;
	
}
include_once("footer.php");

?>
