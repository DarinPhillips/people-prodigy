<?php
include("../session.php");
include("header.php");
class Assign_Test_builder
{
 function assign_test1($common,$db_object,$user_id,$form_array)
 {
 	while(list($kk,$vv)=@each($form_array))
 	{
 		$$kk=$vv;
 	}
 	$path=$common->path;
 		$xFile=$path."templates/career/assign_test_intermediate.html";
    		$xTemplate=$common->return_file_content($common,$xFile);
	if($fNext)
	{
	$this->assign_test($common,$db_object,$user_id,$testtype);
	}
	else
	{
	$xTemplate=$common->direct_replace($db_object,$xTemplate,$vals);
	echo $xTemplate;
	}
}
	
   function assign_test($common,$db_object,$user_id,$testtype)
    {

    	
    	$path=$common->path;
    	$xFile=$path."templates/career/assign_test_builder.html";
    	$xTemplate=$common->return_file_content($common,$xFile);
    		$skills=$common->prefix_table("skills");
		$position_table=$common->prefix_table("position");
    		$user_table=$common->prefix_table("user_table");

$xTemplate=preg_replace("/<{prelimary_loopstart}>(.*?)<{preliminary_loopend}>/s","",$xTemplate);
if($testtype=="i")
{
	$multiple="[] multiple";
	$xTemplate=preg_replace("/<{empty_s}>(.*?)<{empty_e}>/s","",$xTemplate);
$xTemplate=preg_replace("/<{multiple}>/s",$multiple,$xTemplate);
		$selqry="select skill_name,skill_id from skills where skill_type='i'";
		$skill_set=$db_object->get_rsltset($selqry);
		
}
else
{
$xTemplate=preg_replace("/<{multiple}>/s","[]",$xTemplate);
		$selqry="select skill_name,skill_id from skills where skill_type='t'";
		$skill_set=$db_object->get_rsltset($selqry);
}

		$selqry="select $user_table.username,$user_table.user_id
			from $user_table,$position_table
			where $position_table.pos_id=$user_table.position
			and admin_id='$user_id'
			order by $position_table.level_no desc";
			
		$names_result=$db_object->get_rsltset($selqry);

//		echo $selqry;
		$values["username_loop"]=$names_result;

		
		$values["skill_loop"]=$skill_set;
$xTemplate=$common->multipleloop_replace($db_object,$xTemplate,$values,0);
$vals["testtype"]=$testtype;
$xTemplate=$common->direct_replace($db_object,$xTemplate,$vals);
    	echo $xTemplate;
    	
    }
    function add_to_tables($common,$db_object,$user_id,$form_array,$error_msg)
    {
    	while(list($kk,$vv)=@each($form_array))
    	{
    		$$kk=$vv;
    	}
    
$user_table=$common->prefix_table("user_table");
$skills_table=$common->prefix_table("skills");
$assign_test_builder=$common->prefix_table("assign_test_builder");

$selqry="select username,password,email from $user_table where user_id='$fUser_id'";
$user_name=$db_object->get_a_line($selqry);


$skilids=@implode(",",$fSkill);
$selqry="select skill_id,skill_name from $skills_table where skill_id in ($skilids)";
$skill_name=$db_object->get_rsltset($selqry);
$skill_name=$common->return_Keyedarray($skill_name,"skill_id","skill_name");





$selqry="select email from $user_table where user_id='$user_id'";
$admin_details=$db_object->get_a_line($selqry);

$chkqry="select user_id from $assign_test_builder where skill_id='$fSkill'";
$user_set=$db_object->get_single_column($chkqry);
/*
if(@in_array($fUser_id,$user_set))
{
	echo $skill_name["skill_name"];
	echo ":<br>";
	echo "This Skill has been already assigned to Some one";
	include("footer.php");
	exit;
}*/



if($fTest_type=="t")
{
$fSkill=$fSkill[0];
//echo "You entering Techinical Test <br>";
$insqry="insert into $assign_test_builder set user_id='$fUser_id',skill_id='$fSkill',admin_id='$user_id',date=now()";
$db_object->insert($insqry);
echo $user_name["username"];
echo $error_msg["cHasassigntobuild"];
echo $skill_name[$fSkill];
$skillname=$skill_name[$fSkill];
echo "<br>";
}
else
{
	$selqry="select max(group_id) from $assign_test_builder";
	$maxcount=$db_object->get_single_column($selqry);
	$cnt=$maxcount[0]+1;

	
	for($i=0;$i<count($fSkill);$i++)
	{

$skill_id_to_be=$fSkill[$i];
$skillname.=$skill_name[$skill_id_to_be];
$insqry="insert into $assign_test_builder set user_id='$fUser_id',skill_id='$skill_id_to_be',admin_id='$user_id',date=now(),group_id='$cnt'";
$db_object->insert($insqry);
//echo "$insqry<br>";

	}
	
}
$path=$common->path;
$path=$path."index.php";


			$config=$common->prefix_table("config");
			$selqry="select test_subject,test_message from $config";
			$emaildetails=$db_object->get_a_line($selqry);
			$to=$user_name["email"];
			$logininfo=$user_name["username"];
			$password=$user_name["password"];
			$from=$admin_details["email"];
			$subject=$emaildetails["test_subject"];
			$message=$emaildetails["test_message"];
			$user=$user_name["username"];
			$skill=$skillname;
			$url=$path;
			$message=preg_replace("/{{(.*?)}}/e","$$1",$message);
			
		$bool=$common->send_mail($to,$subject,$message,$from);
		if($bool)
		{
			echo $error_msg["cUserInformedofTest"];
		}

    }
}
$testobj=new Assign_Test_builder;

if($fAssign)
{
$user_table=$common->prefix_table("user_table");
$selqry="select admin_id from $user_table where user_id='$fUser_id'";
$admin_details=$db_object->get_a_line($selqry);
if($user_id==$admin_details["admin_id"])
{	
$testobj->add_to_tables($common,$db_object,$user_id,$post_var,$error_msg);
}
else
{
	echo "You cannot Assign the Test for this User";
}
}
else
{
	$user_id_admin=$common->is_admin($db_object,$user_id);
	if($user_id_admin!=false)
	{
	$testobj->assign_test1($common,$db_object,$user_id,$post_var);
	}
	else
	{
		echo "Trespassers will be Prosecuted";
	}
}
include("footer.php");
?>