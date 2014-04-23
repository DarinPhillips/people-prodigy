<?php
/*---------------------------------------------
SCRIPT: alert_ratings.php
AUTHOR:info@chrisranjana.com	
UPDATED:6th Oct

DESCRIPTION:
This script displays alert for rating other persons who have intimated them

---------------------------------------------*/
include("../session.php");


class technicalRatings
{
	function show_technical_ratings($db_object,$common,$post_var,$user_id)
	{
		while(list($kk,$vv)=@each($post_var))
		{
		$$kk=$vv;
		}
		
		$xPath=$common->path;
		$returncontent=$xPath."/templates/career/technical_ratings.html";
		$returncontent=$common->return_file_content($db_object,$returncontent);
		
		$user_table = $common->prefix_table('user_table');
		$mysql = "select username from $user_table where user_id = '$user_to_rate'";
		$name_arr = $db_object->get_a_line($mysql);
		$username = $name_arr['username'];
		
		$values['username'] = $username;
		$values['user_to_rate'] = $user_to_rate;
		
		$returncontent = $common->direct_replace($db_object,$returncontent,$values);
		echo $returncontent;
		
	}
	
	function change_status($db_object,$common,$post_var,$user_id,$default)
	{
		while(list($kk,$vv)=@each($post_var))
		{
		$$kk=$vv;
		}
		//print_r($post_var);
		
		$tech_references = $common->prefix_table('tech_references');
		$reject_table=$common->prefix_table("reject_rating");
		
		
		$mysql = "update $tech_references set status = 'r' , date_rating_requested = now() where user_to_rate = '$user_to_rate' and ref_userid = '$user_id'";
		$db_object->insert($mysql);
		$sql="insert into $reject_table set rater_id='$user_id',user_id='$user_to_rate',date_rejected=now()";
		$db_object->insert($sql);
		
		
		
	}
	
	
	
}
$obj = new technicalRatings;
//$post_var	= array_merge($_POST,$_GET);

//print_r($post_var);
if($fChoosetorate)
{
	if($fChoose == 'show')
	{
		header("Location:rate_others_tech.php?user_to_rate=$user_to_rate");
	}
	elseif($fChoose == 'reject')
	{
		include_once("header.php");
		
		$obj->change_status($db_object,$common,$post_var,$user_id,$default);
		
		$message = $error_msg["cTechrating_rejected"];
 		echo $message;
		
	}
}
	else
{
include_once("header.php");	
$obj->show_technical_ratings($db_object,$common,$post_var,$user_id);
}
include_once("footer.php");
?>
