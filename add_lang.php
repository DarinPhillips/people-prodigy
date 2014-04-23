<?php
/*
SCRIPT:add_lang.php
AUTHOR:info@chrisranjana.com
UPDATED:23 Sep 2003.
----------------------------------------------------
----------------------------------------------------*/
include_once("../session.php");
if(!$submit)
{
include_once("header.php");
}
class Language
{
function show_add($db_object,$common,$error_msg)
{
	while(list($key,$value)=@each($error_msg))
	{
		$$key=$value;
	}
	$file = file("../templates/core/add_lang.html");
	$out = join("",$file);
	$out = preg_replace("/{{(.*?)}}/e","$$1",$out);
	echo $out;

return $body;
}
//------------------------------------------------
function add_lang($db_object,$common,$lang,$langcharset=null,$default)
{
$folder=$_COOKIE["lang"];

$lang_table=$common->prefix_table("language");

$checkqry = "select count(lang_id) from $lang_table where lang_$default = '$lang'";
$checkres = $db_object->get_a_line($checkqry);
$present= $checkres[0];

if($folder==null)
{
$folder="1";
}
else
{
$mysql="select lang_id	from $lang_table where lang_id='$folder'";

$arr=$db_object->get_a_line($mysql);

$folder=$arr[0];

}

include("../lang/$folder/lang.php");

if($lang==null)
{
$a[]="<br>";
$a[]=0;
return $a;
}

$lang_table=$common->prefix_table("language");
$charset_table=$common->prefix_table("language_charset");

$mysql="insert into $lang_table set lang_$folder='$lang'";

$lang_id=$db_object->insert_data_id($mysql);

$mysql="insert into $charset_table set language_id='$lang_id',charset='$langcharset'";
$db_object->insert($mysql);

$mysql="alter table $lang_table add column lang_$lang_id varchar(255)";

$db_object->insert($mysql);



$emp_type=$common->prefix_table("employment_type");

$mysql="alter table $emp_type add column type_$lang_id varchar(255)";

$db_object->insert($mysql);

$acc_rights = $common->prefix_table("access_rights");
$mysql = "alter table $acc_rights add column type_$lang_id varchar(255)";
$db_object->insert($mysql);

$contacts = $common->prefix_table("contacts");
$mysql = "alter table $contacts add column contact_display_$lang_id varchar(255)";
$db_object->insert($mysql);

$dev_basic = $common->prefix_table("dev_basic");
$mysql = "alter table $dev_basic add column coursetype_$lang_id varchar(255)";
$db_object->insert($mysql);

$dev_interbasic = $common->prefix_table("dev_interbasic");

$mysql = "alter table $dev_interbasic add column coursename_$lang_id varchar(255)";
$db_object->insert($mysql);

$temp_devbuilder = $common->prefix_table("temp_devbuilder");
$mysql = "alter table $temp_devbuilder add column (title_$lang_id text, description_$lang_id text,url_$lang_id text)";
$db_object->insert($mysql);

$approved_devbuilder = $common->prefix_table("approved_devbuilder");
$mysql = "alter table $approved_devbuilder add column (title_$lang_id text,description_$lang_id text,url_$lang_id text)";
$db_object->insert($mysql);


$unapproved_devbuilder = $common->prefix_table("unapproved_devbuilder");
$mysql = "alter table $unapproved_devbuilder add column (title_$lang_id text,description_$lang_id text,url_$lang_id text)";
$db_object->insert($mysql);

$temp_cat = $common->prefix_table("temp_category");
$qry = "alter table $temp_cat add column category_$lang_id text";
$db_object->insert($qry);


$temp_met = $common->prefix_table("temp_metrics");
$qry = "alter table $temp_met add column metrics_$lang_id text";
$db_object->insert($qry);



$unapp_cat = $common->prefix_table("unapproved_category");
$qry = "alter table $unapp_cat add column category_$lang_id text";
$db_object->insert($qry);


$unapp_met = $common->prefix_table("unapproved_metrics");
$qry = "alter table $unapp_met add column metrics_$lang_id text";
$db_object->insert($qry);

$app_cat = $common->prefix_table("approved_category");
$qry = "alter table $app_cat add column category_$lang_id text";
$db_object->insert($qry);


$app_met = $common->prefix_table("approved_metrics");
$qry = "alter table $app_met add column metrics_$lang_id text";
$db_object->insert($qry);

$message = $common->prefix_table("performance_message");
$qry = "alter table $message add column (appsub_subject_$lang_id text, appsub_message_$lang_id text,
approved_subject_$lang_id text,approved_message_$lang_id text,resubmit_subject_$lang_id text,resubmit_message_$lang_id text)";
$db_object->insert($qry);

$name = $common->prefix_table("name_fields");
$qry = "alter table $name add column name_$lang_id varchar(255)";
$db_object->insert($qry);


$reject = $common->prefix_table("rejected_category");
$qry = "alter table $reject add column category_$lang_id text";
$db_object->insert($qry);

$ureject = $common->prefix_table("unapproved_rejected_category");
$qry = "alter table $ureject add column category_$lang_id text";
$db_object->insert($qry);

$areject = $common->prefix_table("approved_rejected_category");
$qry = "alter table $areject add column category_$lang_id text";
$db_object->insert($qry);

$skillraters_table = $common->prefix_table('skill_raters');
$qry = "alter table $skillraters_table add column rater_level_$lang_id varchar(255)";
$db_object->insert($qry);

$selected = $common->prefix_table("temp_selected_objective");
$qry = "alter table $selected add column (objective_$lang_id text,how_to_get_$lang_id text)";
$db_object->insert($qry);

$uselected = $common->prefix_table("unapproved_selected_objective");
$qry = "alter table $uselected add column (objective_$lang_id text,how_to_get_$lang_id text)";
$db_object->insert($qry);

$aselected = $common->prefix_table("approved_selected_objective");
$qry = "alter table $aselected add column (objective_$lang_id text,how_to_get_$lang_id text)";
$db_object->insert($qry);

$qualify = $common->prefix_table("qualification");
$qry = "alter table $qualify add column qualification_$lang_id text";
$db_object->insert($qry);

$rating = $common->prefix_table("rating");
$qry = "alter table $rating add column rating_$lang_id text";
$db_object->insert($qry);

$priority = $common->prefix_table("priority");
$qry = "alter table $priority add column priority_$lang_id text";
$db_object->insert($qry);

$owner = $common->prefix_table("owner_defined_text");
$qry = "alter table $owner add column text_$lang_id text";
$db_object->insert($qry);

$path=$common->path;

$folder_name=$path."/lang/$lang_id";

umask(000);

mkdir("$folder_name",0777);

$script=$path."/lang/1/lang.php";

$content=$common->return_file_content($db_object,$script);

$script=$path."/lang/$lang_id/lang.php";

$common->write_to_file($db_object,$script,$content);





$a[]=$error_msg["cSavedlang"];

$a[]=1;

return $a;



}
//------------------------------------------------

}
$obj=new Language;
if($submit)
{
$lang_table = $common->prefix_table("language");
$checkqry = "select count(lang_id) from $lang_table where lang_$default = '$fLang'";
$checkres = $db_object->get_a_line($checkqry);
$present= $checkres[0];
if($present > 0)//check if
{
		include("header.php");
		echo $error_msg['cLangexist'];
}
else
{	
	$a=$obj->add_lang($db_object,$common,$fLang,$fLangcharset,$default);
	header("location: view_lang.php");
}
}
if($a[1]==0 or $submit =="")
{
$xBody=$obj->show_add($db_object,$common,$error_msg);
}



include_once("footer.php");
?>
