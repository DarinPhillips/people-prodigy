<?php
/*---------------------------------------------
SCRIPT: q_sortratings.php
AUTHOR:info@chrisranjana.com	
UPDATED:30th Sept

DESCRIPTION:
This script displays q-sort evaluation.

---------------------------------------------*/
include("../session.php");


class qSortMethod
{ 
	function show_qsort_screen($db_object,$common,$post_var,$user_id)
	{
		while(list($kk,$vv)=@each($post_var))
		{
		$$kk=$vv;
		}
	
		$xPath=$common->path;
		$returncontent=$xPath."/templates/career/q_sortratings.html";
		$returncontent=$common->return_file_content($db_object,$returncontent);
	
		
		//print_r($post_var);
		
		$check_boss = $common->immediate_boss($db_object,$rated_user_id);

		
		if($check_boss == $rater_userid || $rater_userid == $rated_user_id)
		{
			$returncontent = preg_replace("/<{checkif_admin_start}>(.*?)<{checkif_admin_end}>/s","",$returncontent);
		}
		else
		{
			$returncontent = preg_replace("/<{checkif_admin_(.*?)}>/s","",$returncontent);
			
		}
		
		
		$values['rater_userid'] = $rater_userid;
		$values['rated_user_id']  = $rated_user_id;
		$returncontent = $common->direct_replace($db_object,$returncontent,$values);
	
		
		echo $returncontent;
		
	}  //end of function show_qsort_screen...
	
	function change_status($db_object,$common,$post_var,$rater_userid,$rated_user_id)
	{
		while(list($kk,$vv)=@each($post_var))
		{
		$$kk=$vv;
		}
		//print_r($post_var);
		
		$otherraters_table = $common->prefix_table("other_raters");
		
		$reject_rating=$common->prefix_table("reject_rating");
		
		$sql="insert into $reject_rating set user_id='$rated_user_id',rater_id='$rater_userid',
		
		date_rejected=now()";
		
		$db_object->insert($sql);
		
			
		$mysql = "update $otherraters_table set status='r' where rater_userid='$rater_userid' and cur_userid = '$rated_user_id'";
		
		$db_object->insert($mysql);
		

		
		
	}  //end of function mail_rater

}  //end of class

$obj = new qSortMethod;
//$post_var	= array_merge($_POST,$_GET);
//print_r($post_var);
if($qsort_method == 'graph')
{
	header("Location:graphical_qsort.php?rater_userid=$rater_userid&rated_user_id=$rated_user_id");
}
if($qsort_method == 'text')
{
 	header("Location:textonly_qsort.php?rater_userid=$rater_userid&rated_user_id=$rated_user_id");
}
/*if($qsort_method == 'none')
{
	include_once("header.php");
 	$message = $error_msg["cQsort_rejected"];
 	echo $message;
	
 	//send alert to the person (s) saying that this fellow has rejected the offer of rating...
 	
 	$obj->change_status($db_object,$common,$post_var,$rater_userid,$rated_user_id);
 	
}*/
if($qsort_method == 'reject')
{
	include_once("header.php");
 	$message = $error_msg["cQsort_rejected"];
 	echo $message;
	
 	//send alert to the person (s) saying that this fellow has rejected the offer of rating...
 	
 	$obj->change_status($db_object,$common,$post_var,$rater_userid,$rated_user_id);
 	
}
else
{
include_once("header.php");
$obj->show_qsort_screen($db_object,$common,$post_var,$user_id);
}
 
include_once("footer.php");
?>
