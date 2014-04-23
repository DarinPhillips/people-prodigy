<?php
include("../session.php");

include_once("header.php");

class assign
{
	function assign_plan($db_object,$common,$user_id,$fBoss_id,$error_msg)
	{
		$assign_succession_plan=$common->prefix_table("assign_succession_plan");
		
		$config=$common->prefix_table("config");
		
		$user_table=$common->prefix_table("user_table");
		
		$check_qry="select plan_id from $assign_succession_plan where assigned_to='$fBoss_id'";
		
		$check_res=$db_object->get_rsltset($check_qry);
		
		if($check_res[0]=="")
		{
		
		$qry="insert into $assign_succession_plan set assigned_by='$user_id',
		
		assigned_to='$fBoss_id',assigned_on=now()";
			
		$qry_res=$db_object->insert($qry);
		
		$mail_qry="select succession_plan_subject,succession_plan_message from $config where 
		
		id='1'";
		
		$mail_res=$db_object->get_a_line($mail_qry);
		
		$subject=$mail_res[succession_plan_subject];
		
		$message=$mail_res[succession_plan_message];
		
		$admin_mail_qry="select email from $user_table where user_id='$user_id'";
		
		$boss_mail_qry="select email from $user_table where user_id='$fBoss_id'";
		
		$admin_res=$db_object->get_a_line($admin_mail_qry);
		
		$boss_res=$db_object->get_a_line($boss_mail_qry);
		
		$to=$boss_res[email];
		
		$from=$admin_res[email];
		
		$xArray["boss"]=$common->name_display($db_object,$fBoss_id);
		
		$xArray["admin"]=$common->name_display($db_object,$user_id);
		
		$message1=$common->direct_replace($db_object,$message,$xArray);
		
		$common->send_mail($to,$subject,$message1,$from);
		
		echo $error_msg['cBossInformed'];
		}
		else
		{
			echo $error_msg['cBossAlreadyAssigned'];
		}
		
	}
	
	function succession_plan_alert($db_object,$common,$user_id,$error_msg)
	{
		
		$user_table=$common->prefix_table("user_table");
		
		$pos_qry="select position from $user_table where user_id='$user_id'";
			
			$pos_res=$db_object->get_a_line($pos_qry);
			
			$position=$pos_res[position];
			
			$users_under=$common->get_chain_below($position,$db_object,$twodarr);
			
			$user_under_id=$common->get_user_id($db_object,$users_under);
			
			$b=0;
		
			for($a=0;$a<count($user_under_id);$a++)
			{
				$ch_id=$user_under_id[$a][user_id];
				
				$ch_boss=$common->is_boss($db_object,$ch_id);
				
				if($ch_boss)
				{
					$users1[$b][user_id]=$ch_id;
					
					$users1[$b][username]=$common->name_display($db_object,$ch_id);
					
					$b++;
				}
			}
			if($users1[0]=="")
			{
				echo $error_msg['cNoBossUnderBoss'];
				
				include_once("footer.php");exit;
			}
			$path=$common->path;
			
			$xtemplate=$path."templates/career/assign_succession_plan.html";
			
			$file=$common->return_file_content($db_object,$xtemplate);
			
			$pattern="/<{boss_loopstart}>(.*?)<{boss_loopend}>/s";
			
			preg_match($pattern,$file,$match);
			
			$match=$match[0];
			
			for($a=0;$a<count($users1);$a++)
			{
				$boss_id=$users1[$a][user_id];
				
				$boss_name=$common->name_display($db_object,$boss_id);
				
				$str.=preg_replace("/<{(.*?)}>/e","$$1",$match);
			}
			$file=preg_replace($pattern,$str,$file);
			
			$file=$common->direct_replace($db_object,$file,$xArray);
			
			echo $file;
			
	}
	
}
$obj=new assign();

switch($action)
{
	case NULL:
	
	$obj->assign_plan($db_object,$common,$user_id,$fBoss_id,$error_msg);
	
	break;
	
	case "assign":
	
	$obj->succession_plan_alert($db_object,$common,$user_id,$error_msg);
	
	break;
	
}

include_once("footer.php");

?>
