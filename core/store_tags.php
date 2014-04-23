<?php
/*---------------------------------------------
SCRIPT:edit_tags.php
AUTHOR:info@chrisranjana.com	
UPDATED:19th Sept

DESCRIPTION:


---------------------------------------------*/
include("../session.php");
include_once("header.php");

class editEeo_tags
{
	function showTags($common,$db_object,$form_array,$default)
	{
		$xPath=$common->path;
		$xTemplate=$xPath."/templates/core/store_tags.html";
		$returncontent=$common->return_file_content($db_object,$xTemplate);
		
		while(list($kk,$vv)=@each($form_array))
		{
		$$kk=$vv;
		//echo "key is $kk val is $vv<br>";
		}
	//	print_r($form_array);
		
	//	$returncontent = preg_replace("/{{(.*?)}}/e","$$1",$returncontent);
		
		$values["eeo_id"] = $eeo_id;
		$returncontent = $common->direct_replace($db_object,$returncontent,$values);
		
		echo $returncontent;
	}
	
	function edit_Tags($common,$db_object,$form_array,$default)
	{
		
		
		while(list($kk,$vv)=@each($form_array))
		{
		$$kk=$vv;
		//echo "key is $kk val is $vv<br>";
		}
		//print_r($form_array);
		
		$eeo_tag_table=$common->prefix_table("eeo_tags");
		
		$fields = $common->return_fields($db_object,$eeo_tag_table);
		$mysql = "select $fields from $eeo_tag_table where tag_id='$tag_id'";
		//echo $mysql;
		$tag_arr = $db_object->get_a_line($mysql);
		
		$eeo_id = $tag_arr["eeo_id"];
		
		$mysql = "update $eeo_tag_table set tag_name='$tag_name',eeo_id='$eeo_id' where tag_id='$tag_id'";
		//echo $mysql;
		$db_object->insert($mysql);
	}
	
	function delete_tags($common,$db_object,$form_array,$default)
	{
		
		while(list($kk,$vv)=@each($form_array))
		{
		$$kk=$vv;
		//echo "key is $kk val is $vv<br>";
		}
		
		
		$eeo_tag_table=$common->prefix_table("eeo_tags");
		$mysql = "delete from $eeo_tag_table where tag_id='$tag_id'";
	
		$db_object->insert($mysql);
	}
	function add_Tags($common,$db_object,$form_array,$default)
	{
		
		while(list($kk,$vv)=@each($form_array))
		{
		$$kk=$vv;
		}
		
		//print_r($form_array);
		
			
		//$returncontent = preg_replace("/{{(.*?)}}/e","$$1",$returncontent);
		
		$values["eeo_id"] = $eeo_id;
		$returncontent = $common->direct_replace($db_object,$returncontent,$values);
		
		$eeo_tag_table=$common->prefix_table("eeo_tags");
		
		$mysql = "insert into $eeo_tag_table set tag_name = '$fTag_name' , eeo_id = '$eeo_id'";
		//echo $mysql;
		$db_object->insert($mysql);
		
		
	}
}
$obj = new editEeo_tags;

	while(list($kk,$vv)=@each($_POST))
		{
		$$kk=$vv;
		$form_array["$kk"]=$vv;
		
		}

	while(list($kk,$vv)=@each($_GET))
		{
		$$kk=$vv;
		$form_array["$kk"]=$vv;
		}
		
//print_r($form_array);


if($fAdd_tag)
{
	$message = $error_msg["cAddtag"];
	echo $message;
	$obj->add_Tags($common,$db_object,$form_array,$default);
	$obj->showTags($common,$db_object,$form_array,$default);
	
}
elseif($fEdit_tag)
{
	$message = $error_msg["cEdittag"];
	echo $message;
	$obj->edit_Tags($common,$db_object,$form_array,$default);
	$obj->showTags($common,$db_object,$form_array,$default);
	
}
elseif($fDelete_tag)
{
	$message = $error_msg["cDeletetag"];
	echo $message;
	$obj->delete_Tags($common,$db_object,$form_array,$default);
	$obj->showTags($common,$db_object,$form_array,$default);
	
}
include_once("footer.php");
