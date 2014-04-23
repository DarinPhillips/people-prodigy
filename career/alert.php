<?php
include("../session.php");
include("header.php");
class Alerts_for_approval
{
  function display($common,$db_object,$user_id,$error_msg)
  {
	if($user_id==1)
	{
		$manualskills=$common->prefix_table("unapproved_skills");
		$user=$common->prefix_table("user");
		$mnlqry="select distinct($manualskills.emp_id),$user.username from $manualskills,$user where $user.user_id=$manualskills.emp_id ";
		$skillset=$db_object->get_rsltset($mnlqry);
	
		$xFile="../templates/career/alert.html";
		$xTemplate=$common->return_file_content($db_object,$xFile);
		preg_match("/<{alert_start}>(.*?)<{alert_end}>/s",$xTemplate,$matched);
		$replace=$matched[1];
	
		while(list($kk,$vv)=@each($skillset))
		{
			$values["directreplace"]["emp_name"]=$skillset[$kk]["username"];
			$values["directreplace"]["emp_id"]=$skillset[$kk]["emp_id"];
			$replaced.=$common->direct_replace($db_object,$replace,$values);
		}
		if($replaced=="")
		{
			$replaced=$error_msg["cEmptyrecords"];
		}

		
		$skills_for_act=$common->prefix_table("unapproved_skills_for_activities");
		$xTemplate=preg_replace("/<{alert_start}>(.*?)<{alert_end}>/s",$replaced,$xTemplate);
		
				

		$unapskill_build=$common->prefix_table("unapproved_skill_builder");
		$qry="select distinct($unapskill_build.emp_id),$user.username from $unapskill_build,$user where $unapskill_build.emp_id=$user.user_id order by $user.username";
		$skillset1=$db_object->get_rsltset($qry);
		preg_match("/<{skill_act_start}>(.*?)<{skill_act_end}>/s",$xTemplate,$match);
		$replace1=$match[1];
		while(list($kk,$vv)=@each($skillset1))
		{
			$values["directreplace"]["emp_name"]=$skillset1[$kk]["username"];
			$values["directreplace"]["emp_id"]=$skillset1[$kk]["emp_id"];
			$replaced1.=$common->direct_replace($db_object,$replace1,$values);
		}
		if($replaced1=="")
		{
			$replaced1=$error_msg["cEmptyrecords"];
		}
		$xTemplate=preg_replace("/<{skill_act_start}>(.*?)<{skill_act_end}>/s",$replaced1,$xTemplate);
		
		
		
		$unapproved_tablename = $common->prefix_table("unapproved_tests");
		$user_tablename = $common->prefix_table("user");
		
		$mysql = "select $unapproved_tablename.user_id,$unapproved_tablename.status,$unapproved_tablename.test_name,$unapproved_tablename.test_id,$user_tablename.username from $unapproved_tablename,$user_tablename where $user_tablename.user_id=$unapproved_tablename.user_id and $unapproved_tablename.status='p'";
		
		$test_arr = $db_object->get_rsltset($mysql);
		
		//print_r($test_arr);
	
		preg_match("/<{testapprove_loopstart}>(.*?)<{testapprove_loopend}>/s",$xTemplate,$matched1);
		$replace2=$matched1[1];
	
		while(list($kk,$vv)=@each($test_arr))
		{
			$user_id = $test_arr[$kk]["user_id"];
			$values["directreplace"]["user_id"]	= $test_arr[$kk]["user_id"]; 
			//$values["directreplace"]["username"]=$test_arr[$kk]["username"];
			
			$username = $common->name_display($db_object,$user_id);
			$values["directreplace"]["username"] = $username;
			
			$values["directreplace"]["test_name"]=$test_arr[$kk]["test_name"];
			$values["directreplace"]["test_id"]=$test_arr[$kk]["test_id"];
			$values["directreplace"]["test_mode"]="approve";
			
			
			$replaced2 .= $common->direct_replace($db_object,$replace2,$values);
		}
		if($replaced2 =="")
		{
			$replaced2 =$error_msg["cEmptyrecords"];
		}
		
		$xTemplate=preg_replace("/<{testapprove_loopstart}>(.*?)<{testapprove_loopend}>/s",$replaced2,$xTemplate);
		
		
	$values=array();
	$xTemplate=$common->direct_replace($db_object,$xTemplate,$values);
				
			echo $xTemplate;
		
		
	}
	
   }
}
$altobj=new Alerts_for_approval;
if($user_id==1)
{
$altobj->display($common,$db_object,$user_id,$error_msg);
}
else
{
	echo $error_msg["cNoPermission"];
}

include("footer.php");
?>
