<?php
/*---------------------------------------------
SCRIPT:manual_skill_entry.php
AUTHOR:info@chrisranjana.com	
UPDATED:1st Sept

DESCRIPTION:

---------------------------------------------*/
include_once("../session.php");
include_once("header.php");

$xPath=$common->path;
class manual_skill
{
  function display($common,$db_object,$form_array)
  {
     while(list($kk,$vv)=@each($form_array))
     {
     	$vv=str_replace("$","&#36;",$vv);
	$$kk=$vv;
     }
$xTemplate=$xPath."../templates/career/manual_skill_entry.html";
$filecontent=$common->return_file_content($db_object,$xTemplate);
$val=array();
$filecontent=$common->direct_replace($db_object,$filecontent,$val);

echo $filecontent;

  }
  function saveandadd($common,$db_object,$form_array,$user_id,$alert_msg)
  {

      while(list($kk,$vv)=@each($form_array))
	{
	$$kk=$vv;
	}
	if($user_id==1)
	{
		$tablename=$common->prefix_table("skills");
		$sql="insert into $tablename set skill_name='$fskill_name',skill_description='$fskill_def',DATE_OF_ADDITION=NOW(),added_by='$user_id',over_used='$fOverused',career_killer='$fCareerkiller',compensator='$fCompensator'";
  		if($radiobutton=="finter_skill")
		{
			$sql=$sql.",unskilled_desc='$funskill_def',skill_type='i'";
		}
		else
		{
			$sql=$sql.",skill_type='t'";
		}
	}
	else
  	{ 	$tablename=$common->prefix_table("unapproved_skills");
  		$sql="insert into $tablename set skill_name='$fskill_name',skill_description='$fskill_def',emp_id='$user_id',date_posted=curdate(),over_used='$fOverused',career_killer='$fCareerkiller',compensator='$fCompensator'";

  	if($radiobutton=="finter_skill")
	{
		$sql=$sql.",unskilled_desc='$funskill_def',skill_type='i'";
	}
	else
	{
		$sql=$sql.",skill_type='t'";
	}
  	}
//echo $sql;
	$db_object->insert($sql);
//--mail is sent to the admin if any other sets the skill

    if($user_id!=1)
    {
	$config=$common->prefix_table("config");
	$subqry="select msubject,mmessage from $config";
	$rslt=$db_object->get_a_line($subqry);
	
	$msubject=$rslt["msubject"];
	$mmessage=$rslt["mmessage"];
	$user=$common->prefix_table("user");
	$subqry2="select username from $user where user_id='$user_id'";
	$user_name=$db_object->get_a_line($subqry2);


	$emailqry="select username,password,email from $user where user_id=1";
	$email_id=$db_object->get_a_line($emailqry);
	$email=$email_id["email"];
	$to=$email;
	$from=$user_name["email"];


	$values["directreplace"]["username"]=$user_name["username"];
	$values["directreplace"]["logininfo"]=$email_id["username"];
	$values["directreplace"]["password"]=$email_id["password"];
	$values["directreplace"]["url"]=$common->http_path."/index.php";
	$mmessage=$common->direct_replace($db_object,$mmessage,$values);
	$sent=$common->send_mail($to,$msubject,$mmessage,$from);
	if($sent)
	{
		
		echo $alert_msg["cMailsent"];
	}
	else
	{
		echo $alert_msg["cFailmail"];
	}
	
    }
  }  

}
$msobj=new manual_skill;
if($submit3)
{
	echo "<script>window.location='front_panel.php';</script>";
}

if($submit2)
{
	$msobj->saveandadd($common,$db_object,$_POST,$user_id,$alert_msg);
	echo "<script>window.location='front_panel.php';</script>";
}
if($submit1)
{
$msobj->saveandadd($common,$db_object,$_POST,$user_id,$alert_msg);
}
$msobj->display($common,$db_object,$form_array);
include("footer.php");
?>
