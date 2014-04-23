<?
include_once("../session.php");
include_once("header.php");
class persetting
	{
		function view($db_object,$common,$post_var,$err,$user_id,$gbl_gr_select)
		{
			while(list($key,$value)=@each($post_var))
			{
				$$key = $value;
			}	

			$path = $common->path;
			$path = $path."templates/performance/per_setting.html";
			$file = $common->return_file_content($db_object,$path,$user_id);
		//tables
			$user = $common->prefix_table("user_table");
			$alert_table = $common->prefix_table("performance_alert");
			$reject = $common->prefix_table("rejected_objective");		
			$unappcat = $common->prefix_table("unapproved_category");
			$app_feedback = $common->prefix_table("approved_feedback");
			$position_table=$common->prefix_table("position");
			$unapproveduser_objective=$common->prefix_table("unapproveduser_objective");
			$performance_alert=$common->prefix_table("performance_alert");
			$plan=$common->prefix_table("plan");
			$plan_improvement=$common->prefix_table("plan_improvement");
			$temp_plan_improvement=$common->prefix_table("temp_plan_improvement");
			$approved_selected_objective  = $common->prefix_table("approved_selected_objective");
			$performance_feedback = $common->prefix_table("performance_feedback");
			$unapproved_plan_improvement=$common->prefix_table("unapproved_plan_improvement");
			$assign_performance_appraisal = $common->prefix_table("assign_performance_appraisal");
			$approved_performance_appraisal= $common->prefix_table("approved_performance_appraisal");

	//graph change
		$pattern_graphch = "/<{graphchange_loopstart}>(.*?)<{graphchange_loopend}>/s";		
		preg_match($pattern_graphch,$file,$gr);
		$match_gr = $gr[1];
				
	//displaying the alert link
		//
			$cres = 0;
			$cares = 0;
			$crres = 0;
			$cappres = 0;
			$crejres = 0;
		//plan approval
		   if($user_id=='1')
		   {
			$qry = "select user_id from $unappcat where status='NP' group by user_id";
			$res = $db_object->get_single_column($qry);
		 	$cres = count($res);
       		   }

		//check objective approval
			$usqry  = "select position from $user where user_id='$user_id'";
			$usres = $db_object->get_a_line($usqry);
			$bossid = $usres['position'];
			$aqry ="select user_id from $alert_table where boss_id='$bossid'";
			$ares = $db_object->get_single_column($aqry);
			$cares = count($ares);

		//rejected plan
			$rejqry = "select user_id from $unappcat where status='RJ' and user_id='$user_id' group by user_id";
			$rejres = $db_object->get_single_column($rejqry);
			$crejres = count($rejres);

		//performance verification
			$appqry = "select user_id from $app_feedback where boss_id='$user_id' and status='1' and active='A'
					and (reject is null or reject='') group by user_id";	
			$appres = $db_object->get_single_column($appqry);			
			$cappres = count($appres);						
		
		//check Rejected objective
			$rqry = "select count(r_id),user_id,boss_id from $reject where user_id='$user_id' group by r_id";
			$rres = $db_object->get_a_line($rqry);
			$crres = $rres[0];	


		//Highlight employee with Approved Objetcive
			$selqry="select position from $user where user_id='$user_id'";
			$userposition=$db_object->get_a_line($selqry);
			$temp_pos=$userposition["position"];
			$directreports= array();
			$directreports=$common->get_chain_below($temp_pos,$db_object,$twodim);
		//Lists the User from perfroamce alert table so that the USers are haev the unapproved Objectives

			if(!is_array($directreports))
			{
				$directreports= array();
			}
				$directreports[$arr_cnt] = $temp_pos;
			$pos=@implode("','",$directreports);
			$selqry="select distinct($user.user_id),$user.username,
			$alert_table.submit_date as date,user1.username as bossname,user1.email,user1.user_id as id from
			$user left join $alert_table  on $user.user_id=$alert_table.user_id
			left join $position_table on $user.position=$position_table.pos_id
			left join $user as  user1 on user1.position=$position_table.boss_no
			where $alert_table.user_id is not null  and $alert_table.boss_id in ('$pos') group by $alert_table.user_id";
			$employeeset=$db_object->get_rsltset($selqry);	
		
			$count_high = count($employeeset);
			if($count_high > 0)
			{
				$val['class'] = "highlight";
			}
		//------------

		//--
			
		//PERFORMANCE IMPROVEMENT PLAN ALERT


			
			$alert_qry="select plan_id from $plan where employee_id='$user_id' and check_status='n' and status='a'";
			//$alert_qry="select plan_id from $plan where employee_id='$user_id' and check_status='n'";
//			echo $alert_qry;
					
			$alert_res=$db_object->get_rsltset($alert_qry);
			
			if($alert_res[0]=="")
			{
				$file=preg_replace("/<{ifplan_loopstart}>(.*?)<{ifplan_loopend}>/s","",$file);
			}
			else
			{
				$file=preg_replace("/<{ifplan_loop(.*?)}>/s","",$file);
			}	
			

			
		//PERFORMANCE IMPROVEMENT PLAN APPROVAL ALERT
			
		$id=$common->employees_under_admin_boss($db_object,$user_id);
		
		if(count($id)>=1)
		{
			$id=@implode(",",$id);
			
			$id="(".$id.")";
			
			$plan_clause="and $plan_improvement.employee_id in $id";
		}
		else
		{
			
			
			$plan_clause="";
		}
		
		$al_qry="select $unapproved_plan_improvement.employee_id,$unapproved_plan_improvement.plan_id,$user.username
		
		from $unapproved_plan_improvement,$user where $unapproved_plan_improvement.status='u' and 
		
		$unapproved_plan_improvement.employee_id=$user.user_id  group by $unapproved_plan_improvement.plan_id";
		
		//echo $al_qry;
			
			//$al_qry="select * from $plan_improvement where status='u'".$plan_clause." group by plan_id";
			
			$al_res=$db_object->get_rsltset($al_qry);
			
			if($al_res[0]=="")
			{
				$file=preg_replace("/<{ifapp_loopstart}>(.*?)<{ifapp_loopend}>/s","",$file);
			}
			else
			{
				$file=preg_replace("/<{ifapp_(.*?)}>/s","",$file);
			}
			
			
		//VIEW PERFORMANCE PLAN
			
			$sql="select $plan.plan_id from $plan,$temp_plan_improvement where $plan.plan_id=$temp_plan_improvement.plan_id
			
			 and $plan.status='a' and $temp_plan_improvement.employee_id='$user_id' group by plan_id";
			 
			 $res=$db_object->get_rsltset($sql);
			 
			 //echo $sql;
			 if($user_id==1 || count($res)==0)
			{
				$file=preg_replace("/<{ifmain_loopstart}>(.*?)<{ifmain_loopend}>/s","",$file);
			}
			else
			{
				preg_match("/<{plan_loopstart}>(.*?)<{plan_loopend}>/s",$file,$match);
				
				$match=$match[0];
				
				for($i=0;$i<count($res);$i++)
				{
					$plan_id=$res[$i][plan_id];
					
					$j=$i+1;
					
					$id=$j;
					
					$str.=preg_replace("/<{(.*?)}>/e","$$1",$match);
				}
				$file=preg_replace("/<{plan_loopstart}>(.*?)<{plan_loopend}>/s",$str,$file);
				
				
			}
			$sql1="select * from $plan where employee_id='$user_id' and check_status='c'";
			
			$sql1_res=$db_object->get_rsltset($sql1);
			
			 if($user_id==1 || count($sql1_res)==0)
			{
				$file=preg_replace("/<{ifuserplan_loopstart}>(.*?)<{ifuserplan_loopend}>/s","",$file);
			}
			else
			{
				$file=preg_replace("/<{ifuserplan_(.*?)}>/s","",$file);
			}
			//ASSIGN PERFRMANCE APPROVAL	
			
			
		//IF PERFORMANCE APPRAISAL
		
		

		$app_sql="select dummy_id from $assign_performance_appraisal where boss_user_id='$user_id' and check_status<>'c'";
		
		$app_res=$db_object->get_single_column($app_sql);
		
		
		if($app_res[0]=="")
		{
			$file=preg_replace("/<{ifappraisal_loopstart}>(.*?)<{ifappraisal_loopend}>/s","",$file);	
		}
		else
		{
			$file=preg_replace("/<{ifappraisal_(.*?)}>/s","",$file);
		}

	//IF APPROVED APPRAISAL
		
		$ap_qry="select * from $approved_performance_appraisal where user_id='$user_id' and status='h'";
		
		$ap_res=$db_object->get_rsltset($ap_qry);
			
		if($ap_res[0]=="")
		{
			$file=preg_replace("/<{ifemp_appraisal_loopstart}>(.*?)<{ifemp_appraisal_loopend}>/s","",$file);	
		}
		else
		{
			$file=preg_replace("/<{ifemp_appraisal_(.*?)}>/s","",$file);
		}
		
		//If Employees Approved APPRAISALS
		
		$users=$common->return_direct_reports($db_object,$user_id);
		
		if(count($users)>1)
		{
			$user_1=@implode(",",$users);
			
			$user_2="(".$user_1.")";
			
		}
		else
		{
			$user_2="(".$users[0].")";
		}

		if($user_2!='()')
		{
		$sql="select $user.username,$approved_performance_appraisal.approved_on from
		
		 $user,$approved_performance_appraisal where 
		
		$approved_performance_appraisal.user_id=$user.user_id 
		
		and $approved_performance_appraisal.user_id in $user_2 group by approved_on";
		
		$sql_result=$db_object->get_rsltset($sql);
		

		if($sql_result[0]=="")
		{
			
			$file=preg_replace("/<{if_app_appraisal_loopstart}>(.*?)<{if_app_appraisal_loopend}>/s","",$file);		

		}
		else
		{
						
			$file=preg_replace("/<{if_app_appraisal_(.*?)}>/s","",$file);
		}
	
		}
		//Fix non approved appraisals
		
		$sql="select dummy_id from $assign_performance_appraisal 
		where boss_user_id='$user_id' and check_status='c'";
	
		$sql_res=$db_object->get_single_column($sql);
		
		if($sql_res[0]=="")
		{
			$file=preg_replace("/<{ifnonapp_loopstart}>(.*?)<{ifnonapp_loopend}>/s","",$file);		
		}
		else
		{
			$file=preg_replace("/<{ifnonapp_(.*?)}>/s","",$file);	
		}
		
		//If any Appraisal
		
		$qry="select dummy_id from $assign_performance_appraisal where user_id='$user_id'
		
		order by dummy_id desc limit 1";
		
		$res=$db_object->get_a_line($qry);
	
		if($res[0]=="")
		{
			$file=preg_replace("/<{ifany_appraisal_loopstart}>(.*?)<{ifany_appraisal_loopend}>/s","",$file);		
		}
		else
		{
			$val[dummy_id]=$res[dummy_id];
		
		}
		
		
	//APPRAISE AND VIEW APPRAISAL
		
		$temp=$common->is_admin($db_object,$user_id);
		
		$pattern = "/<{appraise_loopstart}>(.*)<{appraise_loopend}>/s";
		
		preg_match($pattern,$file,$mat);
		
		$replace=$mat[0];
		
		$pattern_app = "/<{view_appraisal_loopstart}>(.*)<{view_appraisal_loopend}>/s";
		
		preg_match($pattern_app,$file,$mat_1);
		
		$replace_1=$mat_1[0];
		//echo $repalce_1;
		if($temp)
		{
			$file = $common->view_dashboard($db_object,$user_id,$pattern,$replace,$file);		
			
			$file = $common->view_dashboard($db_object,$user_id,$pattern_app,$replace_1,$file);		
		}
	//VIEW OUTSTANDING ASSIGNMENTS
		$user_table=$common->prefix_table("user_table");
		
		$current_date=time()-(7*24*60*60);

		$today = date("Y-m-d H:i:s ",$current_date);  
	
		$qry="select user_id from $user_table where admin_id='$user_id'";
	
		$users=$db_object->get_single_column($qry);	
		
		$assign_performance_appraisal=$common->prefix_table("assign_performance_appraisal");	
		
		if(count($users)>0)
		{
			$users=@implode(",",$users);
			
			$users_set="(".$users.")";
			
			if($user_id!=1)
			{
			
				$sql="select user_id,boss_user_id,date_format(date_added,'%m.%d.%Y.%i:%s'),status from $assign_performance_appraisal where user_id in $users_set and date_added<'$today'";
				
			}
			else
			{
				$sql="select user_id,boss_user_id,date_format(date_added,'%m.%d.%Y.%i:%s'),status from $assign_performance_appraisal where date_added<'$today'";
				
				
			}
		
			$read=$db_object->get_rsltset($sql);
					
			
		}
$b=0;


		for($i=0;$i<count($read);$i++)
		{
			$user1=$read[$i][user_id];
						
			$assigned=$read[$i][boss_user_id];
					
			$status=$read[$i][status];
			
			if($status=="")
			{
				$status1[$b]=$user1;

				$b++;
			}
		
		}
		
		if(count($status1)>0)
		{
		$val[highlight]="class=highlight";
		}
		
		//-Highlight employees without verified updates
		
			$selqry="select position from $user where user_id='$user_id'";
			$userposition=$db_object->get_a_line($selqry);
			$temp_pos=$userposition["position"];

			$directreportspos=$common->get_chain_below($temp_pos,$db_object,$twodim);
			$directreportsname=$common->get_user_id($db_object,$directreportspos);

			$userids=$user_id;
			for($i=0;$i<count($directreportsname);$i++)
			{
				$userids.=",".$directreportsname[$i]["user_id"];
			}

		//--date of submission has to be updated
		//--this displays the whole chain of command in wihch they have dont have the verified updates

			$selqry="select distinct($user.user_id),$user.username,$app_feedback.boss_id,
			$app_feedback.approved_date as date,user1.username as bossname,user1.email from
			$user left join $app_feedback  on $user.user_id=$app_feedback.user_id
			left join $position_table on $user.position=$position_table.pos_id
			left join $user as  user1 on user1.position=$position_table.boss_no
			where $app_feedback.status=1 and $app_feedback.active='A' and $app_feedback.vaccept is NULL   and $app_feedback.boss_id in ($userids)";
			$employeeset=$db_object->get_rsltset($selqry);
				
			$count_update = count($employeeset);
			if($count_update > 0)
			{
				$val['class1']  = "highlight";
			}


		//-----------


			$val['cTotalalert'] = ($cres + $cares + $crejres + $crres + $cappres);
				//echo "1-$cres  2-$cares 3-$crejres  4-$crres  5-$cappres";
			$tot =  ($cres + $crejres + $cares + $crres + $cappres);

			$pattern1 = "/<!--alert_start-->(.*?)<!--alert_end-->/s";
			$space="";
									
			if($tot==0)
			{
				$file = preg_replace($pattern1,$space,$file);
			}
			$pattern2 = "/<!--setplan_start-->(.*?)<!--setplan_end-->/s";


			$set=$common->is_admin($db_object,$user_id);
			if($set==0)
			{
				$file = preg_replace($pattern2,$space,$file);

			}
//KK




$pattern3 = "/<{boss_dashboard_start}>(.*?)<{boss_dashboard_end}>/s";
$pattern4 = "/<{admins_area_loopstart}>(.*?)<{admins_area_loopend}>/s";
$pattern5 = "/<!--fix_start-->(.*?)<!--fix_end-->/s";
$pattern7 = "/<{view_objective_start}>(.*)<{view_objective_end}>/s";
preg_match($pattern7,$file,$mat1);
$replace1=$mat1[0];

	$temparr=$common->is_admin($db_object,$user_id);
	

if($temparr)
{	
	$file = $common->view_dashboard($db_object,$user_id,$pattern7,$replace1,$file);
}
else
{
	$file=preg_replace($pattern4,"",$file);
}
$temp=$common->is_boss($db_object,$user_id);

if(!$temp)
{
	$file=preg_replace($pattern3,"",$file);
}
	
//KK

			$rrqry = "select count(r_id),user_id,boss_id from $reject where user_id='$user_id' group by r_id";
			$rrres = $db_object->get_a_line($rrqry);
			if($rrres[0]!="")
			{
				$val["emp_id"]=$rrres['user_id'];
			}
			else
			{
				$file = preg_replace($pattern5,"",$file);
			}
			
		$pattern6 = "/<{view_dashboard_start}>(.*?)<{view_dashboard_end}>/s";
		preg_match($pattern6,$file,$mat);
		$replace=$mat[0];
			$file = $common->view_dashboard($db_object,$user_id,$pattern6,$replace,$file);
//performance summary (progress)
		$pattern9 = "/<{view_progress_loopstart}>(.*?)<{person_loopend}>/s";
		preg_match($pattern9,$file,$arr9);
		$match9 = $arr[0];


		$pattern8 = "/<{person_loopstart}>(.*?)<{person_loopend}>/s";
		preg_match($pattern8,$file,$arr8);
		$match8 = $arr8[0];
		$con="";			
		
		$below = $common->get_chain_below($temp_pos,$db_object,$arr);
		$below_user = $common->get_user_id($db_object,$below);
		$spt_user = array();
		for($c=0;$c<count($below_user);$c++)
		{
			$spt_user[] = $below_user[$c]['user_id'];
		}

		$split = @implode("','",$spt_user);

		if($user_id!=1)
			{
				$selqry="select user_id from $user where admin_id='$user_id' and $user.user_id not in('$split')";
			}
			else
			{
				$selqry="select $user.user_id from $user,$position_table where
					 $user.position=$position_table.pos_id and ($user.position<>NULL or $user.position<>0) and
					 $user.user_id!=1 and $user.user_id not in('$split')
					 order by $position_table.level_no desc";			
			}
			$userset=$db_object->get_single_column($selqry);

			if(!is_array($userset))
			{
				$userset = array();
			}

		for($b=0;$b<count($below_user);$b++)
		{	
			$userset[] = $below_user[$b]['user_id'];		
		}
				
		for($d=0;$d<count($userset);$d++)
		{	
			$uid = $userset[$d];
			$username = $common->name_display($db_object,$uid);
			$con.=preg_replace("/<{(.*?)}>/e","$$1",$match8);
		}	
		
		$file = preg_replace($pattern8,$con,$file);

	// TO display the "Document your Objective Link"
/*
	check whether the boss objective is approved once,if not, 
	document Your objective link and Update your
	objective Link should be disabled.
*/
		$im_boss = $common->immediate_boss($db_object,$user_id);
		if($user_id!='1')
		{							
		$dqry = "select count(sl_id) as slid from $approved_selected_objective where user_id='$im_boss' and 
				status='I'";
		}
		else
		{
		$dqry = "select count(sl_id) as slid from $approved_selected_objective where user_id='$user_id' and 
				status='I'";
		}		
		$dres = $db_object->get_single_column($dqry);
		$ct_slid = $dres[0];	
			
	
		$document_pattern = "/<!--document_objective_start-->(.*?)<!--document_objective_end-->/s";
		$update_pattern = "/<!--update_objective_start-->(.*?)<!--update_objective_end-->/s";

		if(($ct_slid==0))
		{
			$file = preg_replace($document_pattern,"",$file);
			$file = preg_replace($update_pattern,"",$file);			
		}
		else
		{
			$uqry = "select count(sl_id)  from $approved_selected_objective where user_id='$user_id' and 
				(status='I' or status='A')";

			$ures = $db_object->get_single_column($uqry);
			$ct_uslid = $ures[0];
			if($ct_uslid==0)
			{
				$file = preg_replace($update_pattern,"",$file);
			}
			else
			{
				$file = preg_replace($document_pattern,"",$file);
			}						
		}
		$val['userid'] = $user_id;
/*
highlight the link "update progress against my objective" 
when self rating alert comes
*/
		$performqry = "select sl_id from $performance_feedback where user_id='$user_id'
				 and request_from='$user_id' and latest='N' and status ='I' order by f_id";
		$performres = $db_object->get_single_column($performqry);

		$ct_per  = count($performres);

		if($ct_per==0)
		{
			$val['class2'] = "code";
		}
		else
		{
			$val['class2'] = "highlight";

		}
/*
	Highlight the Link "Provide Feedback to others" when 
	any rating has to done;
*/
		$otherqry = "select request_from,s_date from $performance_feedback where user_id='$user_id' 
		and request_from<>'$user_id' and  status='I'
		group by request_from order by f_id";

		$otherres = $db_object->get_rsltset($otherqry);
		$ct_other = count($otherres);
		
		if($ct_other==0)
		{
			$val['class3'] = "code";
		}
		else
		{
			$val['class3'] = "highlight";
		}



//graph change
	
		$gr_keys = array_keys($gbl_gr_select);
		$gr_admin = $common->is_admin($db_object,$user_id);
		$gr_boss = $common->is_boss($db_object,$user_id);
		if($fGraphchange=="")
		{
			$fGraphchange=1;
		}
		for($i=0;$i<count($gbl_gr_select);$i++)
		{
			$index = $gr_keys[$i];
			$selected ="";			
			if(($index==1)||($gr_admin==1 and $index==3)||($gr_boss==1 and $index==2))
			{
				$inval = $index;
				$indisplay = $gbl_gr_select[$index];
				if($index==$fGraphchange)
				{
					$selected = "selected";
				}
				$grp .= preg_replace("/<{(.*?)}>/e","$$1",$match_gr);
			}
		}	
		$file = preg_replace($pattern_graphch,$grp,$file);
		$href="";
		$href1="";
		if($fGraphchange==1)
		{
			$val['img'] = "graph_emp.php";
			$val['img1'] = "graph_emp1.php";
			$val['href'] = "<a href = 'performance_summary1.php'>";
		}
		else if($fGraphchange==2)
		{
			$val['img']= "graph_boss.php";
			$val['img1']= "graph_boss1.php";
		}
		else if($fGraphchange==3)
		{
			$val['img']= "graph_admin.php";
			$val['img1']= "graph_admin1.php";
			$val['href'] = "<a href='employee_list.php'>";
			$val['href1'] = "<a href='raters_list.php'>";
		}
		$file=preg_replace("/<{(.*?)}>/s","",$file);
		$file = $common->direct_replace($db_object,$file,$val);
		echo $file;
		}//end view
		
	}//end class
	$ob = new persetting;
	
	$ob->view($db_object,$common,$post_var,$error_msg,$user_id,$gbl_gr_select);
	

include("footer.php");
?>

