<?php
/*---------------------------------------------
SCRIPT:eeo_status.php
AUTHOR:info@chrisranjana.com	
UPDATED:5th Sept

DESCRIPTION:
Shows All EEO Status

---------------------------------------------*/
include("../session.php");
include_once("header.php");

class eeoStatus
{
	function displayEeo_status($db_object,$common,$form_array,$default)
	{
		while(list($kk,$vv)=@each($form_array))
		{
		$$kk=$vv;
		}
		$xPath=$common->path;
		$xTemplate=$xPath."/templates/core/eeo_status.html";
		$returncontent=$common->return_file_content($db_object,$xTemplate);
		
		
		$eeo_maintable=$common->prefix_table("eeo_main");
		$eeo_tags_table=$common->prefix_table("eeo_tags");
		
		$fields = $common->return_fields($db_object,$eeo_maintable);
		$mysql = "select $fields from $eeo_maintable order by eeo_id";

//---------newwly$mysql="select $eeo_maintable.eeo_id,$eeo_maintable.eeo_name,$eeo_tags_table.tag_name,$eeo_tags_table.tag_id from $eeo_maintable left join $eeo_tags_table on $eeo_maintable.eeo_id=$eeo_tags_table.eeo_id";

//		echo $mysql;
		
		$eeo_arr = $db_object->get_rsltset($mysql);
		preg_match("/<{eeostatus_loopstart}>(.*?)<{eeostatus_loopend}>/s",$returncontent,$match);
		
		$replace=$match[1];
		
		for($i=0;$i<count($eeo_arr);$i++)
		{
			$eeo_id = $eeo_arr[$i]["eeo_id"];
//---------newwly	$tag_id=$eeo_arr[$i]["tag_id"];
//---------newwly	$tag_name=$eeo_arr[$i]["tag_name"];
			$eeo_name = $eeo_arr[$i]["eeo_name"];
			$str .= preg_replace("/<{(.*?)}>/e","$$1",$replace);
		}
		$returncontent=preg_replace("/<{eeostatus_loopstart}>(.*?)<{eeostatus_loopend}>/s",$str,$returncontent);

		$returncontent = $common->direct_replace($db_object,$returncontent,$values);
		
		echo $returncontent;
	}
	function addNew_eeo($db_object,$common,$form_array,$error_msg,$default)
	{
		while(list($kk,$vv)=@each($form_array))
		{
		$$kk=$vv;
	
		}
			
		$eeo_maintable=$common->prefix_table("eeo_main");
		
		$mysql = "select eeo_name from $eeo_maintable";
		//echo $mysql;
		$check_arr = $db_object->get_single_column($mysql);
		//print_r($check_arr);
		for($i=0;$i<count($check_arr);$i++)
		{
			$exist_eeo_name = $check_arr[$i];
			//echo "$exist_eeo_name<br>";
			if($fEeo_name == $exist_eeo_name)
			{
				$message = $error_msg["cExistingeeo"];
				echo $message;
				$this->displayEeo_status($db_object,$common,$form_array);
				
				exit;
			}
		}
		
		
		$mysql = "insert into $eeo_maintable set eeo_name='$fEeo_name'";
	
		$db_object->insert($mysql);
	
	}
}
$obj = new eeoStatus;
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

if($fAdd_eeo)
{
	
	$obj->addNew_eeo($db_object,$common,$form_array,$error_msg,$default);
	$message = $error_msg["cAddeeo"];
	echo $message;
	$obj->displayEeo_status($db_object,$common,$form_array,$default);
	
}
else
{
$obj->displayEeo_status($db_object,$common,$form_array,$default);
}
include_once("footer.php");
?>