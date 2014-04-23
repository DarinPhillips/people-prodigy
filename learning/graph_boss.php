<?
	include("../session.php");
	$feedback_table	=$common->prefix_table("learning_feedback_results");
	$skill_table = $common->prefix_table("skills");
	$plan_table = $common->prefix_table("approved_devbuilder");
	$user_table = $common->prefix_table("user_table");		

	$posqry  = "select position from $user_table where user_id='$user_id'";
	$posres = $db_object->get_a_line($posqry);
	$position = $posres['position'];

	$chain_below = $common->get_chain_below($position,$db_object,$sam);

	$get_user = $common->get_user_id($db_object,$chain_below);
		
	$users = array();
	
	for($i=0;$i<count($get_user);$i++)
	{
		$users[] = $get_user[$i]['user_id'];
	}

	$urs = @implode("','",$users);
	

	
	$mysql="select skill_name,$feedback_table.skill_id,rated_id,rater_id,skill_description from $feedback_table,
					$plan_table,$skill_table where $feedback_table.rated_id=$plan_table.user_id 
					and $feedback_table.skill_id=$plan_table.skill_id 
					and $feedback_table.skill_id=$skill_table.skill_id and $feedback_table.status='1'
					and rated_id in('$urs') and $plan_table.pstatus='a' group by $feedback_table.skill_id,rated_id order by rated_id";
		$dFeed= $db_object->get_rsltset($mysql);

		for($i=0;$i<count($dFeed);$i++)
		{
		
			$skill_name	=$dFeed[$i]["skill_name"];
			$skills		=$dFeed[$i]["skill_id"];
			$skill_description	=$dFeed[$i]["skill_description"];
			$rat_id	= $dFeed[$i]['rated_id'];

			$activityrepl = "";
		
			$mysql 	= "select count(*) as improvedno from $feedback_table where rated_id='$rat_id'
						 and rater_id <> rated_id and skill_id='$skills' and value='2'";

			$dImprove = $db_object->get_a_line($mysql);
			$improved_no = $dImprove['improvedno'];
			
			$mysql 	= "select count(*) as total from $feedback_table where rated_id='$rat_id'
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
	$final_val = @($final_val / count($get_user));

	//$array=array("50","100","50","34"); 
	$remain = 100  - $final_val;
	if($remain < 0)
	{
		$remain = 0;
	}
	
	$array=array("$final_val",$remain); 
	$vals= $image->return_Array($array);
 
	$heads = array(
	array($error_msg['cAsboss'], 2, "c"),
	);   
	$image->init(150,150, $vals);//CREATES AN IMAGE
	$image->draw_heading($heads);//FOR HEADING
	$image->set_legend_percent();//TO SHOW THE PERCENTAGE IN THE RIGHT HAND SIDE
	$image->set_legend_value();//TO SHOW THE REAL VALUES IN THE RHS
	$filename = $graphtest;
	$image->display($filename);
?>
