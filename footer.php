<?php

class Footer1
{
  function footer_display($common,$db_object,$user_id)
  {
  	$user_table=$common->prefix_table("user_table");
  	
  	$sql="select user_type from $user_table where user_id='$user_id'";

  	$sql_res=$db_object->get_a_line($sql);

  	if($sql_res[user_type]=="external")
  	{
	
  		$path=$common->path;
	    $filename=$path."templates/ext_footer.html";
	    
	    $values[path]="$common->http_path";
		
  	}
  	else
  	{
  	$path=$common->path;
	    $filename=$path."templates/footer.html";
  	}
	    $filecontent=$common->return_file_content($db_object,$filename);
$myhtml=<<<EOD
		<a href="career/front_panel.php">Front Panel</a>
EOD;

$exist=$_COOKIE["viewasadmin"];
	if($exist!="")
	{
	$filecontent=preg_replace("/<{(.*?)}>/s","",$filecontent);
	}
	else
	{
	$filecontent=preg_replace("/<{Admin_mode_Setup}>(.*)<{Admin_mode_Setup}>/s","",$filecontent);
	}

	$filecontent = $common->direct_replace($db_object,$filecontent,$values);
	echo $filecontent;
  }

}

$ftobj=new Footer1;

$ftobj->footer_display($common,$db_object,$user_id);

?>

		
