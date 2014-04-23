<?php
include_once("../session.php");
class language
{
function delete_language($db_object,$common,$default,$error_msg,$lang_id)
{
$language=$common->prefix_table("language");

if($lang_id==1)
{
return;
}


$mysql="delete from $language where lang_id='$lang_id'";

$db_object->insert($mysql);

$mysql="alter table $language drop column lang_$lang_id ";

$db_object->insert($mysql);

$emp_type=$common->prefix_table("employment_type");
$mysql="alter table $emp_type drop column type_$lang_id ";
$db_object->insert($mysql);


$acc_rights = $common->prefix_table("access_rights");
$mysql = "alter table $acc_rights drop column type_$lang_id ";
$db_object->insert($mysql);

$contacts = $common->prefix_table("contacts");
$mysql = "alter table $contacts drop  column contact_display_$lang_id ";
$db_object->insert($mysql);

$dev_basic = $common->prefix_table("dev_basic");
$mysql = "alter table $dev_basic drop column coursetype_$lang_id ";
$db_object->insert($mysql);

$dev_interbasic = $common->prefix_table("dev_interbasic");
$mysql = "alter table $dev_interbasic drop column coursename_$lang_id ";
$db_object->insert($mysql);

$temp_devbuilder = $common->prefix_table("temp_devbuilder");
$mysql = "alter table $temp_devbuilder drop column title_$lang_id";
$db_object->insert($mysql);
$mysql = "alter table $temp_devbuilder drop column description_$lang_id";
$db_object->insert($mysql);
$mysql = "alter table $temp_devbuilder drop column url_$lang_id";
$db_object->insert($mysql);

$unapproved_devbuilder = $common->prefix_table("unapproved_devbuilder");
$mysql = "alter table $unapproved_devbuilder drop title_$lang_id,drop description_$lang_id, drop url_$lang_id ";
$db_object->insert($mysql);


$approved_devbuilder = $common->prefix_table("approved_devbuilder");
$mysql = "alter table $approved_devbuilder drop column title_$lang_id,drop description_$lang_id,drop url_$lang_id ";
$db_object->insert($mysql);


$temp_cat = $common->prefix_table("temp_category");
$qry = "alter table $temp_cat drop column category_$lang_id";
$db_object->insert($qry);


$temp_met = $common->prefix_table("temp_metrics");
$qry = "alter table $temp_met drop column metrics_$lang_id";
$db_object->insert($qry);

$unapp_cat = $common->prefix_table("unapproved_category");
$qry = "alter table $unapp_cat drop column category_$lang_id";
$db_object->insert($qry);

$unapp_met = $common->prefix_table("unapproved_metrics");
$qry = "alter table $unapp_met drop column metrics_$lang_id";
$db_object->insert($qry);

$app_cat = $common->prefix_table("approved_category");
$qry = "alter table $app_cat drop column category_$lang_id";
$db_object->insert($qry);


$app_met = $common->prefix_table("approved_metrics");
$qry = "alter table $app_met drop column metrics_$lang_id";
$db_object->insert($qry);

$message = $common->prefix_table("performance_message");
$qry = "alter table $message drop column appsub_subject_$lang_id ,drop column appsub_message_$lang_id ,
drop column approved_subject_$lang_id ,drop column approved_message_$lang_id ,drop column resubmit_subject_$lang_id ,
drop column resubmit_message_$lang_id";
$db_object->insert($qry);

$name = $common->prefix_table("name_fields");
$qry = "alter table $name drop column name_$lang_id";
$db_object->insert($qry);

$reject = $common->prefix_table("rejected_category");
$qry = "alter table $reject drop column category_$lang_id";
$db_object->insert($qry);

$ureject = $common->prefix_table("unapproved_rejected_category");
$qry = "alter table $ureject drop column category_$lang_id";
$db_object->insert($qry);

$areject = $common->prefix_table("approved_rejected_category");
$qry = "alter table $areject drop column category_$lang_id";
$db_object->insert($qry);

$skillraters_table = $common->prefix_table('skill_raters');
$qry = "alter table $skillraters_table drop column rater_level_$lang_id";
$db_object->insert($qry);

$selected = $common->prefix_table("temp_selected_objective");
$qry = "alter table $selected drop column objective_$lang_id,
		drop column how_to_get_$lang_id ";
$db_object->insert($qry);

$uselected = $common->prefix_table("unapproved_selected_objective");
$qry = "alter table $uselected drop column objective_$lang_id ,drop column how_to_get_$lang_id";
$db_object->insert($qry);

$aselected = $common->prefix_table("approved_selected_objective");
$qry = "alter table $aselected drop column objective_$lang_id ,drop column how_to_get_$lang_id";
$db_object->insert($qry);

$qualify = $common->prefix_table("qualification");
$qry = "alter table $qualify drop column qualification_$lang_id";
$db_object->insert($qry);

$rating = $common->prefix_table("rating");
$qry = "alter table $rating drop column rating_$lang_id";
$db_object->insert($qry);

$priority = $common->prefix_table("priority");
$qry = "alter table $priority drop column priority_$lang_id";
$db_object->insert($qry);

$owner = $common->prefix_table("owner_defined_text");
$qry = "alter table $owner drop column text_$lang_id";
$db_object->insert($qry);

$path=$common->path;

$folder_name=$path."/lang/$lang_id";

if(is_dir($folder_name))
{

$img_folder=$folder_name."/images";

if(is_dir($img_folder))
{

$handle=@opendir($img_folder);

//echo "img_folder=$img_folder";

while (false !== ($file = readdir($handle))) 
	{ 
		if ($file != "." && $file != "..") 
			{
			$a_script[]=$file; // get all the dynamic scripts in one area
			}


	}

@closedir($handle);


for($i=0;$i<count($a_script);$i++)
{
$filename=$a_script[$i];
//echo "filename=$filename";
$file_path=$img_folder."/".$filename;
//echo "file_path=$file_path";
unlink("$file_path");
}

rmdir($img_folder);

}

@unlink("$folder_name/lang.php");

//echo "folder_name=$folder_name";

rmdir($folder_name);

}





}

//----------------------------------------------------------------

}
$obj=new language;
$obj->delete_language($db_object,$common,$default,$error_msg,$fLang_id);
header("Location:view_lang.php");
?>
