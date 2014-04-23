<?php
/*---------------------------------------------
SCRIPT:view_appraisal.php
AUTHOR:info@chrisranjana.com	
UPDATED:31th Oct

DESCRIPTION:
This script displays alert for seting the options for viewing the appraisal results.

---------------------------------------------*/
include("../session.php");
include_once("header.php");

class viewAppraisal
{
	function show_screen($db_object,$common,$user_id,$default,$gbl_skill_categories,$gbl_grouprater_inter,$post_var)
	{
		while(list($kk,$vv)=@each($post_var))
		{
		$$kk=$vv;
		}

				
		$xPath=$common->path;
		$returncontent=$xPath."/templates/career/view_appraisal.html";
		$returncontent=$common->return_file_content($db_object,$returncontent);
		
		$rater_category = $common->prefix_table('rater_category');
		$multirater = $common->prefix_table('multirater');
		$rater_group = $common->prefix_table('rater_group');
		$family = $common->prefix_table('family');
		
		preg_match("/<{category_loopstart}>(.*?)<{category_loopend}>/s",$returncontent,$match_cat);
		$matched_cat = $match_cat[1];
		
		preg_match("/<{group_loopstart}>(.*?)<{group_loopend}>/s",$returncontent,$match_grp);
		$matched_grp = $match_grp[1];
		
		if($fEmpl_id != '' && $fEmpl_id != 0)
		{

			$values['fEmpl_id'] = $fEmpl_id;
		}
		
		
//Categories Display....		
		$mysql = "select $rater_category.category_name from $rater_category,$multirater where $rater_category.multirater_id = $multirater.multirater_id and $multirater.skill_id = 'i'";
		
		$category_arr  = $db_object->get_single_column($mysql);


//Groups Display...
		
		$mysql = "select $rater_group.rater_group_name from $rater_group,$multirater where $rater_group.multirater_id = $multirater.multirater_id and $multirater.skill_id = 'i'";
		//echo $mysql;
		$group_arr  = $db_object->get_single_column($mysql);
		//print_r($group_arr);
		
		
		
		for($i=0;$i<count($category_arr);$i++)
		{
			$cat_id = $category_arr[$i];
			
			$cat_name = $gbl_skill_categories[$cat_id];
			
			$cat_all[] = "fCat_".$cat_id;
			
			$str .= preg_replace("/<{(.*?)}>/e","$$1",$matched_cat);
			
			
		}
		
	
		$returncontent = preg_replace("/<{category_loopstart}>(.*?)<{category_loopend}>/s",$str,$returncontent);
		
		for($j=0;$j<count($group_arr);$j++)
		{
			$grp_id = $group_arr[$j];
			
			$group_name = $gbl_grouprater_inter[$grp_id];
			
			$group_all[] = "grp_id_".$grp_id;
			
			$str1 .= preg_replace("/<{(.*?)}>/e","$$1",$matched_grp);
			
		}
		
		//print_r($group_all);
		//for showing javascript alert to select category...
		
		$grp_full1 = @implode("','",$group_all);
		$values['grp_full1'] = $grp_full1;
		
		$cat_full = @implode("','",$cat_all);
		$values['cat_full'] = $cat_full;
		
		
		
		$returncontent = preg_replace("/<{group_loopstart}>(.*?)<{group_loopend}>/s",$str1,$returncontent);

		//$position = $common->prefix_table('position');
		
		$mysql = "select family_id,family_name from $family";
		$fam_arr = $db_object->get_rsltset($mysql);
		//print_r($pos_arr);

		$fam_arr1	= $common->conv_2Darray($db_object,$fam_arr);

		$returncontent = $common->pulldown_replace($db_object,'<{family_loopstart}>','<{family_loopend}>',$returncontent,$fam_arr1,'');
			
					
		$returncontent = $common->direct_replace($db_object,$returncontent,$values);
		
		echo $returncontent;
		
		
	}
}
$obj = new viewAppraisal;

//$post_var  = @array_merge($_POST,$_GET);



$obj->show_screen($db_object,$common,$user_id,$default,$gbl_skill_categories,$gbl_grouprater_inter,$post_var);

include_once('footer.php');
?>
