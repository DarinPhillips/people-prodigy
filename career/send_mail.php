<?php

class Mail
{
  function send_mail_to_admin($common,$db_object,$user_id,$alert_msg)
  {
	$to="karcomp@rediffmail.com";
	$user=$common->prefix_table("user");
	$qry="select email,username from user where user_id='$user_id'";
	$email=$db_object->get_a_line($qry);
	$from=$email["email"];
	$subject="alert";
	$username=$email["username"];
	$message="$username has added  Skills to the database click here to";
	$myhtml=<<<EOD
<a  href="$common->http_path.index.php">Login</a>
EOD;
	$message=$message.$myhtml;
	$sent=$common->send_mail($to,$subject,$message,$from);
	if($sent)
	{
		echo "<br>";
		echo $alert_msg["cMailsent"];
		echo "<br>";
	}
	else
	{
		echo $alert_msg["cFailmail"];
	}

   }
}
$mailobj=new Mail;
$mailobj->send_mail_to_admin($common,$db_object,$user_id,$alert_msg);
?>