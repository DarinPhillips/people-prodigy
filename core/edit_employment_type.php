<?php
/*---------------------------------------------
SCRIPT:edit_employment_type.php
AUTHOR:info@chrisranjana.com	
UPDATED:24th Sept

DESCRIPTION:


---------------------------------------------*/
include("../session.php");
include_once("header.php");

class editEmployment_type
{
	function show_fields($db_object,$common,$form_array,$default)
	{
		while(list($kk,$vv)=@each($form_array))
		{
		$$kk=$vv;
		}
		$xPath=$common->path;
		$xTemplate=$xPath."/templates/core/edit_employment_type.html";
		$returncontent=$common->return_file_content($db_object,$xTemplate);
		
		
		$emptype_table = $common->prefix_table("employment_type");
		$mysql = "desc $emptype_table";
		
		$mysql_arr = $db_object->get_single_column($mysql);
	
		
		while(list($kk,$vv)=@each($mysql_arr))
		{
		$$kk=$vv;
		
		if(ereg("^type_",$vv))
			{
			$type_arr[$vv] = $vv;
			
			
			}
		}
	
		$type_keys = @array_keys($type_arr);
			
		
		$str	= '';		
		preg_match("/<{type_loopstart}>(.*?)<{type_loopend}>/s",$returncontent,$mat);
		$mat	= $mat[1];

		for($i=0;$i<count($type_arr);$i++)
		{
//==============
//code for name change depending on language
			
	$lang_table = $common->prefix_table("language");
	
	$desc = "desc $lang_table";
	$res = $db_object->get_single_column($desc);

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
			
				
//===============
			
			
		$tkey = $type_keys[$i];
		list($un,$tid)=split("_",$tkey);
		
		$mysql = "select type_$tid from $emptype_table";
		$fields_arr = $db_object->get_single_column($mysql);
	
		$loopstart="<{fields_loopstart}>";
		$loopend="<{fields_loopend}>";


		$temp = $common->singleloop_replace($db_object,$loopstart,$loopend,$mat,$fields_arr,"");

		$str	.= preg_replace("/~{(.*?)}~/e","$$1",$temp);
		

			}
		}
		
		$returncontent	= preg_replace("/<{type_loopstart}>(.*?)<{type_loopend}>/s",$str,$returncontent);
		
		$returncontent = $common->direct_replace($db_object,$returncontent,$values);
		
		echo $returncontent;
		
		
	}
function update_changes($db_object,$common,$form_array,$default)
{
	while(list($kk,$vv)=@each($form_array))
		{
		$$kk=$vv;
		
		//echo "key $kk and val $vv<br>";
		
		if(ereg("^type_",$kk))
			{
			$type_arr[$kk] = $vv;
			
			
			}
		
		}
		$emptype_table = $common->prefix_table("employment_type");
		
		//print_r($type_arr);
		
		$type_keys = @array_keys($type_arr);
		
		//print_r($type_keys);
		
		for($i=0;$i<count($type_arr);$i++)
		{
			$tkey = $type_keys[$i];
			//echo $tkey;
			list($un,$tid,$fid)=split("_",$tkey,3);
			$tval = $type_arr[$tkey];
			//echo "value is $tval<br>";
			//echo $tid;
			//echo $fid;
			$id = $tid+1;
			$mysql = "update $emptype_table set type_$fid='$tval' where id=$id";
			//echo "$mysql<br>";
			$db_object->insert($mysql);
			
		}
		
		//echo "everything inserted";
		
		$this->show_fields($db_object,$common,$form_array,$default);
		
}

}
$obj = new editEmployment_type;

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

if($fEdit_type)
{
	$obj->update_changes($db_object,$common,$form_array,$default);
}
else
{
$obj->show_fields($db_object,$common,$form_array,$default);
}
include_once("footer.php");
