<?php
/*---------------------------------------------
SCRIPT:edit_eeo.php
AUTHOR:info@chrisranjana.com	
UPDATED:17th Sept

DESCRIPTION:


---------------------------------------------*/
include("../session.php");
include_once("header.php");

class eeoEdit
{
	function show_eeo($db_object,$common,$form_array,$default)
	{
		
		
		while(list($kk,$vv)=@each($form_array))
		{
		$$kk=$vv;
		}
		
		$xPath=$common->path;
		$xTemplate=$xPath."/templates/core/edit_eeo.html";
		$returncontent=$common->return_file_content($db_object,$xTemplate);
		
		$eeo_main_table=$common->prefix_table("eeo_main");
		
		$fields = $common->return_fields($db_object,$eeo_main_table);
		$mysql = "select $fields from $eeo_main_table where eeo_id='$eeo_id'";
		//echo $mysql;
		$eeo_arr = $db_object->get_a_line($mysql);
		$eeo_name = $eeo_arr["eeo_name"];
		$eeo_id = $eeo_arr["eeo_id"];
		$returncontent = preg_replace("/<{(.*?)}>/e","$$1",$returncontent);
		
		$returncontent = $common->direct_replace($db_object,$returncontent,$values);
	
		echo $returncontent;
		
		
	}
	
	function edit_eeo($db_object,$common,$form_array,$default)
	{
		while(list($kk,$vv)=@each($form_array))
		{
		$$kk=$vv;
		}
		//print_r($form_array);
		$eeo_main_table=$common->prefix_table("eeo_main");
		$mysql = "update $eeo_main_table set eeo_name='$fEeo_name' where eeo_id='$eeo_id'";
		$db_object->insert($mysql);
	}
}
$obj = new eeoEdit;
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
		if($fEdit_eeo)
		{
			$obj->edit_eeo($db_object,$common,$form_array,$default);
			$message = $error_msg["cAddeeo"];
			echo $message;
			$obj->show_eeo($db_object,$common,$form_array,$default);
		}
		else
		{
		$obj->show_eeo($db_object,$common,$form_array,$default);
		}
		include_once("footer.php");
