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
		$sql="select position from $user_table where user_id='$user_id'";
		
		$res_sql=$db_object->get_a_line($sql);
		
		$pos=$res_sql[position];
		
		$users_under=$common->get_chain_below($pos,$db_object,$twodarr);
		
		$users_under_id=$common->get_user_id($db_object,$users_under);
		
		for($i=0;$i<count($users_under_id);$i++)
		{
			$users_id[$i]=$users_under_id[$i][user_id];
		}
		
		//$users_id=$common->return_direct_reports($db_object,$user_id);
		}
		else
		{
			$sql="select user_id from $user_table where user_id <>'$user_id'";
			
			$users_id=$db_object->get_single_column($sql);
		}
		
		
		if($users_id[0]!="")
		{
			$users=@implode(",",$users_id);
				
		$current_date=time()-(7*24*60*60);

		$today = date("Y-m-d H:i:s ",$current_date);                         

		$sql="select rater_userid as rater_id,cur_userid as user_id,date_format(date_rating_requested,'%m.%d.%Y.%i:%s') as date,email
		
		 from $other_raters,$user_table where $other_raters.cur_userid=$user_table.user_id and
		 
		  cur_userid<>rater_userid and date_rating_requested<'$today' and 
		  
		  rating_over='n' and rater_userid in"."(".$users.")"."order by date asc";
	
		$result1=$db_object->get_rsltset($sql);
		
		for($a=0;$a<count($result1);$a++)
		{
			$result1[$a][test_type]="Inter Personal";
		}
		
		$sql="select ref_userid as rater_id,user_to_rate as user_id,date_format(date_rating_requested,'%m.%d.%Y.%i:%s') as date,email
		
		from $tech_references,$user_table where $tech_references.user_to_rate=$user_table.user_id and
		
		user_to_rate<>ref_userid and date_rating_requested<'$today' and 
		
		rating_over='n' and ref_userid in "."(".$users.")"." order by date asc";

		$result2=$db_object->get_rsltset($sql);
		
		for($a=0;$a<count($result2);$a++)
		{
			$result2[$a][test_type]="Technical";
		}
		
		$result1=@array_merge($result1,$result2);
		
		$pattern="/<{user_loopstart}>(.*?)<{user_loopend}>/s";
					
		$path=$common->path;
		
		$xtemplate=$path."templates/career/reports_compliance_others.html";
		
		$file=$common->return_file_content($db_object,$xtemplate);
			
		preg_match($pattern,$file,$match);
		
		$match=$match[0];
		
		for($a=0;$a<count($result1);$a++)
		{
			$userid=$result1[$a][rater_id];
			
			$user=$result1[$a][user_id];
	
			$name=$common->name_display($db_object,$userid);
			
			$reqby=$common->name_display($db_object,$user);
			
			$rating=$result1[$a][test_type];
			
			$date=$result1[$a][date];
			
			$email=$result1[$a][email];
			
			$str.=preg_replace("/<{(.*?)}>/e","$$1",$match);
		}
		if($result1[0]!="")
		{
		$file=preg_replace($pattern,$str,$file);
		
		$file=$common->direct_replace($db_object,$file,$xArray);
		
		echo $file;
		}
		else
		{
			echo $error_msg['cNoPending'];
			
			include_once("footer.php");exit;
		}
		
		
		
		}
		else
		{
			echo $error_msg['cNoReports'];
			
			include_once("footer.php");
			
			exit;
		}
	}

}
$obj=new reports();

$obj->reports_compliance($db_object,$common,$user_id,$error_msg);

include_once("footer.php");

?>

