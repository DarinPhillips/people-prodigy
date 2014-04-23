<?php
/*---------------------------------------------
SCRIPT:rejected_offers.php
AUTHOR:info@chrisranjana.com	
UPDATED:29th Oct

DESCRIPTION:
This script displays alert for the rejected offers.

---------------------------------------------*/
include("../session.php");
include_once("header.php");

class rejectedOffers
{
	function show_screen($db_object,$common,$post_var,$default,$user_id)
	{
		while(list($kk,$vv)=@each($post_var))
		{

		$$kk=$vv;
	
		}
	
		$xPath=$common->path;
		$returncontent=$xPath."/templates/career/rejected_offers.html";
		$returncontent=$common->return_file_content($db_object,$returncontent);
		
		$otherraters_table = $common->prefix_table("other_raters");
		$user_table = $common->prefix_table("user_table");
		$tech_references = $common->prefix_table('tech_references');
		
//>>>>>>>>>>>>>>>>>INTERPERSONAL REJECTED OFFERS START
		
		
		
//pregmatch of the rejected offers...
		
		preg_match("/<{alertrejected_start}>(.*?)<{alertrejected_end}>/s",$returncontent,$matchedrej);
		$replacerejected = $matchedrej[1];
		
		
//Rejected raters displayed	
		
		$mysql ="select rater_email from $otherraters_table where status='r' and cur_userid=$user_id";
		$rej_arr = $db_object->get_single_column($mysql);
	
		if($rej_arr != '')
		{
			$returncontent=preg_replace("/<{interpersonalrej_loop(.*?)}>/s","",$returncontent);			

		}
		else
		{
			$returncontent=preg_replace("/<{interpersonalrej_loopstart}>(.*?)<{interpersonalrej_loopend}>/s","",$returncontent);
		}
		
//get the id of the record to be changed
		$mysql = "select rater_id,group_belonging from $otherraters_table where status='r' and cur_userid = '$user_id'";

		$id_arr = $db_object->get_a_line($mysql);
		$rater_id = $id_arr["rater_id"];
		$was_in_group  = $id_arr["group_belonging"];

		for($k=0;$k<count($rej_arr);$k++)
		{
			$email = $rej_arr[$k];
			$mysql = "select username,user_id from $user_table where email='$email'";
			$name_arr = $db_object->get_a_line($mysql);
			$username = $name_arr["username"];
			$rejected_userid = $name_arr["user_id"];
			
			
			$replacedrej  .= preg_replace("/<{(.*?)}>/e","$$1",$replacerejected);
			
			

		}
	
		$returncontent=preg_replace("/<{alertrejected_start}>(.*?)<{alertrejected_end}>/s",$replacedrej,$returncontent);

	
		
		
//>>>>>>>>>>>>>>>>>INTERPERSONAL REJECTED OFFERS END
		
//>>>>>>>>>>>>>>>>>TECHNICAL REJECTED OFFERS START	
		
//pregmatch of the rejected offers...
	
	preg_match("/<{alertrejectedtech_start}>(.*?)<{alertrejectedtech_end}>/s",$returncontent,$matchedrej_tech);
	$replacerejected_tech = $matchedrej_tech[1];
		
		
//Rejected raters displayed	
	
	$mysql = "select distinct(ref_userid) from $tech_references where user_to_rate = '$user_id' and status = 'r'";
	//echo $mysql;
	$rej_arr = $db_object->get_single_column($mysql);
	
	//print_r($rej_arr);
	
		if($rej_arr != '')
		{
			$returncontent=preg_replace("/<{technicalrej_loop(.*?)}>/s","",$returncontent);			

		}
		else
		{
			$returncontent=preg_replace("/<{technicalrej_loopstart}>(.*?)<{technicalrej_loopend}>/s","",$returncontent);
		}
	
	
	for($l=0;$l<count($rej_arr);$l++)
	{
		$rejected_userid = $rej_arr[$l];
		
		$mysql = "select username from $user_table where user_id = '$rejected_userid'";
		//echo $mysql;
		$name_arr = $db_object->get_a_line($mysql);
		
		$username = $name_arr['username'];
		
		
		$replacedrej_tech  .= preg_replace("/<{(.*?)}>/e","$$1",$replacerejected_tech);
		
	}
	
	$returncontent=preg_replace("/<{alertrejectedtech_start}>(.*?)<{alertrejectedtech_end}>/s",$replacedrej_tech,$returncontent);	
		
		
		
		
//>>>>>>>>>>>>>>>>>TECHNICAL REJECTED OFFERS END		
		$returncontent = $common->direct_replace($db_object,$returncontent,$values);
		echo $returncontent;
	}
	
}
$obj = new rejectedOffers;

//$post_var	= array_merge($_POST,$_GET);

$obj->show_screen($db_object,$common,$post_var,$default,$user_id);
include_once("footer.php");
?>
