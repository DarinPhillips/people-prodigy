<?php
/*---------------------------------------------
SCRIPT:replace_rej_tech.php
AUTHOR:info@chrisranjana.com	
UPDATED:29th Oct

DESCRIPTION:
This script displays alert for the Rejected Offers of the User.

---------------------------------------------*/
include("../session.php");
include_once("header.php");

class alertForRejectedOffer
{
	function show_rejectedoffer($db_object,$common,$post_var,$user_id,$default)
	{
		while(list($kk,$vv)=@each($post_var))
		{

		$$kk=$vv;

		}
	
		$xPath=$common->path;
		$returncontent=$xPath."/templates/career/replace_rej_tech.html";
		$returncontent=$common->return_file_content($db_object,$returncontent);
		
		$skills_table = $common->prefix_table('skills');
		$skillraters_table = $common->prefix_table('skill_raters');
		
		$appraisal_table = $common->prefix_table('appraisal');
		$tech_rating    = $common->prefix_table('tech_rating');
		$tech_references = $common->prefix_table('tech_references');
		$user_table	= $common->prefix_table('user_table');
		
		
		$values['rejected_userid'] = $rejected_userid;
		
//determine the skill which the user has asked to rate and display here....

		$mysql = "select ratingid from $tech_references where ref_userid='$rejected_userid' and user_to_rate='$user_id'";
//echo $mysql;
		$ratingid_arr = $db_object->get_single_column($mysql);
		//print_r($ratingid_arr);
		
		preg_match("/<{replacerejoffer_loopstart}>(.*?)<{replacerejoffer_loopend}>/s",$returncontent,$rej_arr);
		$newrej = $rej_arr[1];
		
		
		for($i=0;$i<count($ratingid_arr);$i++)
		{
		$ratingid = $ratingid_arr[$i];
		
		$mysql = "select skill_id,selfrating_labelid from $tech_rating where ratingid = '$ratingid'";
		
		$skillsel_arr = $db_object->get_a_line($mysql);

		$skill_sel = $skillsel_arr['skill_id'];
		$rating_sel = $skillsel_arr['selfrating_labelid'];
		
//determine the email of the rejected person and display here....
	
		$mysql = "select email from $user_table where user_id = '$rejected_userid'";
		$rej_email_arr = $db_object->get_a_line($mysql);
		
		$email = $rej_email_arr['email'];
		$values['email'] = $email;
//--------------
//display the skills that are yet to be rated only...
			
		$mysql = "select skill_id,skill_name from $skills_table where skill_type = 't'"; //  limit 0,20,$subclause

		$skills_arr = $db_object->get_rsltset($mysql);
		
		$mysql = "select count(*) as skill_count from $skills_table where skill_type='t'";
		$count_arr = $db_object->get_a_line($mysql);
		$count_skills = $count_arr['skill_count'];
		
		$values['count_skills'] = $count_skills;
	
		$skills_arr1	= $common->conv_2Darray($db_object,$skills_arr);
		$newrej1	 = $common->pulldown_replace($db_object,'<{skill_loopstart}>','<{skill_loopend}>',$newrej,$skills_arr1,$skill_sel);
	
		
//display of labels................................		

		$mysql = "select rater_id,rater_level_$default from $skillraters_table where skill_type = 't'";
		$label_arr = $db_object->get_rsltset($mysql);
	
		$label_arr1	= $common->conv_2Darray($db_object,$label_arr);
		$newrej1 = $common->pulldown_replace($db_object,'<{label_loopstart}>','<{label_loopend}>',$newrej1,$label_arr1,$rating_sel);

	
		$mysql = "select raters from $appraisal_table where user_id = '$user_id' and test_type = 't'";
		$raters_arr = $db_object->get_a_line($mysql);
	
		$raters = $raters_arr['raters'];
//----------------
		$str .= preg_replace("/<{(.*?)}>/e","$$1",$newrej1);
		}
		$returncontent = preg_replace("/<{replacerejoffer_loopstart}>(.*?)<{replacerejoffer_loopend}>/s",$str,$returncontent);
		$returncontent = $common->direct_replace($db_object,$returncontent,$values);
		
		echo $returncontent;

	}
	
	function replace_rater($db_object,$common,$post_var,$user_id,$default,$rejected_userid)
	{
		while(list($kk,$vv)=@each($post_var))
		{
		$$kk=$vv;
		if(ereg("^email_",$kk))
		{
			$email = $vv;
		}
		if(ereg("^tech_skills",$kk))
		{
			$tech_arr[] = $vv;
		}
		}
		//print_r($tech_arr);
		
		$tech_skills = @implode("','",$tech_arr);
		//echo $tech_skills;
		
		$tech_references = $common->prefix_table('tech_references');
		$user_table  = $common->prefix_table('user_table');
		$tech_rating	= $common->prefix_table('tech_rating');
		
		//print_r($post_var);
		//exit;
		
		$mysql = "select user_id from $user_table where email = '$email'";
		//echo $mysql;exit;
		$ref_userid_arr = $db_object->get_a_line($mysql);
		//print_r($ref_userid_arr);exit;
		
		$ref_userid = $ref_userid_arr['user_id'];
		
		//Check if the user is already in the process of rating the person...
		
		
		$mysql = "select $tech_references.ref_userid
				from $tech_rating,$tech_references
				where $tech_references.ratingid = $tech_rating.ratingid
				and $tech_rating.skill_id in('$tech_skills')
				and $tech_references.user_to_rate = '$user_id'
				and $tech_references.ref_userid = '$ref_userid'
				and $tech_references.status = 'a'";
		//echo $mysql;
		//exit;
		//$mysql = "select ref_userid from $tech_references where ref_userid = '$ref_userid' and user_to_rate = '$user_id'";
	
		$same_arr = $db_object->get_single_column($mysql);
		
		if(@in_array($ref_userid,$same_arr))
		{
			return 0;
		}
		
		
		
		$mysql = "select ratingid,ref_id from $tech_references where user_to_rate = '$user_id' and ref_userid = '$rejected_userid' and status = 'r'";
		//echo $mysql;exit;
		$ratingid_arr = $db_object->get_a_line($mysql);
		//print_r($ratingid_arr);exit;
		
		$ratingid = $ratingid_arr['ratingid'];
		$ref_id  = $ratingid_arr['ref_id'];
		
		$mysql = "update $tech_references set ref_userid = '$ref_userid' , status='a' ,date_rating_requested = now() where user_to_rate = '$user_id' and ref_userid ='$rejected_userid'";  // and ref_id = '$ref_id' and ratingid = '$ratingid' **not required since there may be many skills assigned for one person...
		//echo $mysql;exit;
		$db_object->insert($mysql);
		
		
		
		
		$rejected_userid = $ref_userid;
		
		return $rejected_userid;
		
	}
	
	
}
$obj = new alertForRejectedOffer;

//$post_var	= array_merge($_POST,$_GET);

if($fAdd_skill)
{
	$temp =$obj->replace_rater($db_object,$common,$post_var,$user_id,$default,$rejected_userid);
	
	
	if($temp == 0)
	{
		$message = $error_msg['cReplacerej_alreadyexists'];
		
		echo $message;
		$obj->show_rejectedoffer($db_object,$common,$post_var,$user_id,$default);
	}
	else
	{
	
	$post_var['rejected_userid'] = $temp;
	
	$obj->show_rejectedoffer($db_object,$common,$post_var,$user_id,$default);
	}
	
}
else
{

$obj->show_rejectedoffer($db_object,$common,$post_var,$user_id,$default);
}
include_once('footer.php');
?>
