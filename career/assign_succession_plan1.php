<?php
include("../session.php");
include_once("header.php");

class assign
{
	function assign_plan($db_object,$common,$user_id,$fBoss,$error_msg,$pos_id_sel)
	{
		
		$path=$common->path;
		$xtemplate=$path."templates/career/assign_succession_plan1.html";
		$file=$common->return_file_content($db_object,$xtemplate);
	
		
		$user_table=$common->prefix_table("user_table");
		$position=$common->prefix_table("position");
		
		$xArray[employee]=$common->name_display($db_object,$fBoss);
		$xArray[fUser]=$fBoss;
		
		$pos_qry="select position from $user_table where user_id='$fBoss'";
		$pos_res=$db_object->get_a_line($pos_qry);
		$pos=$pos_res[position];
		
		$users_under=$common->get_chain_below($pos,$db_object,$twodarr);
		$user_under_id=$common->get_user_id($db_object,$users_under);
		
		
		$pattern="/<{employee_loopstart}>(.*?)<{employee_loopend}>/s";
		preg_match($pattern,$file,$match);
	
		$match=$match[0];
		
		for($i=0;$i<count($users_under);$i++)
		{
			
			$pos_id=$users_under[$i];
			
			if($pos_id != -1)
			{
				$qry="select position_name from $position where pos_id='$pos_id'";
				$res=$db_object->get_a_line($qry);
				
				$position_name=$res[position_name];
					
				if($pos_id == $pos_id_sel)
				{
				$sel = "selected";
				}
				else
				{
				$sel = "";
				}
				
				$str.=preg_replace("/<{(.*?)}>/e","$$1",$match);
			}	
		}

		$file=preg_replace($pattern,$str,$file);
		$file=$common->direct_replace($db_object,$file,$xArray);
		
		echo $file;
		
	}
	
	function assign_succession_plan($db_object,$common,$user_id,$fPosition,$fUser,$error_msg)
	{
		$assign_succession_plan_sub=$common->prefix_table("assign_succession_plan_sub");
		$user_table=$common->prefix_table("user_table");
		$config=$common->prefix_table("config");
		$position=$common->prefix_table("position");
		
		/*$sql="select position from $user_table where user_id='$fUser'";
		
		$res_sql=$db_object->get_a_line($sql);
		
		$pos=$res_sql[position];*/
		
		$sql="select username,email from $user_table where user_id='$user_id'";
		$res=$db_object->get_a_line($sql);
		$from=$res[email];
		
		$sql="select username,email from $user_table
			 where user_id='$fUser'";
		$res_mail=$db_object->get_a_line($sql);
		
		$boss_mail=$res_mail[email];
		
		$mail_qry="select mail_to_boss_subject,mail_to_boss_message from $config";
		$mail_res=$db_object->get_a_line($mail_qry);
		
		$subject=$mail_res[mail_to_boss_subject];
		$message=$mail_res[mail_to_boss_message];
		
		for($i=0;$i<count($fPosition);$i++)
		{
			$pos1=$fPosition[$i];
			
			$sql="select * from $assign_succession_plan_sub where position='$pos1' and status<>'y'";

			$res=$db_object->get_rsltset($sql);
					
			$sql1="select position_name,user_id,username,email from $user_table,$position
					 where $user_table.position=$position.pos_id and $user_table.position='$pos1'";
			
			$res1=$db_object->get_a_line($sql1);
			
			if($res[0]!="")
			{
				$qry="select position_name from $position where pos_id='$pos1'";
				
				$res_qry=$db_object->get_a_line($qry);
				
				$name=$res_qry[position_name];
				
				echo $error_msg['cSucAlreadyAssigned'];
							
				echo "$name<br>";
			}
			else
			{
				$qry="insert into $assign_succession_plan_sub set assigned_to='$fUser',
						assigned_by='$user_id',assigned_on=now(),position='$pos1'";

				$db_object->insert($qry);
				
				$value[user]=$res_mail[username];
				$value[boss]=$res_mail[username];
				$value[emp]=$res1[username];
				
				$to=$res_mail[email];
				$value[click]="front_panel.php";
				
				$message1=$common->direct_replace($db_object,$message,$value);
				$common->send_mail($to,$subject,$message1,$from);
				
				$value[user]=$res1[username];
				$value[boss]=$res_mail[username];
				$value[emp]=$res1[username];
				$value[click]="front_panel.php";
				$to=$res1[email];
				
				$message1=$common->direct_replace($db_object,$message,$value);
				$common->send_mail($to,$subject,$message1,$from);
				
				echo $res_mail[username];
			
				echo $error_msg['cAssignedtoUpdate'];
			
				echo $res1[position_name];
			
				echo "<br>";
				
			}
		}
		}
}

$obj=new assign();

if($fAssign)
{
	$action="assign";
}

switch($action)
{
	case NULL:
		$obj->assign_plan($db_object,$common,$user_id,$fBoss,$error_msg,$pos_id_sel);
	break;
	
	case "assign":
		$obj->assign_succession_plan($db_object,$common,$user_id,$fPosition,$fUser,$error_msg);
	break;
}

include_once("footer.php");

?>
