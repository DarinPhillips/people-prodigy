<?php
/*---------------------------------------------
SCRIPT:model_detail.php
AUTHOR:info@chrisranjana.com	
UPDATED:6th Jan

DESCRIPTION:
This script displays the details of the models selected...

---------------------------------------------*/
include('../session.php');
include('header.php');

class modelDetails
{
function show_details($db_object,$common,$post_var,$user_id,$default)
	{
		while(list($kk,$vv) = @each($post_var))
		{
			$$kk = $vv;
		}
	$xPath=$common->path;
	$returncontent=$xPath."/templates/career/model_detail.html";
	$returncontent=$common->return_file_content($db_object,$returncontent);
	
	$model_name_table 	= $common->prefix_table('model_name_table');
	$model_skills		= $common->prefix_table('model_skills');
	$skills 		= $common->prefix_table('skills');
		
//MODEL NAME DISPLAY...
	$mysql = "select model_name from $model_name_table where model_id = '$m_id'";
	$modelname_arr = $db_object->get_a_line($mysql);
	$model_name = 	$modelname_arr['model_name'];
	$values['model_name'] = $model_name;

	
	$mysql = "select skill_id,level_chosen from $model_skills where model_id = '$m_id'";
	$model_data_arr = $db_object->get_rsltset($mysql);
	
//INTERPERSONAL SKILLS REPLACED
	
	
	
	preg_match("/<{interskills_loopstart}>(.*?)<{interskills_loopend}>/s",$returncontent,$matchold1);
	$match1_new = $matchold1[1];
	
	for($i=0;$i<count($model_data_arr);$i++)
	{
		$model_skillid = $model_data_arr[$i]['skill_id'];
		$model_level   = $model_data_arr[$i]['level_chosen'];
		
		$mysql = "select skill_type from $skills where skill_id = '$model_skillid'";
		$skilltype_arr = $db_object->get_a_line($mysql);
		$skilltype = $skilltype_arr['skill_type'];
		
		if($skilltype == 'i')
		{
		
		$mysql = "select skill_name from $skills where skill_id = '$model_skillid'";
		$skillname_arr = $db_object->get_a_line($mysql);
		$model_skillname = $skillname_arr['skill_name'];
		
		$str1 .= preg_replace("/<{(.*?)}>/e","$$1",$match1_new);
		}
	}	
	if($str1=="")
	{
		$values[interreplace]="<tr><td class=code colspan=2>{{cEmptyrecords}}</td></tr>";
	}
	$returncontent = preg_replace("/<{interskills_loopstart}>(.*?)<{interskills_loopend}>/s",$str1,$returncontent);
	
//TECHNICAL SKILLS REPLACED...
	preg_match("/<{techskills_loopstart}>(.*?)<{techskills_loopend}>/s",$returncontent,$matchold2);
	$match2_new = $matchold2[1];
	
	for($i=0;$i<count($model_data_arr);$i++)
	{
		$model_tskillid = $model_data_arr[$i]['skill_id'];
		$model_tlevel   = $model_data_arr[$i]['level_chosen'];
		
		$mysql = "select skill_type from $skills where skill_id = '$model_skillid'";
		$skilltype_arr = $db_object->get_a_line($mysql);
		$skilltype = $skilltype_arr['skill_type'];
		
		if($skilltype == 't')
		{
		
		$mysql = "select skill_name from $skills where skill_id = '$model_tskillid'";
		$skillname_arr = $db_object->get_a_line($mysql);
		$model_tskillname = $skillname_arr['skill_name'];
		
		$str2 .= preg_replace("/<{(.*?)}>/e","$$1",$match2_new);
		}
	}
	if($str2=="")
	{
		$values[techreplace]="<tr><td class=code colspan=2>{{cEmptyrecords}}</td></tr>";
	}
	
	$returncontent = preg_replace("/<{techskills_loopstart}>(.*?)<{techskills_loopend}>/s",$str2,$returncontent);
	
	


	$returncontent = $common->direct_replace($db_object,$returncontent,$values);	
		$returncontent = $common->direct_replace($db_object,$returncontent,$values);	
	echo $returncontent;
		
	}
}
$obj = new modelDetails;
$obj->show_details($db_object,$common,$post_var,$user_id,$default);
include('footer.php');
?>
