<?php
/*---------------------------------------------
SCRIPT:refused_details.php
AUTHOR:info@chrisranjana.com	
UPDATED:Jan 8th

DESCRIPTION:
This script displays the appraisal preference

---------------------------------------------*/
include("../session.php");
include_once("header.php");

class RefusedEmployees
{
function show_employees($db_object,$common,$post_var,$user_id,$error_msg)
	{

		while(list($kk,$vv) = @each($post_var))
		{
		$$kk = $vv;
		}
		$xPath=$common->path;
		
		$returncontent=$xPath."/templates/career/refused_raters.html";
		
		$returncontent=$common->return_file_content($db_object,$returncontent);
		
		$reject_rating=$common->prefix_table("reject_rating");
		
		$user_table=$common->prefix_table("user_table");
		
		if($user_id==1)
		{
			$sql="select user_id from $user_table where user_id<>'1'";
		}
		else
		{
			$sql="select user_id from $user_table where admin_id='$user_id'";
		}
		$users_under_admin=$db_object->get_single_column($sql);
		
		preg_match("/<{user_loopstart}>(.*?)<{user_loopend}>/s",$returncontent,$match);
		
		$match=$match[0];
		
		$a=0;
		
		for($i=0;$i<count($users_under_admin);$i++)
		{
			$user=$users_under_admin[$i];
			
			$sql="select count(rater_id) as count from $reject_rating where rater_id='$user'";
			
			$res=$db_object->get_single_column($sql);
			
			$count1=$res[0];
			
			if($count1>0)
			{
				$count[$a]=$count1;
				
				$users[$a]=$user;
				
				$a++;
				
//				$str.=preg_replace("/<{(.*?)}>/e","$$1",$match);
			}
		}
		if(count($count)==0)
		{
			echo $error_msg['cNoResultAvailable'];
			
			include_once("footer.php");
			
			exit;
		}

		@arsort($count);

		$keys=@array_keys($count);
		
		for($i=0;$i<count($count);$i++)
		{
			$key=$keys[$i];
			
			$count1=$count[$i];
			
			$user=$users[$key];
			
			$sql="select email from $user_table where user_id='$user'";
			
			$res=$db_object->get_a_line($sql);
			
			$email=$res[email];
			
			$username=$common->name_display($db_object,$user);
			
			$str.=preg_replace("/<{(.*?)}>/e","$$1",$match);
			
			
		}
		
		$returncontent=preg_replace("/<{user_loopstart}>(.*?)<{user_loopend}>/s",$str,$returncontent);
		
		$returncontent=$common->direct_replace($db_object,$returncontent,$xArray);
		
		echo $returncontent;
	}
}
$obj = new RefusedEmployees;

$obj->show_employees($db_object,$common,$post_var,$user_id,$error_msg);

include_once("footer.php");
?>
