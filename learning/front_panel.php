<?php
include("../session.php");

include("header.php");
class front
{

	function front_display($common,$db_object,$post_var,$user_id,$learning,$gbl_gr_select)
	{
		while(list($key,$value)=@each($post_var))
		{
			$$key=$value;
		}
		$xuser=$user_id;		
		$devbuilder		=$common->prefix_table("unapproved_devbuilder");
		$approved_devbuilder=$common->prefix_table("approved_devbuilder");
		$user			=$common->prefix_table("user_table");
		$skills_table		=$common->prefix_table("skills");
		$assign_solution_builder=$common->prefix_table("assign_solution_builder");
		$feedback_table 	=$common->prefix_table("learning_feedback_results");
		$temp_devbuilder=$common->prefix_table("temp_devbuilder");
	
	$sol_qry2="select skill_id from $temp_devbuilder where user_id='$user_id' group by skill_id";

	$sol_result2=$db_object->get_single_column($sol_qry2);
	

	if(count($sol_result2)>0)
		{
	$un_arr=@implode(",",$sol_result2);

	//$sol_qry1="select skill_id from $assign_solution_builder where user_id='$user_id' and skill_id not in ($un_arr)";
	$sol_qry1="select skill_id from $assign_solution_builder where user_id='$user_id' and status='i'";
	

	$sol_result=$db_object->get_rsltset($sol_qry1);
		}
	else
	{
		$sol_qry="select skill_id from $assign_solution_builder where user_id='$user_id'";
		
		$sol_result=$db_object->get_rsltset($sol_qry);
	}

			

		
		$plan_qry="select skill_id from $assign_solution_builder where user_id='$user_id' and pstatus='i'";
			
		
		$plan_result=$db_object->get_rsltset($plan_qry);		

		$user_table=$common->prefix_table("user_table");
		
		//print_r($sol_result);

		$path=$common->path;
		
		$filename=$path."/templates/learning/front_panel.html";
		
		$filecontent=$common->return_file_content($db_object,$filename,$user_id);


		$all_ids=$learning->persons_to_be_rated($db_object,$common,$user_id);
		

		if(count($all_ids) > 0)
		{


		$filecontent=preg_replace("/<{requests_(.*?)}>/is","",$filecontent);
	

		}

		else
		{
			$filecontent=preg_replace("/<{requests_start}>(.*?)<{requests_end}>/is","",$filecontent);			
		}
		


		if($sol_result[0]=="")
		{
			$filecontent=preg_replace("/<{if_loopstart}>(.*?)<{if_loopend}>/s","",$filecontent);
		}
		else
		{
			$filecontent=preg_replace("/<{if_(.*?)}>/s","",$filecontent);
		}
		if($plan_result[0]=="")
		{
			$filecontent=preg_replace("/<{ifplan_loopstart}>(.*?)<{ifplan_loopend}>/s","",$filecontent);
		}
		else
		{
			$filecontent=preg_replace("/<{ifplan_(.*?)}>/s","",$filecontent);
		}


	
/* FOR ALERT FOR NEW APPROVAL  */

		$mysql="select distinct($devbuilder.user_id),skill_id,$user.username from $devbuilder,$user where $user.user_id=$devbuilder.user_id and $devbuilder.status='u'";
		$newsolution=$db_object->get_rsltset($mysql);
	
		$mysql_plan="select distinct($approved_devbuilder.user_id),skill_id,$user.username from $approved_devbuilder,$user where $user.user_id=$approved_devbuilder.user_id and $approved_devbuilder.pstatus='t'";
		$solution_plan=$db_object->get_rsltset($mysql_plan);

		$qry = "select count(build_id)  from $approved_devbuilder where update_status='u'";
		$res = $db_object->get_single_column($qry);
		$updated_plan_progress = $res[0];
		
		
		if( ( $newsolution[0][0] == "" && $solution_plan[0][0] == "" && $updated_plan_progress==0 && $user_id==1)  || $user_id != '1')
		{
			$filecontent = preg_replace("/<{newalert_start}>(.*?)<{newalert_end}>/s","",$filecontent);	
		}
		else
		{
			$filecontent=preg_replace("/<{newalert_(.*?)}>/s","",$filecontent);
		}
		
/*  FOR  LEARNING PLAN  BUILDER   */

		$mysql="select skill_name,$approved_devbuilder.skill_id from $skills_table,$approved_devbuilder,$assign_solution_builder  
					where $approved_devbuilder.skill_id=$skills_table.skill_id and  $approved_devbuilder.user_id=$user_id 
					and $assign_solution_builder.skill_id=$skills_table.skill_id and  $assign_solution_builder.user_id=$user_id 
					and $assign_solution_builder.pstatus='i' and ($approved_devbuilder.pstatus='u' or $approved_devbuilder.pstatus='r') 
					group by $approved_devbuilder.skill_id "; 

					
		$arr=$db_object->get_rsltset($mysql);

		if($arr[0][0] == "")
		{
			$filecontent = preg_replace("/<{plan_buider_start}>(.*?)<{plan_buider_end}>/s","",$filecontent);	
		}
		else
		{
			$filecontent=preg_replace("/<{plan_buider_(.*?)}>/s","",$filecontent);
		}

		
/* FOR LEARNING PLAN SUBMITTED FOR APPROVAL  */

		$mysql="select skill_name,$approved_devbuilder.skill_id from $skills_table join $approved_devbuilder  
					where $approved_devbuilder.skill_id=$skills_table.skill_id and  
					$approved_devbuilder.user_id=$user_id and $approved_devbuilder.pstatus='t' group by 
					$approved_devbuilder.skill_id "; 
		$wait_arr=$db_object->get_rsltset($mysql);
		if($wait_arr[0][0] == "")
		{
			$filecontent = preg_replace("/<{waitingplan_start}>(.*?)<{waitingplan_end}>/s","",$filecontent);	
		}
		else
		{
			$filecontent=preg_replace("/<{waitingplan_(.*?)}>/s","",$filecontent);
		}
		
/* FOR UPDATE LEARNING PLAN  */

		$mysql="select skill_name,$approved_devbuilder.skill_id from $skills_table join $approved_devbuilder  
					where $approved_devbuilder.skill_id=$skills_table.skill_id and  
					$approved_devbuilder.user_id=$user_id and $approved_devbuilder.pstatus='a' group by 
					$approved_devbuilder.skill_id ";
		$update_arr=$db_object->get_rsltset($mysql);	

		if($update_arr[0][0] == "")
		{
			$filecontent = preg_replace("/<{update_start}>(.*?)<{update_end}>/s","",$filecontent);	
		}
		else
		{
			$filecontent=preg_replace("/<{update_(.*?)}>/s","",$filecontent);
		}
		
/* FOR  LEARNING PROGRESS SUMMARY  */

		$mysql="select skill_name,$feedback_table.skill_id,rated_id,rater_id,skill_description from $feedback_table,
					$approved_devbuilder,$skills_table where $feedback_table.rated_id=$approved_devbuilder.user_id 
					and $feedback_table.skill_id=$skills_table.skill_id and $feedback_table.status='1'
					and rated_id='$user_id' and $approved_devbuilder.pstatus='a' group by $feedback_table.skill_id";
		$summaryarr=$db_object->get_rsltset($mysql);	
		$link_fl=0;
		if($summaryarr[0][0] == "")
		{
			$filecontent = preg_replace("/<{summary_start}>(.*?)<{summary_end}>/s","",$filecontent);
		//this $link_fl is used for displaying the href link
			$link_fl=1	;
		}
		else
		{
			$filecontent=preg_replace("/<{summary_(.*?)}>/s","",$filecontent);
		}
		

/* FOR PREVIEW OF APPROVED PLANS */

		$mysql="select skill_name,$approved_devbuilder.skill_id from $skills_table join $approved_devbuilder  
					where $approved_devbuilder.skill_id=$skills_table.skill_id and  
					$approved_devbuilder.user_id=$user_id and $approved_devbuilder.pstatus='a' group by 
					$approved_devbuilder.skill_id ";
		$previewarr=$db_object->get_rsltset($mysql);	

		if($previewarr[0][0] == "")
		{
			$filecontent = preg_replace("/<{preview_start}>(.*?)<{preview_end}>/s","",$filecontent);	
		}
		else
		{
			$filecontent=preg_replace("/<{preview_(.*?)}>/s","",$filecontent);
		}
$boss=$common->is_boss($db_object,$user_id);
if($boss==1)
{
	$filecontent=preg_replace("/<{ifboss_(.*?)}>/s","",$filecontent);
	
	$sql="select position from $user_table where user_id='$user_id'";
	
	$pos_result=$db_object->get_a_line($sql);
	
	$pos=$pos_result[position];
	
	$users_below=$common->get_chain_below($pos,$db_object,$twodarr);
	
	$pattern="/<{user1_loopstart}>(.*?)<{user1_loopend}>/s";
	
	preg_match($pattern,$filecontent,$match);
	
	$match=$match[0];
	
	$users=$common->get_user_id($db_object,$users_below);
	
	for($i=0;$i<count($users);$i++)
	{
		$user=$users[$i][user_id];
		
		$user_name=$common->name_display($db_object,$user);
		
		$str1.=preg_replace("/<{(.*?)}>/e","$$1",$match);
	}
	
	$filecontent=preg_replace($pattern,$str1,$filecontent);
	
}
else
{
	$filecontent=preg_replace("/<{ifboss_loopstart}>(.*?)<{ifboss_loopend}>/s","",$filecontent);
}

$yes=$common->is_admin($db_object,$user_id);


if($yes)
{
	
	$pattern="/<{view_dashboard_start}>(.*?)<{view_dashboard_end}>/s";
	//$pattern="/<{user_loopstart}>(.*?)<{user_loopend}>/s";
	preg_match($pattern,$filecontent,$match1);

	$replace=$match1[0];
	
	$filecontent = $common->view_dashboard($db_object,$user_id,$pattern,$replace,$filecontent);		
	
	
	//VIEW OUTSTANDING ASSIGNMENTS
	
	$qry="select user_id from $user_table where admin_id='$user_id'";
	
	$users=$db_object->get_single_column($qry);
	
	if(count($users)>0)
	{
		$users=@implode(",",$users);
		
		$users="(".$users.")";
	
	$one_week_back=time()-(7*24*60*60);
	
	$date1=date("Y-m-d H:i:s",$one_week_back);
	
	if($user_id!=1)
	{
	
	$qry="select distinct($approved_devbuilder.user_id),$approved_devbuilder.status,
	
	$assign_solution_builder.skill_id,date from $assign_solution_builder,
	
	$approved_devbuilder where $assign_solution_builder.user_id=$approved_devbuilder.user_id
	
	 and date<='$date1' and $approved_devbuilder.status<>'a' and $assign_solution_builder.user_id in $users";
	 
	 $qry_res=$db_object->get_rsltset($qry);
	}
	else
	{
		
	$qry="select distinct($approved_devbuilder.user_id),$approved_devbuilder.status,
	
	$assign_solution_builder.skill_id,date from $assign_solution_builder,
	
	$approved_devbuilder where $assign_solution_builder.user_id=$approved_devbuilder.user_id
	
	 and date<='$date1' and $approved_devbuilder.status<>'a'";
	 
	 $qry_res=$db_object->get_rsltset($qry);	
	}
	 
	 if($qry_res[0]!="")
	 {
	 	$xArray["highlight"]="class=highlight";
	 }

	}
	
	//$qry="select position from $user_table where admin_id='$user_id'";
	if($user_id!=1)
	{
	$qry="select username,user_id from $user_table where admin_id='$user_id'";
	
	$result=$db_object->get_rsltset($qry);
	
	}
	else
	{
		$qry="select username,user_id from $user_table where user_id<>'1'";
	
		$result=$db_object->get_rsltset($qry);
	}

	//$position=$result[position];
	
	//$pos=$common->get_chain_below($position,$db_object,$twodarr);
	
	//$user=$common->get_user_id($db_object,$pos);
	
	
	preg_match("/<{employee_loopstart}>(.*?)<{employee_loopend}>/s",$filecontent,$match);
	
	$match=$match[0];
	
	for($i=0;$i<count($result);$i++)
	{
		$u_id=$result[$i][user_id];
			
		$user_name=$common->name_display($db_object,$u_id);
				
		$str3.=preg_replace("/<{(.*?)}>/e","$$1",$match);
	
		//$filecontent=$common->simpleloopprocess($db_object,$filecontent ,$values);
	
	}
	
	$filecontent=preg_replace("/<{employee_loopstart}>(.*?)<{employee_loopend}>/s",$str3,$filecontent);
		
}
else
{
	$filecontent=preg_replace("/<{adminarea_loopstart}>(.*?)<{adminarea_loopend}>/s","",$filecontent);
//	$filecontent=preg_replace("/<{(.*?)}>/s","",$filecontent);
}
		
		$xArray["user"]="$xuser";
		$xArray["action"]="show";
		$xArray["view"]="view";
	//graph change
		$pattern_graphch = "/<{graphchange_loopstart}>(.*?)<{graphchange_loopend}>/s";		
		preg_match($pattern_graphch,$filecontent,$gr);
		$match_gr = $gr[1];

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

			
		$filecontent = preg_replace($pattern_graphch,$grp,$filecontent);

		
		$href="";
		$href1="";
		if($fGraphchange==1)
		{
			$xArray['img'] = "graph_emp.php";
			$xArray['img1'] = "graph_emp1.php";
			if($link_fl==0)
			{
				$xArray['href'] = "<a href='learning_progress_summary.php'>";
			}
			if($link_fl==0)
			{
				$xArray['href1'] = "<a href='learning_progress_summary.php'>";
			}

		}
		else if($fGraphchange==2)
		{
			$xArray['img']= "graph_boss.php";
			$xArray['img1']= "graph_boss1.php";
		}
		else if($fGraphchange==3)
		{
			$xArray['img']= "graph_admin.php";
			$xArray['img1']= "graph_admin1.php";
			$xArray['href'] = "<a href ='raters_list.php'>";
			$xArray['href1'] = "<a href = 'users_list.php'>";
		}
	


		$filecontent=preg_replace("/<{(.*?)}>/s","",$filecontent);
		$filecontent=$common->direct_replace($db_object,$filecontent,$xArray);
		echo $filecontent;
	}
}
$frobj=new front;
$frobj->front_display($common,$db_object,$post_var,$user_id,$learning,$gbl_gr_select);
include("footer.php");
?>
