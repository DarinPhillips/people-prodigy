<?php
/*---------------------------------------------
SCRIPT:textonly_qsort.php
AUTHOR:info@chrisranjana.com	
UPDATED:13th Oct

DESCRIPTION:
This script displays text only q sort method.

---------------------------------------------*/
include("../session.php");
include_once("header.php");

class textOnlySort
{
	function determine_initialvalues($db_object,$common,$post_var,$default,$user_id)
	{
		while(list($kk,$vv)=@each($post_var))
		{
		$$kk=$vv;
		}
		
		$user_table 		= $common->prefix_table('user_table');
		$skills_table		= $common->prefix_table('skills');
		$skills_for_rating 	= $common->prefix_table('skills_for_rating');
		$skillraters_table 	= $common->prefix_table('skill_raters');
		$raterlabelrelate_table = $common->prefix_table('rater_label_relate');
		$textqsort_table 	= $common->prefix_table('textqsort_rating');
		$otherraters_table 	= $common->prefix_table("other_raters");	
		$temptextqsort_table 	= $common->prefix_table("temp_textqsort");
		
		
		

		
		
//no of labels assigned by admin is counted...
		
		$mysql = "select count(rater_labelno)as count_label from $raterlabelrelate_table where rater_type='i'";
		
		$countlabel_arr = $db_object->get_a_line($mysql);
		
//no of labels counted for the skills left to sort task...		
		
		$count_label = $countlabel_arr['count_label'];
		
//label id and name are obtained...
		
		$mysql = "select $raterlabelrelate_table.rater_labelno as rater_label_no,
				$skillraters_table.rater_level_$default 
				from $skillraters_table,$raterlabelrelate_table 
				where $raterlabelrelate_table.rater_id = $skillraters_table.rater_id
				and rater_type='i' ";
		$labelname_arr = $db_object->get_rsltset($mysql);
		
//$mysql = "select skill_id,skill_name from $skills_table where skill_id not in('$skillsid_low') and skill_type = 'i' order by rand(skill_id)";
		
$mysql = "select skills_for_rating.skill_id,skill_name 
		from skills_for_rating,skills
		where skills.skill_id = skills_for_rating.skill_id
		and skills_for_rating.skill_type='i'
		and skills_for_rating.usr_id = '$rated_user_id'
		and skills_for_rating.skill_id not in('$skillsid_low')
		order by rand(skills_for_rating.skill_id)";

		$skillsall_arr = $db_object->get_rsltset($mysql);
		
//counted for displaying the skills left to sort...
		
		$count_full = count($skillsall_arr);
		
//find the average no of piles to be allotted to each label....

		$avg_count = $count_full/$count_label;
		
		$remaining_skills = (int)$avg_count;
	
		$count_array = "";
		
		$left_out=$count_full - ($remaining_skills * $count_label) ; 
//echo $left_out;
		
	
		for($i=0;$i<$count_label;$i++)
			{
				

			$labelname = $labelname_arr[$i]["rater_level_$default"];

			$labelid = $labelname_arr[$i]["rater_label_no"];

			$my_array[$labelid]=$remaining_skills;
						
			}

		$array=array_keys($my_array);

//print_r($array);
//$x = count($array);
//echo "count is $x";
		
if($left_out !=0)
	{
	
		for($i=0;$i<=count($array);$i++)

			{
//echo $i;
//echo $array[$i];
			$index=$array[$i];

			$my_array[$index]+=1;
			
			$left_out--;

				if($left_out==0)
				{
				break;
				}

			}
	}
	
		$mysql = "select skill_id from $textqsort_table where rated_user = '$rated_user_id' and rater_id = '$user_id'and rater_label_no = '$labelid'";
		//echo "$mysql<br>";
		$skills_arr = $db_object->get_single_column($mysql);
	
		$skills_arr = @array_unique($skills_arr);
		
				if($skills_arr != '')
				{
				$count_cur_skills = count($skills_arr);
				}
				else
				{
				$count_cur_skills = 0;
				}

		$rem_val_each = $my_array[$labelid]- $count_cur_skills;
		
		return $my_array;
	
	}  //end of function
	
	
	function show_textonly_screen($db_object,$common,$post_var,$gbl_skill_categories,$user_id,$default)
	{
		while(list($kk,$vv)=@each($post_var))
		{

		$$kk=$vv;
		
		if(ereg("^fMoveskill_",$kk))
			{

				$clicked = 'true';
				$cl = 0;
			}
			
		}
		
	
		$disabled = 'disabled';	

		$xPath=$common->path;
		$returncontent=$xPath."/templates/career/textonly_qsort.html";
		$returncontent=$common->return_file_content($db_object,$returncontent);
		
//table prefixes...
		$user_table 		= $common->prefix_table('user_table');
		$skills_table 		= $common->prefix_table('skills');
		$skillraters_table 	= $common->prefix_table('skill_raters');
		$skills_for_rating 	= $common->prefix_table('skills_for_rating');
		$raterlabelrelate_table = $common->prefix_table('rater_label_relate');
		$textqsort_table 	= $common->prefix_table('textqsort_rating');
		$otherraters_table	= $common->prefix_table("other_raters");	
		$temptextqsort_table 	= $common->prefix_table("temp_textqsort");
		

//check for the user names and id and determine if they are genuine...
//checks if the user is rating the same person alloted to him
		
		$mysql = "select cur_userid from $otherraters_table where rater_userid='$user_id' and status = 'a'";
		$arr = $db_object->get_single_column($mysql);
		
		$mysql = "select username,user_id from $user_table where user_id = '$rated_user_id'";
		$username_arr = $db_object->get_a_line($mysql);
	
		$rated_username = $username_arr['username'];
		$rated_user_id = $username_arr['user_id'];
	
		$check = @in_array("$rated_user_id",$arr);
		
			if(!$check)
			{
				echo "You are Restricted";
				exit;
			
			}
		


//label id and name are obtained...
		
		$mysql = "select $raterlabelrelate_table.rater_labelno as rater_label_no,
				$skillraters_table.rater_level_$default 
				from $skillraters_table,$raterlabelrelate_table 
				where $raterlabelrelate_table.rater_id = $skillraters_table.rater_id
				and rater_type='i' ";
		$labelname_arr = $db_object->get_rsltset($mysql);
		
		
//call the function to determine the initial no of left out skill to sort
		
		if($clicked != 'true')
		{
		
		$my_array = $this->determine_initialvalues($db_object,$common,$post_var,$default,$user_id);

		
		}

//replace the contents outside the loop...
		
		preg_match("/<{labeldisplay_loopstart}>(.*?)<{labeldisplay_loopend}>/s",$returncontent,$matched);
		$replace=$matched[1];
		$replace1	= $replace;
		
		$mysql = "select skill_id from $textqsort_table where rated_user = '$rated_user_id' and rater_type = 'i' and rater_id = '$user_id'";
		//echo "$mysql<br>";
		$skills_arr = $db_object->get_single_column($mysql);
		
		$skillsid_low = @implode("','",$skills_arr);
		
//skillid which are present in the textqsort table are not shown in the skills table
		
		//$mysql = "select skill_id,skill_name from $skills_table
				//where skill_id not in('$skillsid_low') and skill_type = 'i' order by rand(skill_id)";//WHERE CONDITION TO BE GIVEN RELATED TO THE RATER LABELS..
		$mysql = "select skills.skill_id,skill_name 
			from skills_for_rating,skills
			where skills.skill_id = skills_for_rating.skill_id
			and skills_for_rating.skill_type='i'
			and skills_for_rating.usr_id = '$rated_user_id'
			and skills_for_rating.skill_id not in('$skillsid_low')
			order by rand(skills_for_rating.skill_id)";

		$skillsall_arr = $db_object->get_rsltset($mysql);


//counted for displaying the skills left to sort...
		
		$count_full = count($skillsall_arr);
	
		
//replace the loop which displays the full contents of skills...
		
		$values['skill_loop'] = $skillsall_arr;
		if(count($skillsall_arr)>0)
		{
			$disabled="disabled";
		}
		else
		{
			$disabled='';
		}
		$returncontent	= $common->multipleloop_replace($db_object,$returncontent,$values,'');
		
		$mysql = "select count(rater_labelno)as count_label from $raterlabelrelate_table where rater_type='i'";
		$countlabel_arr = $db_object->get_a_line($mysql);
		
		
//no of labels counted for the skills left to sort task...		
		
		$count_label = $countlabel_arr['count_label'];
		
//to display the label names in the page...

		
		for($i=0;$i<$count_label;$i++)
		{

			$labelname = $labelname_arr[$i]["rater_level_$default"];

			$labelname = preg_replace("/\"/s",'&#34;',$labelname);
			
			$labelid = $labelname_arr[$i]["rater_label_no"];
						
		
			$count_array[$labelid] .= $remaining_skills;

//displays the contents of the labels text boxes...
			
			$mysql = "select skill_id from $textqsort_table where rated_user = '$rated_user_id' and rater_id = '$user_id' and rater_label_no = '$labelid'";
			//echo "$mysql<br>";
			$skills_arr = $db_object->get_single_column($mysql);
		
			$skills_arr = @array_unique($skills_arr);
			
			if($skills_arr != "")
			{
			$each_count[$labelid] = count($skills_arr);
			}
			else
			{
				$each_count[$labelid] = 0;
			}
			//print_r($each_count);
			
			$mysql = "select left_out from $temptextqsort_table where label_id = '$labelid' and user_rated = '$rated_user_id' and rating_user = '$user_id'";
			//echo "$mysql<br>";
			$max_alloted_arr = $db_object->get_a_line($mysql);
			
			$max_alloted = $max_alloted_arr["left_out"];
			
			
			
			if($clicked != 'true')
			{
				
				$each_count[$labelid] = 0;
						
			
			}
			if($clicked == 'true')
			{
			if($each_count[$labelid] == $max_alloted)
			{

				$disable_label_button = "disabled";
				
			}
			
			else
			{
				$disable_label_button = "";
			}
			}
	
			if($clicked != 'true')
			{
				$rem_val_each = $my_array[$labelid]- $count_cur_skills;

			}

			if(($clicked == 'true') || ($deselect == '1'))
			{
			
			$rem_val= "rem_val_each"."_".$labelid;
			$val = $$rem_val;
		

			$mysql = "select $val-count(*) as rem_each_skill from $textqsort_table where rater_label_no = '$labelid' and rater_id = '$user_id' and rated_user = '$rated_user_id'";
			
			$rem_arr = $db_object->get_a_line($mysql);
	
			$rem_val_each = $rem_arr['rem_each_skill'];
		
			}

		
		
		
		$remaining_skills = $rem_val_each;


			if($clicked != 'true')
			{
			
			$each_remaining[$labelid] = $remaining_skills;
		
			}
			if(($clicked == 'true') ||($deselect == 1))
			{
				$rem_val= "rem_val_each"."_".$labelid;
			
				$val = $$rem_val;
				$each_remaining[$labelid] = $val;
				
			}

		$rem_each_skill = $remaining_skills;
	
		$iscomplete[]  = $rem_each_skill;


//decrementing of the no of piles for each label...

			if (count($skills_arr) > 0)
			{
				$skills_all = @implode("','",$skills_arr);
			}
	
		
		//$mysql = "select skill_id,skill_name from $skills_table where skill_id in('$skills_all')";
			
//*********************************************

		$mysql = "select skills.skill_id,skill_name 
			from skills_for_rating,skills
			where skills.skill_id = skills_for_rating.skill_id
			and skills_for_rating.skill_type='i'
			and skills_for_rating.usr_id = '$rated_user_id'
			and skills_for_rating.skill_id in('$skills_all')";	
		$skillinlabels_arr = $db_object->get_rsltset($mysql);
		
			
		
		$values	= array();
		$values['skillinlabels_loop'] = $skillinlabels_arr;
		$replace	= $common->multipleloop_replace($db_object,$replace1,$values,'');

		$replaced .= preg_replace("/<{(.*?)}>/e","$$1",$replace);

			
		}

	$returncontent=preg_replace("/<{labeldisplay_loopstart}>(.*?)<{labeldisplay_loopend}>/s",$replaced,$returncontent);
	$count=0;
		for($i=0;$i<count($iscomplete);$i++)
		{
			$check = $iscomplete[$i];
			if($check == 0)
			{
				$count++;
				
				if($count == count($iscomplete))
				{
					$disabled = "";
				}
				else
				{
					$disabled = "disabled";
				}
			}
			
			
		}
		$values['disabled'] = $disabled;
		

	preg_match("/<{remainingskills_loopstart}>(.*?)<{remainingskills_loopend}>/s",$returncontent,$matched_rem);
	$replace_rem=$matched_rem[1];
	$temp_val = array();

//$temptextqsort_table = $common->prefix_table('temp_textqsort');

if($clicked != 'true')
{

$mysql = "delete from $temptextqsort_table where user_rated = '$rated_user_id' and rating_user='$user_id'";

$db_object->insert($mysql);
}

		//print_r($each_remaining);
		//for($i=1;$i<=count($each_remaining);$i++)

		while(list($kk,$vv) = @each($each_remaining))
		{
			//$rem_val_each = $each_remaining[$kk];
			
			$rem_val_each = $vv;
			
			//echo "key is $kk and val is $vv<br>";
			//echo $vv;
			//$lab_id[] = $kk;
			
			$temp_val[]	= "rem_val_each_$kk=".$each_remaining[$kk];

			if($clicked != 'true')
			{
			$mysql = "insert into $temptextqsort_table set user_rated='$rated_user_id',label_id = '$kk',left_out = '$rem_val_each' , rating_user = '$user_id'";

			//echo "$mysql<br>";//exit;

			$db_object->insert($mysql);

			}
			
			$replaced_rem .= preg_replace("/<{(.*?)}>/e","$$1",$replace_rem);
		
		}
		

		
		$lab_id = @implode(",",$lab_id);
		
		$temp_val	= implode("&",$temp_val);
		
		
		$values['temp_val'] = $temp_val;
		$values['lab_id'] = $lab_id;

	$returncontent=preg_replace("/<{remainingskills_loopstart}>(.*?)<{remainingskills_loopend}>/s",$replaced_rem,$returncontent);

//direct replace values...
	
		$values["rated_username"] = $rated_username;
		$values["rem_val_each"]=$rem_val_each;
		$values["count_full"] 	= $count_full;
		$values["rated_user_id"] = $rated_user_id;
	
		
		$returncontent = $common->direct_replace($db_object,$returncontent,$values);

		echo $returncontent;

		
		
	}//end of function show_textonly_screen
	
	function add_skills($db_object,$common,$post_var,$user_id)
	{
		while(list($kk,$vv)=@each($post_var))
		{
		$$kk=$vv;
			if(ereg("^fMoveskill_",$kk))
			{

				$label_id = ereg_replace("fMoveskill_","",$kk);
		
			}
		}
		
		$textqsort_table = $common->prefix_table('textqsort_rating');
		
		$temptextqsort_table = $common->prefix_table('temp_textqsort');
		
		$mysql = "select left_out from $temptextqsort_table where user_rated='$rated_user_id' and rating_user = '$user_id'and label_id='$label_id'";
		//echo $mysql;
		$max_arr = $db_object->get_a_line($mysql);
		$max_limit = $max_arr['left_out'];
		
		
		
		for($i=0;$i<count($fullskills);$i++)
		{
			
			
		$mysql = "select count(*)+1 as count from $textqsort_table where rated_user='$rated_user_id' and rater_label_no = '$label_id' and rater_id = '$user_id'";
		//echo $mysql;
		$count_each_arr = $db_object->get_a_line($mysql);
		//print_r($count_each_arr);
		
		$count = $count_each_arr['count'];
			
			
			if($count <=$max_limit)
			{
			$skills_chosen = $fullskills[$i];
		
	//----------------------------		
			$rater_label_relate = $common->prefix_table('rater_label_relate');
			$skill_raters 	  = $common->prefix_table('skill_raters');
			
			$mysql = "select $rater_label_relate.rater_labelno 
					from $rater_label_relate,$skill_raters
					where $rater_label_relate.rater_id = $skill_raters.rater_id
					and $skill_raters.type_name = 'd'";
			$dna_label_arr = $db_object->get_a_line($mysql);

			
			$dna_labelid  = $dna_label_arr['rater_labelno'];
			
			if($label_id == $dna_labelid)
			{
				$label_id = 0;
			}
	//----------------------------
			$mysql = "select skill_id from $textqsort_table where rated_user = '$rated_user_id' and rater_id='$user_id' and skill_id in ('$skills_chosen')";

			$check_prev_existence_arr = $db_object->get_a_line($mysql);
				
			if($check_prev_existence_arr =='')
				{
				$mysql = "insert into $textqsort_table set rater_label_no = '$label_id',rater_id='$user_id',rater_type = 'i',rated_user = '$rated_user_id',skill_id = '$skills_chosen'";
				$db_object->insert($mysql);
				}
			}
		
		}
	}

		
	function remove_skills($db_object,$common,$post_var,$user_id)
	{
		while(list($kk,$vv)=@each($post_var))
		{
		$$kk=$vv;
		}
		
		$textqsort_table = $common->prefix_table('textqsort_rating');
		
		$mysql = "delete from $textqsort_table where rated_user = '$rated_user_id' and rater_id = '$user_id'and skill_id = '$skill_id'";
		$db_object->insert($mysql);
	}
		

}

$obj = new textOnlySort;

$label_id = "";

while(list($kk,$vv)=@each($post_var))
	{
	$$kk=$vv;
		if(ereg("^fMoveskill_",$kk))
		{
		
		$obj->add_skills($db_object,$common,$post_var,$user_id);
		}
	}

if($deselect == 1)
{

	$obj->remove_skills($db_object,$common,$post_var,$user_id);
}

if($fSubmit)
{


	$other_raters = $common->prefix_table('other_raters');

	$mysql = "update $other_raters set rating_over = 'y',date_rating_over=now() where rater_userid = '$user_id' and cur_userid = '$rated_user_id'";
	
	$db_object->insert($mysql);
	
	$message = $error_msg['cTextqsort_thanks'];
	echo $message;
	
	include_once('footer.php');
	exit;
}

$obj->show_textonly_screen($db_object,$common,$post_var,$gbl_skill_categories,$user_id,$default);

include_once("footer.php");
?>
