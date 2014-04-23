<?php
include_once("../session.php");

include_once("header.php");

class outstanding
{
	function outstanding_assignments($db_object,$common,$user_id,$error_msg,$gbl_date_format)
	{
		$path=$common->path;
		
		$xtemplate=$path."templates/career/outstanding_assignments_appraisal.html";
		
		$file=$common->return_file_content($db_object,$xtemplate);
		
		$appraisal=$common->prefix_table("appraisal");
		
		$user_table=$common->prefix_table("user_table");
		
		$user_tests=$common->prefix_table("user_tests");
		
		$skills=$common->prefix_table("skills");
		
		$other_raters=$common->prefix_table("other_raters");
		
		$tech_references=$common->prefix_table("tech_references");
			
		$qry="select user_id from $user_table where admin_id='$user_id'";
		
		$users=$db_object->get_single_column($qry);	
		
		if(count($users)>0)
		{
			$users=@implode(",",$users);
			
			$users_set="(".$users.")";
			
			if($user_id!=1)
			{
			
				$sql="select user_id,test_mode,test_type,date_format(date_assigned,'$gbl_date_format') as date_assigned from $appraisal where 
				
				user_id in $users_set";
			}
			else
			{
				$sql="select user_id,test_mode,test_type,date_format(date_assigned,'$gbl_date_format') as date_assigned from $appraisal";
			}
		
			$result=$db_object->get_rsltset($sql);
					
			
		}
		
		if($result[0]=="")
		{
			echo $error_msg['cNoAppraisalAssigned'];
			
			include_once("footer.php");
			
			exit;
		}
		
		$pattern="/<{row_loopstart}>(.*?)<{row_loopend}>/s";
				
		preg_match($pattern,$file,$match);
		
		$match=$match[0];
							
		for($i=0;$i<count($result);$i++)
		{
			
			$date=$result[$i][date_assigned];
			
			$user=$result[$i][user_id];
			
			$username=$common->name_display($db_object,$user);
			
			$mail_qry="select email from $user_table where user_id='$user'";
			
			$mail_res=$db_object->get_a_line($mail_qry);
			
			$email=$mail_res[email];
			
			$test_mode=$result[$i][test_mode];
			
			$test_type=$result[$i][test_type];
			
			if($test_type=='i')
			{
				$skill_type="Inter Personal";
			}
			if($test_type=='t')
			{
				$skill_type="Technical";
			}
			
			if($test_mode=='Test' )
			{
				$qry="select test_completed from $user_tests where user_id='$user' and test_type='$test_type'";

				$res=$db_object->get_a_line($qry);
				
				$status=$res[test_completed];
				
				if($status=='y')
				{
					$status='a';
				}
			}
			if($test_mode=='360')
			{
				if($test_type=='i')
				{

					$qry="select status from $other_raters where cur_userid='$user'";
					
					$res=$db_object->get_a_line($qry);
					
					$status=$res[status];
				}
				if($test_type=='t')
				{
					$qry="select status from $tech_references where user_to_rate='$user'";
					
					$res=$db_object->get_a_line($qry);
					
					$status=$res[status];
				}
				
			}
			
			if($status!='a')
			{
				$highlight="class=TR";
				
			}
			else
			{
				$highlight="";
			}
			
			$str.=preg_replace("/<{(.*?)}>/e","$$1",$match);
		}

		$file=preg_replace($pattern,$str,$file);

		$file=$common->direct_replace($db_object,$file,$xArray);
		
		echo $file;
		
	}
}

$obj=new outstanding();

$obj->outstanding_assignments($db_object,$common,$user_id,$error_msg,$gbl_date_format);

include_once("footer.php");

?>
