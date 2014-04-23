<?php
/*---------------------------------------------
SCRIPT:families.php
AUTHOR:info@chrisranjana.com	
UPDATED:17th Sept

DESCRIPTION:


---------------------------------------------*/
include("../session.php");
include_once("header.php");

class family

{
	function showFamily($db_object,$common,$form_array,$default)
	
	{
		while(list($kk,$vv)=@each($form_array))
		{
		$$kk=$vv;
		}
		$xPath=$common->path;
		$xTemplate=$xPath."/templates/core/families.html";
		$returncontent=$common->return_file_content($db_object,$xTemplate);
	
		$position_table = $common->prefix_table("position");
		
		$fields = $common->return_fields($db_object,$position_table);
		$mysql = "select $fields from $position_table";
		$pos_arr = $db_object->get_rsltset($mysql);
		//print_r($pos_arr);
		
		if($pos_arr!="")
		{
		for($i=0;$i<count($pos_arr);$i++)
		{
			$position_name = $pos_arr[$i]["position_name"];
			$pos_arr1	= $common->conv_2Darray($db_object,$pos_arr);
			$returncontent = $common->pulldown_replace($db_object,'<{position_loopstart}>','<{position_loopend}>',$returncontent,$pos_arr1,"");
		
		}
		}
		else
		{
			$returncontent = $common->pulldown_replace($db_object,'<{position_loopstart}>','<{position_loopend}>',$returncontent,"","");
		
		}
		
		$family_table = $common->prefix_table("family");
		
		$fields = $common->return_fields($db_object,$family_table);
		$mysql = "select $fields from $family_table";
		$display_arr = $db_object->get_rsltset($mysql);
		//print_r($display_arr);
		
		preg_match("/<{families_loopstart}>(.*?)<{families_loopend}>/s",$returncontent,$match);
		
		$replace=$match[1];
		for($i=0;$i<count($display_arr);$i++)
		{
			$family_id = $display_arr[$i]["family_id"];
			$family_name = $display_arr[$i]["family_name"];
			$str .= preg_replace("/<{(.*?)}>/e","$$1",$replace);
			
		
		}
		$returncontent=preg_replace("/<{families_loopstart}>(.*?)<{families_loopend}>/s",$str,$returncontent);

		$returncontent = $common->direct_replace($db_object,$returncontent,$values);
		
		
		echo $returncontent;
	}
	function addFamily($db_object,$common,$form_array,$error_msg,$default,$user_id)
	{
		while(list($kk,$vv)=@each($form_array))
		{
		$$kk=$vv;
		}
		//print_r($form_array);
		
		$family_table = $common->prefix_table("family");
		$fam_position_table = $common->prefix_table("family_position");
		
		$mysql = "select family_name from $family_table";
		$fam_arr = $db_object->get_single_column($mysql);
		
		for($i=0;$i<count($fam_arr);$i++)
		{
			$fam_name = $fam_arr[$i];
			
			if($fam_name == $fFamily_name)
			{
				$message = $error_msg["cAlreadyexists"];
				echo $message;
				$this->showFamily($db_object,$common,$form_array);
				include("footer.php");
				exit;
			}
		}
		
		
		$mysql = "insert into $family_table set family_name='$fFamily_name',date_added=now(),added_by='$user_id'";
		//echo $mysql;
		$fam_id = $db_object->insert_data_id($mysql);
		
		//print_r($related_pos);
		
		for($i=0;$i<count($related_pos);$i++)
		{
			$position_id = $related_pos[$i];
			
		$mysql = "insert into $fam_position_table set family_id='$fam_id',position_id='$position_id'";
		//echo $mysql;
		$db_object->insert($mysql);
		}
		
		
		
	}
}
$obj = new family;

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
		
	if($fAdd_family)
	{
		$obj->addFamily($db_object,$common,$form_array,$error_msg,$default,$user_id);
		$message = $error_msg["cAddfamily"];
		echo $message;
		$obj->showFamily($db_object,$common,$form_array,$default);
	}
		else
		{
	$obj->showFamily($db_object,$common,$form_array,$default);
		}
		
	include("footer.php");