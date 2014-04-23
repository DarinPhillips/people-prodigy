<?php
/*---------------------------------------------
SCRIPT: alert_ratings.php
AUTHOR:info@chrisranjana.com	
UPDATED:6th Oct

DESCRIPTION:
This script displays alert for rating other persons who have intimated them

---------------------------------------------*/
include("../session.php");
include_once("header.php");

class rateOthers
{
	function show_ratingalert($db_object,$common,$post_var,$user_id)
	{
		while(list($kk,$vv)=@each($post_var))
		{
		$$kk=$vv;
		}
		
		$xPath=$common->path;
		$returncontent=$xPath."/templates/career/alert_ratings.html";
		$returncontent=$common->return_file_content($db_object,$returncontent);
		
		
		$user_table = $common->prefix_table("user_table");
		$otherraters_table = $common->prefix_table("other_raters");	
		
		$tech_references = $common->prefix_table('tech_references');
		
		preg_match("/<{alertratings_start}>(.*?)<{alertratings_end}>/s",$returncontent,$matched);
		$replace=$matched[1];
		
		preg_match("/<{alerttechratings_start}>(.*?)<{alerttechratings_end}>/s",$returncontent,$matchedtech);
		$replacetech=$matchedtech[1];
		
//INTERPERSONAL...

		$mysql = "select cur_userid,date_format(date_rating_requested,'%m.%d.%Y.%H.%i') as date_rating_requested from $otherraters_table where rater_userid='$user_id' and status = 'a' and rating_over = 'n'";

		$arr = $db_object->get_rsltset($mysql);
		//print_r($arr);
		
//TECHNICAL...
		
		$mysql = "select distinct(user_to_rate) as user_to_rate,date_rating_requested from $tech_references where ref_userid = '$user_id' and rating_over = 'n'";
		$tech_arr = $db_object->get_rsltset($mysql);
		
		
		
		for($i=0;$i<count($arr);$i++)
			{
			$cur_userid = $arr[$i]['cur_userid'];
			
			$date_of_request = $arr[$i]['date_rating_requested']; 

			
			
			$rater_userid = $user_id;  	//the present user id...
		
			$mysql = "select username from $user_table where user_id='$cur_userid'";
			
			$name_arr = $db_object->get_a_line($mysql);
			
			//$name = $name_arr["username"];
			
			$name = $common->name_display($db_object,$cur_userid);
			
			$replaced .= preg_replace("/<{(.*?)}>/e","$$1",$replace);
		
			}
		
			
//The following code is to display the alerts so that for one user (though with many skills) will be shown as a single alert		
/*			print_r($tech_arr);
		$temp_tech_arr = @array_unique($tech_arr);	
		//print_r($tech_arr);
		$newkey = 0;
		while(list($kk,$vv) = @each($temp_tech_arr))
		{
			$new_tech_arr[$newkey] = $vv;
			$newkey++;
		}
		
*/	
		for($j=0;$j<count($tech_arr);$j++)
		{
			$user_to_rate = $tech_arr[$j]['user_to_rate'];
			$date_of_request = $tech_arr[$j]['date_rating_requested'];
			$mysql = "select username from $user_table where user_id = '$user_to_rate'";
			$tech_name_arr = $db_object->get_a_line($mysql);
			//$tech_name = $tech_name_arr['username'];
			
			$tech_name = $common->name_display($db_object,$user_to_rate);
			
			//$values['user_to_rate'] = $user_to_rate;
			$replacedtech1 .= preg_replace("/<{(.*?)}>/e","$$1",$replacetech);
		}
			
		
		$returncontent=preg_replace("/<{alerttechratings_start}>(.*?)<{alerttechratings_end}>/s",$replacedtech1,$returncontent);
		$returncontent=preg_replace("/<{alertratings_start}>(.*?)<{alertratings_end}>/s",$replaced,$returncontent);
		$returncontent = $common->direct_replace($db_object,$returncontent,$values);
	
		
		echo $returncontent;
		
	}
}
$obj = new rateOthers;
//$post_var	= array_merge($_POST,$_GET);

$obj->show_ratingalert($db_object,$common,$post_var,$user_id);
include_once("footer.php");
?>
