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
		$xTemplate=$xPath."/templates/core/showeeo_tags.html";
		$returncontent=$common->return_file_content($db_object,$xTemplate);
		
		$returncontent = $common->direct_replace($db_object,$returncontent,$values);
		
		echo $returncontent;
	}
	
	function edit_tags($db_object,$common,$form_array,$default)
	{
		$xPath=$common->path;
		$xTemplate=$xPath."/templates/core/edit_tag.html";
		$returncontent=$common->return_file_content($db_object,$xTemplate);
		
		
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
		$tag_name = $tag_arr["tag_name"];
		$tag_id = $tag_arr["tag_id"];
		$eeo_id = $tag_arr["eeo_id"];
		
		$returncontent = preg_replace("/<{(.*?)}>/e","$$1",$returncontent);
		
		$returncontent = $common->direct_replace($db_object,$returncontent,$values);
		
		echo $returncontent;
			
	}
	
	function delete_tags($db_object,$common,$form_array,$default)
	{
		$xPath=$common->path;
		$xTemplate=$xPath."/templates/core/delete_tag.html";
		$returncontent=$common->return_file_content($db_object,$xTemplate);
		
		while(list($kk,$vv)=@each($form_array))
		{
		$$kk=$vv;
		//echo "key is $kk val is $vv<br>";
		}
		$eeo_tag_table=$common->prefix_table("eeo_tags");
		$fields = $common->return_fields($db_object,$eeo_tag_table);
		$mysql = "select $fields from $eeo_tag_table where tag_id='$tag_id'";
		//echo $mysql;
		$tag_arr = $db_object->get_a_line($mysql);
		$tag_name = $tag_arr["tag_name"];
		$tag_id = $tag_arr["tag_id"];
		$eeo_id = $tag_arr["eeo_id"];
		
		$returncontent = preg_replace("/<{(.*?)}>/e","$$1",$returncontent);
		
		$returncontent = $common->direct_replace($db_object,$returncontent,$values);
		
		echo $returncontent;

		
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
		
if($action == "edit")
{

	$obj->edit_tags($db_object,$common,$form_array,$default);
}
elseif($action == "delete")
{
	$obj->delete_tags($db_object,$common,$form_array,$default);
}
else
{
	$obj->showTags($common,$db_object,$form_array,$default);
}


include_once("footer.php");
