<?php
include_once("../session.php");
class language
{
function delete_language($db_object,$common,$default,$error_msg,$lang_id)
{

if($lang_id==1)
{
	return;
}

//table prefix

$emp_type=$common->prefix_table("employment_type");
$language=$common->prefix_table("language");
$acc_rights = $common->prefix_table("access_rights");
$contacts = $common->prefix_table("contacts");
$dev_basic = $common->prefix_table("dev_basic");
$dev_interbasic = $common->prefix_table("dev_interbasic");
$temp_devbuilder = $common->prefix_table("temp_devbuilder");
$unapproved_devbuilder = $common->prefix_table("unapproved_devbuilder");
$approved_devbuilder = $common->prefix_table("approved_devbuilder");
$temp_cat = $common->prefix_table("temp_category");
$unapp_cat = $common->prefix_table("unapproved_category");
$app_cat = $common->prefix_table("approved_category");
$temp_met = $common->prefix_table("temp_metrics");
$unapp_met = $common->prefix_table("unapproved_metrics");
$app_met = $common->prefix_table("approved_metrics");
$message = $common->prefix_table("performance_message");
$name = $common->prefix_table("name_fields");
$reject = $common->prefix_table("rejected_category");
$ureject = $common->prefix_table("unapproved_rejected_category");
$areject = $common->prefix_table("approved_rejected_category");
$skillraters_table = $common->prefix_table('skill_raters');
$selected = $common->prefix_table("temp_selected_objective");
$uselected = $common->prefix_table("unapproved_selected_objective");
$aselected = $common->prefix_table("approved_selected_objective");
$qualify = $common->prefix_table("qualification");
$rating = $common->prefix_table("rating");
$priority = $common->prefix_table("priority");
$owner = $common->prefix_table("owner_defined_text");
$langsetting = $common->prefix_table("learning_settings");
$langresult = $common->prefix_table("learning_result");
//end prefix


$mysql="delete from $language where lang_id='$lang_id'";
$db_object->insert($mysql);

//table name and its field to be deleted
$field_array = array(
	"$language"=>array("lang_$lang_id"),
	"$emp_type"=>array("type_$lang_id"),
	"$acc_rights"=>array("type_$lang_id"),
	"$contacts"=>array("contact_display_$lang_id"),
	"$dev_basic"=>array("coursetype_$lang_id"),
	"$dev_interbasic"=>array("coursename_$lang_id"),	
	"$temp_devbuilder"=>array("title_$lang_id","description_$lang_id","url_$lang_id"),
	"$unapproved_devbuilder"=>array("title_$lang_id","description_$lang_id","url_$lang_id"),
	"$approved_devbuilder"=>array("title_$lang_id","description_$lang_id","url_$lang_id"),
	"$temp_cat"=>array("category_$lang_id"),
	"$unapp_cat"=>array("category_$lang_id"),
	"$app_cat"=>array("category_$lang_id"),
	"$temp_met"=>array("metrics_$lang_id"),
	"$unapp_met"=>array("metrics_$lang_id"),
	"$app_met"=>array("metrics_$lang_id"),
	"$message"=>array("appsub_subject_$lang_id","appsub_message_$lang_id","approved_subject_$lang_id",
				"approved_message_$lang_id","resubmit_subject_$lang_id",
				"resubmit_message_$lang_id","reject_subject_$lang_id","reject_message_$lang_id",
				"obj_message_$lang_id","obj_subject_$lang_id","obj_app_subject_$lang_id",
				"obj_app_message_$lang_id","verification_submit_sub_$lang_id","verification_submit_message_$lang_id",
				"verification_rej_sub_$lang_id","verification_rej_message_$lang_id","verification_remind_sub_$lang_id",
				"verification_remind_message_$lang_id","app_feedback_subject_$lang_id","$app_feedback_message_$lang_id"),
	"$name"=>array("name_$lang_id"),
	"$reject"=>array("category_$lang_id"),
	"$ureject"=>array("category_$lang_id"),
	"$areject"=>array("category_$lang_id"),
	"$skillraters_table"=>array("rater_level_$lang_id"),
	"$selected"=>array("objective_$lang_id","how_to_get_$lang_id"),
	"$uselected"=>array("objective_$lang_id","how_to_get_$lang_id"),
	"$aselected"=>array("objective_$lang_id","how_to_get_$lang_id"),
	"$qualify"=>array("qualification_$lang_id"),
	"$rating"=>array("rating_$lang_id"),
	"$priority"=>array("priority_$lang_id"),
	"$owner"=>array("text_$lang_id"),
	"$langsetting"=>array("requesttext_$lang_id","approvaltext_$lang_id"),
	"$langresult"=>array("result_$lang_id")
	);

//table name
$table_array = array("$language","$emp_type","$acc_rights","$contacts","$dev_basic",
			"$dev_interbasic","$temp_devbuilder","$unapproved_devbuilder",
			"$approved_devbuilder","$temp_cat","$unapp_cat","$app_cat","$temp_met",
			"$unapp_met","$message","$name","$reject","$ureject","$areject",
			"$skillraters_table","$selected","$uselected","$aselected","$qualify","$rating",
			"$priority","$owner","$langsetting","$langresult"
			);



for($i=0;$i<count($table_array);$i++)
{
	$sql="";
		$tablename = $table_array[$i];			
		$qry = "desc $tablename";
		$res = $db_object->get_single_column($qry);

		for($j=0;$j<count($field_array[$tablename]);$j++)
		{
				$field = $field_array[$tablename][$j];
				//echo "field = $field<br>";
				if(in_array($field,$res))
				{
					$sql.="drop column $field,";
										
				}

		}		
		if($sql!="")
		{
			$sql1 = substr($sql,0,-1);
			$qry = "alter table $tablename $sql1";			
			$db_object->insert($qry);
		}
					
}



$path=$common->path;
$folder_name=$path."/lang/$lang_id";
$common->delete_files($folder_name,$folder_name);

}

//----------------------------------------------------------------

}

$obj=new language;
$obj->delete_language($db_object,$common,$default,$error_msg,$fLang_id);
header("Location:view_lang.php");

?>
