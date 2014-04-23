<?php
/*---------------------------------------------
SCRIPT:contacts.php
AUTHOR:info@chrisranjana.com	
UPDATED:22th Sept

DESCRIPTION:

Displays all the contacts
---------------------------------------------*/
include("../session.php");
include_once("header.php");

class contacts
{
	function show_contacts($db_object,$common,$form_array,$error_msg,$default)
	{
		while(list($kk,$vv)=@each($form_array))
		{
		$$kk=$vv;
		}
		//print_r($default);
		
		$xPath=$common->path;
		$xTemplate=$xPath."/templates/core/contacts.html";
		$returncontent=$common->return_file_content($db_object,$xTemplate);
		
		$contacts_table = $common->prefix_table("contacts");
		$mysql = "select contact_display_$default as contact_display, if(status='Yes','checked','')  as yeschecked, if(status='No','checked','')  as nochecked, status,contact_id from $contacts_table";
		$contacts_arr = $db_object->get_rsltset($mysql);
			

		$values = array("contacts_loop"=>$contacts_arr);
		
		$returncontent = $common->simpleloopprocess($db_object,$returncontent,$values);
		
		$returncontent = $common->direct_replace($db_object,$returncontent,$values);
		
		
		echo $returncontent;
	}
	function update_contacts($db_object,$common,$form_array,$error_msg,$default)
	{
//		print_R($form_array);
		while(list($kk,$vv)=@each($form_array))
		{
		$$kk=$vv;
		
		//print_r($form_array);
		
		if(ereg("^status_",$vv))
			{
				$ids=split("_",$vv);
				$id=$ids[1];
			$status_arr[] = $id;
			}
		}
		
		$contacts_table = $common->prefix_table("contacts");
			
/*			$status_keys = @array_keys($status_arr);
		
			$s_key = $status_keys[0];
			list($un,$qid)=split("_",$s_key);
			$sval = $status_arr[$s_key];
			*/

			
			$mysql = "update $contacts_table set status='No'";
			$db_object->insert($mysql);
			for($i=0;$i<count($status_arr);$i++)
			{
			$qid=$status_arr[$i];
			$mysql = "update $contacts_table set status='Yes' where contact_id='$qid'";
			$db_object->insert($mysql);
			}
			$message = $error_msg["cContactupdated"];
			echo $message;
			$this->show_contacts($db_object,$common,$form_array,$error_msg,$default);
	}
}
$obj = new contacts;

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

if($fContact_submit)
{
	$obj->update_contacts($db_object,$common,$form_array,$error_msg,$default);
}
else
{
$obj->show_contacts($db_object,$common,$form_array,$error_msg,$default);
}
include_once("footer.php");