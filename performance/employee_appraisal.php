<?
include_once("../session.php");

include_once("header.php");

class appraisal
{
	function save_appraisal_results($db_object,$common,$_POST)
	{
		
		$assign_performance_appraisal=$common->prefix_table("assign_performance_appraisal");
		
		$performance_appraisal=$common->prefix_table("performance_appraisal");
		
		$appraisal_results=$common->prefix_table("appraisal_results");
		
		$app_id=$_POST[fAppraisal_id];
		
		$user_id=$_POST[user_id];
		
		$boss_id=$_POST[boss_id];
		
		$qry="select dummy_id from $performance_appraisal where appraisal_id='$app_id'
		
		group by appraisal_id";
		
		$result=$db_object->get_rsltset($qry);
		
		$sql="select dummy_id from $performance_appraisal where appraisal_id='$app_id'
		
		order by dummy_id asc limit 1";
		
		$sql_res=$db_object->get_a_line($sql);
		
		$dummy_id=$sql_res[dummy_id];
		
		$sql1="select dummy_id from $appraisal_results where appraisal_id='$app_id'
		
		order by dummy_id asc limit 1";
		
		$sql1_res=$db_object->get_a_line($sql1);
		
		$dummy=$sql1_res[dummy_id];
		
		$arr=@array_splice($_POST,3);
		
		$keys=@array_keys($arr);
		
	
		  $c=count($arr)-5;
		
		  $key=$keys[$c];
		  
		  $exp=explode("_",$key);
		  
		  $obj_no=$exp[1];//no of objectives
		  

		  
		for($i=0;$i<(count($arr)-4);$i++)
		{

			if($i==0)
			{
				
				$check=$keys[$i];
				
				$obj_id=$arr[$check];
				
				$chk=explode("_",$check);
				
				$check_obj=$chk[1];
				
				$i++;
			
			}
		
			$check_key1=$keys[$i];
			
			$check_key=explode("_",$check_key1);
			
			$c=count($check_key)-1;
			
			$who=$check_key[$c];
			
			if($who==0)
			{
				$who='b';
			}
			else
			{
				$who='r';
			}
			
			if($check_key[0]!="fPoint2")
			{
				
			$rater_id=$arr[$check_key1];
			
			$i++;
			
			$key=$keys[$i];
			
			$rater_comment=$arr[$key];
			
			if($sql_res[0]=="")
			{
			$qry="insert into $performance_appraisal set user_id='$user_id',rater_id='$rater_id',
			
			rater_comment='$rater_comment',o_id='$obj_id',who='$who',appraisal_id='$app_id',boss_id='$boss_id'";
			
			$db_object->insert($qry);
			}
			else
			{
			$qry="update $performance_appraisal set user_id='$user_id',boss_id='$boss_id',rater_id='$rater_id',
			
			rater_comment='$rater_comment',o_id='$obj_id',who='$who',appraisal_id='$app_id'
			
			where dummy_id='$dummy_id'";
			
			$db_object->insert($qry);
			
			$dummy_id++;
			}
			
			
			
			}
			else
			{
						
				$rater_point=$arr[$check_key1];
				
				$final_point2=$final_point2+$rater_point;
				
				$i++;
				
				$exp_key=$keys[$i];
				
				$rater_exp=$arr[$exp_key];
				
				$i++;
				if($sql1_res[0]=="")
				{
				
				$sql="insert into $appraisal_results set user_id='$user_id',appraisal_id='$app_id',
				
				o_id='$obj_id',rater_point='$rater_point',rater_summary='$rater_exp',boss_id='$boss_id'";
				
				//echo $sql;
				
				$db_object->insert($sql);
				}
				else
				{
				$sql="update $appraisal_results set user_id='$user_id',boss_id='$boss_id',appraisal_id='$app_id',
				
				o_id='$obj_id',rater_point='$rater_point',rater_summary='$rater_exp' where 
				
				dummy_id='$dummy'";
				
				$dummy++;
				
				//echo $sql;
				
				$db_object->insert($sql);
				}
				
				$arr=array_splice($arr,$i);
				
				$keys=@array_keys($arr);
				
				$i=-1;
							
			}
			
			
		}
		
	
		
		//$key=$keys[$i];
		
		//$final_point2=$arr[$key];
		
		$i++;
		
		$key=$keys[$i];
		
		$final_summary=$arr[$key];
		
		$qry1="select dummy_id from $appraisal_results where appraisal_id='$app_id'
		
		order by dummy_id asc limit 1";
		
		$res1=$db_object->get_a_line($qry1);
		
		$dummy_id=$res1[dummy_id];
		
		for($i=0;$i<$obj_no;$i++)
		{
			$qry2="update $appraisal_results set
			
			final_point2='$final_point2',final_summary='$final_summary' where dummy_id='$dummy_id'";

			//echo $qry2;
			
			$db_object->insert($qry2);
			
			$dummy_id++;
		}
			
	}
	
	function approve_appraisal($db_object,$common,$user_id,$boss_id,$error_msg)
	{
		$performance_appraisal=$common->prefix_table("performance_appraisal");
		
		$assign_performance_appraisal=$common->prefix_table("assign_performance_appraisal");
		
		$appraisal_results=$common->prefix_table("appraisal_results");
		
		$performance_message=$common->prefix_table("performance_message");
		
		$user_table=$common->prefix_table("user_table");
		
		$imm_boss_id=$common->immediate_boss($db_object,$boss_id);
		
		$app_id=$_POST[fAppraisal_id];
		
		$up_qry="update $assign_performance_appraisal set submitted_on=now() where dummy_id='$app_id'";
		
		$db_object->insert($up_qry);
		
		$user_id=$_POST[user_id];
		
		$boss_id=$_POST[boss_id];
		
		$qry="select dummy_id from $performance_appraisal where appraisal_id='$app_id'
		
		group by appraisal_id";
		
		$result=$db_object->get_rsltset($qry);
	
		
		$sql="select dummy_id from $performance_appraisal where appraisal_id='$app_id'
		
		order by dummy_id asc limit 1";
		
		$sql_res=$db_object->get_a_line($sql);
		
		$dummy_id=$sql_res[dummy_id];
		
		$sql1="select dummy_id from $appraisal_results where appraisal_id='$app_id'
		
		order by dummy_id asc limit 1";
		
		$sql1_res=$db_object->get_a_line($sql1);
		
		$dummy=$sql1_res[dummy_id];
		
		$arr=@array_splice($_POST,3);
		
		$keys=@array_keys($arr);
		
	
		  $c=count($arr)-5;
		
		  $key=$keys[$c];
		  
		  $exp=explode("_",$key);
		  
		  $obj_no=$exp[1];//no of objectives
		  

		  
		for($i=0;$i<(count($arr)-4);$i++)
		{

			if($i==0)
			{
				
				$check=$keys[$i];
				
				$obj_id=$arr[$check];
				
				$chk=explode("_",$check);
				
				$check_obj=$chk[1];
				
				$i++;
			
			}
		
			$check_key1=$keys[$i];
			
			$check_key=explode("_",$check_key1);
			
			$c=count($check_key)-1;
			
			$who=$check_key[$c];
			
			if($who==0)
			{
				$who='b';
			}
			else
			{
				$who='r';
			}
			
			if($check_key[0]!="fPoint2")
			{
				
			$rater_id=$arr[$check_key1];
			
			$i++;
			
			$key=$keys[$i];
			
			$rater_comment=$arr[$key];
			
			if($sql_res[0]=="")
			{
			$qry="insert into $performance_appraisal set user_id='$user_id',rater_id='$rater_id',
			
			rater_comment='$rater_comment',o_id='$obj_id',who='$who',appraisal_id='$app_id',boss_id='$boss_id'";
			
			$db_object->insert($qry);
			}
			else
			{
			$qry="update $performance_appraisal set user_id='$user_id',rater_id='$rater_id',boss_id='$boss_id',
			
			rater_comment='$rater_comment',o_id='$obj_id',who='$who',appraisal_id='$app_id'
			
			where dummy_id='$dummy_id'";
			
			$db_object->insert($qry);
			
			//echo $qry;
				
			$dummy_id++;
			}
			
		
			}
			else
			{
						
				$rater_point=$arr[$check_key1];
				
				$final_point2=$final_point2+$rater_point;
				
				$i++;
				
				$exp_key=$keys[$i];
				
				$rater_exp=$arr[$exp_key];
				
				$i++;
				if($sql1_res[0]=="")
				{
				
				$sql="insert into $appraisal_results set user_id='$user_id',appraisal_id='$app_id',boss_id='$boss_id',
				
				o_id='$obj_id',rater_point='$rater_point',rater_summary='$rater_exp'";
				
				//echo $sql;
				
				$db_object->insert($sql);
				}
				else
				{
				$sql="update $appraisal_results set user_id='$user_id',appraisal_id='$app_id',boss_id='$boss_id',
				
				o_id='$obj_id',rater_point='$rater_point',rater_summary='$rater_exp' where 
				
				dummy_id='$dummy'";
				
				$dummy++;
				
				//echo $sql;
				
				$db_object->insert($sql);
				}
				
				$arr=array_splice($arr,$i);
				
				$keys=@array_keys($arr);
				
				$i=-1;
							
			}
			
			
		}
		
	
		
		//$key=$keys[$i];
		
		//$final_point2=$arr[$key];
		
		$i++;
		
		$key=$keys[$i];
		
		$final_summary=$arr[$key];
		
		$qry1="select dummy_id from $appraisal_results where appraisal_id='$app_id'
		
		order by dummy_id asc limit 1";
		
		$res1=$db_object->get_a_line($qry1);
		
		$dummy_id=$res1[dummy_id];
		
		for($i=0;$i<$obj_no;$i++)
		{
			$qry2="update $appraisal_results set boss_id='$boss_id',
			
			final_point2='$final_point2',final_summary='$final_summary' where dummy_id='$dummy_id'";
			
			//echo $qry2;
			
			$db_object->insert($qry2);
			
			$dummy_id++;
		}
		
		
		$dummy_id=$dummy_id-$obj_no;
		
		
		$sql="select dummy_id from $assign_performance_appraisal where user_id='$user_id' and 
		
		boss_user_id='$imm_boss_id' and status<>'h'";
		
		$res=$db_object->get_a_line($sql);
		
		if($res[0]=="")
		{
		
		$qry="insert into $assign_performance_appraisal set user_id='$user_id',
		
		boss_user_id='$imm_boss_id',date_added=now(),check_status='n'";
				
		//echo $qry;
		
		$db_object->insert($qry);
		
		$qry="select appraisal_approved_subject_1,appraisal_approved_message_1 from $performance_message";
		
		$mail=$db_object->get_a_line($qry);
		
		$subject=$mail[appraisal_approved_subject_1];
		
		$message=$mail[appraisal_approved_message_1];
		
		$sql="select email,username from $user_table where user_id in('$imm_boss_id','1')";
		
		$res=$db_object->get_rsltset($sql);
		
		$sql1="select username,email from $user_table where user_id='$user_id'";
		
		$result=$db_object->get_a_line($sql1);
		
		$sql2="select username,email from $user_table where user_id='$boss_id'";
		
		$result2=$db_object->get_a_line($sql2);
		
		$from=$result2[email];$boss=$result2[username];
		
		$user=$result[username];
		
		$admin=$res[0][username];$to[0]=$res[0][email];
		
		$imm_boss[0]=$res[0][username];
		
		$to[1]=$res[1][email];$imm_boss[1]=$res[1][username];
		
		$click="http://www.cat45.com/Pms/performance/complete_employee_appraisal.php";
		
		$click=$common->direct_replace($db_object,$click,$arr);
		
		for($i=0;$i<2;$i++)
		{
		
		$xArray=array("imm_boss"=>$imm_boss[$i],"user"=>$user,"click"=>$click,"boss"=>$boss);
		
		$message1=$common->direct_replace($db_object,$message,$xArray);
					
		$common->send_mail($to[$i],$subject,$message1,$from);
		}
		
		echo $error_msg['cAppraisalSubmitted'];
		}
		else
		{
			
			echo $error_msg['cAppraisalAlreadyAssigned'];
		}
	}
	
	function final_approval($db_object,$common,$_POST,$error_msg)
	{
	
		
		$performance_appraisal=$common->prefix_table("performance_appraisal");
		
		$appraisal_results=$common->prefix_table("appraisal_results");
		
		$assign_performance_appraisal=$common->prefix_table("assign_performance_appraisal");
		
		$approved_performance_appraisal=$common->prefix_table("approved_performance_appraisal");
		
		$approved_appraisal_results=$common->prefix_table("approved_appraisal_results");
		
		$performance_message=$common->prefix_table("performance_message");
		
		$user_table=$common->prefix_table("user_table");
				
		$app_id=$_POST[fAppraisal_id];
		
		$user_id=$_POST[user_id];
		
		$boss_id=$_POST[boss_id];
		
		$qry="select dummy_id from $performance_appraisal where appraisal_id='$app_id'
		
		group by appraisal_id";
		
		$result=$db_object->get_rsltset($qry);
	
		
		$sql="select dummy_id from $performance_appraisal where appraisal_id='$app_id'
		
		order by dummy_id asc limit 1";
		
		$sql_res=$db_object->get_a_line($sql);
		
		$dummy_id=$sql_res[dummy_id];
		
		$sql1="select dummy_id from $appraisal_results where appraisal_id='$app_id'
		
		order by dummy_id asc limit 1";
		
		$sql1_res=$db_object->get_a_line($sql1);
		
		$dummy=$sql1_res[dummy_id];
		
		$arr=@array_splice($_POST,3);
		
		$keys=@array_keys($arr);
		
	
		  $c=count($arr)-5;
		
		  $key=$keys[$c];
		  
		  $exp=explode("_",$key);
		  
		  $obj_no=$exp[1];//no of objectives
		  

		  
		for($i=0;$i<(count($arr)-4);$i++)
		{

			if($i==0)
			{
				
				$check=$keys[$i];
				
				$obj_id=$arr[$check];
				
				$chk=explode("_",$check);
				
				$check_obj=$chk[1];
				
				$i++;
			
			}
		
			$check_key1=$keys[$i];
			
			$check_key=explode("_",$check_key1);
			
			$c=count($check_key)-1;
			
			$who=$check_key[$c];
			
			if($who==0)
			{
				$who='b';
			}
			else
			{
				$who='r';
			}
			
			if($check_key[0]!="fPoint2")
			{
				
			$rater_id=$arr[$check_key1];
			
			$i++;
			
			$key=$keys[$i];
			
			$rater_comment=$arr[$key];
			
			if($sql_res[0]=="")
			{
			$qry="insert into $performance_appraisal set user_id='$user_id',rater_id='$rater_id',
			
			rater_comment='$rater_comment',o_id='$obj_id',who='$who',appraisal_id='$app_id',boss_id='$boss_id'";
			
			$db_object->insert($qry);
			}
			else
			{
			$qry="update $performance_appraisal set user_id='$user_id',rater_id='$rater_id',
			
			rater_comment='$rater_comment',o_id='$obj_id',who='$who',appraisal_id='$app_id',boss_id='$boss_id'
			
			where dummy_id='$dummy_id'";
			
			$db_object->insert($qry);
			
			//echo $qry;
				
			$dummy_id++;
			}
			
		
			}
			else
			{
						
				$rater_point=$arr[$check_key1];
				
				$final_point2=$final_point2+$rater_point;
				
				$i++;
				
				$exp_key=$keys[$i];
				
				$rater_exp=$arr[$exp_key];
				
				$i++;
				if($sql1_res[0]=="")
				{
				
				$sql="insert into $appraisal_results set user_id='$user_id',appraisal_id='$app_id',boss_id='$boss_id',
				
				o_id='$obj_id',rater_point='$rater_point',rater_summary='$rater_exp'";
				
				//echo $sql;
				
				$db_object->insert($sql);
				}
				else
				{
				$sql="update $appraisal_results set user_id='$user_id',appraisal_id='$app_id',boss_id='$boss_id',
				
				o_id='$obj_id',rater_point='$rater_point',rater_summary='$rater_exp' where 
				
				dummy_id='$dummy'";
				
				$dummy++;
				
				//echo $sql;
				
				$db_object->insert($sql);
				}
				
				$arr=array_splice($arr,$i);
				
				$keys=@array_keys($arr);
				
				$i=-1;
							
			}
			
			
		}
		
	
		
		//$key=$keys[$i];
		
		//$final_point2=$arr[$key];
		
		$i++;
		
		$key=$keys[$i];
		
		$final_summary=$arr[$key];
		
		$qry1="select dummy_id from $appraisal_results where appraisal_id='$app_id'
		
		order by dummy_id asc limit 1";
		
		$res1=$db_object->get_a_line($qry1);
		
		$dummy_id=$res1[dummy_id];
		
		for($i=0;$i<$obj_no;$i++)
		{
			$qry2="update $appraisal_results set boss_id='$boss_id',
			
			final_point2='$final_point2',final_summary='$final_summary' where dummy_id='$dummy_id'";
			
			//echo $qry2;
			
			$db_object->insert($qry2);
			
			$dummy_id++;
		}
		
		
		
	
		$user_id=$_POST[user_id];
		
	
		$check_qry="select * from $approved_performance_appraisal where user_id='$user_id'
		
		and appraisal_id='$app_id'";
		
		$check_result=$db_object->get_a_line($check_qry);
		
		if($check_result[0]!="")
		{
			echo $error_msg['cAppraisalAlreadyAssigned'];
		}
		else
		{
		
		$sql="select * from $performance_appraisal where user_id='$user_id' order by dummy_id asc";
		
		$result=$db_object->get_rsltset($sql);
		
		$sql1="select * from $appraisal_results where user_id='$user_id' order by dummy_id asc";
		
		$result1=$db_object->get_rsltset($sql1);
		
		$sql2="select date_added from $assign_performance_appraisal where user_id='$user_id'
		
		order by dummy_id asc limit 1";
		
		$result2=$db_object->get_a_line($sql2);
		
		$added_on=$result2[date_added];
		
		for($i=0;$i<count($result);$i++)
		{
			$appraisal_id=$result[$i][appraisal_id];
			
			$o_id=$result[$i][o_id];
						
			$user_id=$result[$i][user_id];
			
			$rater_id=$result[$i][rater_id];
						
			$rater_comment=$result[$i][rater_comment];
			
			$rater_comment=addcslashes($rater_comment,"'");
			
			$who=$result[$i][who];
			
			$qry="insert into $approved_performance_appraisal set appraisal_id='$appraisal_id',
			
			o_id='$o_id',user_id='$user_id',rater_id='$rater_id',rater_comment='$rater_comment',
			
			who='$who',status='h',approved_on=now(),added_on='$added_on',boss_id='$boss_id' ";
					
			
			
			$db_object->insert($qry);
		}
	
		for($j=0;$j<count($result1);$j++)
		{
			$appraisal_id=$result1[$j][appraisal_id];
			
			$o_id=$result1[$j][o_id];
			
			$final_point2=$result1[$j][final_point2];
			
			$final_summary=$result1[$j][final_summary];
			
			$final_summary=addcslashes($final_summary,"'");
			
			$rater_point=$result1[$j][rater_point];
			
			$rater_summary=$result1[$j][rater_summary];
			
			$rater_summary=addcslashes($rater_summary,"'");
			
			$user_id=$result1[$j][user_id];
			
			$qry1="insert into $approved_appraisal_results set appraisal_id='$appraisal_id',o_id='$o_id',
			
			final_point2='$final_point2',final_summary='$final_summary',rater_point='$rater_point',
			
			rater_summary='$rater_summary',user_id='$user_id',boss_id='$boss_id',added_on='$added_on',
			
			approved_on=now(),status='h'";
					
			$db_object->insert($qry1);
			
			
			
			
		}
		}
		$qry4="delete from $performance_appraisal where user_id='$user_id'";
		
		$qry5="delete from $appraisal_results where user_id='$user_id'";
		
		//$qry6="delete from $assign_performance_appraisal where user_id='$user_id'";
		
		$qry6="update $assign_performance_appraisal set status='h' where user_id='$user_id'";
		
		$db_object->insert($qry4);
		
		$db_object->insert($qry5);
		
		$db_object->insert($qry6);
		
		echo $error_msg['cAppraisalApproved'];
		
		$mail_qry="select appraisal_approved_subject_final_1,appraisal_approved_message_final_1
		
		from $performance_message";
		
		$mail_res=$db_object->get_a_line($mail_qry);
		
		$subject=$mail_res[appraisal_approved_subject_final_1];
		
		$message=$mail_res[appraisal_approved_message_final_1];
		
		$imm_boss_id=$common->immediate_boss($db_object,$user_id);
		
		$user_qry="select username,email from $user_table where user_id in ('$user_id','$imm_boss_id')";
		
		$user_res=$db_object->get_rsltset($user_qry);
		
		$admin_qry="select username,email from $user_table where user_id='1'";
		
		$admin_res=$db_object->get_a_line($admin_qry);
		
		$qry="select $user_table.username,$approved_appraisal_results.approved_on from 
		
		$approved_appraisal_results,$user_table where $approved_appraisal_results.user_id=$user_table.user_id order by $approved_appraisal_results.dummy_id desc limit 1";
		
		$res=$db_object->get_a_line($qry);
		
		$date=$res[approved_on];
		
		
		
		$click="http://www.cat45.com/Pms/performance/view_approved_appraisal.php";
		
		for($i=0;$i<2;$i++)
		{
			$xArray[emp]=$res[username];
			
			$xArray[user]=$user_res[$i][username];
			
			$xArray[click]=$click;
			
			$to=$user_res[$i][email];
			
			$from=$admin_res[email];
			
			$subject=$subject;
			
			
			$message1=$common->direct_replace($db_object,$message,$xArray);
			
						
			$common->send_mail($to,$subject,$message1,$from);
		}
		
	}//end of function
	function reject_appraisal($db_object,$common,$_POST,$error_msg)
	{
		$assign_performance_appraisal=$common->prefix_table("assign_performance_appraisal");
		
		$performance_appraisal=$common->prefix_table("performance_appraisal");
		
		$appraisal_results=$common->prefix_table("appraisal_results");
		
		$app_id=$_POST[fAppraisal_id];
		
		$user_id=$_POST[user_id];
		
		$boss_id=$_POST[boss_id];
		
			
		$imm_boss_id=$common->immediate_boss($db_object,$boss_id);
				
		$sql="select dummy_id from $assign_performance_appraisal where user_id='$user_id' and 
		
		boss_user_id='$imm_boss_id'";
		
		$res=$db_object->get_a_line($sql);
		
		if($res[0]=="")
		{
		
		$delqry="delete from $assign_performance_appraisal where dummy_id='$app_id'";
		
		$db_object->insert($delqry);
		
		$dummy_qry="select dummy_id from $assign_performance_appraisal where user_id='$user_id'
		
		order by dummy_id desc limit 1";
		
		$dummy_res=$db_object->get_a_line($dummy_qry);
		
		$dummy=$dummy_res[dummy_id];
		
		$fFinal_explanation=$_POST[fFinal_explanation];
		
		$ins_qry="update $assign_performance_appraisal set status='r',reject_exp='$fFinal_explanation',
		
		rejection_date=now() where dummy_id='$dummy'";
	//	echo $ins_qry;
		$db_object->insert($ins_qry);
		
		$del_sql="delete from $performance_appraisal where user_id='$user_id' and boss_id='$boss_id'";
		//echo $del_sql;
		//$db_object->insert($del_sql);
	
		$del_sql1="delete from $appraisal_results where user_id='$user_id' and boss_id='$boss_id'";
	//	echo $del_sql1;
		$db_object->insert($del_sql1);
		
		}
		else
		{
			echo $error_msg['cAppraisalAlreadyAssigned'];
		}
		
		
	}
	

	
	

}

$obj=new appraisal();

if($fSave)
{
	$action="save";
}
if($fApprove)
{
	$action="approve";
}
if($fReject)
{
	$action="reject";
}

switch($action)
{
	case "save":
	
		$user_id=$_POST[user_id];
		
		$boss_id=$_POST[boss_id];
		
		$performance_appraisal=$common->prefix_table("performance_appraisal");
		
		$appraisal_results=$common->prefix_table("appraisal_results");
		
		$assign_performance_appraisal=$common->prefix_table("assign_performance_appraisal");
		
		$position=$common->prefix_table("position");
		
		$check_boss=$common->immediate_boss($db_object,$boss_id);
		
		$qry="select boss_user_id from $assign_performance_appraisal where user_id='$user_id' and boss_user_id not in ('$boss_id')";
		
		$result=$db_object->get_single_column($qry);
				
		$act=0;
		
		for($i=0;$i<count($result);$i++)
		{
			if($check_boss==$result[$i])
			{
				$act=1;
			}
						
		}
		if($act!=1)
		{
		$obj->save_appraisal_results($db_object,$common,$_POST);
		
		echo $error_msg['cInformationSaved'];
		}
		else
		{
			
			echo $error_msg['cCouldNotSave'];
		}
	
	
	break;
	
	case "approve":
	
		$user_id=$_POST[user_id];
		
		$boss_id=$_POST[boss_id];
		
		if($boss_id!=1)
		{
		
		$obj->approve_appraisal($db_object,$common,$user_id,$boss_id,$error_msg);
		
		}
		else
		{
						
			$obj->final_approval($db_object,$common,$_POST,$error_msg);
		}
	
	break;
	
	case "reject":
	
		$obj->reject_appraisal($db_object,$common,$_POST,$error_msg);
		
		break;
		
	
	
}
include_once("footer.php");
?>
