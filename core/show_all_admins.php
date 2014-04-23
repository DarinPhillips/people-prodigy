<?php
/*---------------------------------------------
SCRIPT:show_all_admins.php
AUTHOR:info@chrisranjana.com	
UPDATED:24th Sept

DESCRIPTION:


---------------------------------------------*/
include("../session.php");
include_once("header.php");

class showAdmins
{
	function show_admins($db_object,$common,$form_array,$default)
	{
		while(list($kk,$vv)=@each($form_array))
		{
		$$kk=$vv;
		}
		
		$xPath=$common->path;
		$xTemplate=$xPath."/templates/core/show_all_admins.html";
		$returncontent=$common->return_file_content($db_object,$xTemplate);
	
		$user_table = $common->prefix_table("user_table");
		$mysql = "select email,user_id from $user_table";
		$mail_arr = $db_object->get_rsltset($mysql);
		//print_r($mail_arr);
		
		$values=array("admins_loop"=>$mail_arr);
		$returncontent=$common->simpleloopprocess($db_object,$returncontent,$values);
	
		$returncontent = $common->direct_replace($db_object,$returncontent,$values);
	

		
		echo $returncontent;
		
	}
}
$obj = new showAdmins;

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
		
		$obj->show_admins($db_object,$common,$form_array,$default);
	include_once("footer.php");
