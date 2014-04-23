<?php
/*---------------------------------------------
SCRIPT:delete_families.php
AUTHOR:info@chrisranjana.com	
UPDATED:22th Sept

DESCRIPTION:


---------------------------------------------*/
include("../session.php");
include_once("header.php");

class deleteFamily

{
function show_family($db_object,$common,$form_array,$default)
	{
		
		
		while(list($kk,$vv)=@each($form_array))
		{
		$$kk=$vv;
		}
		//print_r($form_array);
		$xPath=$common->path;
		$xTemplate=$xPath."/templates/core/delete_families.html";
		$returncontent=$common->return_file_content($db_object,$xTemplate);
		
		$family_table=$common->prefix_table("family");
		
		$fields = $common->return_fields($db_object,$family_table);
		$mysql = "select $fields from $family_table where family_id='$family_id'";
		//echo $mysql;
		$family_arr = $db_object->get_a_line($mysql);
		$family_name = $family_arr["family_name"];
		$family_id = $family_arr["family_id"];
		$returncontent = preg_replace("/<{(.*?)}>/e","$$1",$returncontent);
		
		
		$returncontent = $common->direct_replace($db_object,$returncontent,$values);
	
		echo $returncontent;
		
		
	}
		function delete_family($db_object,$common,$form_array,$error_msg,$default)
	{
		while(list($kk,$vv)=@each($form_array))
		{
		$$kk=$vv;
		}
		//print_r($form_array);
		
		$family_table=$common->prefix_table("family");
		$family_pos_table = $common->prefix_table("family_position");
		
		
		$mysql = "delete from $family_table where family_id='$family_id'";
		$db_object->insert($mysql);
		
		$mysql = "delete from $family_pos_table where family_id='$family_id'";
		$db_object->insert($mysql);
		
		$message = $error_msg["cDeletefamily"];
			echo $message;
		$this->show_family($db_object,$common,$form_array,$default);
		
	}
	
}
$obj = new deleteFamily;
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
		if($fDelete_family)
		{
			$obj->delete_family($db_object,$common,$form_array,$error_msg,$default);
			
		}
		
		else
		{
		$obj->show_family($db_object,$common,$form_array,$default);
		}
		include_once("footer.php");
