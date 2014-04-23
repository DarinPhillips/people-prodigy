<?php
/*---------------------------------------------
SCRIPT:addeeo_tags.php
AUTHOR:info@chrisranjana.com	
UPDATED:17th Sept

DESCRIPTION:


---------------------------------------------*/
include("../session.php");
include_once("header.php");

class eeoTags

{
	function showTags($db_object,$common,$form_array,$default)
	
	{
		while(list($kk,$vv)=@each($form_array))
		{
		$$kk=$vv;
		}
		$xPath=$common->path;
		$xTemplate=$xPath."/templates/core/addeeo_tags.html";
		$returncontent=$common->return_file_content($db_object,$xTemplate);
	
	
		$eeo_main_table=$common->prefix_table("eeo_main");
		$mysql = "select eeo_name from $eeo_main_table where eeo_id='$eeo_id'";
		//echo $mysql;
		$eeo_arr = $db_object->get_a_line($mysql);
		//print_r($eeo_arr);
		$eeo_name = $eeo_arr["eeo_name"];
		
		$returncontent = preg_replace("/{{(.*?)}}/e","$$1",$returncontent);
		echo $returncontent;
	
}
	function addTags($db_object,$common,$form_array,$default)
	{
		while(list($kk,$vv)=@each($form_array))
		{
		$$kk=$vv;
		}
	//	print_r($form_array);
		$eeo_tag_table=$common->prefix_table("eeo_tags");
		$mysql = "insert into $eeo_tag_table set tag_name='$fTag_name',eeo_id='$eeo_id'";
		//echo $mysql;
		$db_object->insert($mysql);
		//print_r($eeo_arr);
		
	}
}
$obj = new eeoTags;
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
//	print_r($form_array);
		if($fAdd_tag)
		{
		$obj->addTags($db_object,$common,$form_array,$default);
		$obj->showTags($db_object,$common,$form_array,$default);
		}
		else
		{
		$obj->showTags($db_object,$common,$form_array,$default);	
		}
include("footer.php");
