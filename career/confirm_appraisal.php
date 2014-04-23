<?php
/*---------------------------------------------
SCRIPT:confirm_appraisal.php
AUTHOR:info@chrisranjana.com	
UPDATED:17th Sept

DESCRIPTION:
This script displays all the questions entered for interpersonal skills.

---------------------------------------------*/
include("../session.php");
include("header.php");

class confirmAppraisal

{
	function show_appraisal($db_object,$common,$post_var,$gbl_skill_type,$default,$gbl_test_mode,$error_msg,$check)
	{
		while(list($kk,$vv)=@each($post_var))
		{

		$$kk=$vv;

		
	
		}
	
	   	//print_r($post_var);exit;
		$values['yesdisable'] = $yesdisable;
		
		$xPath		=$common->path;
		$xTemplate	=$xPath."/templates/career/confirm_appraisal.html";
		$returncontent	=$common->return_file_content($db_object,$xTemplate);
		
		$appraisal_table 	= $common->prefix_table('appraisal');
		$position_table 	= $common->prefix_table('position');
		$user_table 		= $common->prefix_table('user_table');
		$skills 		= $common->prefix_table('skills');
		$questions 		= $common->prefix_table('questions');
		
		preg_match("/<{skill_loopstart}>(.*?)<{skill_loopend}>/s",$returncontent,$smatch);
		$newskillmatch = $smatch[1];

		preg_match("/<{user_loopstart}>(.*?)<{user_loopend}>/s",$returncontent,$umatch);
		$newmatch = $umatch[1];

//===============
			//print_r($check);
		$pop = substr($check,0,-1);
		//echo $pop;
		$populate = @explode(",",$pop);
		
		//print_r($populate);
		$array_populate = @implode("','",$populate);

			$arr = "populated_array = Array ('$array_populate');\n";
			
			$returncontent=preg_replace("/<{pop_loopstart}>(.*?)<{pop_loopend}>/s",$arr,$returncontent);

//=================


//boss name is selected...
 		
		if($bossid != 0)
		
		{
					
	//first find position of boss
			$mysql = "select position from $user_table where user_id = '$bossid'";
			$posid_arr = $db_object->get_a_line($mysql);
			$positionid = $posid_arr["position"];
			
	//get the position id of the workers of the position "positionid"...	
		$mysql = "select pos_id from $position_table where boss_no = $positionid";
		$id_arr = $db_object->get_single_column($mysql);
		$id_full = @implode(",",$id_arr);
		
	//get the user id of the employees whose boss is given...	
	
		$mysql = "select user_id as emp_id from $user_table where position in($id_full)";
		$emp_arr = $db_object->get_single_column($mysql);
		
		$str="";

		$users_boss = @implode("','",$emp_arr);		

//Remove the users already selected under boss...
		
		$user_id_all = @array_diff($user_id_all,$emp_arr);
		
			for($i=0;$i<count($emp_arr);$i++)
				{
					
				$emp_id = $emp_arr[$i];
				$mysql = "select username,user_id from $user_table where user_id='$emp_id'";
				$user_arr = $db_object->get_a_line($mysql);
				
				$userid = $user_arr["user_id"];
				$username = $common->name_display($db_object,$userid);
							
				$str .= preg_replace("/<{(.*?)}>/e","$$1",$newmatch);	
				}
				
//the replace of the test type and test mode is done here
		


			while(list($kk,$vv) = @each($gbl_skill_type))
			{
		
			$$sel = "";
			
			$skill_name = $vv;
			
			$type = "test_mode_".$kk;
			
			$val = $$type;
			
			$testmode = "test_mode_".$kk;
			$testmodeval = $$testmode;

			
			
			$sel = "sel_".$testmodeval;
			$$sel = $gbl_test_mode[$testmodeval];         //test or 360 
			

			$raters = "raters_".$kk;
			$raterval  = $$raters;     //raters values

			
//DISPLAY OF THE SKILLS SELECTED FOR RATING...
			
			$s_arr = "skills_".$kk;
			$skills_arr = $$s_arr;

			$skills_all = @implode("','",$skills_arr);
			
			if($skills_arr[0] == "All")
			{
				$subclause = '';
			}
			else
			{
				$subclause = "and skill_id in ('$skills_all')";
			}
			
			$mysql = "select skill_id,skill_name from $skills where skill_type = '$kk' $subclause";
			$skills_display_arr = $db_object->get_rsltset($mysql);

			$value['skilldisplay_loop'] = $skills_display_arr;
			
			$newskillmatch1 = $common->multipleloop_replace($db_object,$newskillmatch,$value,'');


			
				if($testmodeval!='')
				{
					$str1 .= preg_replace("/<{(.*?)}>/e","$$1",$newskillmatch1);
				}
				else
				{
					$newskillmatch = preg_replace("/<{check_isnull_(.*?)}>/s","",$newskillmatch);
				}
			}

			@reset($gbl_skill_type);
		
		$returncontent = preg_replace("/<{skill_loopstart}>(.*?)<{skill_loopend}>/s",$str1,$returncontent);
			
		}
		
	
//User names are selected...


$new_array = @array_merge($emp_arr,$user_id_all);
$another_array = @array_unique($new_array);

$user_full = @implode(",",$user_id_all);

if($user_id_all[0] !=0)
		{
			
			
			for($i=0;$i<count($user_id_all);$i++)
			{
			$mysql = "select username,user_id from $user_table where user_id='$user_id_all[$i]'";
			
			$user_arr = $db_object->get_a_line($mysql);
		 
			$userid = $user_arr["user_id"];
			
			$username = $common->name_display($db_object,$userid);
			
			$str .= preg_replace("/<{(.*?)}>/e","$$1",$newmatch);	
			

			}

	
			@reset($gbl_skill_type);
		
			while(list($kk,$vv) = @each($gbl_skill_type))
			{
				$$kk = $vv;
				
				$$sel = "";
				
				$skill_name = $vv;
	   		
			 	$testmode = "test_mode_".$kk;
			 	//echo $testmode;	
			 	$testmodeval = $$testmode;
			 	//echo "testmodeval=$testmodeval";
	
			 	$sel = "sel_".$testmodeval;
			 	//echo $sel;

			 	$sel = "sel_".$testmodeval;
				
				$$sel = $gbl_test_mode[$testmodeval];          //test mode values

			 	$type = "test_mode_".$kk;
				$val = $$type;
			
				$raters = "raters_".$kk;
				$raterval  = $$raters;       //raters values

	//DISPLAY OF THE SKILLS SELECTED FOR RATING...
			
				$s_arr = "skills_".$kk;
				$skills_arr = $$s_arr;

				$skills_all = @implode("','",$skills_arr);
				
					if($skills_arr[0] == "All")
					{
						$subclause = '';
					}
					else
					{
						$subclause = "and skill_id in ('$skills_all')";
					}
			
			$mysql = "select skill_id,skill_name from $skills where skill_type = '$kk' $subclause";
			$skills_display_arr = $db_object->get_rsltset($mysql);
			$value['skilldisplay_loop'] = $skills_display_arr;
			
			$newskillmatch1 = $common->multipleloop_replace($db_object,$newskillmatch,$value,'');



			if($testmodeval!='')
			{
				$str2 .= preg_replace("/<{(.*?)}>/e","$$1",$newskillmatch1);
			}
			else
			{
				$newskillmatch = preg_replace("/<{check_isnull_(.*?)}>/s","",$newskillmatch);	
			}


			
		}
		$returncontent = preg_replace("/<{skill_loopstart}>(.*?)<{skill_loopend}>/s",$str2,$returncontent);
	
		
		}
		
		
		$returncontent = preg_replace("/<{user_loopstart}>(.*?)<{user_loopend}>/s",$str,$returncontent);
		
		$returncontent = $common->direct_replace($db_object,$returncontent,$values);
		echo $returncontent;
		
	}
	function email_admin($common,$db_object,$user_id,$post_var,$error_msg,$default)
  	{
  
//mail to users about the test they are allotted...

  	while(list($kk,$vv)=@each($post_var))
		{

		$$kk=$vv;
	
		}
	
		
	$empl_id = $user_id_all;

//codings for sending email to the system owner....
	
	$config=$common->prefix_table("config");
	$appraisal_table=$common->prefix_table("appraisal");
 	$user=$common->prefix_table("user_table");
	
	
	$mysql="select tasubject,tamessage from $config";
	$rslt_arr=$db_object->get_a_line($mysql);



	$tasubject=$rslt_arr["tasubject"];
	$tamessage=$rslt_arr["tamessage"];


	preg_match("/<{test_loopstart}>(.*?)<{test_loopend}>/s",$tamessage,$match);
	
	$newmatch = $match[1];
	$str = "";
	for($i=0;$i<count($empl_id);$i++)
	{
		$subqry2="select username,email from $user where user_id='$empl_id[$i]'";

		$user_name=$db_object->get_a_line($subqry2);
		//$to=$user_name["username"]."admin@Pms.com";
		$to=$user_name["email"];

		$subqry2="select email from $user where user_id='1'";

		$sys_email=$db_object->get_a_line($subqry2);
		$from=$sys_email["email"];
		
		$mysql = "select test_mode,test_type from $appraisal_table where user_id='$empl_id[$i]'";

		$testdetails_arr = $db_object->get_rsltset($mysql);

		for($j=0;$j<count($testdetails_arr);$j++)
		{
			$test_mode = $testdetails_arr[$j]["test_mode"];
			$test_type = $testdetails_arr[$j]["test_type"];
			
			
			
			if($test_mode == "Test")
			{
				$mess_test_mode = "Test Mode";
			}
			elseif($test_mode == "360")
			{
				$mess_test_mode = "360 Mode";
			}
			if($test_type == "t")
			{
				$mess_test_type = "Technical";
			}
			elseif($test_type == "i")
			{
				$mess_test_type = "Inter Personal";
			}
		
	$str .= preg_replace("/<{(.*?)}>/e","$$1",$newmatch);


		
		}



$tamessage = preg_replace("/<{test_loopstart}>(.*?)<{test_loopend}>/s",$str,$tamessage);


$values["username"]		=$user_name["username"];
$values["login_username"] 	= $user_name["username"];

$values["url"]=$common->http_path."/index.php";

$tamessage1=$common->direct_replace($db_object,$tamessage,$values);


//echo "to $to<br> sub $tasubject<br> mess $tamessage1<br> from $from<br><br>";

$sent=$common->send_mail($to,$tasubject,$tamessage1,$from);

	}
	if($sent)
		{
		
			echo $error_msg["cAppraisalMail_sent"];
		
		}
		else
		{
			echo $error_msg["cAppraisalMail_fail"];
		}

}

function add_appraisal($db_object,$common,$post_var,$gbl_skill_type,$default,$user_id)
	{
		while(list($kk,$vv)=@each($post_var))
		{

		$$kk=$vv;

		if(ereg("^skills_",$kk))
		{
			$id=ereg_replace("skills_","",$kk);
			
			$skills_sel_arr[$id] = $vv;
		}
		}
		
		

	$skills_for_rating 	= $common->prefix_table('skills_for_rating');
	$appraisal_table 	= $common->prefix_table("appraisal");

if($bossid != 0)
	{
	
	//first find position of boss
			$mysql = "select position from $user_table where user_id = '$bossid'";
	
			$posid_arr = $db_object->get_a_line($mysql);
			$positionid = $posid_arr["position"];
			
	//get the position id of the workers of the position "positionid"...	
		$mysql = "select pos_id from $position_table where boss_no = $positionid";
		$id_arr = $db_object->get_single_column($mysql);
	
		$id_full = @implode(",",$id_arr);
		
	//get the user id of the employees whose boss is given...	
		
	$mysql = "select user_id as emp_id from $user_table where position in($id_full)";
	$user_id_all = $db_object->get_single_column($mysql);
	
	}

//delete if there are any previous existence of the skills for that person...
	
	$mysql = "delete from $skills_for_rating where usr_id = '$app_user_id'";
	$db_object->insert($mysql);


//====================================================
	for($i=0;$i<count($user_id_all);$i++)
	{
		$app_user_id = $user_id_all[$i];

		@reset($skills_sel_arr);
		@reset($skill_all_arr);
		while(list($kk,$vv) = @each($skills_sel_arr))
		{
			$skill_all_arr = $vv;
			while(list($key1,$val1) = @each($skill_all_arr))
			{
				
				$mysql = "insert into $skills_for_rating set skill_id = '$val1' , usr_id = '$app_user_id' , skill_type = '$kk'";
				//echo "$mysql<br>";
				$db_object->insert($mysql);
			}
							
		}
	}
//====================================================
	
		  for($i=0;$i<count($user_id_all);$i++)
		  {
			$app_user_id = $user_id_all[$i];
			
//if his details are already present then delete it...
			
			$mysql = "delete from $appraisal_table where user_id='$app_user_id'";
			$db_object->insert($mysql);

		
	@reset($gbl_skill_type);

	while(list($kk,$vv) = @each($gbl_skill_type))
			{
		
				
			$test_modevar="test_mode_".$kk;
			
			$test_mode=$$test_modevar;
			
			$raters_modevar = "raters_".$kk;
			$raters = $$raters_modevar;

				if($test_mode != '')
				{
				$mysql = "insert into $appraisal_table set user_id='$app_user_id',test_mode='$test_mode',test_type='$kk',raters='$raters' ,date_assigned=now()";
				$db_object->insert($mysql);
				}
			}
			
		  }
		
	}

//mail to all the GROUPS selected in the MULTIRATER ASSESSMENT by admin
	
	function mail_allothers($db_object,$common,$post_var,$user_id,$error_msg)
	{
		while(list($kk,$vv)=@each($post_var))
		{

		$$kk=$vv;
	
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

	
		for($i=0;$i<count($group_arr);$i++)
		{
			$group_var = $group_arr[$i];
		
			$name=$group_var;	

			$$name=1;
		}

//  emails in OTHERS category will be sent in the multirater_appraisal.php

		for($x=0;$x<count($user_id_all);$x++)
		{
			
		$appraisal_userid = $user_id_all[$x];
	 
//OCT 11 MAILS SHOULD BE SENT FROM HERE TO BOSS , BOSS' BOSS , SELF ONLY

//boss id
		
		$boss_no = $common->immediate_boss($db_object,$appraisal_userid);
		

//boss' boss id 
		 
		
		$boss_boss_no = $common->immediate_boss($db_object,$boss_no);

	//check with the group names...
		$current_user_id = "";
		
		$current_user_id[] = $appraisal_userid;  //converting the current userid to array 

			if($grp_boss == 1)
			{
				if($boss_no != 0)
				{
				$array_users4 = $boss_no;
				}
				
		
		
			}
		 
	 		if($grp_topboss == 1)
			{
				if($boss_boss_no !=0)
				{
				$array_users7 = $boss_boss_no;
				}
				
			}
			 

		$array_users  = @array_merge($array_users4,$array_users7,$current_user_id);	
	
//all the persons who are supposed to recieve email is obtained...
	
		$array_users = @array_unique($array_users);


//codings to send mail...
		
		$mysql="select masubject,mamessage from $config";
	
		$rslt_arr=$db_object->get_a_line($mysql);

		

		$masubject=$rslt_arr["masubject"];
		$mamessage=$rslt_arr["mamessage"];

//sender email details...
		$mysql = "select username,email from $user_table where user_id = '$appraisal_userid'";

		$sender_arr = $db_object->get_a_line($mysql);
		$user = $sender_arr["username"];
		$from = $sender_arr["email"];

		
		$user_idstring=implode(",",$array_users);

		$mysql = "select email,username,user_id from $user_table 
		where user_id in ($user_idstring)";
						

		$email_arr1 = $db_object->get_rsltset($mysql);

		$email_arr=array();

		for($l=0;$l<count($email_arr1);$l++)
			{
				
				$id=$email_arr1[$l]["user_id"];
				$email_arr[$id]["user_id"] = $email_arr1[$l]["user_id"];
				$email_arr[$id]["email"]=$email_arr1[$l]["email"];
				$email_arr[$id]["username"]=$email_arr1[$l]["username"];
				
			}




		while (list($kk,$vv) = @each($array_users))
			{
			$useridToMail = $array_users[$kk];
			
			
				if($useridToMail != 0)
				{

			
				$email = $email_arr[$useridToMail]["email"];

				$username = $email_arr[$useridToMail]["username"];	
				$rater_user_id = $email_arr[$useridToMail]["user_id"];

				$to = $email;
				
				$values["username"]	=$username;
				$values["user"] 	= $user;
				$values["login_username"] = $username;
				
				
				$values["url"]=$common->http_path."/index.php";

				$message=$common->direct_replace($db_object,$mamessage,$values);

				//echo "to $to<br> sub $masubject<br> mess $message<br> from $from<br><br>";

				$sent=$common->send_mail($to,$masubject,$message,$from);
 
//store the data of the persons to whom the mail has been sent
		
				$rater_id = $rater_user_id;
				$user_to_be_rated = $appraisal_userid;
				$rater_email = $email;
		
//insert the data regarding the raters into the table...
				
//if the user is the rater, it means that he belong to the self category...			
				
				if($user_to_be_rated == $rater_id)
				{
				$group_belongingto = 'grp_self';
				}
				elseif($rater_id == $boss_boss_no)
				{
				$group_belongingto = 'grp_topboss';	
				}
				else
				{
				$group_belongingto = 'grp_boss';	
				}
				
				
				$mysql = "insert into $otherraters_table set rater_userid = '$rater_id' , rater_email = '$rater_email' ,cur_userid = '$user_to_be_rated' , group_belonging = '$group_belongingto' ,date_rating_requested=now()";
				
				$db_object->insert($mysql);
		
				//user_to_be_rated = "";  //from
				//rater_id = "";          //to
		
				}
		

			}
		
	
		
	}
		if($sent)
		{
		
			echo $error_msg["cMultiraterAppraisalMail_sent"];
		
		}
		else
		{
			echo $error_msg["cMultiraterAppraisalMail_fail"];
		}
		
	
}	//end of function


//FUNCTION TO CHECK IF THE USERS SELECTED HAVE ALREADY TAKEN THE TESTS OR NOT...

function check_if_testtaken($db_object,$common,$post_var,$gbl_skill_type,$default,$user_id,$error_msg)
{
	
	while(list($kk,$vv) = @each($post_var))
	{
		$$kk = $vv;

		if(ereg("^test_mode_",$kk))
			{
			$qid=ereg_replace("test_mode_","",$kk);
			$testmode_array[$qid] = $vv;
			}

		if(ereg("^skills_",$kk))
			{
			$sid=ereg_replace("skills_","",$kk);
			$skills_sel_array["$sid"] = $vv;
			}
		
	}
	
	$skills_for_rating = $common->prefix_table('skills_for_rating');
	$skills = $common->prefix_table('skills');

	$count = 0;

		
//IF BOSS IS SELECTED THEN SELECT THE USERS UNDER HIM...
	
	if($bossid != 0)
	{
		$users_under_boss = $common->return_direct_reports($db_object,$bossid);

		$user_id_all_old = @array_merge($users_under_boss,$users_under_boss);
		$user_id_all_old = @array_unique($user_id_all_old);

		while(list($kk,$vv) = @each($user_id_all_old))	
			{
				$user_id_all_boss[] = $user_id_all_old[$kk];
			}
	
	$user_id_all_junk = @array_merge($user_id_all,$user_id_all_boss);

	$user_id_all_unordered = @array_unique($user_id_all_junk);


	while(list($key,$val) = @each($user_id_all_unordered))
		{

			$user_id_all_new[] = $val;//$user_id_all_unordered[$key];
		}	
	
	}
	else
	{
		$user_id_all_new = $user_id_all;
	}
		
	
	//print_r($user_id_all_new);exit;


	for($i=0;$i<count($user_id_all_new);$i++)
	{
		@reset($gbl_skill_type);
		while(list($kk,$vv) = @each($gbl_skill_type))
		{

			$user_id_sel = $user_id_all_new[$i];
			
			$mysql = "select distinct(skill_id) from $skills_for_rating where skill_type = '$kk' and usr_id = '$user_id_sel'";
			$skills_already_used_arr = $db_object->get_single_column($mysql);

			$skills_selected = $skills_sel_array[$kk];

			$skills_all = @implode("','",$skills_selected);

			$mysql = "select distinct(skill_id) from $skills_for_rating where skill_id in ('$skills_all') and usr_id = '$user_id_sel'";
			$skills_present_arr = $db_object->get_single_column($mysql);

			$the_skills_present = @implode("','",$skills_present_arr);

			$name = $common->name_display($db_object,$user_id_sel);
			
			
			$mysql = "select skill_name from $skills where skill_id in ('$the_skills_present')";
			$skillnames_arr = $db_object->get_single_column($mysql);

			$skillnames = @implode(",",$skillnames_arr);
			
			$skillnames_arr1 .= "$skillnames".","; 

			//$array_populate = @implode("','",$skillnames_arr);

			//$arr = "populated_array = Array ('$array_populate');\n";
			
			//$returncontent=preg_replace("/<{loopstart}>(.*?)<{loopend}>/s",$arr,$returncontent);

				if($skills_present_arr != '')
				{
					$message1 = $error_msg['cThe_following'];
					$message2 = $vv;
					$message3 = $error_msg['cSkillsof'];
					$message4 = $name;
					$message5 = $error_msg['cArealreadyassigned'];
					$message6 = $skillnames;
					$message7 = $error_msg['cPleasedeselct_proceed'];
					
					//echo "The following $vv skills of <b>$name</b> are already assigned<br>
					//<b>$skillnames</b><br>Please deselect them and proceed<br><br>";
					$full_message = $message1.$message2.$message3.$message4.$message5.$message6.$message7;
					echo $full_message;
					
					$count++;
				}
			
		}

		if($count == 0)
		{
			return 1;
		}
		else
		{
			return $skillnames_arr1;
		}
		
	}
	
	 
	
}

}	//end of class

$obj = new confirmAppraisal;


if($fYes)
{
	while(list($kk,$vv)=@each($post_var))
	{
	$$kk=$vv;
	}

	//$check = $obj->check_if_testtaken($db_object,$common,$post_var,$gbl_skill_type,$default,$user_id,$error_msg);


	 
	
	$obj->add_appraisal($db_object,$common,$post_var,$gbl_skill_type,$default,$user_id);
		
//email to all the persons about the test details
	$obj->email_admin($common,$db_object,$user_id,$post_var,$error_msg,$default);
	
//email to all the groups selected in the multirater assessment / 360 of interpersonal set by admin

	if($test_mode_i == 360 )
		{
		$obj->mail_allothers($db_object,$common,$post_var,$user_id,$error_msg);
		}
	 
	
}
	
else if($fSubmit)
{
	
	$check = $obj->check_if_testtaken($db_object,$common,$post_var,$gbl_skill_type,$default,$user_id,$error_msg);
//=========
	

	if($check != 1)
	{
	
	$yesdisable = "disabled";
	$values['yesdisable'] = $yesdisable;
	
	$post_var['yesdisable'] = $yesdisable;
	}
	else
	{
		$post_var['yesdisable'] = '';
	}

//===========
 
 
	$obj->show_appraisal($db_object,$common,$post_var,$gbl_skill_type,$default,$gbl_test_mode,$error_msg,$check);
			
}
else
{
	
	$obj->show_appraisal($db_object,$common,$post_var,$gbl_skill_type,$default,$gbl_test_mode,$error_msg,$check);
}
	
include_once("footer.php");
?>
