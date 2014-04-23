<?php
/*---------------------------------------------
SCRIPT:alert_multirater.php
AUTHOR:info@chrisranjana.com	
UPDATED:30th Sept
DESCRIPTION:
This script displays alert for the 360.
---------------------------------------------*/
include("../session.php");
include_once("header.php");

class alertForMultirater
{ 
	function show_alert_screen($db_object,$common,$post_var,$gbl_test_mode,$gbl_skill_type,$default,$user_id,$error_msg)
	{
		while(list($kk,$vv)=@each($post_var))
		{

		$$kk=$vv;
	
		}
	
		$xPath=$common->path;
		$returncontent=$xPath."/templates/career/alert_multirater.html";
		$returncontent=$common->return_file_content($db_object,$returncontent);
		
		$ratergroup_table  = $common->prefix_table("rater_group");
		$otherraters_table = $common->prefix_table("other_raters");
		$user_table = $common->prefix_table("user_table");
		
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

//preg_match of all the groups...		
		

//COMMON ONE>>>>>>		
		//preg_match("/<{alertgroups_start}>(.*?)<{alertgroups_end}>/s",$returncontent,$matchedgroups);
		//$replacegroups = $matchedgroups[1];
		
//>>>>>>>>>>>>>>>>>		
/*		

//pregmatch of the rejected offers...
preg_match("/<{alertrejected_start}>(.*?)<{alertrejected_end}>/s",$returncontent,$matchedrej);
$replacerejected = $matchedrej[1];

*/		
//alert to be shown according to the groups selected in multirater assessment

		$mysql = "select rater_group_name from $ratergroup_table";
		$grpname_arr = $db_object->get_single_column($mysql);
		//print_r($grpname_arr);
		
		$other_raters = $common->prefix_table('other_raters');

//>>>>>>>>>>>>>>>>>
		
		
		$c = 0;
		for($x=0;$x<count($grpname_arr);$x++)
		{
			
			$grpname = $grpname_arr[$x];
			
			
			$mysql = "select group_belonging from $other_raters where group_belonging = '$grpname' and cur_userid = '$user_id'";
			$grp_arr = $db_object->get_a_line($mysql);
			
			$group_exists = $grp_arr['group_belonging'];
			
			if($grpname == "grp_team")
			{
						
				if($group_exists == '')
				{
				
				$message[$c]['cMultirateralert_enter'] = $error_msg['cMultirateralert_enter'];
				$message[$c]['fullmess'] = $error_msg['cMultirateralert_fullmess_team'];
				$message[$c]["grpname"] = $grpname;
				$c++;
				}
				 				
				
			}
			
			if($grpname == "grp_incus")
			{
				if($group_exists == '')
				{
				$message[$c]['cMultirateralert_enter'] = $error_msg['cMultirateralert_enter'];
				$message[$c]['fullmess'] = $error_msg['cMultirateralert_fullmess_incus'];
				$message[$c]["grpname"] = $grpname;
				$c++;
				}
			}
			if($grpname == "grp_peer")
			{
				if($group_exists == '')
				{
				$message[$c]['cMultirateralert_enter'] = $error_msg['cMultirateralert_enter'];
				$message[$c]['fullmess'] = $error_msg['cMultirateralert_fullmess_peer'];
				$message[$c]["grpname"] = $grpname;
				$c++;
				}
			}
			if($grpname == "grp_excus")
			{
				if($group_exists == '')
				{
				$message[$c]['cMultirateralert_enter'] = $error_msg['cMultirateralert_enter'];
				$message[$c]['fullmess'] = $error_msg['cMultirateralert_fullmess_excus'];
				$message[$c]["grpname"] = $grpname;
				$c++;
				}
			}
			if($grpname == "grp_dirrep")
			{
				if($group_exists == '')
				{
				$message[$c]['cMultirateralert_enter'] = $error_msg['cMultirateralert_enter'];
				$message[$c]['fullmess'] = $error_msg['cMultirateralert_fullmess_dirrep'];
				$message[$c]["grpname"] = $grpname;
				$c++;
				}
			}
			if($grpname == "grp_other")
			{
				if($group_exists == '')
				{
				$message[$c]['cMultirateralert_enter'] = $error_msg['cMultirateralert_enter'];
				$message[$c]['fullmess'] = $error_msg['cMultirateralert_fullmess_other'];
				$message[$c]["grpname"] = $grpname;
				$c++;
				}
			}

		}

		
		$values = array("alertgroups_loop"=>$message);
		 
		$returncontent = $common->simpleloopprocess($db_object,$returncontent,$values);

	
//>>>>>>>>>>>>>>>>>		
/*
//Rejected raters displayed	
$mysql ="select rater_email from $otherraters_table where status='r' and cur_userid=$user_id";
//echo $mysql;exit;
$rej_arr = $db_object->get_single_column($mysql);
//get the id of the record to be changed
$mysql = "select rater_id,group_belonging from $otherraters_table where status='r' and cur_userid = '$user_id'";
//echo $mysql
$id_arr = $db_object->get_a_line($mysql);
$rater_id = $id_arr["rater_id"];
$was_in_group  = $id_arr["group_belonging"];
for($k=0;$k<count($rej_arr);$k++)
{$email = $rej_arr[$k];
$mysql = "select username,user_id from $user_table where email='$email'";
$name_arr = $db_object->get_a_line($mysql);
$username = $name_arr["username"];
$rejected_userid = $name_arr["user_id"];
$replacedrej  .= preg_replace("/<{(.*?)}>/e","$$1",$replacerejected);
}
$returncontent=preg_replace("/<{alertrejected_start}>(.*?)<{alertrejected_end}>/s",$replacedrej,$returncontent);
*/

		$returncontent = $common->direct_replace($db_object,$returncontent,$values);
		echo $returncontent;	

		
	}
	
}
$obj = new alertForMultirater;

$obj->show_alert_screen($db_object,$common,$post_var,$gbl_test_mode,$gbl_skill_type,$default,$user_id,$error_msg);
 
include_once("footer.php");
?>
