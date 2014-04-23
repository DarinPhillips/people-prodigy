<?php
/*---------------------------------------------
SCRIPT: multirater_appraisal.php
AUTHOR:info@chrisranjana.com	
UPDATED:30th Sept

DESCRIPTION:
This script displays alert for the 360.

---------------------------------------------*/
include("../session.php");
include_once("header.php");

class multiraterAppraisal
{ 
	function show_appraisal_screen($db_object,$common,$post_var,$gbl_test_mode,$gbl_skill_type,$default,$user_id,$error_msg)
	{
		$user_table=$common->prefix_table("user_table");
		
		$sql="select user_type from $user_table where user_id='$user_id'";
		
		$sql_res=$db_object->get_a_line($sql);
		
		if($sql_res[user_type]=="external")
		{
			
		$xPath=$common->path;
		$returncontent=$xPath."/templates/career/ext_multirater_appraisal.html";
		$returncontent=$common->return_file_content($db_object,$returncontent);
		$sql="select username,email from $user_table where user_id='1'";
		$xArray=$db_object->get_a_line($sql);
		$xArray[grpname]="grp_other";
		$xArray[no_of_persons_rating]=1;
		$returncontent=$common->direct_replace($db_object,$returncontent,$xArray);
		echo $returncontent;
		include_once("footer.php");exit;
		}
		while(list($kk,$vv)=@each($post_var))
		{

		$$kk=$vv;
	
		}
		//making the groupname available after form submit....
		$values["grpname"] = $grpname;
		
		$xPath=$common->path;
		$returncontent=$xPath."/templates/career/multirater_appraisal.html";
		$returncontent=$common->return_file_content($db_object,$returncontent);
		
		
		preg_match("/<{teammates_loopstart}>(.*?)<{teammates_loopend}>/s",$returncontent,$matched);
		$replace=$matched[1];
		
		$appraisal_table = $common->prefix_table("appraisal");
		$otherraters_table = $common->prefix_table("other_raters");
		$user_table =  $common->prefix_table("user_table");
		
		//take the no of raters from the table where interpersonal userid 360
		//echo "present userid is $user_id<br>";
		
		$mysql = "select raters from $appraisal_table where user_id='$user_id' and test_mode='360' and test_type='i'";
		$members_arr = $db_object->get_a_line($mysql);
		//print_r($members_arr);exit;
		
		$raters = $members_arr["raters"];
		//echo "rater no is $raters";
		$values['no_of_persons_rating'] = $raters;
		


		$mysql = "select rater_userid from $otherraters_table where group_belonging='$grpname' and cur_userid = '$user_id'";
		//echo $mysql;
		$existing_arr = $db_object->get_single_column($mysql);
		//print_r($existing_arr);
		
		//$raters = 3;
			
		for($i=0;$i<$raters;$i++)
		{
			$existing_userid = $existing_arr[$i];
			$mysql = "select username,email from $user_table where user_id = '$existing_userid'";
			//echo $mysql;
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
			$replaced  .= preg_replace("/<{(.*?)}>/e","$$1",$replace);
			
		}
		
		
		$returncontent=preg_replace("/<{teammates_loopstart}>(.*?)<{teammates_loopend}>/s",$replaced,$returncontent);
		
		$returncontent = $common->direct_replace($db_object,$returncontent,$values);
	
		echo $returncontent;	
		
	}  //end of function show_appraisal_screen
	
	function validate_NameandMail($db_object,$common,$post_var,$error_msg)
	{
		while(list($kk,$vv)=@each($post_var))
		{
		$$kk=$vv;
		
		if(ereg("^teammates_",$kk))
				{
				$t_qid=ereg_replace("teammates_","",$kk);
				$team_array[$t_qid] = $vv;
				}


		if(ereg("^email_",$kk))
				{
					if($vv != $error_msg[cMultiraterappraisal_enteremail])
					{
					$e_qid=ereg_replace("email_","",$kk);	
					$email_array[$e_qid] = $vv;
					}
				}


		}
		$user_table = $common->prefix_table("user_table");
			//print_r($team_array);
		
			//$message = "";
		
			for($i=0;$i<count($team_array);$i++)
			{
				$team_name = $team_array[$i];
				$email     = $email_array[$i];
				
				//the name is checked by email since email has the probablity of being accurate than name...
				
				$mysql = "select username,email,user_id from $user_table where email = '$email'";
				//echo $mysql;
				$name_arr = $db_object->get_a_line($mysql);
				//print_r($name_arr);
				$dtbase_name = $name_arr["username"];
				$dtbase_email = $name_arr["email"];
				
				//echo "$team_name and $dtbase_name<br>";
				
				if($team_name != $dtbase_name)
				{
					$message .= $error_msg["cMultiraterappraisal_wrongname"];
							
				}
				if($email !=$dtbase_email)
				{
					$message .=$error_msg["cMultiraterappraisal_wrongemail"];
				}
			
				return $message;
				
	
			}
		
		
	}  //end of function validate_NameandMail...
	
	function store_data($db_object,$common,$post_var,$user_id,$error_msg)
	{

		while(list($kk,$vv)=@each($post_var))
		{
		$$kk=$vv;
		
		if(ereg("^email_",$kk))
				{
				if($vv != $error_msg[cMultiraterappraisal_enteremail])
					{	
					$e_qid=ereg_replace("email_","",$kk);	
					$email_array[$e_qid] = $vv;
					}
				}
				
		
		}
		
		
		$user_table = $common->prefix_table("user_table");
		$otherraters_table = $common->prefix_table("other_raters");
		
		$mysql = "select rater_email from $otherraters_table where cur_userid='$user_id'";

		$existing_arr = $db_object->get_single_column($mysql);
		
		for($i=0;$i<count($existing_arr);$i++)
		{
		$existing_email = $existing_arr[$i];
			for($j=0;$j<count($email_array);$j++)
			{
				$new_email = $email_array[$j];
				if($new_email == $existing_email)
				{
					echo $error_msg["cMultiraterappraisalNamepresent"];
					return 1;
				}
			}
		}
		
		$mysql = "delete from $otherraters_table where cur_userid = '$user_id' and group_belonging = '$grpname'";
//		echo "sql=$mysql<br>";exit;
		$db_object->insert($mysql);
		
		for($i=0;$i<count($email_array);$i++)
		{
			$email = $email_array[$i];
			
			$mysql = "select user_id from $user_table where email='$email'";
			$userid_arr = $db_object->get_a_line($mysql);
			$rater_userid = $userid_arr["user_id"];
			
			$mysql = "insert into $otherraters_table set rater_email='$email',cur_userid = '$user_id',rater_userid='$rater_userid' , group_belonging = '$grpname',date_rating_requested = now()";

			$db_object->insert($mysql);
			
		}
		
		
		
		
		
	}  //end of function store_data
	
	function mail_ToRaters($db_object,$common,$post_var,$user_id,$error_msg)
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
		

	$config=$common->prefix_table("config");
	$appraisal_table=$common->prefix_table("multirater_appraisal");
 	$otherraters_table = $common->prefix_table("other_raters");
	$user_table=$common->prefix_table("user_table");
	$ratergroup_table = $common->prefix_table("rater_group");
	$position_table = $common->prefix_table("position");

	
//groups to be sent mail...
	
	$mysql = "select rater_group_name from $ratergroup_table";
	$group_arr = $db_object->get_single_column($mysql);
	//print_r($group_arr);
	
	for($i=0;$i<count($group_arr);$i++)
	{
		$group_var = $group_arr[$i];
		
		$name=$group_var;

		$$name=1;
	}
	 
	
	$email = @implode("','",$email_array);
 
	
	//emails of those selected in the OTHERS category
	
	
	$mysql = "select user_id from $user_table where email in ('$email')";
 
	$userid_arr = $db_object->get_single_column($mysql);
	
	
//codings to send mail...
	$mysql="select masubject,mamessage from $config";
	
	$rslt_arr=$db_object->get_a_line($mysql);

	$masubject=$rslt_arr["masubject"];
	$mamessage=$rslt_arr["mamessage"];

//sender email details...
	$mysql = "select username,email from $user_table where user_id = '$user_id'";

	$sender_arr = $db_object->get_a_line($mysql);
	$user = $sender_arr["username"]; 
	$from = $sender_arr["email"]; 
	
	while (list($kk,$vv) = @each($userid_arr))
	{
		$useridToMail = $userid_arr[$kk];
		
		if($useridToMail != 0)
		{
			
		$mysql = "select email,username,password from $user_table where user_id = '$useridToMail'";

		$email_arr = $db_object->get_a_line($mysql);
		$email = $email_arr["email"];
		$username = $email_arr["username"];	
		$login_password = $email_arr['password'];

		$to = $email;
		$values["username"]=$username;
		
		$values['login_username'] = $username;
		$values['login_password'] = $login_password;
		
		$values["user"] = $user;
		$values["url"]=$common->http_path."/index.php";

		$message=$common->direct_replace($db_object,$mamessage,$values);

		$sent=$common->send_mail($to,$masubject,$message,$from);

		
		}
		
		if($sent)
		{
		
			echo $error_msg["cMultiraterAppraisalMail_sent"];
		
		}
		else
		{
			echo $error_msg["cMultiraterAppraisalMail_fail"];
		}
		
	}



}  //end of function mail_ToRaters
	
	
}  //end of class multiraterAppraisal

$obj = new multiraterAppraisal;
//$post_var	= array_merge($_POST,$_GET);
	
if($fProceed)
{
	$message = $obj->validate_NameandMail($db_object,$common,$post_var,$error_msg);
	

	if($message != "")
	{
		
		echo $message;
		$obj->show_appraisal_screen($db_object,$common,$post_var,$gbl_test_mode,$gbl_skill_type,$default,$user_id,$error_msg);	
		
		
	}
	else
	{
		$check = $obj->store_data($db_object,$common,$post_var,$user_id,$error_msg);
		if($check !=1)
		{
		$obj->mail_ToRaters($db_object,$common,$post_var,$user_id,$error_msg);
		
		}
		else
		{
			$obj->show_appraisal_screen($db_object,$common,$post_var,$gbl_test_mode,$gbl_skill_type,$default,$user_id,$error_msg);	
		}
	
	}
}
else
{
	
$obj->show_appraisal_screen($db_object,$common,$post_var,$gbl_test_mode,$gbl_skill_type,$default,$user_id,$error_msg);
}
include_once("footer.php");
?>
