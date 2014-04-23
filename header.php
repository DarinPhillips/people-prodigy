<?php
class header
{
  function  header_display($common,$db_object,$user_id,$error_msg,$default)
  {

	$filename="templates/header.html";

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
	  //$user_id = $user_table["user_id"];


//------------------Codings for alert of Session Timed Out...
	  $config = $common->prefix_table("config"); 	
	
	  $mysql = "select min_time from $config";
	  $time_arr = $db_object->get_a_line($mysql);
	  $min_time = $time_arr['min_time'];

	  $path = $common->http_path;	
	  
	  $date = date("m.d.Y.H:i");

                 
	  $values["directreplace"]["user_name"]=$user_name;
	  $values["directreplace"]["date"]=$date;

	  //$values["directreplace"]["user_id"] = $user_id;
	  $values["directreplace"]["min_time"] = $min_time;
	  $values["directreplace"]["path"] = $path;
	  
	  
  	  $values["directreplace"]["charset"]=$charst["charset"];

		

  	  $filecontent=$common->direct_replace($db_object,$filecontent,$values);
          
		echo $filecontent;
	}
	else
	{
	 		
		echo $error_msg["cSessionTimedOut"];
	}


   }

}
$hdobj=new header;
$hdobj->header_display($common,$db_object,$user_id,$error_msg,$default);

$common->lang_menu($db_object,$default,$form_array);
?>
