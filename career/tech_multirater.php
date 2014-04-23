<?php
/*---------------------------------------------
SCRIPT: tech_multirater.php
AUTHOR:info@chrisranjana.com	
UPDATED:6th Oct

DESCRIPTION:
This script displays alert for technical ratings...

---------------------------------------------*/
include("../session.php");


class techRatings
{
	function show_Appraisal($db_object,$common,$post_var,$user_id,$default)
	{
		while(list($kk,$vv)=@each($post_var))
		{
		$$kk=$vv;
		}
		$user_table=$common->prefix_table("user_table");
		$sql="select user_type from $user_table where user_id='$user_id'";
		
		$sql_res=$db_object->get_a_line($sql);
		if($sql_res[user_type]!="external")
		{
		$xPath=$common->path;
		$returncontent=$xPath."/templates/career/tech_multirater.html";
		$returncontent=$common->return_file_content($db_object,$returncontent);
		}
		else
		{
		$xPath=$common->path;
		$returncontent=$xPath."/templates/career/ext_tech_multirater.html";
		$returncontent=$common->return_file_content($db_object,$returncontent);
		}
		
		$skills_table = $common->prefix_table('skills');
		$skillraters_table = $common->prefix_table('skill_raters');
		$appraisal_table = $common->prefix_table('appraisal');
		$temp_tech_rating    = $common->prefix_table('temp_tech_rating');
		$temp_tech_references = $common->prefix_table('temp_tech_references');
		$skills_for_rating = $common->prefix_table('skills_for_rating');
		$rater_label_relate = $common->prefix_table('rater_label_relate');

		
		$mysql = "select skill_id from $temp_tech_rating where rating_user = '$user_id'";
		$skills_rated_arr = $db_object->get_single_column($mysql);
		$skills_rated = @implode(",",$skills_rated_arr);
		
		if($skills_rated != '')
		{
		$subclause = "and skill_id not in($skills_rated)";
		}
		
	
		$displaycontent = '';

//display of skills...............................		
	
		
//display the skills that are yet to be rated only...
		preg_match("/<{skillsperpage_loopstart}>(.*?)<{skillsperpage_loopend}>/s",$returncontent,$displaymatch);
		$newdisplaymatch = $displaymatch[1];
		
		
		//$mysql = "select skill_id,skill_name from $skills_table where skill_type = 't' $subclause"; //  limit 0,20,$subclause
		$mysql = "select $skills_table.skill_id , $skills_table.skill_name 
				from $skills_for_rating,$skills_table 
				where $skills_table.skill_id = $skills_for_rating.skill_id
				and $skills_for_rating.usr_id = '$user_id'
				and $skills_for_rating.skill_type = 't'";

		$skills_arr = $db_object->get_rsltset($mysql);
		//print_r($skills_arr);
		$mysql = "select count(*) as skill_count from $skills_for_rating where usr_id = '$user_id' and $skills_for_rating.skill_type='t'";
		$count_arr = $db_object->get_a_line($mysql);
		$count_skills = $count_arr['skill_count'];
		
		$values['count_skills'] = $count_skills;
	
		$skills_arr1	= $common->conv_2Darray($db_object,$skills_arr);
		
		$mysql = "select skill_id from $temp_tech_rating where rating_user = '$user_id'";
		$rating_skill_arr = $db_object->get_single_column($mysql);
		
		
		
		
		$displaycontent = $common->pulldown_replace($db_object,'<{skill_loopstart}>','<{skill_loopend}>',$newdisplaymatch,$skills_arr1,'');
	
		
//display of labels................................		

		$mysql = "select rater_id,rater_level_$default from $skillraters_table where skill_type = 't'";
		$label_arr = $db_object->get_rsltset($mysql);
		//print_r($label_arr);

		for($i=0;$i<count($label_arr);$i++)
		{
			$rater_id = $label_arr[$i]['rater_id'];
			
			$mysql = "select rater_labelno as rater_id from $rater_label_relate where rater_id = '$rater_id'";
			$labelno_arr  = $db_object->get_a_line($mysql);
			$labelno = $labelno_arr['rater_id'];
			
			$label_arr[$i]['rater_id'] = $labelno;
			$label_arr[$i][0] = $labelno;
		}
		
		//print_r($label_arr);
		$label_arr1	= $common->conv_2Darray($db_object,$label_arr);
		//print_r($label_arr1);
		$displaycontent = $common->pulldown_replace($db_object,'<{label_loopstart}>','<{label_loopend}>',$displaycontent,$label_arr1,'');

	
		$mysql = "select raters from $appraisal_table where user_id = '$user_id' and test_type = 't'";

		$raters_arr = $db_object->get_a_line($mysql);
	
		$raters = $raters_arr['raters'];

//display of company directory which is based on the no of raters specified by admin ...
	
		preg_match("/<{reference_loopstart}>(.*?)<{reference_loopend}>/s",$displaycontent,$refmatch);
		$newrefmatch = $refmatch[1];
			$sql="select user_type from $user_table where user_id='$user_id'";
		
		$sql_res=$db_object->get_a_line($sql);
		$sql="select email from $user_table where user_id='1'";
		$result=$db_object->get_a_line($sql);
		if($sql_res[user_type]=="external")
		{
			$raters=1;
		}
	
		for($i=0;$i<$raters;$i++)
		{

			$ref_no = $i+1;
			
			$email=$result[email];
			
			$str .= preg_replace("/<{(.*?)}>/e","$$1",$newrefmatch);	
		}
	
		$displaycontent = preg_replace("/<{reference_loopstart}>(.*?)<{reference_loopend}>/s",$str,$displaycontent);
	

		
	if($fAdd_skill == '')
	{
				
		if($count_skills >=20)
		{
		$page_count = 20;
		}
		else
		{
		$page_count = $count_skills;
		}
		
		$total_count = $page_count;
		
		
		
	}
	else
	{
		
		
		if($page_count >= 20)
		{
			$page_count = $count_skills - $page_count;
			
		
		}
		
		$total_count = $total_count + $page_count;
		
	}
	
	if($total_count == $count_skills)
	{
		$disable_add = 'disabled';
		
	}
	
	
	
		for($l=0;$l<$page_count;$l++)
		{
			$no = $l+1;
			$displaycontent1 .= preg_replace("/~{(.*?)}~/e","$$1",$displaycontent);
			
		}
		
		$returncontent = preg_replace("/<{skillsperpage_loopstart}>(.*?)<{skillsperpage_loopend}>/s",$displaycontent1,$returncontent);
	
		$values['page_count']  = $page_count;
		$values['total_count'] = $total_count;	
		$values['disable_add'] = $disable_add;
		$values['email_count'] = $raters;
		
		$returncontent = $common->direct_replace($db_object,$returncontent,$values);

///////////////////////		
		
		
		
	 	echo $returncontent;
	 	
	}	//end of function show_Appraisal()
	
	function add_moreskills($db_object,$common,$post_var,$user_id,$default,$error_msg)
	{
		while(list($kk,$vv)=@each($post_var))
		{
		$$kk=$vv;
		
		if(ereg("^tech_skills_",$kk))
		{
			if($vv != 0)
			{
				$tech_skillid = ereg_replace("tech_skills_","",$kk);
	
				$tech_skills_arr[$tech_skillid] = $vv; 
			}
		}
		if(ereg("^label_id_",$kk))
		{
			if($vv != 0)
			{
				$id = ereg_replace("label_id_","",$kk);
				$label_id_arr[$id] = $vv;
			}
		}
			if(ereg("^email_",$kk))
				{
					
					if($vv != $error_msg['cTechMul_enteremail'])
					{
					
					$email_id=ereg_replace("email_","",$kk);
					list($qid,$aid)=split("_",$email_id);
					
					$email_array[$aid][$qid] = $vv;
					}
				}
		
		}

	
		$temp_tech_rating    = $common->prefix_table('temp_tech_rating');
		$temp_tech_references = $common->prefix_table('temp_tech_references');
		$user_table 	    = $common->prefix_table('user_table');	
		
		$mysql = "select skill_id from $temp_tech_rating where rating_user = '$user_id'";
	
		$skills_present_arr = $db_object->get_single_column($mysql);

	
		while (list($kk,$vv) = @each($tech_skills_arr))
		{
			
			$tech_skills = $tech_skills_arr[$kk];
			
			$lab_id = $label_id_arr[$kk];
			
			
			$email_all = @implode("','",$email_array[$kk]);
			$email_sub = $email_array[$kk];
			
				
			$mysql = "insert into $temp_tech_rating set skill_id = '$tech_skills' , selfrating_labelid = '$lab_id' , rating_user = '$user_id'";
			$dataid = $db_object->insert_data_id($mysql);
			
		
			//for($j=1;$j<=count($email_sub);$j++)
			//{	
			$mysql = "select user_id from $user_table where email in ('$email_all')";

			$email_userid_arr = $db_object->get_single_column($mysql); 
	
			
			
			for($k=0;$k<count($email_userid_arr);$k++)
			{
			$ref_userid = $email_userid_arr[$k];
			
			$mysql = "insert into $temp_tech_references set temp_ratingid = '$dataid' , ref_userid ='$ref_userid' , user_to_rate = '$user_id'";
	
			$db_object->insert($mysql);
			
			}
			//}
			
			
		}
		
	
	
	
	}	//end of function add_moreskills()

function store_to_maintable($db_object,$common,$post_var,$user_id,$default,$error_msg)
{
	while(list($kk,$vv)=@each($post_var))
		{
		$$kk=$vv;
		}
		
		$tech_rating = $common->prefix_table('tech_rating');
		$tech_references = $common->prefix_table('tech_references');
	
		$temp_tech_rating = $common->prefix_table('temp_tech_rating');
		$temp_tech_references = $common->prefix_table('temp_tech_references');
		
		
//move data into master table....
		
		
		$fields = $common->return_fields($db_object,$temp_tech_rating);

		$fields1 = $common->return_fields($db_object,$temp_tech_references);
		
		$mysql = "select $fields from $temp_tech_rating where rating_user = '$user_id'";
		$temp_arr = $db_object->get_rsltset($mysql);
		
			
		for($i=0;$i<count($temp_arr);$i++)
		{
			$temp_ratingid 	  = $temp_arr[$i]['temp_ratingid'];
			$skill_id 	   	  = $temp_arr[$i]['skill_id'];
			$selfrating_labelid = $temp_arr[$i]['selfrating_labelid'];
			$rating_user	  = $temp_arr[$i]['rating_user'];
			
			$mysql = "insert into $tech_rating set ratingid = '',skill_id = '$skill_id',selfrating_labelid = '$selfrating_labelid',rating_user = '$rating_user'";
			$dataid = $db_object->insert_data_id($mysql);
			
			
			$mysql = "select $fields1 from $temp_tech_references where temp_ratingid = '$temp_ratingid'";
			$fields_arr = $db_object->get_rsltset($mysql);
			
			
			
			for($j=0;$j<count($fields_arr);$j++)
			{
				$ref_userid   = $fields_arr[$j]['ref_userid'];
				$user_to_rate = $fields_arr[$j]['user_to_rate'];
				
				$mysql = "insert into $tech_references set ratingid = '$dataid' , ref_userid = '$ref_userid' , user_to_rate = '$user_to_rate',date_rating_requested = now()";
				$db_object->insert($mysql);
			}
			
		//after moving the data to the main table, delete the info in the temp table...
			
		$mysql = "delete from $temp_tech_references where temp_ratingid = '$temp_ratingid'";
		$db_object->insert($mysql);
			
	
			
			
		}
//Boss should also be included in the rating process...
//==============================
//boss id
	$user_table 	= $common->prefix_table('user_table');
	$position_table 	= $common->prefix_table('position');
	
	/*
	
		//the position of the user is found out...
				
		$mysql = "select position from $user_table where user_id = '$user_id'";
		$user_position_arr = $db_object->get_a_line($mysql);
		$position_user = $user_position_arr["position"];
		
		$mysql = "select boss_no from $position_table where pos_id='$position_user'";
		$boss_position_arr = $db_object->get_a_line($mysql);
		$boss_position = $boss_position_arr["boss_no"];
		
		
		
		$mysql = "select user_id from $user_table where position = '$boss_position'";
		$boss_arr = $db_object->get_a_line($mysql);
		
		
	*/
	
	$boss_no = $common->immediate_boss($db_object,$user_id);
	
		//check if the employee doesnt have a boss...
			
	
			if($boss_no != 0)
			{
			//$boss_no = $boss_arr["user_id"];
			
			
			$mysql = "select ratingid from $tech_rating where rating_user = '$user_id'";
			
			$ratingid_arr = $db_object->get_single_column($mysql);
			
				for($i=0;$i<count($ratingid_arr);$i++)
				{
				$ratingid = $ratingid_arr[$i];
				$mysql = "insert into $tech_references set ratingid = '$ratingid' , ref_userid = '$boss_no' , user_to_rate = '$user_to_rate'";
				
				$db_object->insert($mysql);
				}
			
			
			
			}
			



//==============================			
			
		
		
		
		
//after moving the data to the main table, delete the info in the temp table...
		
		$mysql = "delete from $temp_tech_rating where rating_user = '$user_id'";
		$db_object->insert($mysql);
		
		
	
}  //end of function store_to_maintable()...


function send_mails($db_object,$common,$post_var,$user_id,$default,$error_msg)
{
	while(list($kk,$vv)=@each($post_var))
		{
		$$kk=$vv;
		}
		
		
		
		$config=$common->prefix_table("config");
		$user_table=$common->prefix_table("user_table");
		
		$tech_rating = $common->prefix_table('tech_rating');
		$tech_references = $common->prefix_table('tech_references');
	
		
//persons to be sent mail...		
		
		$mysql = "select distinct(ref_userid) from $tech_references where user_to_rate = '$user_id'";
		//echo $mysql;		
		$users_tomail_arr = $db_object->get_single_column($mysql);
		
		
		
//Boss should also be sent an email...
//=====================
//boss id
	$user_table 	= $common->prefix_table('user_table');
	$position_table 	= $common->prefix_table('position');
	
		//the position of the user is found out...
				
		$mysql = "select position from $user_table where user_id = '$user_id'";
		//echo $mysql;exit;
		$user_position_arr = $db_object->get_a_line($mysql);
		$position_user = $user_position_arr["position"];
		//echo $position_user;exit;
		
		//now find who's the boss of the user...
		$mysql = "select boss_no from $position_table where pos_id='$position_user'";
		$boss_position_arr = $db_object->get_a_line($mysql);
		//print_r($boss_position_arr);exit;
		$boss_position = $boss_position_arr["boss_no"];
		//echo $boss_position;exit;
		
		//now find the id of the boss...
		$mysql = "select user_id from $user_table where position = '$boss_position'";
		$boss_arr = $db_object->get_single_column($mysql);
		//print_r($boss_arr); exit;
		
		
			//if the employee doesnt have a boss...
			//if($boss_arr != "")
			//{
			//$boss_no = $boss_arr["user_id"];
			//}
			//else
			//{
			//	$boss_no = 0;
			//}


//=====================		

$users_tomail_arr 	= @array_merge($users_tomail_arr,$boss_arr);


//codings to send mail...
		$mysql="select matechsubject,matechmessage from $config";
	
		$rslt_arr=$db_object->get_a_line($mysql);

		$matechsubject=$rslt_arr["matechsubject"];
		$matechmessage=$rslt_arr["matechmessage"];
		
//sender email details...
		$mysql = "select username,email from $user_table where user_id = '$user_id'";

		$sender_arr = $db_object->get_a_line($mysql);
		$user = $sender_arr["username"]; 
		$from = $sender_arr["email"]; 		
	
	for($i=0;$i<count($users_tomail_arr);$i++)
	{
		$userid_tomail = $users_tomail_arr[$i];
	
		$mysql = "select email,username,password from $user_table where user_id = '$userid_tomail'";
		$reciever_arr = $db_object->get_a_line($mysql);
		
		$email = $reciever_arr["email"];
		$username = $reciever_arr["username"];		
		$password = $reciever_arr["password"];
		
		$to = $email;
		
		$values["login_username"] = $username;
		$values["login_password"] = $password;
		
		$values["username"]=$username;
		$values["user"] = $user;
		$values["url"]=$common->http_path."/index.php";

		$message=$common->direct_replace($db_object,$matechmessage,$values);

		$sent=$common->send_mail($to,$matechsubject,$message,$from);

		
	}
		
		if($sent)
		{
		
			echo $error_msg["cMultiraterAppraisalMail_sent"];
		
		}
		else
		{
			echo $error_msg["cMultiraterAppraisalMail_fail"];
		}
	
	
	
}	//end of function send mails...



} //end of class

$obj = new techRatings;

//$post_var	= array_merge($_POST,$_GET);

if($fAdd_skill)
{
	
	
	$obj->add_moreskills($db_object,$common,$post_var,$user_id,$default,$error_msg);
}
if($fNext)
{
	//save the remaining skills to rate...
	
	include_once("header.php");
	
	$obj->add_moreskills($db_object,$common,$post_var,$user_id,$default,$error_msg);
	
	$obj->store_to_maintable($db_object,$common,$post_var,$user_id,$default,$error_msg);
	
	$obj->send_mails($db_object,$common,$post_var,$user_id,$default,$error_msg);
}
elseif ($fFinish_later)
{
	
	$obj->add_moreskills($db_object,$common,$post_var,$user_id,$default,$error_msg);
	
	header("Location:front_panel.php");
	
}
else
{
include_once("header.php");	
$obj->show_Appraisal($db_object,$common,$post_var,$user_id,$default);
}

include_once('footer.php');
?>
