<?
include("../session.php");

	$user_table = $common->prefix_table("user_table");
	$approved_selected_objective = $common->prefix_table("approved_selected_objective");
	$approveduser_objective = $common->prefix_table("approveduser_objective");
	$approved_feedback = $common->prefix_table("approved_feedback");
	$verified_user = $common->prefix_table("verified_user");
	$position_table  = $common->prefix_table("position");

//get the users id who are all under his admin chain
	if($user_id!=1)
	{
		$selqry="select user_id from $user_table where admin_id='$user_id' order by user_id";
	}
	else
	{
		$selqry="select $user_table.user_id from $user_table,$position_table where 
		$user_table.position=$position_table.pos_id and ($user_table.position<>NULL or 
		$user_table.position<>0) and $user_table.user_id!=1   order by $user_table.user_id";
	}
	$userres = $db_object->get_single_column($selqry);
	$noofusers = count($userres);
	$split = @implode("','",$userres);


//get total no of self ratings
	$select= "select distinct(user_id) from $approved_feedback where user_id in ('$split') 
			and raters_id in ('$split') and vaccept='A'";
	$result = $db_object->get_rsltset($select);
	$noofratings = count($result);

//calculate percentage
	$final_val = @($noofratings / $noofusers) * 100;
	//$array=array("50","100","50","34"); 
	$remain = 100 - $final_val;
	$array=array("$final_val",$remain); 
	$vals= $image->return_Array($array);
 
	$heads = array(
	array($error_msg['cAsadmin'], 2, "c"),
	);   
	$image->init(150,150, $vals);//CREATES AN IMAGE
	$image->draw_heading($heads);//FOR HEADING
	//$image->set_legend_percent();//TO SHOW THE PERCENTAGE IN THE RIGHT HAND SIDE
	$image->set_legend_value();//TO SHOW THE REAL VALUES IN THE RHS
	$filename = $graphtest;
	$image->display($filename);
?>
