<?php
include("../session.php");
include("header.php");
class Mail_option
{
  function mail_display($common,$db_object)
	{
	$path=$common->path;
	$xFile=$path."templates/core/mail_option.html";
	$xTemplate=$common->return_file_content($db_object,$xFile);
	$config=$common->prefix_table("config");
	$selqry="select emp_subject,emp_message,toadmin_subject,toadmin_message,lplanassign_subject,
	lplanassign_message,lsolution_subject,lsolution_message from $config";
	$rslt=$db_object->get_a_line($selqry);
	$subject=$rslt["emp_subject"];
	$message=$rslt["emp_message"];
	$vals["subject"]=$subject;
	$vals["message"]=$message;

//---------This Mail option has been already shown in career mail option	
	$vals["subject2"]=$rslt["toadmin_subject"];
	$vals["message2"]=$rslt["toadmin_message"];

	$vals["subject3"]=$rslt["lplanassign_subject"];
	$vals["message3"]=$rslt["lplanassign_message"];
	
	
	$vals["subject4"]=$rslt["lsolution_subject"];
	$vals["message4"]=$rslt["lsolution_message"];

	$xTemplate=$common->direct_replace($db_object,$xTemplate,$vals);	
	echo $xTemplate;

	}

function update($common,$db_object,$form_array)
	{
		while(list($kk,$vv)=each($form_array))
		{
			$$kk=$vv;
		}
		$config=$common->prefix_table("config");
		$updqry="update $config set emp_message='$fMessage',emp_subject='$fSubject',toadmin_subject='$fSubject2',toadmin_message='$fMessage2',lplanassign_subject='$fSubject3',lplanassign_message='$fMessage3',lsolution_subject='$fSubject4',lsolution_message='$fMessage4' where id=1";
		
		
		$db_object->insert($updqry);
	
	}
}
$mailobj=new Mail_option;
if($fSubmit)
{
	$mailobj->update($common,$db_object,$post_var);
}
$mailobj->mail_display($common,$db_object);
include("footer.php");
?>
