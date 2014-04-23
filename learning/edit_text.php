<?php
include("../session.php");
include("header.php");
class Mail_Option
{
 function display_mail_option($common,$db_object,$user_id)
  {

	$path=$common->path;
	$xFile=$path."/templates/learning/dev_text.html";
	$xTemplate=$common->return_file_content($db_object,$xFile);
	$config=$common->prefix_table("config");
$query="select dev_textbox from $config";
	$result=$db_object->get_a_line($query);

	$values["directreplace"]["dev_text"]=$result["dev_textbox"];


	
	$xTemplate=$common->direct_replace($db_object,$xTemplate,$values);
	echo $xTemplate;
   }

function updatemail($common,$db_object,$form_array)
   {
   	while(list($kk,$vv)=@each($form_array))
   	{
   		$$kk=$vv;
   	}
   	 	
   	$config=$common->prefix_table("config");


   	$insqry="update $config set dev_textbox='$fdev_text' where id=1";

   	$db_object->insert($insqry);
   	
   }

   
}
$moobj=new Mail_Option;
if($fUpdate && $user_id==1)
{
$moobj->updatemail($common,$db_object,$_POST);
}
$moobj->display_mail_option($common,$db_object,$user_id);
include("footer.php");
?>
