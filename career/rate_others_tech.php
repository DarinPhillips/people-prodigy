<?php
/*---------------------------------------------
SCRIPT: rate_others_tech.php
AUTHOR:info@chrisranjana.com	
UPDATED:30th Oct

DESCRIPTION:
This script displays the technical ratings which the person is supposed to rate.

---------------------------------------------*/
include("../session.php");
include_once("header.php");

class rateOthersTechnical
{
	function show_technicalskills($db_object,$common,$post_var,$user_id,$default)
	{
		while(list($kk,$vv)=@each($post_var))
		{
		$$kk=$vv;
		}
		$xPath=$common->path;
		$returncontent=$xPath."/templates/career/rate_others_tech.html";
		$returncontent=$common->return_file_content($db_object,$returncontent);
		
		
		$user_table = $common->prefix_table('user_table');
		$tech_rating = $common->prefix_table('tech_rating');
		$tech_references = $common->prefix_table('tech_references');
		$skills 	= $common->prefix_table('skills');
		$skillraters_table = $common->prefix_table('skill_raters');
		$rater_label_relate = $common->prefix_table('rater_label_relate');
		
 	
		$mysql = "select username from $user_table where user_id = '$user_to_rate'";
		$username_arr = $db_object->get_a_line($mysql);
		$username = $username_arr['username'];
		
		$mysql = "select ratingid from $tech_references where ref_userid = '$user_id' and user_to_rate = '$user_to_rate' and status = 'a'";
		$ratingid_arr = $db_object->get_single_column($mysql);
		
		$ratingid1_arr = @array_unique($ratingid_arr);
		
		
//making the array $ratingid1_arr with keys in an arranged order...
		
		$my_key = 0;
		while (list($kk,$vv) = @each($ratingid1_arr))
		{
			
			$ratingarr_new[$my_key] = $vv;
			$my_key++;
		}

		$a=0;
		for($i=0;$i<count($ratingarr_new);$i++)
		{
			$ratingid = $ratingarr_new[$i];
			$mysql = "select skill_id,selfrating_labelid from $tech_rating where ratingid = '$ratingid'";
	
			$rated_data_arr = $db_object->get_a_line($mysql);
			
			$skill_arr[$a] = $rated_data_arr['skill_id']; $a++;
			$selfrating_arr[] = $rated_data_arr['selfrating_labelid'];
			
			
		}
		
		if(count($skill_arr)>0)
		{
		$skills_full = @implode("','",$skill_arr); 
		
		$count_skills = count($skill_arr);
		
		
//Display of Skills...
		$mysql = "select skill_id,skill_name from $skills where skill_id in ('$skills_full')";
		$skilldisplay_arr = $db_object->get_rsltset($mysql);

//Display of Ranking Labels...
		//$mysql = "select rater_id,rater_level_$default from $skillraters_table where skill_type = 't'";
		$mysql = "select rater_labelno as rater_id,rater_level_$default from $rater_label_relate,$skillraters_table  
				where $rater_label_relate.rater_id = $skillraters_table.rater_id
				and skill_type = 't'";
		$label_arr = $db_object->get_rsltset($mysql);
		
		
		$skills_arr1	= $common->conv_2Darray($db_object,$skilldisplay_arr);
		
		//print_r($skills_arr1);
		
		$label_arr1		= $common->conv_2Darray($db_object,$label_arr);
		}
		
		preg_match("/<{displayfull_loopstart}>(.*?)<{displayfull_loopend}>/s",$returncontent,$displaymatch);
		$newdisplaymatch = $displaymatch[1];
		
		//echo $newdisplaymatch;exit;
		for($l=0;$l<count($skill_arr);$l++)
		{
			$skill_sel = $skill_arr[$l];
			
			$skillname = $skills_arr1[$skill_sel];
			
			$mysql = "select skill_description from $skills where skill_id = '$skill_sel'";
			$skill_desc_arr = $db_object->get_a_line($mysql);
			$skill_desc = $skill_desc_arr['skill_description'];
			
			//$skill_desc = $skills_arr1[$skill_sel];
			
			$selfrated_sel = $selfrating_arr[$l];
		
			$newdisplaymatch1	= $common->pulldown_replace($db_object,'<{skill_loopstart}>','<{skill_loopend}>',$newdisplaymatch,$skills_arr1,$skill_sel);
			
			
			$newdisplaymatch1 = $common->pulldown_replace($db_object,'<{label_loopstart}>','<{label_loopend}>',$newdisplaymatch1,$label_arr1,$selfrated_sel);
			
			$str .= preg_replace("/<{(.*?)}>/e","$$1",$newdisplaymatch1);
		
			
		}
		
		$returncontent = preg_replace("/<{displayfull_loopstart}>(.*?)<{displayfull_loopend}>/s",$str,$returncontent);
		
	
		$values['user_to_rate'] = $user_to_rate;
		$values['username'] 	= $username;
		$values['count_skills'] = $count_skills;
		
		$returncontent = $common->direct_replace($db_object,$returncontent,$values);
		
		echo $returncontent;
		
		
	}     //end of function show_technicalskills()
	
	function store_data($db_object,$common,$post_var,$user_id,$default)
	{
		while(list($kk,$vv)=@each($post_var))
		{
		$$kk=$vv;
		
		if(ereg("^skillstorate_",$kk))
				{
				$s_qid=ereg_replace("skillstorate_","",$kk);
				$skill_array[$s_qid] = $vv;
				}
		
		if(ereg("^skillslabel_",$kk))
				{
				$lab_qid=ereg_replace("skillslabel_","",$kk);
				$label_array[$lab_qid] = $vv;
				}
				
		}
		
	
		
		$other_raters_tech 	= $common->prefix_table('other_raters_tech');
		$tech_references	= $common->prefix_table('tech_references');
		
	
//before storing the data check if there is any previous existence of the same user being rated by the same person...

$mysql = "delete from $other_raters_tech where rated_user = '$user_to_rate' and rater_id = '$user_id'";
$db_object->insert($mysql);


		for($i=0;$i<$count_skills;$i++)
		{
			$skill_id = $skill_array[$i];
			$label_id = $label_array[$i];
			
			$boss_no = $common->immediate_boss($db_object,$user_to_rate);
			
			
			if($boss_no == $user_id)
			{
				$group_name = 'boss';
			}
			else
			{
				$group_name = 'reference';
			}
			
			$mysql = "insert into $other_raters_tech set skill_id = '$skill_id',label_id = '$label_id',rated_user = '$user_to_rate',rater_id = '$user_id',group_name='$group_name'";
			//echo "$mysql<br>";
			$db_object->insert($mysql);
			
		}
		
		
//after rating, remove the alert from the 'alert for rating others'		
		$mysql = "update $tech_references set rating_over = 'y',date_rating_over=now() where ref_userid ='$user_id' and user_to_rate='$user_to_rate'";
		//echo $mysql;
		$db_object->insert($mysql);
		
		
		
		
	}
}
$obj = new rateOthersTechnical;

//$post_var	= array_merge($_POST,$_GET);
if($fSubmit)
{
	$obj->store_data($db_object,$common,$post_var,$user_id,$default);
	
	
	
	$message = $error_msg['cRateothers_tech_savemess'];
	echo $message;

}
else
{
$obj->show_technicalskills($db_object,$common,$post_var,$user_id,$default);
}
include_once("footer.php");
?>
