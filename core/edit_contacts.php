<?php
/*---------------------------------------------
SCRIPT:edit_contacts.php
AUTHOR:info@chrisranjana.com	
UPDATED:24th Sept

DESCRIPTION:


---------------------------------------------*/
include("../session.php");
include_once("header.php");

class editContacts
{
	function show_fields($db_object,$common,$form_array,$default)
	{
		while(list($kk,$vv)=@each($form_array))
		{
		$$kk=$vv;
		}
		$xPath=$common->path;
		$xTemplate=$xPath."/templates/core/edit_contacts.html";
		$returncontent=$common->return_file_content($db_object,$xTemplate);
		
		
		$contacts_table = $common->prefix_table("contacts");
		$mysql = "desc $contacts_table";
		//echo $mysql;
		$mysql_arr = $db_object->get_single_column($mysql);
	
		
		while(list($kk,$vv)=@each($mysql_arr))
		{
		$$kk=$vv;
		//echo "key $kk and value $vv<br>";
		if(ereg("^contact_display_",$vv))
			{
			$contact_arr[$vv] = $vv;
			
			
			}
		}
	
		$contact_keys = @array_keys($contact_arr);
			
		
		$str	= '';		
		preg_match("/<{contact_loopstart}>(.*?)<{contact_loopend}>/s",$returncontent,$mat);
		$mat	= $mat[1];

		for($i=0;$i<count($contact_arr);$i++)
		{
		
//============================================language change fieldname			

$lang_table = $common->prefix_table("language");
	$desc = "desc $lang_table";
	$res = $db_object->get_single_column($desc);
	//print_r($res);

	$qry = "select lang_id,lang_$default from $lang_table ";
	
	$res = $db_object->get_rsltset($qry);
	//print_r($res);
	for($i=0;$i<count($res);$i++)
			{
				
				$lang_id = $res[$i][0];

				if($lang_id==$default)
					{
					$selected="selected";
					}
				else
					{
					$selected="";
					}
				
				$language = $res[$i][1];
				//$str.=preg_replace("/\<\{(.*?)\}\>/e","$$1",$match);
				
			
//============================================

		$ckey = $contact_keys[$i];
		list($un,$qid,$cid)=split("_",$ckey,3);
		
		$mysql = "select contact_display_$cid from $contacts_table";
		
		$fields_arr = $db_object->get_single_column($mysql);
	
		$loopstart="<{fields_loopstart}>";
		$loopend="<{fields_loopend}>";


		$temp = $common->singleloop_replace($db_object,$loopstart,$loopend,$mat,$fields_arr,"");

		$str	.= preg_replace("/~{(.*?)}~/e","$$1",$temp);
	
		
}
		
		}
		
		$returncontent	= preg_replace("/<{contact_loopstart}>(.*?)<{contact_loopend}>/s",$str,$returncontent);
		
		$returncontent = $common->direct_replace($db_object,$returncontent,$values);
		
		echo $returncontent;
		
		
	}
function update_changes($db_object,$common,$form_array,$default)
{
	while(list($kk,$vv)=@each($form_array))
		{
		$$kk=$vv;
		
		//echo "key $kk and val $vv<br>";
		
		if(ereg("^contact_display_",$kk))
			{
			$contact_arr[$kk] = $vv;
			
			
			}
		
		}
		$contacts_table = $common->prefix_table("contacts");
		
		//print_r($contacts_arr);
		
		$contact_keys = @array_keys($contact_arr);
		
		//print_r($contact_keys);
		
		for($i=0;$i<count($contact_arr);$i++)
		{
			$ckey = $contact_keys[$i];
			//echo $ckey;
			list($un,$unid,$cid,$fid)=split("_",$ckey,4);
			$cval = $contact_arr[$ckey];
			//echo "value is $cval<br>";
			//echo $cid;
			//echo $fid;
			$id = $cid+1;
			$mysql = "update $contacts_table set contact_display_$fid='$cval' where contact_id=$id";
			//echo "$mysql<br>";
			$db_object->insert($mysql);
			
		}
		
		//echo "everything inserted";
		
		$this->show_fields($db_object,$common,$form_array,$default);
		
}

}
$obj = new editContacts;

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

if($fEdit_contact)
{
	$obj->update_changes($db_object,$common,$form_array,$default);
}
else
{
$obj->show_fields($db_object,$common,$form_array,$default);
}
include_once("footer.php");

