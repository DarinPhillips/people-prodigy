<?php
/*---------------------------------------------
SCRIPT:replace_rejectedappraisal.php
AUTHOR:info@chrisranjana.com	
UPDATED:30th Sept

DESCRIPTION:
This script displays alert for the 360.

---------------------------------------------*/
include("../session.php");
include_once("header.php");

class replaceRejected
{
	function replace_rater($db_object,$common,$post_var,$user_id)
	{
		while(list($kk,$vv)=@each($post_var))
		{

		$$kk=$vv;
	
		}
		
	
		$xPath=$common->path;
		$returncontent=$xPath."/templates/career/replace_rejectedappraisal.html";
		$returncontent=$common->return_file_content($db_object,$returncontent);
		
		$otherraters_table = $common->prefix_table('other_raters');
		$user_table = $common->prefix_table('user_table');
		


	$mysql = "select rater_userid from $otherraters_table where rater_userid = '$rejected_userid' and cur_userid = '$user_id'";

	$raterid_arr = $db_object->get_single_column($mysql);

		
		for($i=0;$i<1;$i++)
		{
			$raterid_rej = $raterid_arr[$i];
			
			$mysql = "select username,email from $user_table where user_id = '$raterid_rej'";
		
			$details_arr = $db_object->get_a_line($mysql);
			$username = $details_arr['username'];
			$email = $details_arr['email'];

			if($username == '')
			{
				$username	= $error_msg['cMultiraterappraisal_entername'];

			}
			if($email == '')
			{
				$email = $error_msg['cMultiraterappraisal_enteremail'];
			}
			
			$returncontent   = preg_replace("/<{(.*?)}>/e","$$1",$returncontent);
			
		}
		

		$values["rejected_userid"]  = $rejected_userid;
		$values["was_in_group"]	 = $was_in_group;	
		$values["mode"]		= "rejected";
		
		$returncontent = $common->direct_replace($db_object,$returncontent,$values);
		
		echo $returncontent;
		
	} //end of function replace_rater.
	
	function store_data($db_object,$common,$post_var,$user_id,$rejected_userid,$error_msg)
	{
		while(list($kk,$vv)=@each($post_var))
		{
		$$kk=$vv;
		
		if(ereg("^email_",$kk))
				{
				$e_qid=ereg_replace("email_","",$kk);	
				$email_array[$e_qid] = $vv;
				}
				
		
		}
		//echo "current user id is $user_id<br>";
		//print_r($post_var);exit;
		
		$user_table = $common->prefix_table("user_table");
		$otherraters_table = $common->prefix_table("other_raters");
		
		for($i=0;$i<count($email_array);$i++)
		{
			$email = $email_array[$i];
			
			$mysql = "select user_id from $user_table where email='$email'";
			$userid_arr = $db_object->get_a_line($mysql);
			$rater_userid = $userid_arr["user_id"];
			
//before inserting the data into table check if the user already exists...
			$mysql  = "select rater_email from $otherraters_table where status='a' and cur_userid = '$user_id' ";

			$check_arr = $db_object->get_single_column($mysql);

			for($x=0;$x<count($check_arr);$x++)
			{
				$check_email = $check_arr[$x];
				if($check_email == $email)
				{
					echo $error_msg["cReplacerejected_existingname"];
					return 1;
				}
			}
			
//insert into table
			$mysql = "update $otherraters_table set rater_email='$email',cur_userid = '$user_id',rater_userid='$rater_userid', status = 'a',date_rating_requested = now() where rater_id = '$rater_id_toreplace'";
			$db_object->insert($mysql);
		
			$rejected_userid = $rater_userid; 
			return $rejected_userid;
			
		}
		
		
		
		
		
	}  //end of function store_data
	
	//------------------
	function mail_ToRaters($db_object,$common,$post_var,$user_id,$error_msg)
	{
		while(list($kk,$vv)=@each($post_var))
		{
		$$kk=$vv;
		}
		

	$config=$common->prefix_table("config");
	$appraisal_table=$common->prefix_table("multirater_appraisal");
 	$otherraters_table = $common->prefix_table("other_raters");
	$user_table=$common->prefix_table("user_table");
	$boss_relate_table = $common->prefix_table("boss_relate");
	
	
	$mysql="select masubject,mamessage from $config";
	
	$rslt_arr=$db_object->get_a_line($mysql);

	$masubject=$rslt_arr["masubject"];
	$mamessage=$rslt_arr["mamessage"];

	
	$mysql = "select rater_email from $otherraters_table where rater_email='$email_0'";

	$mail_arr = $db_object->get_a_line($mysql);


		$to=$mail_arr["rater_email"];

		$mysql = "select username from $user_table where email='$to'";
		$arr = $db_object->get_a_line($mysql);
		$username = $arr["username"];
		
		$mysql="select email,username from $user_table where user_id='$user_id'";
	
		$sender_email=$db_object->get_a_line($mysql);
		$from=$sender_email["email"];
		$user = $sender_email["username"];
		
		
		
		$values["username"]=$username;
		$values["user"] = $user;
		$values["url"]=$common->http_path."/index.php";

		$message=$common->direct_replace($db_object,$mamessage,$values);

		//echo "to $to<br> sub $masubject<br> mess $message<br> from $from<br><br>";

		$sent=$common->send_mail($to,$masubject,$message,$from);


		if($sent)
		{
		
			echo $error_msg["cReplacerejected_done"];
		
		}
		else
		{
			echo $error_msg["cMultiraterAppraisalMail_fail"];
		}

		
		
		

	$returncontent = $common->direct_replace($db_object,$returncontent,$values);

	
	}  //end of function mail_ToRaters

	//------------------
	
	
}  //end of class
$obj = new replaceRejected;
//$post_var	= array_merge($_POST,$_GET);
	
if($fProceed)
{
	$temp = $check = $obj->store_data($db_object,$common,$post_var,$user_id,$rejected_userid,$error_msg);
	
	$post_var['rejected_userid'] = $temp;
	
	if($check != 1)
	{
	$obj->mail_ToRaters($db_object,$common,$post_var,$user_id,$error_msg);
	}
	
	$obj->replace_rater($db_object,$common,$post_var,$user_id);
}
else
{
$obj->replace_rater($db_object,$common,$post_var,$user_id);
}
 
include_once("footer.php");
?>
