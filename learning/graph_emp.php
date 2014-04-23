<?
	include("../session.php");
	$feedback_table	=$common->prefix_table("learning_feedback_results");
	$skill_table 		=$common->prefix_table("skills");
	$plan_table		=$common->prefix_table("approved_devbuilder");
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
		
			$mysql 	= "select count(*) as improvedno from $feedback_table where rated_id='$user_id'
						 and rater_id <> rated_id and skill_id='$skills' and value='2'";

			$dImprove = $db_object->get_a_line($mysql);
			$improved_no = $dImprove['improvedno'];
			
			$mysql 	= "select count(*) as total from $feedback_table where rated_id='$user_id'
						 and rater_id <> rated_id and skill_id='$skills'";
			$dTotal 	= $db_object->get_a_line($mysql);
			$total 	=$dTotal['total'];
			
			if( ( $total != 0 ))
			{
				$improved_percentage = $improved_no / $total ;
			}
			else
			{
				$improved_percentage = 0;
			}
			$final_val +=$improved_percentage;
		}
		$final_val = @($final_val / count($dFeed));
	$final_val *= 100;
	//$array=array("50","100","50","34"); 
	$remain = 100  - $final_val;
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
