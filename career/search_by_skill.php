<?php
/*---------------------------------------------
SCRIPT:search_by_skill.php
AUTHOR:info@chrisranjana.com	
UPDATED:6 Jan

DESCRIPTION:
This script displays all skills comparision of employees for the ADMIN view

---------------------------------------------*/
include("../session.php");
include("header.php");

class search
{
	function show_form($db_object,$common,$user_id,$default)
	{
		
		$path=$common->path;
		$xtemplate=$path."templates/career/search_by_skill.html";
		$file=$common->return_file_content($db_object,$xtemplate);

		$skills			= $common->prefix_table("skills");
		$rater_label_relate	= $common->prefix_table("rater_label_relate");
		$skill_raters		= $common->prefix_table("skill_raters");
		

		$selqry="select skill_id,skill_name from $skills where skill_type='t'";
		$selres=$db_object->get_rsltset($selqry);
		
		$selqry1="select skill_id,skill_name from $skills where skill_type='i'";
		$selres1=$db_object->get_rsltset($selqry1);
		
		$selqry2="select rater_labelno,rater_level_$default from $rater_label_relate,$skill_raters where
				$rater_label_relate.rater_id = $skill_raters.rater_id and rater_type = 'i'";
		$selres2=$db_object->get_rsltset($selqry2);
		
		$selqry3="select rater_labelno,rater_level_$default from $rater_label_relate,$skill_raters where
				$rater_label_relate.rater_id = $skill_raters.rater_id and rater_type = 't'";
		$selres3=$db_object->get_rsltset($selqry3);
		
		preg_match("/<{skill_loopstart}>(.*?)<{skill_loopend}>/s",$file,$match);
		
		$match=$match[0];
		
		for($i=0;$i<count($selres);$i++)
		{
			$skill_name=$selres[$i][skill_name];
			
			$skill_id=$selres[$i][skill_id];
			
			$str.=preg_replace("/<{(.*?)}>/e","$$1",$match);
		}
		
		$file=preg_replace("/<{skill_loopstart}>(.*?)<{skill_loopend}>/s",$str,$file);
		
		preg_match("/<{skill1_loopstart}>(.*?)<{skill1_loopend}>/s",$file,$match1);
		
		$match1=$match1[0];
		
		for($i=0;$i<count($selres1);$i++)
		{
			$skill_name=$selres1[$i][skill_name];
			
			$skill_id=$selres1[$i][skill_id];
			
			$str1.=preg_replace("/<{(.*?)}>/e","$$1",$match);
		}
		
		$file=preg_replace("/<{skill1_loopstart}>(.*?)<{skill1_loopend}>/s",$str1,$file);
		
		preg_match("/<{comp_loopstart}>(.*?)<{comp_loopend}>/s",$file,$match2);
		
		$match2=$match2[0];
		
		for($i=0;$i<count($selres2);$i++)
		{
			$field="rater_level_$default";
			
			$rater_labelno=$selres2[$i][rater_labelno];
						
			$rater_level=$selres2[$i][$field];
						
			$str2.=preg_replace("/<{(.*?)}>/e","$$1",$match2);
		}
		
		$file=preg_replace("/<{comp_loopstart}>(.*?)<{comp_loopend}>/s",$str2,$file);
		
		preg_match("/<{comp1_loopstart}>(.*?)<{comp1_loopend}>/s",$file,$match3);
		
		$match3=$match3[0];
		
		for($i=0;$i<count($selres3);$i++)
		{
			$field="rater_level_$default";
			
			$rater_labelno=$selres3[$i][rater_labelno];
			
			$rater_level=$selres3[$i][$field];
			
			$str3.=preg_replace("/<{(.*?)}>/e","$$1",$match3);
		}
		
		$file=preg_replace("/<{comp1_loopstart}>(.*?)<{comp1_loopend}>/s",$str3,$file);
		
		$file=$common->direct_replace($db_object,$file,$xArray);
		
		echo $file;
	}
	
	function search_reports($db_object,$common,$user_id,$post_var,$error_msg)
	{
				
		$fSkill_id_t=$post_var[fSkill_id_t];
		
		$fSkill_id_i=$post_var[fSkill_id_i];
		
		$fComp_id_t=$post_var[fComp_id_t];
		
		$fComp_id_i=$post_var[fComp_id_i];
				
		$textqsort_rating	= $common->prefix_table("textqsort_rating");
		$other_raters_tech	= $common->prefix_table("other_raters_tech");
		$user_table 		= $common->prefix_table("user_table");
		
		$skills=$common->prefix_table("skills");
		
		//$user_below=$common->return_direct_reports($db_object,$user_id);
		
//SELECT THE USERS BELOW THIS ADMIN...
		$mysql = "select user_id from $user_table where admin_id = '$user_id'";
		$user_below = $db_object->get_single_column($mysql);

		$qry="select skill_id from $skills where skill_type='t'";
		$qry_res=$db_object->get_single_column($qry);
		
		$qry1="select skill_id from $skills where skill_type='i'";
		$qry_res1=$db_object->get_single_column($qry1);
		
		if($fSkill_id_t!="")
		{
			
			if(count($user_below)>1)
			{
				$userid=@implode(",",$user_below);
			
				$user="(".$userid.")";
			
				$user_clause=" and rated_user in $user";
			}
			if(count($user_below)==1)
			{
				$user="(".$user_below[0].")";
			
				$user_caluse=" and rated_user in $user";
			}
			
			if($fComp_id_t!="")
			{
								
				$comp_clause=" and label_id='$fComp_id_t'";
			}
			else
			{
				$comp_clause=="";
			}
			
			$res_qry="select * from $other_raters_tech where skill_id='$fSkill_id_t'".$user_clause.$comp_clause." group by rated_user";
			
			$result=$db_object->get_rsltset($res_qry);
			

		}
		else
		{
			if(count($user_below)>1)
			{
				$userid=@implode(",",$user_below);
			
				$user="(".$userid.")";
			
				$user_clause=" and rated_user in $user";
			}
			if(count($user_below)==1)
			{
				$user="(".$user_below[0].")";
			
				$user_clause=" and rated_user in $user";
			}
			
			if($fComp_id_i!="")
			{
								
				$comp_clause=" and rater_label_no='$fComp_id_i'";
			}
			else
			{
				$comp_clause=="";
			}
			
			$res_qry="select * from $textqsort_rating where skill_id='$fSkill_id_i'".$user_clause.$comp_clause." group by rated_user";
			
			$result=$db_object->get_rsltset($res_qry);
			
			
		}
		
		if($result[0]=="")
		{
			echo $error_msg['cNoResult'];
			
			include_once("footer.php");
			
			exit;
		}
		$xpath=$common->path;
		
		$xtemplate=$xpath."templates/career/search_reports_skill_result.html";
		$file=$common->return_file_content($db_object,$xtemplate);
		
		preg_match("/<{user_loopstart}>(.*?)<{user_loopend}>/s",$file,$match);
		
		$match=$match[0];
	
		for($i=0;$i<count($result);$i++)
		{
			$user_id=$result[$i][rated_user];

			$username=$common->name_display($db_object,$user_id);
			
			$str.=preg_replace("/<{(.*?)}>/e","$$1",$match);
		}
		$file=preg_replace("/<{user_loopstart}>(.*?)<{user_loopend}>/s",$str,$file);
		
		$file=$common->direct_replace($db_object,$file,$xArray);
		
		echo $file;
		
	}
}
$obj=new search();

if($fSubmit)
{
	$action="search";
}

switch ($action)
{

	case NULL:
	
	$obj->show_form($db_object,$common,$user_id,$default);
	
	break;

	case "search":
	
	$obj->search_reports($db_object,$common,$user_id,$post_var,$error_msg);
	
	break;
}
include_once("footer.php");	
?>
