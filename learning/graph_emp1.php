<?
	include("../session.php");
	$feedback_table	=$common->prefix_table("learning_feedback_results");
	$skill_table 		=$common->prefix_table("skills");
	$plan_table		=$common->prefix_table("approved_devbuilder");
	$solution_table	=$common->prefix_table("assign_solution_builder");
	$mysql="select skill_name,$feedback_table.skill_id,rated_id,rater_id,skill_description from $feedback_table,
					$plan_table,$skill_table where $feedback_table.rated_id=$plan_table.user_id 
					and $feedback_table.skill_id=$plan_table.skill_id 
					and $feedback_table.skill_id=$skill_table.skill_id and $feedback_table.status='1'
					and rated_id='$user_id' and $plan_table.pstatus='a' group by $feedback_table.skill_id";
		$dFeed= $db_object->get_rsltset($mysql);

		for($i=0;$i<count($dFeed);$i++)
		{
		
			$skill_name	=$dFeed[$i]["skill_name"];
			$skills		=$dFeed[$i]["skill_id"];
			$skill_description	=$dFeed[$i]["skill_description"];
			$activityrepl = "";
		
				$mysql 	= "select cdate,completed_date,plan_approved_date,
(((to_days(cdate) - to_days(plan_approved_date))/(to_days(completed_date) - to_days(plan_approved_date)))*100) as per
				from $plan_table,$solution_table,$feedback_table where 
				$plan_table.user_id=$solution_table.user_id and $plan_table.skill_id=$solution_table.skill_id
				and $plan_table.user_id=$feedback_table.rated_id and $plan_table.skill_id=$feedback_table.skill_id
				and $plan_table.pstatus='a' and $plan_table.user_id='$user_id' and $plan_table.skill_id='$skills'
				and $feedback_table.status='1' $rsltqry ";	

			$dResult  =$db_object->get_rsltset($mysql);
			$count=0;
			$totalper=0;
			for($k=0;$k<count($dResult);$k++)
			{
				$per	= $dResult[$k]['per'];	
				if( $per != "" )
				{
					$count=$count+1;	
					$totalper=$totalper+$per;
				}

			}
			if($count != '0')
			{
				$avg=$totalper/$count;
			}
			else
			{
				$avg = '0';	
			}
			$avg_round=round($avg,2);
			$final_val += $avg_round;
				
		}
		$final_val = @($final_val/count($dFeed));

	//$array=array("50","100","50","34"); 
	$remain = 100  - $final_val;
	if($remain < 0)
	{
		$remain = 0;
	}
	$array=array("$final_val",$remain); 
	$vals= $image->return_Array($array);
 
	$heads = array(
	array($error_msg['cAsemp'], 2, "c"),
	);   
	$image->init(150,150, $vals);//CREATES AN IMAGE
	$image->draw_heading($heads);//FOR HEADING
	$image->set_legend_percent();//TO SHOW THE PERCENTAGE IN THE RIGHT HAND SIDE
	$image->set_legend_value();//TO SHOW THE REAL VALUES IN THE RHS
	$filename = $graphtest;
	$image->display($filename);
?>
