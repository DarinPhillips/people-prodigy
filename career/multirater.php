<?php
/*---------------------------------------------
SCRIPT:multirater.php
AUTHOR:info@chrisranjana.com	
UPDATED:1th Oct

DESCRIPTION:
This script displays all the information for about the 360 mode of assessment.

---------------------------------------------*/
include("../session.php");
include_once("header.php");

class multiRater
{
	function show_rater_screen($db_object,$common,$post_var,$error_msg,$gbl_skill_categories,$gbl_grouprater_inter,$default)
	{
		while(list($kk,$vv)=@each($post_var))
		{

		$$kk=$vv;
	
		}
	
		$xPath=$common->path;
		$xTemplate=$xPath."/templates/career/multirater.html";
		$returncontent=$common->return_file_content($db_object,$xTemplate);
		
		$multirater_table = $common->prefix_table("multirater");
		$skillraters_table = $common->prefix_table("skill_raters");
		$ratercategory_table = $common->prefix_table("rater_category");
		$ratergroup_table = $common->prefix_table("rater_group");
		
	
//for interpersonal skills...	
		
	preg_match("/<{ratinglevel_loopstart}>(.*?)<{ratinglevel_loopend}>/s",$returncontent,$ratingmatch);
	$newratingmatch = $ratingmatch[1];
	
	preg_match("/<{language_loopstart}>(.*?)<{language_loopend}>/s",$returncontent,$langmatch);
	$newlangmatch = $langmatch[1];


	preg_match("/<{ratinglabel_loopstart}>(.*?)<{ratinglabel_loopend}>/s",$newlangmatch,$labelmatch);	
	$newlabelmatch = $labelmatch[1];

	$str = "";
	$str1 = "";
	$str2 = "";
	
	$level_no = $error_msg["level_no"];  //no of levels...  taken from lang...  
		
	$mysql = "select no_of_raters from $multirater_table where skill_id = 'i'";
	$arr = $db_object->get_a_line($mysql);
	$levelchecked = $arr['no_of_raters'];

	for($i=3;$i<=$level_no;$i++)
	{
		$checked  = "";
		
		$level = $i;
		if($level == $levelchecked)
		{
			$checked = "checked";
			$lev_che = $level;
			
		}
		
		
		
		$str .= preg_replace("/<{(.*?)}>/e","$$1",$newratingmatch);
		
	}
	$returncontent = preg_replace("/<{ratinglevel_loopstart}>(.*?)<{ratinglevel_loopend}>/s",$str,$returncontent);

	
	$mysql = "select multirater_id from $multirater_table where skill_id = 'i'";
	$id_arr = $db_object->get_a_line($mysql);
	$multirater_id = $id_arr['multirater_id'];
	
	$mysql = "desc $skillraters_table";
	$fields_arr = $db_object->get_single_column($mysql);

	while(list($kk,$vv)=@each($fields_arr))
	{
	$$kk=$vv;
	
	if(ereg("^rater_level_",$vv))
		{
		$raterlevel_arr[$vv] = $vv;
		
		
		}
	}


	if($fDoesntapplyi == 'yes')
	{
		$yeschecked = 'checked';
		
	}
	else
	{
		$nochecked = 'checked';
	}

	$raterlevel_keys = @array_keys($raterlevel_arr);
	
		
	$yeschecked='';
	$nochecked='';
	
//----------------lang change
	$lang_table = $common->prefix_table("language");
	$desc = "desc $lang_table";
	$res = $db_object->get_single_column($desc);
	//print_r($res);

	$qry = "select lang_id,lang_$default from $lang_table ";
	$res = $db_object->get_rsltset($qry);
	
	//print_r($res);

//----------------lang change end
			

$temp_lang = array();




	for($i=0;$i<count($res);$i++)
			{
				
				$lang_id = $res[$i][0];

				$language = $res[$i][1];
	
		$temp_lang[]	= $lang_id;
		
		$levelkey = $raterlevel_keys[$i];

		list($un,$uid,$lid)=split("_",$levelkey);
	
		//echo "level_no $lid<br>";
		$mysql = "select lang_id,lang_$lid from $lang_table";
		$lang_arr = $db_object->get_a_line($mysql);
		//print_r($lang_arr);
	
		$mysql = "select rater_level_$lid from $skillraters_table where multirater_id = '$multirater_id' and type_name <>'d'";
		//echo "$mysql<br>";
			
		$level_arr = $db_object->get_single_column($mysql);
		//print_r($level_arr);

		$mysql = "select count(*) as count from $skillraters_table where multirater_id = '$multirater_id'";
		//echo $mysql;
		$count_arr = $db_object->get_a_line($mysql);
		$count_levels = $count_arr['count'];
		
		//echo $count_levels;


$mysql = "select rater_level_$lid from $skillraters_table where multirater_id = '$multirater_id' and type_name = 'd'";
		//echo "$mysql<br>";
			
		$level_doesnt_arr = $db_object->get_single_column($mysql);
		
		
$str1 = '';
		for($j=0;$j<$level_no;$j++)
		{
		
			$labelno = $j + 1;
			
		 	$label_val = $level_arr[$j];
			
			$label_val = preg_replace("/\"/s",'&#34;',$label_val);


			$label_notapp = $level_doesnt_arr[0];

			if($label_notapp != '')
			{
				$dis_not = '';
				$yeschecked = 'checked';
			}
			else
			{
				$dis_not = 'disabled';
				$nochecked = 'checked';
			}
			
			if($j+1>$levelchecked)
			{
				$dis = 'disabled';
			}
			else
			{
				$dis = "";
			}

		 		
			$str1 .=preg_replace("/<{(.*?)}>/e","$$1",$newlabelmatch);


			
				
		}


		
		$temp	= preg_replace("/<{ratinglabel_loopstart}>(.*?)<{ratinglabel_loopend}>/s",$str1,$newlangmatch);


$str2 .=preg_replace("/<{(.*?)}>/e","$$1",$temp);		
			
			
	}
//echo $str2;
//print_r($temp_lang);
	$temp_lang = @implode(",",$temp_lang);
	$values['temp_lang'] = $temp_lang;

	
		/*if($yeschecked == "")
		{
			$nochecked = "checked";
		}*/
		
		$values["yeschecked"] = $yeschecked;
		$values["nochecked"] = $nochecked;	
		//echo $str1;exit;
		$returncontent = preg_replace("/<{language_loopstart}>(.*?)<{language_loopend}>/s",$str2,$returncontent);
		
		$returncontent = preg_replace("/<{ratinglabel_loopstart}>(.*?)<{ratinglabel_loopend}>/s",$str1,$returncontent);

		
	
//replacing the category loop
$mysql = "select category_name from $ratercategory_table";
$catsel_arr = $db_object->get_single_column($mysql);

		
		$loopstart = "<{ratingcategory_loopstart}>";
		$loopend = "<{ratingcategory_loopend}>";
		$returncontent	= $common->pulldown_replace_multiple($db_object,$loopstart,$loopend,$returncontent,$gbl_skill_categories,$catsel_arr);
	
//replacing the group names loop
		
$mysql = "select rater_group_name from $ratergroup_table";
$grpsel_arr = $db_object->get_single_column($mysql);

		$loopstart = "<{ratinggroup_loopstart}>";
		$loopend = "<{ratinggroup_loopend}>";
		$returncontent = $common->pulldown_replace_multiple($db_object,$loopstart,$loopend,$returncontent,$gbl_grouprater_inter,$grpsel_arr);
	

//for technical skills...
		
$mysql = "select no_of_raters from $multirater_table where skill_id = 't'";
$arr = $db_object->get_a_line($mysql);
$levelchecked = $arr['no_of_raters'];		
		
	preg_match("/<{ratingtech_loopstart}>(.*?)<{ratingtech_loopend}>/s",$returncontent,$techmatch);
	$newtechmatch = $techmatch[1];
	

	
	
		for($i=3;$i<=$level_no;$i++)
		{
			$checked = "";
			$level = $i;
			if($level == $levelchecked)
			{
				$checked = "checked";
				$level_che = $level;
			}
			$tech .= preg_replace("/<{(.*?)}>/e","$$1",$newtechmatch);
			
		}
		$returncontent = preg_replace("/<{ratingtech_loopstart}>(.*?)<{ratingtech_loopend}>/s",$tech,$returncontent);
	
	
	preg_match("/<{langtech_loopstart}>(.*?)<{langtech_loopend}>/s",$returncontent,$langtechmatch);
	$newlangtechmatch = $langtechmatch[1];	
		
	preg_match("/<{techlabel_loopstart}>(.*?)<{techlabel_loopend}>/s",$newlangtechmatch,$labeltechmatch);
	$newlabeltechmatch = $labeltechmatch[1];

$mysql = "select multirater_id from $multirater_table where skill_id = 't'";
$id_arr = $db_object->get_a_line($mysql);
$multirater_id = $id_arr['multirater_id'];

$mysql = "select rater_level_$default from $skillraters_table where multirater_id = '$multirater_id'";
$level_arr = $db_object->get_single_column($mysql);

//-----
$temp_lang= array();
$tech2 = '';

	for($i=0;$i<count($res);$i++)
		{

		$lang_id = $res[$i][0];
		$language = $res[$i][1];	

	$levelkey = $raterlevel_keys[$i];
		
	list($un,$uid,$lid)=split("_",$levelkey);
	

	
	$mysql = "select rater_level_$lid from $skillraters_table where multirater_id = '$multirater_id'";

			
	$level_arr = $db_object->get_single_column($mysql);

	$tech1='';
	for($j=0;$j<$level_no;$j++)
		{
			
			$labelno = $j + 1;
			$labelval = $level_arr[$j];
			$labelval = preg_replace("/\"/s",'&#34;',$labelval);
			
			if($j+1>$levelchecked)
			{
				$dis = 'disabled';
			}
			else
			{
				$dis = "";
			}
		
			$tech1 .=preg_replace("/<{(.*?)}>/e","$$1",$newlabeltechmatch);
			
		}
		
	
		
		$temp1	= preg_replace("/<{techlabel_loopstart}>(.*?)<{techlabel_loopend}>/s",$tech1,$langtechmatch);


		$tech2 .=preg_replace("/<{(.*?)}>/e","$$1",$temp1[0]);
		
		
		
	}
	
	
		$returncontent = preg_replace("/<{techlabel_loopstart}>(.*?)<{techlabel_loopend}>/s",$tech1,$returncontent);
		$returncontent = preg_replace("/<{langtech_loopstart}>(.*?)<{langtech_loopend}>/s",$tech2,$returncontent);
		
		
		$returncontent = $common->direct_replace($db_object,$returncontent,$values);
				
		echo $returncontent;
		
	}  //=====function show_rater_screen() ends.
	
	function store_raterValues($db_object,$common,$post_var,$gbl_skill_categories,$gbl_grouprater_inter,$default)
	{
		 
		while(list($kk,$vv)=@each($post_var))
		{
		$$kk=$vv;
		
		//echo "key is $kk and val is $vv<br>";
		
		if(ereg("^rating_level_",$kk))
				{
					if($vv !="")
					{
				$l_qid=ereg_replace("rating_level_","",$kk);
				$level_array["$l_qid"] = $vv;
					}
				}
		
		if(ereg("^rating_label_",$kk))
			{
				if($vv !="")
				{
				$label_qid = ereg_replace("rating_label_","",$kk);
			
				list($typeid,$labid,$langid)=split("_",$label_qid);
			
				$label_array[$typeid][$labid][$langid] =  $vv;
						
				}
			}
		
		if(ereg("^rating_category_",$kk))
			{
				if($vv!="")
				{
				$cat_qid = ereg_replace("rating_category_","",$kk);
				$cat_array[] = $vv; //key was $cat_qid
				}
			
			}
		if(ereg("^rating_group_",$kk))
			{
				if($vv!="")
				{
				$group_array[] = $vv;
				}
			}
		if(ereg("^label_",$kk))
			{
				if($vv!="")
				{
					$label_doesnt[] = $vv;
				}
			}
		}
		
		//print_r($group_array);
		//print_r($cat_array);
		//print_r($label_array);exit;
		//print_r($level_array);
		//print_r($post_var);
		//print_r($label_doesnt);
		
		$c =0;
		$once =0;
		$multirater_table = $common->prefix_table("multirater");
		$skillraters_table = $common->prefix_table("skill_raters");
		$ratercategory_table = $common->prefix_table("rater_category");
		$ratergroup_table = $common->prefix_table("rater_group");
		$raterlabelrelate_table = $common->prefix_table("rater_label_relate");
		
	//storing  rater levels ie 3 - 7	
		
		while(list($kk,$vv)=@each($level_array))
		{
			//echo "$kk and $vv<br>";
		
			//delete any previous existence of the skill rater levels...
			
			$mysql = "delete from $multirater_table where skill_id='$kk'";
			$db_object->insert($mysql);
	
			//insert the latest skill rater values...
			
			$mysql = "insert into $multirater_table set skill_id='$kk' , no_of_raters='$vv'";
			//echo $mysql;
			$multirater_id = $db_object->insert_data_id($mysql);
		
			$skill_id[] = $kk;
			//echo $multirater_id;
		
		
		}

		//delete any previous existence of rater levels in skill_raters table
		$mysql = "delete from $skillraters_table";
		$db_object->insert($mysql);
		
		//delete any previous existence of raterlabelrelate values...
		$mysql = "delete from $raterlabelrelate_table";
		$db_object->insert($mysql);
		
	//storing  label names 

		//print_r($label_array);

		while(list($key,$val) = @each($label_array))
		{
			
			
			$label_array1 = $label_array[$key];
			
			$skill_id = $key;
			
			$mysql = "select multirater_id from $multirater_table where skill_id = '$skill_id' order by multirater_id";

			$id_arr = $db_object->get_a_line($mysql);

			$multirater_id = $id_arr["multirater_id"];
			
			
			while(list($key1,$val1) = @each($label_array1))
			{
					
			
				
				$label_array2 = $label_array1[$key1];
				
				
				$setclause = '';
				$newclause = '';
				while(list($key2,$val2) = @each($label_array2))
				{
					
					
					
					
					$label_lang = $label_array2[$key2];
					
					$setclause .= "rater_level_$key2 = '$label_lang',";
					
					
				}
				$setclause=substr($setclause,0,-1);
			//====
					if($key1 == 'doesnt')
					{
					$mysql = "insert into $skillraters_table set multirater_id='$multirater_id', $setclause , type_name='d',skill_type='$skill_id'";
				
					$data_id[] = $db_object->insert_data_id($mysql);
				
					}
					else
					{
					$mysql = "insert into $skillraters_table set multirater_id='$multirater_id', $setclause , type_name='n',skill_type='$skill_id'";
				
					$data_id[] = $db_object->insert_data_id($mysql);
					}
				
		

		

			}
		}

	

		@reset($level_array);
		$z=0;


		$mysql = "select rater_id , skill_type from $skillraters_table";  // 
		//echo $mysql;
		$raterlabel_arr = $db_object->get_rsltset($mysql);

/*-------
		$mysql = "select rater_id , skill_type from $skillraters_table where type_name = 'd'";
		$raterlabeld_arr = $db_object->get_a_line($mysql);
		
		//print_r($raterlabeld_arr);
		
		if($raterlabeld_arr != '')
		{
			$rater_id = $raterlabeld_arr[0];
			$mysql = "insert into $raterlabelrelate_table set rater_id = '$rater_id',rater_labelno = '0',rater_type = 'i'";
			//echo $mysql;
			$db_object->insert($mysql);
		}
------------*/		
//print_r($raterlabel_arr);
		$v = 1;

			
		for($i=0;$i<count($raterlabel_arr);$i++)
		{
			$rater_id = $raterlabel_arr[$i]['rater_id'];
			$skill_type = $raterlabel_arr[$i]['skill_type'];

			$mysql = "select count(*)as count from $skillraters_table where skill_type = '$skill_type' order by skill_type";
			//echo "$mysql<br>";
			$count_arr = $db_object->get_a_line($mysql);
			//print_r($count_arr);
			$count_skills = $count_arr['count'];
			
			
			if($v == $count_skills)
			{
			$mysql = "insert into $raterlabelrelate_table set rater_id = '$rater_id',rater_labelno = '$v',rater_type = '$skill_type'";
			//echo "<br>$mysql<br>";
			$db_object->insert($mysql);
			
			$v = 1;
			}
			else
			{
			$mysql = "insert into $raterlabelrelate_table set rater_id = '$rater_id',rater_labelno = '$v',rater_type = '$skill_type'";
			//echo "<br>$mysql<br>";
			$db_object->insert($mysql);
			$v++;
			
			
			}
			
			
	
			
		}

		
		
/*===========================================
===========================================*/
	//storing category names
		
	//since the category names are present only for inter skills the following code is hardcoded...
		
		$mysql = "select multirater_id from $multirater_table where skill_id = 'i'";
		$arr = $db_object->get_a_line($mysql);
		$multirater_id = $arr['multirater_id'];
		
		//delete any previous data if present in the category table and group table...
		
		$mysql = "delete from $ratercategory_table";
		$db_object->insert($mysql);
		$mysql = "delete from $ratergroup_table";
		$db_object->insert($mysql);

		
		for($i=0;$i<count($cat_array);$i++)
		
		{
			$catval = $cat_array[$i];
			
			@reset($gbl_skill_categories);
			while(list($gblkk,$gblvv) = @each($gbl_skill_categories))
			{
				if($catval == $gblkk)
				{
					$mysql = "insert into $ratercategory_table set multirater_id = '$multirater_id' , category_name = '$gblkk'";
					//echo $mysql;
					$db_object->insert($mysql);
				}
				
			}
		}
		
		for($j=0;$j<count($group_array);$j++)
		{
			$grpval = $group_array[$j];
			@reset($gbl_grouprater_inter);
			while(list($gblkk,$gblvv) = @each($gbl_grouprater_inter))
			{
				if($grpval == $gblkk)
				{
					$mysql = "insert into $ratergroup_table set multirater_id = '$multirater_id' , rater_group_name = '$gblkk'";
					//echo "$mysql<br>";
					$db_object->insert($mysql);
				}
			}
		}
		
	} //end of function store_raterValues()
	
	
		
		
	
	
}  //====class multiRater ends.

$obj = new multiRater;

//$post_var	= array_merge($_POST,$_GET);
if($fSubmit)
{
	//print_r($post_var);
	$obj->store_raterValues($db_object,$common,$post_var,$gbl_skill_categories,$gbl_grouprater_inter,$default);
	$message = $error_msg['multirater_saved'];
	echo $message;
	
	$obj->show_rater_screen($db_object,$common,$post_var,$error_msg,$gbl_skill_categories,$gbl_grouprater_inter,$default);

}
else
{
$obj->show_rater_screen($db_object,$common,$post_var,$error_msg,$gbl_skill_categories,$gbl_grouprater_inter,$default);
}
include_once("footer.php");
