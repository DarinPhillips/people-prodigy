<?php
include("../session.php");
include("header.php");
class DevSolution
{
	function display_file($common,$db_object,$user_id)
	{
		$path=$common->path;
		
		$xFile=$path."templates/learning/assign_solution_builder.html";
		
		$xTemplate=$common->return_file_content($db_object,$xFile);

		$skills_table=$common->prefix_table("skills");
		
		$user_table=$common->prefix_table("user_table");
		
		$selqry="select skill_id,skill_name from $skills_table where skill_type='i'";
		
		$skillset=$db_object->get_rsltset($selqry);
		
		$selectqry="select skill_id,skill_name from $skills_table where skill_type='t'";
		
		$Tskillset=$db_object->get_rsltset($selectqry);
	
		/*$pos_qry="select position from user_table where user_id='$user_id'";
		
		
		
		$pos_result=$db_object->get_a_line($pos_qry);
		
		$position=$pos_result[position];
		
		$user=$common->get_chain_below($position,$db_object,$twodarr);
		
		$k=0;
		
		for($c=0;$c<count($user);$c++)
		{
			$pos=$user[$c];
			
			$user_pos[$c]=$common->get_chain_below($pos,$db_object,$twodarr);
			
			$user[$c]=$common->get_user_id($db_object,$user_pos[$c]);
			
			$user_arr=array();
			
			for($j=0;$j<count($user[$c]);$j++)
			{
				
				$user_array[$k][user_id]=$user[$c][$j][user_id];
				
				$user_array[$k][username]=$user[$c][$j][username];
				
				$check[$k]=$user[$c][$j][user_id];
			
				$k++;
				
			}
						
		}
		$check=@array_unique($check);
		
		$keys=@array_keys($check);
		
		for($i=0;$i<count($keys);$i++)
		{
			$key=$keys[$i];
			
			$userset[$i][username]=$user_array[$key][username];
			
			$userset[$i][user_id]=$user_array[$key][user_id];
		}*/
		
		if($user_id!=1)
		{
		
		$user_qry="select user_id,username from user_table where admin_id='$user_id'";
		
		$user_res=$db_object->get_rsltset($user_qry);
		}
		else
		{
			$user_qry="select user_id,username from user_table where user_id <>'1'";
		
			$user_res=$db_object->get_rsltset($user_qry);
		}
		
		
		$values["perskill_loop"]=$skillset;

		$values["user_loop"]=$user_res;
		
		$values["techskill_loop"]=$Tskillset;

		$xTemplate=$common->simpleloopprocess($db_object,$xTemplate,$values);		
		
		$array["user"]=$user_id;
		
		$xTemplate=$common->direct_replace($db_object,$xTemplate,$array);
		
		echo $xTemplate;
	}

	function update_table($common,$db_object,$user_id,$skill_id,$error_msg,$fUser)
	{
		$assign_table=$common->prefix_table("assign_solution_builder");
		
		$config=$common->prefix_table("config");
		
		$user_table=$common->prefix_table("user_table");
		
		$skills=$common->prefix_table("skills");
	
		$existing_skill="select skill_id from $assign_table where user_id='$user_id'";
		
		$existing_skill_result=$db_object->get_rsltset($existing_skill);
		
		for($i=0;$i<count($existing_skill_result);$i++)
		{
			if($skill_id == $existing_skill_result[$i][skill_id])
			{
				echo $error_msg["cAssignerror"];
				
				$this->display_file($common,$db_object,$fUser);
				
				include_once("footer.php");
				
				exit;
			}
		}
		
		

		$selqry="select username,email from $user_table where user_id='$fUser'";
		
		$adminmailid=$db_object->get_a_line($selqry);
		
		//$selqry="select username,email from $user_table where user_id='$user_id'";
		
		//$adminmailid=$db_object->get_a_line($selqry);
		
		$mail_qry="select username,email from $user_table where user_id='$user_id'";
		
		$user_mail=$db_object->get_a_line($mail_qry);
		
		$path=$common->path;
		
		$type_qry="select skill_type from $skills where skill_id='$skill_id'";
		
		$type_result=$db_object->get_a_line($type_qry);
		
		$type=$type_result[skill_type];
		
		$insqry="insert into $assign_table set user_id='$user_id',skill_id='$skill_id',admin_id='$fUser',date=now(),status='i',type='$type'";		
		
		$db_object->insert($insqry);
		
		$selqry="select lsolution_subject,lsolution_message from $config where id=1";
		
		$mailoption=$db_object->get_a_line($selqry);
		
		$subject=$mailoption["lsolution_subject"];
		
		$message=$mailoption["lsolution_message"];
		
		$user_name=$common->name_display($db_object,$user_id);
		
		$skill_qry="select skill_name from $skills where skill_id='$skill_id'";
		
		$skill_result=$db_object->get_a_line($skill_qry);
		
		$skill_name=$skill_result[skill_name];
		
		$xArray=array("user"=>$user_name,"skillname"=>$skill_name);
		
		$message=$common->direct_replace($db_object,$message,$xArray);
		
		
		
		/*$mailpath=$path."dev_solution?action=insert";
		
		$mes="<a href='$mailpath'>{{cLoginhere}}</a>";
		
		$message=preg_replace("/<{url}>/s",$mes,$message);*/

		$to=$user_mail["email"];
		
		$from=$adminmailid["email"];
		
		$bool=$common->send_mail($to,$subject,$message,$from);
			if($bool)
			{
				echo $error_msg["cUserinformed"];
			}
			else
			{
				echo $error_msg["cFailmail"];
			}
			include_once("footer.php");
			exit;
		}
		
		
}
$solobj= new DevSolution;


if($fAssign_t)
{
	$user_id=$_POST["fUsername_t"];
	
	$skill_id=$_POST["fSkill_t"];
	
	
	$solobj->update_table($common,$db_object,$user_id,$skill_id,$error_msg,$fUser);
}
if($fAssign)
{
	$user_id=$_POST["fUsername"];
	
	$skill_id=$_POST["fSkill"];
	
	$solobj->update_table($common,$db_object,$user_id,$skill_id,$error_msg,$fUser);
	
}
else
{
	$solobj->display_file($common,$db_object,$user_id);
}
include("footer.php");
?>
