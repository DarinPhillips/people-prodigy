<?php
include("../session.php");
include("header.php");
class assignplan
{
	function display_file($common,$db_object,$user_id)
	{
		$admin_id=$user_id;
		
		$path=$common->path;
		
		$xFile=$path."templates/learning/assign_plan_builder.html";
		
		$xTemplate=$common->return_file_content($db_object,$xFile);
		
		$skills_table=$common->prefix_table("skills");
		
		$user_table=$common->prefix_table("user_table");
		
			$approved_devbuilder=$common->prefix_table("approved_devbuilder");
			
		$assign_solution_builder=$common->prefix_table("assign_solution_builder");
		
		$qry="select $user_table.user_id,$user_table.username,$skills_table.skill_name,
		
$assign_solution_builder.skill_id,$assign_solution_builder.admin_id from 

$user_table,$assign_solution_builder,$skills_table,$approved_devbuilder
 
where 
$approved_devbuilder.user_id = $assign_solution_builder.user_id

and $approved_devbuilder.skill_id = $assign_solution_builder.skill_id

and $skills_table.skill_id=$assign_solution_builder.skill_id and

$user_table.user_id=$assign_solution_builder.user_id and 

$assign_solution_builder.admin_id='$user_id' and $assign_solution_builder.status='a' group by $assign_solution_builder.user_id,skill_name";


		$result=$db_object->get_rsltset($qry);
		
		for($j=0;$j<count($result);$j++)
		{
			$array_id[$j]=$result[$j][user_id];
				
		}
			
			$unique_id=@array_unique($array_id);
			
			$unique_keys=@array_keys($unique_id);
			
			
			for($i=0;$i<count($unique_id);$i++)
			{
				$key=$unique_keys[$i];
				
				$replace_user[$i][user_id]=$unique_id[$key];
				
				$replace_user[$i][user_name]=$result[$key][username];
				
			}
			
		
			
		$j=0;
$replace=<<<EOD
subcatids = new Array;
subcatnames = new Array;
EOD;
for($i=0;$i<count($result);$i++)
{
	
	if($i==0)
	{
	$user_id=$result[$i][user_id];
	
	$skill_id=$result[$i][skill_id];
	
	$skill_name=$result[$i][skill_name];
	
	$subcatid[$j]="subcatids[".$user_id."]=new Array('',".$skill_id;
	
	$subcatname[$j]="subcatnames[".$user_id."]=new Array('{{cSelect}}',"."\"$skill_name\"";
	
	$j++;
		}
	else
	{
		$user_id=$result[$i][user_id];
		
		if($check_user_id==$user_id)
		{
			$k=$j-1;
			
			$skill_id=$result[$i][skill_id];
			
			$skill_name=$result[$i][skill_name];
			
			$subcatid[$k].=",".$skill_id;
			
			$subcatname[$k].=","."\"$skill_name\"";
		}
		
		else
		{
			
			$skill_id=$result[$i][skill_id];
			
			$skill_name=$result[$i][skill_name];
			
			$subcatid[$j]="subcatids[".$user_id."]=new Array('',".$skill_id;
	
			$subcatname[$j]="subcatnames[".$user_id."]=new Array('{{cSelect}}',"."\"$skill_name\"";
			
			$j++;
		}
	}
	
	$check_user_id=$user_id;
}



if(count($subcatid>0))
		{
$subcat_list=@implode(");\n",$subcatid);

$subcat_list.=");";

$subcatname_list=@implode(");\n",$subcatname);

$subcatname_list.=");";

		}
	

$xArray=array("replace"=>$replace,"catname_list"=>$subcatname_list,"catid_list"=>$subcat_list);

$values["user_loop"]=$replace_user;

$xTemplate=$common->simpleloopprocess($db_object,$xTemplate,$values);

$xArray["admin_id"]=$admin_id;

$output=$common->direct_replace($db_object,$xTemplate,$xArray);

$output=$common->direct_replace($db_object,$output,$xArray);

echo $output;

}

function update_plan_builder($common,$db_object,$user_id,$skill_id,$admin_id,$error_msg)
{
	$skills_table=$common->prefix_table("skills");
		
	$user_table=$common->prefix_table("user_table");
	
	$assign_solution_builder=$common->prefix_table("assign_solution_builder");
	
	$config=$common->prefix_table("config");
	
	$qry1="select pstatus from $assign_solution_builder where user_id='$user_id' and skill_id='$skill_id'";
	
	$res1=$db_object->get_a_line($qry1);
	
	if($res1[pstatus]!="")
	{
		echo $error_msg['cAlreadyassigned'];
		
		include_once("footer.php");
		
		exit;
	}
	
	$qry="update $assign_solution_builder set pstatus='i' where user_id='$user_id' and skill_id='$skill_id'";
	
	$result=$db_object->insert($qry);
	
		$selqry="select username,email from $user_table where user_id='$admin_id'";
		
		$adminmailid=$db_object->get_a_line($selqry);
		
		$mail_qry="select username,email from $user_table where user_id='$user_id'";
		
		$user_mail=$db_object->get_a_line($mail_qry);
		
		$selqry="select lplanassign_subject,lplanassign_message from $config where id=1";
		
		$mailoption=$db_object->get_a_line($selqry);
		
		$skill_qry="select skill_name from $skills_table where skill_id='$skill_id'";
		
		$skill_res=$db_object->get_a_line($skill_qry);
		
		$skill_name=$skill_res[skill_name];
		
		$subject=$mailoption["lplanassign_subject"];
		
		$message=$mailoption["lplanassign_message"];
		
		$user_name=$common->name_display($db_object,$user_id);
		
		$xArray=array("user"=>$user_name,"skillname"=>$skill_name);
		
		$message=$common->direct_replace($db_object,$message,$xArray);
			
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
$solobj= new assignplan();

switch($action)
{
	case NULL:

	$solobj->display_file($common,$db_object,$user_id);
	
	break;

	case "update":

	$user_id=$categorybox;
	
	$skill_id=$subcategorybox1;
	
	$solobj->update_plan_builder($common,$db_object,$user_id,$skill_id,$admin_id,$error_msg);
	
	break;
}
	

include("footer.php");
?>
