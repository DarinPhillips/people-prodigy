<?php
/*
SCRIPT:lang_view.php
AUTHOR:info@chrisranjana.com
UPDATED:23 Sep 2003.

DESCRIPTION:
Displays the Edit Form for Language tool
----------------------------------------------------
----------------------------------------------------*/
include_once("../session.php");

if(!$submit)
	{
include_once("header.php");
	}

class Language
{

function show_Form($db_object,$common,$lang_id,$default,$error_msg)
{
	while(list($key,$value)=each($error_msg))
	{
		$$key=$value;
	}

	if($lang_id==null)
	{
		return ;
	}
	$fLang_id = $lang_id;
	$lang_table=$common->prefix_table("language");
	$path=$common->path;
	$template=$path."/templates/core/edit_lang.html";
	$returncontent=$common->return_file_content($db_object,$template);
	$sellang=$_COOKIE["lang"];
	if($sellang=="")
	{
		$sellang=1;
	}

	
	
	
	$qry = "select lang_$lang_id from $lang_table where lang_id='$lang_id'";

	$res = $db_object->get_a_line($qry);
	$lang = $res[0];

	$mysql="desc language";

	$arr=$db_object->get_single_column($mysql);

	$fieldnames=implode(",",$arr);
	$qry = "select lang_$default from $lang_table";
	$br = $db_object->get_single_column($qry);	

	$qry = "select $fieldnames from $lang_table ";

	$res = $db_object->get_rsltset($qry);

$charset_table=$common->prefix_table("language_charset");
$selqry="select language_id,charset from $charset_table";
$wholecharset=$db_object->get_rsltset($selqry);
$key="language_id";
$value="charset";
$charsetarray=$common->return_Keyedarray($wholecharset,$key,$value);
	

	$pattern="/<{language_loopstart(.*?)<{language_loopend}>/s";
	preg_match($pattern,$returncontent,$arr);
		$match=$arr[0];
		$str="";
			$l = "lang_".$lang_id;

			for($i=0;$i<count($res);$i++)
			{
				
				$lang_id = $res[$i]['lang_id'];
				$language = $res[$i][$l];
				$bracket = $br[$i];
			//	$lang_charset=$charsetarray[$lang_id];
				$str.=preg_replace("/\<\{(.*?)\}\>/e","$$1",$match);

			}
			
	$returncontent=preg_replace($pattern,$str,$returncontent);

	
	

	$mysql="desc language";

	$arr=$db_object->get_single_column($mysql);

	$fieldnames=implode(",",$arr);

	$mysql="select lang_$default from $lang_table ";

$lang_charset=$charsetarray[$fLang_id];
	$arr=$db_object->get_single_column($mysql);
//$cCharset=$error_msg["cCharset"];
	$returncontent = preg_replace("/{{(.*?)}}/e","$$1",$returncontent);

	
	echo $returncontent;

	
}

//------------------------------------------------------------



//------------------------------------------------------------

function save_data($db_object,$common,$fLang_id,$_POST,$default)
{


$id = array();
while(list($key,$value)=each($_POST))
{
	$$key=$value;
	if(ereg("^lang_",$key))
	{
		$id[]=substr($key,5);
	}
	if(ereg("^charset_",$key))
	{
		$ids=split("_",$key);
		$id1=$ids[1];
		$char[$id1]=$value;
	}

}

$charset_table=$common->prefix_table("language_charset");

while(list($kk,$vv)=@each($char))
{
	$upqry="update $charset_table set charset='$vv' where language_id='$kk'";
	$upqry="replace into $charset_table set language_id='$kk',charset='$vv'";
	$db_object->insert($upqry);
}
$lang_table=$common->prefix_table("language");
$lang = "lang_".$fLang_id;
for($i=0;$i<count($id);$i++)
{
$lan = "lang_".$id[$i];
$lan = $$lan;
$mysql="update $lang_table set $lang='$lan' where lang_id='$id[$i]'";

$db_object->insert($mysql);
}

}
//------------------------------------------------------------


}
$obj=new Language;

if($submit)
{
$flag=$obj->save_data($db_object,$common,$fLang_id,$_POST,$default);
header("Location: view_lang.php");
}


$obj->show_Form($db_object,$common,$fLang_id,$default,$error_msg);

$xTemplate=$xPath."/templates/core/edit_lang.html";
$xBody=$common->return_file_content($db_object,$xTemplate);
include_once("footer.php");
?>