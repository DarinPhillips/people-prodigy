<?php
/*---------------------------------------------
SCRIPT:employment_type.php
AUTHOR:info@chrisranjana.com	
UPDATED:22th Sept

DESCRIPTION:


---------------------------------------------*/
include("../session.php");
include_once("header.php");

class employment_type
{
	function show_emp_type($db_object,$common,$form_array,$default)
	{
	while(list($kk,$vv)=@each($form_array))
		{
		$$kk=$vv;
		}
		$xPath=$common->path;
		$xTemplate=$xPath."/templates/core/employment_type.html";
		$returncontent=$common->return_file_content($db_object,$xTemplate);
		
		
		$emptype_table = $common->prefix_table("employment_type");
		$mysql = "select type_$default as type, if(status='Yes','checked','') as yeschecked, if(status='No','checked','') as nochecked,status,id from $emptype_table";
	
		$emp_arr = $db_object->get_rsltset($mysql);
		
		
		$values = array("employment_loop"=>$emp_arr);
		$returncontent = $common->simpleloopprocess($db_object,$returncontent,$values);
		
		$returncontent = $common->direct_replace($db_object,$returncontent,$values);
		
		echo $returncontent;
	}
	function update_type($db_object,$common,$form_array,$error_msg,$default)
	{
	while(list($kk,$vv)=@each($form_array))
		{
		$$kk=$vv;
		if(ereg("^type_",$kk))
			{
			$type_arr[$kk] = $vv;
			}
			
	
		}
		
		
		$type_keys = @array_keys($type_arr);
	
		
		$emptype_table = $common->prefix_table("employment_type");
		
		for($i=0;$i<count($type_arr);$i++)
		{
			$tkey = $type_keys[$i];
			
			$tval = $type_arr[$tkey];
			
			
			list($un,$qid)=split("_",$tkey);
		
			$cval = $type_arr[$tkey];
		
			$mysql = "update $emptype_table set status='$cval' where id='$qid'";
			$db_object->insert($mysql);
			
			
			
			
		}
	$message = $error_msg["cEmpupdated"];
	echo $message;
	$this->show_emp_type($db_object,$common,$form_array,$default);
	
	}
	
}

$obj = new employment_type;

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

		if($fEmp_submit)
		{
			$obj->update_type($db_object,$common,$form_array,$error_msg,$default);
		}
		else
		{
		$obj->show_emp_type($db_object,$common,$form_array,$default);
		}
		
		include_once("footer.php");
		
