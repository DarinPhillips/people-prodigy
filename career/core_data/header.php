<?php
//include("session.php");
class header
{
  function  header_display($common,$db_object,$user_id,$error_msg,$gbl_files,$default)
  {
  	$path=$common->path;

	$filename=$path."templates/career/header.html";
	$filecontent=$common->return_file_content($db_object,$filename);

$charset_table=$common->prefix_table("language_charset");
	$selqry="select charset from $charset_table where language_id='$default'";
	$charst=$db_object->get_a_line($selqry);


	if($user_id)
	{
		$tablename=$common->prefix_table("user");
	  $sql="select * from $tablename where user_id='$user_id'";
	  $user_table=$db_object->get_a_line($sql);
 	  $user_name=$user_table["username"];
	  $date=date("F j, Y, g:i a");                 
	  $values["directreplace"]["user_name"]=$user_name;
	  $values["directreplace"]["date"]=$date;
	  $values["directreplace"]["charset"]=$charst["charset"];  
	   $filecontent=$common->direct_replace($db_object,$filecontent,$values);
        
		echo $filecontent;
		$common->where_am_i($gbl_files);
	}
	else
	{
	 echo $error_msg["cSessionTimedOut"];
	}


   }

}
$hdobj=new header;
$hdobj->header_display($common,$db_object,$user_id,$error_msg,$gbl_files,$default);
$common->lang_menu($db_object,$default,$form_array);
?>