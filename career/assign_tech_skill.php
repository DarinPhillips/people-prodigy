<?php
include("../session.php");
include("header.php");

class Assign_skills
{

  function assign($common,$db_object,$user_id,$form_array)
  {
	$path=$common->path;
	$xFile=$path."templates/career/assign_tech_skill.html";
	$xTemplate=$common->return_file_content($db_object,$xFile);


//-------------------tables
	$user_table=$common->prefix_table("user_table");
	$position_table=$common->prefix_table("position");
	$org_main=$common->prefix_table("org_main");

$selqry="select $user_table.username,$user_table.user_id from $user_table,$position_table where $position_table.pos_id=$user_table.position and $user_table.admin_id=$user_table.user_id order by $position_table.level_no desc";
$names_result=$db_object->get_rsltset($selqry);
$values["username_loop"]=$names_result;
$selqry="select $position_table.level_no from $position_table,$user_table where $position_table.pos_id=$user_table.position and $user_table.user_id='$user_id'";
$admin_level=$db_object->get_a_line($selqry);
$level_no=$admin_level["level_no"];
$selqry="select higher_order from $org_main";
$order=$db_object->get_a_line($selqry);
if($order["higher_order"]=="yes")
{
	$selqry="select position_name,pos_id from $position_table where  level_no<'$level_no' order by level_no desc";
}
else
{
	$selqry="select position_name,pos_id from $position_table where  level_no>'$level_no' order by level_no asc";
}
$position_result=$db_object->get_rsltset($selqry);
$values["position_loop"]=$position_result;
$xTemplate=$common->multipleloop_replace($db_object,$xTemplate,$values,0);
$vals=array();
$xTemplate=$common->direct_replace($db_object,$xTemplate,$vals);
echo $xTemplate;
}
  
  
  
function add_to_table($common,$db_object,$user_id,$form_array,$error_msg)
{
	while(list($kk,$vv)=@each($form_array))
	{
		$$kk=$vv;
	}
	$user_table=$common->prefix_table("user_table");
	$position_table=$common->prefix_table("position");
	$org_main=$common->prefix_table("org_main");
	$assign_tech_skill_builder=$common->prefix_table("assign_tech_skill_builder");

$selqry="select position_id from $assign_tech_skill_builder where user_id='$fUser_id' and status<>'h'";
$userposition=$db_object->get_rsltset($selqry);
for($i=0;$i<count($userposition);$i++)
{
	$uposition[$i]=$userposition[$i]["position_id"];
}

if($uposition!="")
{
	$temp=array_intersect($fPosition,$uposition);
	if($temp[0])
	{
		echo $error_msg["cUserassigned"];
		include_once("footer.php");
		exit;
	}
}
$selqry="select $position_table.level_no from $user_table,$position_table where $position_table.pos_id=$user_table.position and user_id='$fUser_id'";
$user_position=$db_object->get_a_line($selqry);
$user_level=$user_position["level_no"];

$posforcheck=@implode(",",$fPosition);


$selqry="select pos_id,level_no from $position_table where pos_id in ($posforcheck)";

$assign_position=$db_object->get_rsltset($selqry);

$assign_position=$common->return_Keyedarray($assign_position,"pos_id","level_no");


//print_R($assign_position);
//$assign_level=$assign_position["level_no"];
//echo $user_level;

$selqry="select pos_id,position_name from $position_table";
$positionnameset=$db_object->get_rsltset($selqry);
$positionnameset=$common->return_Keyedarray($positionnameset,"pos_id","position_name");
$selqry="select higher_order from $org_main";
$order=$db_object->get_a_line($selqry);
if($order["higher_order"]=="yes")
{
	
		for($i=0,$cnt=0;$i<count($fPosition);$i++)
		{
			$posnt=$fPosition[$i];		
			$assign_level=$assign_position[$posnt];
			if($user_level>=$assign_level)
			{

			$assignqry="insert into $assign_tech_skill_builder set user_id='$fUser_id',position_id='$posnt',admin_id='$user_id',date=now()";
			$db_object->insert($assignqry);
		//	echo $positionnameset[$posnt];	
		//	echo "$assignqry<br>";
			$posassigned.=$positionnameset[$posnt].",";
			$cnt++;
			}
			else
			{
			echo $error_msg["cUserMismatch"];
			echo $positionnameset[$posnt];
			echo "<br>";
			echo $error_msg["cChosesomeotherpos"];
			echo "<br>";		
			}
		}
		if($cnt>0)
		{
			echo "<br>";
		echo $error_msg["cSkillbuilderAssigned"];
		echo "<br>";
		$this->send_mail_to($common,$db_object,$fUser_id,$user_id,$posassigned,$error_msg);		
		}
	
}
else
{
		for($i=0,$cnt=0;$i<count($fPosition);$i++)
		{
		$posnt=$fPosition[$i];
	$assign_level=$assign_position[$posnt];
//echo "assl=$assign_level";
//echo "uslel=$user_level";
	if($user_level<=$assign_level)
	{

		$assignqry="insert into $assign_tech_skill_builder set user_id='$fUser_id',position_id='$posnt',admin_id='$user_id',date=now()";
		$db_object->insert($assignqry);		
//		echo "$assignqry<br>";
//	      echo $positionnameset[$posnt];

$posassigned.=$positionnameset[$posnt].",";		
		$cnt++;
	      
	}
	else
	{	
		echo $error_msg["cUserMismatch"];
		echo $positionnameset[$posnt];
		echo "<br>";
		echo $error_msg["cChosesomeotherpos"];
		echo "<br>";
	
	}
		}
		if($cnt>0)
		{
		echo $error_msg["cSkillbuilderAssigned"];
		$this->send_mail_to($common,$db_object,$fUser_id,$user_id,$posassigned,$error_msg);
		}

		
	
}


}


function send_mail_to($common,$db_object,$fUser_id,$user_id,$posassigned,$error_msg)
	{
	$position=substr($posassigned,0,-1);
	$user_table=$common->prefix_table("user_table");
	$config=$common->prefix_table("config");
	$selqry="select username,user_id,email from $user_table where user_id='$user_id'";
	$emails=$db_object->get_a_line($selqry);

	$selqry="select username,password,user_id,email from $user_table where user_id='$fUser_id'";
	$userdetail=$db_object->get_a_line($selqry);
//	$email=$common->return_Keyedarray($emails,"user_id","email");

	$user=$common->name_display($db_object,$fUser_id);

	$from=$emails["email"];
	
	$logininfo=$userdetail["username"];
	$password=$userdetail["password"];
	$to=$userdetail["email"];

	$selmail="select skill_message,skill_subject from $config";
	$maildetails=$db_object->get_a_line($selmail);

	$message=$maildetails["skill_message"];
	$subject=$maildetails["skill_subject"];

$path=$common->path;	
$url=$path."index.php";
$message=preg_replace("/{{(.*?)}}/e","$$1",$message);
	$bool=$common->send_mail($to,$subject,$message,$from);

if($bool)
{
	echo $error_msg["cUserinformedforskillbuilder"];
}
	return;
	}



  

}
$skillobj=new Assign_skills;
if($fAssign)
{
	$skillobj->add_to_table($common,$db_object,$user_id,$post_var,$error_msg);
}
else
{
	$user_id_admin=$common->is_admin($db_object,$user_id);
	if($user_id_admin)
	{
	$skillobj->assign($common,$db_object,$user_id,$post_var);
	}
	else
	{
		echo "Traspassers will be Prosecuted";
	}
}

include("footer.php");

?>
