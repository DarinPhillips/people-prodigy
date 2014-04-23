<?php
/*---------------------------------------------
SCRIPT:appraisal_preference.php
AUTHOR:info@chrisranjana.com	
UPDATED:Jan 8th

DESCRIPTION:
This script displays the appraisal preference

---------------------------------------------*/
include("../session.php");
include_once("header.php");

class AppraisalPreference
{
function show_preferences($db_object,$common,$post_var,$user_id)
	{
		while(list($kk,$vv) = @each($post_var))
		{
		$$kk = $vv;
		}
		$xPath=$common->path;
		$returncontent=$xPath."/templates/career/appraisal_preference.html";
		$returncontent=$common->return_file_content($db_object,$returncontent);

		$other_raters=$common->prefix_table("other_raters");
		$user_table=$common->prefix_table("user_table");
		$textqsort_rating=$common->prefix_table("textqsort_rating");

		if($user_id==1)
		{
			$sql="select user_id from $user_table where user_id<>'$user_id'";
			$users=$db_object->get_single_column($sql);
		}
		else
		{
			$sql="select user_id from $user_table where admin_id='$user_id'";
			$users=$db_object->get_single_column($sql);
		}
		if(count($users)>0)
		{
		$users_id=@implode(",",$users);
		$users_id="(".$users_id.")";
		$sql="select count(rater_userid) as count1 from $other_raters where rater_userid in $users_id";

		$res=$db_object->get_a_line($sql);
		$count1=$res[count1];
		
		$sql1="select count(rater_userid) as count1 from $other_raters where rater_userid in $users_id and rating_over='y'";
		
		$res=$db_object->get_a_line($sql1);
		$count2=$res[count1];

		
		if($count1!=0)
		{
			$rate=($count2/$count1)*100;
				
			$arr[text_rating]=@sprintf("%01.2f",$rate);
		}
		else
		{
			$arr[text_rating]=0;
		}
		
		}
		$returncontent=$common->direct_replace($db_object,$returncontent,$arr);
		
		echo $returncontent;
	}
}
$obj = new AppraisalPreference;
$obj->show_preferences($db_object,$common,$post_var,$user_id);

include_once("footer.php");
?>
