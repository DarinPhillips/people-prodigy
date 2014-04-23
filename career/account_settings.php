<?php
include("../session.php");
include("header.php");
class MyAccount
{
  function display($common,$db_object,$user_id)
  {
	$xFile="../templates/career/account_settings.html";
	$xTemplate=$common->return_file_content($db_object,$xFile);
	$user=$common->prefix_table("user");
	$qry="select user_id,first_name,last_name,username,password,email,status,reg_date from $user where user_id='$user_id'";
	$result=$db_object->get_rsltset($qry);
	while(list($kk,$vv)=each($result))
	{
		$values["directreplace"]["first_name"]=$result[$kk]["first_name"];
		$values["directreplace"]["last_name"]=$result[$kk]["last_name"];
		$values["directreplace"]["username"]=$result[$kk]["username"];
		$values["directreplace"]["password"]=$result[$kk]["password"];
		$values["directreplace"]["email"]=$result[$kk]["email"];
	
	}
	$xTemplate=$common->direct_replace($db_object,$xTemplate,$values);
	
	echo $xTemplate;

   }
   function update($common,$db_object,$form_array,$user_id,$error_msg)
   {
   	while(list($kk,$vv)=each($form_array))
   	{
   		$$kk=$vv;
   	}


   		$user=$common->prefix_table("user");
		$subquery="select email from $user where user_id<>'$user_id'";
		$emaillist=$db_object->get_single_column($subquery);
		if(!in_array($fEmail,$emaillist))
		{				
   		$query="update $user set first_name='$fFirstname',last_name='$fLastname',password='$fPassword',email='$fEmail' where user_id='$user_id'";
   		$db_object->insert($query);
		}
		else
		{
				echo $error_msg["cEmail"];
		}
      	
   }

   
}
$accobj=new MyAccount;
if($fUpdate)
{	
$accobj->update($common,$db_object,$_POST,$user_id,$error_msg);
}
$accobj->display($common,$db_object,$user_id);
include("footer.php");
?>
