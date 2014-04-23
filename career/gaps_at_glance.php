<?php
/*---------------------------------------------
SCRIPT:gaps_at_glance.php
AUTHOR:info@chrisranjana.com	
UPDATED:26th Dec

---------------------------------------------*/

include("../session.php");


class modelviews
{
	function select_components($db_object,$common,$default,$user_id,$post_var)
	{
		$xPath		= $common->path;
		$returncontent	= $xPath."/templates/career/gaps_at_glance.html";
		$returncontent	= $common->return_file_content($db_object,$returncontent);
		
		while(list($kk,$vv) = @each($post_var))
		{
			$$kk = $vv;
			
			if(ereg("^fDesignee_",$kk))
			{
				
				if($kk == "fDesignee_1" && $kk != '') 
				{
					$desig1_id=ereg_replace("firstdesignee_","",$vv);
							
				}
				if($kk == "fDesignee_2" && $kk != '') 
				{
					$desig2_id=ereg_replace("seconddesignee_","",$vv);
							
				}
			}

		}

		//echo "designee1 is $desig1_id<br>";
		//echo "designee2 is $desig2_id<br>";
		//$user_id = 4;
		//$modelid = 7;

		$values['designee1']= $desig1_id;
		$values['designee2']= $desig2_id;
		$values['modelid'] = $modelid;
		
		$position = $common->prefix_table('position');
		$skills  = $common->prefix_table('skills');
		$user_table = $common->prefix_table('user_table');
		
//PRINTING OF POSITION NAME...
		$mysql = "select position_name from $position where pos_id = '$posid'";
		$posname_arr = $db_object->get_a_line($mysql);
		$posname = $posname_arr['position_name'];
		$values['posname'] = $posname;
		$values['posid'] = $posid;
		
//THE COOKIE CONTAINS THE USERS SELECTED IN THE PREVIOUS SCREEN...
		$cookievalues = $_COOKIE['Usersindepth'];

		$users = @explode(",",$cookievalues);


//LOOP CONTENTS OF THE HAS:NEEDS:EXTRAS...
	
preg_match("/<{gapsataglance_loopstart}>(.*?)<{gapsataglance_loopend}>/s",$returncontent,$gapsold);
$gaps_new = $gapsold[1];

preg_match("/<{gapsskillshas_loopstart}>(.*?)<{gapsskillshas_loopend}>/s",$gaps_new,$gapskillhas_old);
$gapskillhas_new = $gapskillhas_old[1];

preg_match("/<{gapsskillsneeds_loopstart}>(.*?)<{gapsskillsneeds_loopend}>/s",$gaps_new,$gapskillneeds_old);
$gapskillneeds_new = $gapskillneeds_old[1];

preg_match("/<{gapsskillsextras_loopstart}>(.*?)<{gapsskillsextras_loopend}>/s",$gaps_new,$gapskillextras_old);
$gapskillextras_new = $gapskillextras_old[1];

preg_match("/<{gapsskillshastech_loopstart}>(.*?)<{gapsskillshastech_loopend}>/s",$gaps_new,$gapskillhastech_old);
$gapskillhas_tech = $gapskillhastech_old[1];

preg_match("/<{gapsskillsneedstech_loopstart}>(.*?)<{gapsskillsneedstech_loopend}>/s",$gaps_new,$gapskillneedstech_old);
$gapskillneeds_tech = $gapskillneedstech_old[1];

preg_match("/<{gapsskillstechextras_loopstart}>(.*?)<{gapsskillstechextras_loopend}>/s",$gaps_new,$gapskillextratech_old);
$gapskillextras_tech = $gapskillextratech_old[1];


		for($i=0;$i<count($users);$i++)
		{
		$user_to_show = $users[$i];
		
		$userswithkeys = $common->gaps_at_a_glance($db_object,$user_to_show,$modelid);
		$userswithkeys_tech = $common->gaps_at_a_glance_technical($db_object,$user_to_show,$modelid);


		$mysql = "select username from $user_table where user_id = '$user_to_show'";
		$username_arr = $db_object->get_a_line($mysql);
		$username = $username_arr['username'];

		//SKILLS USER HAS...
			$vals_has = @array_merge($userswithkeys[3],$userswithkeys[4],$userswithkeys[5]);
		//SKILLS USER NEEDS...
			$vals_needs = @array_merge($userswithkeys[2],$userswithkeys[1]);
			
		//ALL SKILLS IN MODELS...
			$skills_all_in_models_arr = @array_merge($vals_has,$vals_needs);
			$skills_all_in_models = @implode("','",$skills_all_in_models_arr);
			
		//TECHNICAL SKILLS USER HAS...
			$vals_tech_has = @array_merge($userswithkeys_tech[3],$userswithkeys_tech[4],$userswithkeys_tech[5]);	
		//TECHNICAL SKILLS USER NEEDS...
			$vals_tech_needs = @array_merge($userswithkeys_tech[2],$userswithkeys_tech[1]);	
		//ALL TECHNICAL SKILLS IN MODELS...
			$skills_tech_all_in_models_arr = @array_merge($vals_tech_has,$vals_tech_needs);
			$skills_tech_all_in_models = @implode("','",$skills_tech_all_in_models_arr);
				
		


		
		$str6 = '';
		$str5 = '';
		$str4 = '';
		$str3 = '';		
		$str2 = '';
		$str1 = '';

		for($l=0;$l<count($vals_has);$l++)	
		{
			$skillid = $vals_has[$l];
			
			$mysql = "select skill_name from $skills where skill_id = '$skillid'";
			$skillname_arr = $db_object->get_a_line($mysql);
			$skills_has = $skillname_arr['skill_name'];
			$str1 .= preg_replace("/<{(.*?)}>/e","$$1",$gapskillhas_new);
		}
		
		$gaps_new1 = preg_replace("/<{gapsskillshas_loopstart}>(.*?)<{gapsskillshas_loopend}>/s",$str1,$gaps_new);
		
		for($j=0;$j<count($vals_needs);$j++)
		{
			$skillsid = $vals_needs[$j];
			$mysql = "select skill_name from $skills where skill_id = '$skillsid'";
			$skillname_arr = $db_object->get_a_line($mysql);

			$skills_needs = $skillname_arr['skill_name'];

			$str2 .= preg_replace("/<{(.*?)}>/e","$$1",$gapskillneeds_new);

		}


		$gaps_new2 = preg_replace("/<{gapsskillsneeds_loopstart}>(.*?)<{gapsskillsneeds_loopend}>/s",$str2,$gaps_new1);		
		
//EXTRA SKILLS...
		$mysql = "select distinct(skill_id) from textqsort_rating where rated_user = '$user_to_show' and skill_id not in ('$skills_all_in_models')";
		$extraskills_arr = $db_object->get_single_column($mysql);
		
		for($k=0;$k<count($extraskills_arr);$k++)
		{
			$skillid_e = $extraskills_arr[$k];
			$mysql = "select skill_name from $skills where skill_id = '$skillid_e'";
			$skillname_arr = $db_object->get_a_line($mysql);
			$skills_extras = $skillname_arr['skill_name'];
			$str3 .= preg_replace("/<{(.*?)}>/e","$$1",$gapskillextras_new);
		}
		
		$gaps_new3 = preg_replace("/<{gapsskillsextras_loopstart}>(.*?)<{gapsskillsextras_loopend}>/s",$str3,$gaps_new2);


//TECHNICAL SKILLS DISPLAY...
	
		for($a=0;$a<count($vals_tech_has);$a++)	
		{
			$skill_techid = $vals_tech_has[$a];
			$mysql = "select skill_name from $skills where skill_id = '$skill_techid'";
			$skillname_arr = $db_object->get_a_line($mysql);
			$skills_has_tech = $skillname_arr['skill_name'];
			$str4 .= preg_replace("/<{(.*?)}>/e","$$1",$gapskillhas_tech);
		}
		$gaps_new4 = preg_replace("/<{gapsskillshastech_loopstart}>(.*?)<{gapsskillshastech_loopend}>/s",$str4,$gaps_new3);
	
		for($b=0;$b<count($vals_tech_needs);$b++)
		{
			$skill_tech_id = $vals_tech_needs[$b]; 
			$mysql = "select skill_name from $skills where skill_id = '$skill_tech_id'";
			$skillname_arr = $db_object->get_a_line($mysql);
			$skills_needs_tech = $skillname_arr['skill_name'];
			$str5 .= preg_replace("/<{(.*?)}>/e","$$1",$gapskillneeds_tech);
		}
		$gaps_new5 = preg_replace("/<{gapsskillsneedstech_loopstart}>(.*?)<{gapsskillsneedstech_loopend}>/s",$str5,$gaps_new4);
		
$mysql = "select distinct(skill_id) from other_raters_tech where rated_user = '$user_to_show' and skill_id not in ('$skills_tech_all_in_models')";
$extratech_arr = $db_object->get_single_column($mysql);		

		
		for($c=0;$c<count($extratech_arr);$c++)
		{
			$skilltechid = $extratech_arr[$c];
			$mysql = "select skill_name from $skills where skill_id = '$skilltechid'";
			$skillname_arr = $db_object->get_a_line($mysql);
			$skills_extra_tech = $skillname_arr['skill_name'];
			$str6 .= preg_replace("/<{(.*?)}>/e","$$1",$gapskillextras_tech);
			
		}	
		$gaps_new6 = preg_replace("/<{gapsskillstechextras_loopstart}>(.*?)<{gapsskillstechextras_loopend}>/s",$str6,$gaps_new5);

		$gaps_new7 .= preg_replace("/<{(.*?)}>/e","$$1",$gaps_new6);
		

		}

		$returncontent = preg_replace("/<{gapsataglance_loopstart}>(.*?)<{gapsataglance_loopend}>/s",$gaps_new7,$returncontent);		
		
		$returncontent 	= $common->direct_replace($db_object,$returncontent,$values);
		echo $returncontent;
	}

function save_designees($db_object,$common,$default,$user_id,$post_var)
	{
		while(list($kk,$vv) = @each($post_var))
		{
			$$kk = $vv;
		}
		
		$position_designee1 	= $common->prefix_table('position_designee1');
		$position_designee2 	= $common->prefix_table('position_designee2');
		$deployment_plan 	= $common->prefix_table('deployment_plan');
		$user_table 		= $common->prefix_table('user_table');
		


//echo "designee1 = $designee1 <br>";
//echo "designee2 = $designee2 <br>";
//echo "Position is $posid <br>";

//DETERMINE THE EMPLOYMENT TYPE OF THE DESIGNEES SELECTED AND STORE IN THE DEPLOYMENT TABLE...

	$mysql = "select user_type from $user_table where user_id = '$designee1'";
	$designee1_type_arr = $db_object->get_a_line($mysql);
	$designee1_type = $designee1_type_arr['user_type'];
	
	$mysql = "select user_type from $user_table where user_id = '$designee2'";
	$designee2_type_arr = $db_object->get_a_line($mysql);
	$designee2_type = $designee2_type_arr['user_type'];
	
//DETERMINE THE PLAN ID OF THE POSITION TO BE FILLED...	
	$mysql = "select plan_id from $deployment_plan where position = '$posid'";
	$planidpresent_arr = $db_object->get_a_line($mysql);	
	$planidpresent = $planidpresent_arr['plan_id'];
	
		
	
	$mysql = "update $deployment_plan set designee1 = '$designee1_type' , designee2 = '$designee2_type' where plan_id = '$planidpresent'";
	$db_object->insert($mysql);


//STORE DATA INTO THE TABLE OF A PARTICULAR USER for that position alone...
//RETRIEVE THE PLAN ID OF THE POSITION TO BE FILLED AND THEN STORE THE DESIGNEE VALUES TO THEM

		
		$mysql = "select $position_designee1.plan_id from $deployment_plan,$position_designee1
				where $deployment_plan.plan_id = $position_designee1.plan_id
				and position = '$posid'";

		$planid_arr = $db_object->get_a_line($mysql);
		$planid = $planid_arr['plan_id'];

		
		if($designee1 != '')
		{
			if($planid != '')
			{	
				$mysql = "select plan_id from $deployment_plan
						where position = '$posid'";	
				$planid_arr = $db_object->get_a_line($mysql);
				$planid_exist = $planid_arr['plan_id'];
			
				
				$mysql = "update $position_designee1 set designated_user = '$designee1'
						where plan_id = '$planid_exist'";

				$db_object->insert($mysql);
			}
			else
			{
			$mysql = "select plan_id from $deployment_plan where position = '$posid'";	
				$planid_arr = $db_object->get_a_line($mysql);
				$planid_exist = $planid_arr['plan_id'];

			$mysql = "insert into $position_designee1 set plan_id = '$planid_exist',
						designated_user = '$designee1'";
			
			$db_object->insert($mysql);
			}
		
		}
		

	
		$mysql = "select $position_designee2.plan_id from $deployment_plan,$position_designee2
				where $deployment_plan.plan_id = $position_designee2.plan_id
				and position = '$posid'";

		$planid2_arr = $db_object->get_a_line($mysql);
		$planid2 = $planid2_arr['plan_id'];


		if($designee2 != '')
		{
			if($planid2 != '')
			{
				$mysql = "select plan_id from $deployment_plan where position = '$posid'";	
				$planid_arr = $db_object->get_a_line($mysql);
				$planid_exist = $planid_arr['plan_id'];

				
				$mysql = "update $position_designee2 set designated_user = '$designee2'
						where plan_id = '$planid_exist'";
				$db_object->insert($mysql);
			}
			else
			{
				$mysql = "select plan_id from $deployment_plan where position = '$posid'";	
				$planid_arr = $db_object->get_a_line($mysql);
				$planid_exist = $planid_arr['plan_id'];
				
			$mysql = "insert into $position_designee2 set plan_id = '$planid_exist',
					designated_user = '$designee2'";
			
			$db_object->insert($mysql);
			}
		}

		

		
		header("Location:successionplan_doc_action.php?pid=$posid&modelid=$modelid");
	}

}

$obj = new modelviews;

if($fSave)
{

$obj->save_designees($db_object,$common,$default,$user_id,$post_var);



//$message = $error_msg['cDesingneesaved'];
//echo $message;



}
else
{
include_once("header.php");

$obj->select_components($db_object,$common,$default,$user_id,$post_var);
}

include_once("footer.php");
?>
