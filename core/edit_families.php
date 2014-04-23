<?php
/*---------------------------------------------
SCRIPT:edit_families.php
AUTHOR:info@chrisranjana.com	
UPDATED:17th Sept

DESCRIPTION:


---------------------------------------------*/
include("../session.php");
include_once("header.php");

class editFamily

{
function show_family($db_object,$common,$form_array,$default)
	{
		
		
		while(list($kk,$vv)=@each($form_array))
		{
		$$kk=$vv;
		}
		//print_r($form_array);
		
		$xPath=$common->path;
		$xTemplate=$xPath."/templates/core/edit_families.html";
		$returncontent=$common->return_file_content($db_object,$xTemplate);
		
		$family_table=$common->prefix_table("family");
		$family_pos_table=$common->prefix_table("family_position");
		$position_table = $common->prefix_table("position");
		
		$fields = $common->return_fields($db_object,$family_table);
		$mysql = "select $fields from $family_table where family_id='$family_id'";
		//echo $mysql;
		$family_arr = $db_object->get_a_line($mysql);
		$family_name = $family_arr["family_name"];
		$family_id = $family_arr["family_id"];
		
		$mysql = "select pos_id,position_name from $position_table";
		$pos_arr = $db_object->get_rsltset($mysql);
		//print_r($pos_arr);
		
		$mysql = "select position_id from $family_pos_table where family_id='$family_id'";
		//echo $mysql;
		$sel_arr = $db_object->get_single_column($mysql);
		//print_r($sel_arr);
		
		
		for($i=0;$i<count($pos_arr);$i++)
		{
			$position_name = $pos_arr[$i]["position_name"];
			$pos_id[] = $pos_arr[$i]["pos_id"];
			
		}
		for($j=0;$j<count($sel_arr);$j++)
		{
			$sel_pos[] = $sel_arr[$j];
		}
		

		$pos_arr1	= $common->conv_2Darray($db_object,$pos_arr);

		$returncontent = $common->pulldown_replace_multiple($db_object,'<{position_loopstart}>','<{position_loopend}>',$returncontent,$pos_arr1,$sel_pos);
	
		$returncontent = preg_replace("/<{(.*?)}>/e","$$1",$returncontent);
	
		$returncontent = $common->direct_replace($db_object,$returncontent,$values);
		
		echo $returncontent;
		
		
	}
		function edit_family($db_object,$common,$form_array,$error_msg,$default)
	{
		while(list($kk,$vv)=@each($form_array))
		{
		$$kk=$vv;
		}
		//print_r($form_array);
		
		$family_table=$common->prefix_table("family");
		$family_pos_table=$common->prefix_table("family_position");
		
		$mysql = "select family_id from $family_table where family_name='$fFamily_name' and family_id <> '$family_id'";
		//echo $mysql;
		$check_arr = $db_object->get_a_line($mysql);
		print_r($check_arr);
		if($check_arr[0]=="")
		{
		$mysql = "update $family_table set family_name='$fFamily_name' where family_id='$family_id'";
		$db_object->insert($mysql);
		
		$mysql = "delete from $family_pos_table where family_id='$family_id'";
		$db_object->insert($mysql);
		
		for($i=0;$i<count($related_pos);$i++)
		{
			$position_id = $related_pos[$i];
			$mysql = "insert into $family_pos_table set family_id='$family_id',position_id='$position_id'";
			$db_object->insert($mysql);
		}
		
			$message = $error_msg["cEditfamily"];
			echo $message;
			$this->show_family($db_object,$common,$form_array,$default);
		}
		else
		{
			$message = $error_msg["cAlreadyexists"];
			echo $message;
			$this->show_family($db_object,$common,$form_array,$default);	
			
		}
	}
	
}
$obj = new editFamily;

//-----------------control also comes from families_without_position.php


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
		if($fEdit_family)
		{
			$obj->edit_family($db_object,$common,$form_array,$error_msg,$default);
			
		}
		
		else
		{
		$obj->show_family($db_object,$common,$form_array,$default);
		}
		include_once("footer.php");