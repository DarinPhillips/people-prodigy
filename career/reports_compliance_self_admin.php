<?php
include("../session.php");

include("header.php");

class reports
{
	function reports_compliance($db_object,$common,$user_id,$error_msg)
	{
		$other_raters=$common->prefix_table("other_raters");
		
		$user_tests=$common->prefix_table("user_tests");
		
		$user_table=$common->prefix_table("user_table");
		
		$tech_references=$common->prefix_table("tech_references");
		
	
		if($user_id!=1)
		{
	$sql="select user_id from $user_table where admin_id='$user_id'";
	
	$users_id=$db_object->get_single_column($sql);
		
		
		}
		else
		{
			$sql="select user_id from $user_table where user_id <>'$user_id'";
			
			$users_id=$db_object->get_single_column($sql);
		}
		
		if($users_id[0]!="")
		{
			
			$users=@implode(",",$users_id);
			
			$user_clause="and $other_raters.cur_userid in "."(". $users.")";
				
		$sql1="select rater_userid as user_id,date_format(date_rating_requested,'%m.%d.%Y.%i:%s') as date,email
	
		 from $other_raters,$user_table where $user_table.user_id=$other_raters.cur_userid and 
	 
		 cur_userid=rater_userid  ".$user_clause;
	
		$result1=$db_object->get_rsltset($sql1);
	
		for($a=0;$a<count($result1);$a++)
		{
			$result1[$a][test_type]="Inter Personal";
		}
			
		$sql2="select test_type,$user_tests.user_id,date_format(test_taken_date,'%m.%d.%Y.%i:%s') as date,email from $user_table,$user_tests
		
		 where $user_table.user_id=$user_tests.user_id ";
		
		$result2=$db_object->get_rsltset($sql2);
		
		for($i=0;$i<count($result2);$i++)
		{
			$type=$result2[$a][test_type];
			
			if($type='i')
			{
				$result2[$a][test_type]="Inter Personal";
			}
			if($type='t')
			{
				$result2[$a][test_type]="Technical";
			}
		}
		
		$result1=@array_merge($result1,$resut2);
		
		$sql3="select ref_userid as user_id,date_format(date_rating_requested,'%m.%d.%Y.%i:%s') as date,email
		
		from $tech_references,$user_table where $tech_references.user_to_rate=$user_table.user_id and
		
		user_to_rate=ref_userid and rating_over='n'";
	
		$result3=$db_object->get_rsltset($sql3);
		
		for($a=0;$a<count($result3);$a++)
		{
			$result3[$a][test_type]="Technical";
		}
		
		$result1=@array_merge($result1,$result3);		

		if($result1[0]!="")
		{
		
		$pattern="/<{user_loopstart}>(.*?)<{user_loopend}>/s";
					
		$path=$common->path;
		
		$xtemplate=$path."templates/career/reports_compliance_self.html";
		
		$file=$common->return_file_content($db_object,$xtemplate);
			
		preg_match($pattern,$file,$match);
		
		$match=$match[0];
		
		for($a=0;$a<count($result1);$a++)
		{
			$userid=$result1[$a][user_id];
	
			$name=$common->name_display($db_object,$userid);
			
			$date=$result1[$a][date];
			
			$email=$result1[$a][email];
			
			$rating=$result1[$a][test_type];
			
			$str.=preg_replace("/<{(.*?)}>/e","$$1",$match);
		}
		
		$file=preg_replace($pattern,$str,$file);
		
		$file=$common->direct_replace($db_object,$file,$xArray);
		
		echo $file;
		}
		else
		{
			
			echo $error_msg['cNotAsked'];
			
			include_once("footer.php");exit;
		}
		}
		else
		{
			
			$error_msg['cNoReportsUnderAdmin'];
			
			include_once("footer.php");
			
			exit;
		}
	
	
	}
}

$obj=new reports();

$obj->reports_compliance($db_object,$common,$user_id,$error_msg);

include_once("footer.php");

?>
