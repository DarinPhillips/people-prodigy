<?php
include("../session.php");
include("header.php");
class Mail_Option
{
 function display_mail_option($common,$db_object,$user_id)
  {

	$path=$common->path;
	$xFile=$path."/templates/learning/mail_option.html";
	$xTemplate=$common->return_file_content($db_object,$xFile);
	$config=$common->prefix_table("config");

	$query="select dev_message,dev_subject,lsolution_message,lsolution_subject, 
			lplan_approved_subject,lplan_approved_message,lplan_resubmitted_subject,
			lplan_resubmitted_message,lplan_feedback_subject,lplan_feedback_message from $config";
	$result=$db_object->get_a_line($query);

	$values["directreplace"]["subject"]=$result["dev_subject"];
	$values["directreplace"]["message"]=$result["dev_message"];
	$values["directreplace"]["subject1"]=$result["lsolution_subject"];
	$values["directreplace"]["message1"]=$result["lsolution_message"];
	$values["directreplace"]["subject2"]=$result["lplan_approved_subject"];
	$values["directreplace"]["message2"]=$result["lplan_approved_message"];
	$values["directreplace"]["subject3"]=$result["lplan_resubmitted_subject"];
	$values["directreplace"]["message3"]=$result["lplan_resubmitted_message"];
	$values["directreplace"]["subject4"]=$result["lplan_feedback_subject"];
	$values["directreplace"]["message4"]=$result["lplan_feedback_message"];
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
   	$insqry="update $config set dev_subject='$fSubject',dev_message='$fMessage',
   		lsolution_message='$fMessage1',lsolution_subject='$fSubject1',
   		lplan_approved_subject='$fSubject2',lplan_approved_message='$fMessage2',
   		lplan_resubmitted_subject='$fSubject3',lplan_resubmitted_message='$fMessage3',
   		lplan_feedback_subject='$fSubject4',lplan_feedback_message='$fMessage4' where id=1";
  
   	$db_object->insert($insqry);

   	
   }

   
}
$moobj=new Mail_Option;
if($fUpdatemail && $user_id==1)
{
$moobj->updatemail($common,$db_object,$_POST);
}
$moobj->display_mail_option($common,$db_object,$user_id);
include("footer.php");
?>
