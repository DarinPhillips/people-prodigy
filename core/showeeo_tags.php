<?php
/*---------------------------------------------
SCRIPT:showeeo_tags.php
AUTHOR:info@chrisranjana.com	
UPDATED:19th Sept

DESCRIPTION:


---------------------------------------------*/
include("../session.php");
include_once("header.php");

class eeoTags
{
	function displayEeo_tags($db_object,$common,$form_array,$default)
	{
		while(list($kk,$vv)=@each($form_array))
		{
		$$kk=$vv;
		}
		@reset($form_array);
		
		$xPath=$common->path;
		
		$xTemplate=$xPath."/templates/core/showeeo_tags.html";
		$returncontent=$common->return_file_content($db_object,$xTemplate);
		
		$eeo_main_table=$common->prefix_table("eeo_main");
		$fields = $common->return_fields($db_object,$eeo_main_table);
		$mysql = "select $fields from $eeo_main_table where eeo_id='$eeo_id'";
	
		$eeo_arr = $db_object->get_a_line($mysql);
		$eeo_name = $eeo_arr["eeo_name"];
		
		$eeo_tags_table=$common->prefix_table("eeo_tags");
		$fields = $common->return_fields($db_object,$eeo_tags_table);
		$mysql = "select $fields from $eeo_tags_table where eeo_id='$eeo_id'";
		//echo $mysql;
		$eeo_arr = $db_object->get_rsltset($mysql);
		//print_r($eeo_arr);



		preg_match("/<{eeotags_loopstart}>(.*?)<{eeotags_loopend}>/s",$returncontent,$match);
		
		$replace=$match[1];
		
		for($i=0;$i<count($eeo_arr);$i++)
		{
			$eeo_id = $eeo_arr[$i]["eeo_id"];
			$tag_id = $eeo_arr[$i]["tag_id"];
			$tag_name = $eeo_arr[$i]["tag_name"];
			
			$eeo_main_table=$common->prefix_table("eeo_main");
			$fields = $common->return_fields($db_object,$eeo_main_table);
		$mysql = "select $fields from $eeo_main_table where eeo_id='$eeo_id'";
		//echo $mysql;
		$eeomain_arr = $db_object->get_a_line($mysql);
		//print_r($eeomain_arr);
		$eeo_name = $eeomain_arr["eeo_name"];
			
			
			$str .= preg_replace("/<{(.*?)}>/e","$$1",$replace);
		}
		
		$returncontent=preg_replace("/<{eeotags_loopstart}>(.*?)<{eeotags_loopend}>/s",$str,$returncontent);

		$returncontent = $common->direct_replace($db_object,$returncontent,$values);
		
		echo $returncontent;
	}
	function add_tag($db_object,$common,$form_array,$error_msg,$default)
	{
		while(list($kk,$vv)=@each($form_array))
		{
		$$kk=$vv;
		}
		
		$eeo_tag_table=$common->prefix_table("eeo_tags");
		
		$mysql = "select tag_name from $eeo_tag_table";
		//echo $mysql;
		$check_arr = $db_object->get_single_column($mysql);
		//print_r($check_arr);
		for($i=0;$i<count($check_arr);$i++)
		{
			$exist_tag_name = $check_arr[$i];
			//echo "$exist_tag_name<br>";
			if($fTag_name == $exist_tag_name)
			{
				$message = $error_msg["cExistingeeo"];
				echo $message;
				$this->displayEeo_tags($db_object,$common,$form_array);
				
				exit;
			}
		}
		
		$mysql = "insert into $eeo_tag_table set tag_name='$fTag_name',eeo_id='$eeo_id'";
		//echo $mysql;
		$db_object->insert($mysql);
		
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

		
		$form_array	= @array_merge($form_array,$error_msg,$default);
//print_r($form_array);
if($fAdd_tag)
{
	$obj->add_tag($db_object,$common,$form_array,$error_msg,$default);
	$message = $error_msg["cAddtag"];
	echo $message;
	$obj->displayEeo_tags($db_object,$common,$form_array,$default);
}
else
{
$obj->displayEeo_tags($db_object,$common,$form_array,$default);
}
include_once("footer.php");
