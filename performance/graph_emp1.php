<?
include("../session.php");

	$approved_selected_objective = $common->prefix_table("approved_selected_objective");
	$approved_feedback = $common->prefix_table("approved_feedback");
	$approveduser_objective = $common->prefix_table("approveduser_objective");
	$config_table = $common->prefix_table("config");
	$rating_table = $common->prefix_table("rating");
//Get all the Objectives of the user
	$selobj = "select sl_id,o_id,objective_$default as objective,priority,
		committed_no,percent from $approved_selected_objective 
		where user_id='$user_id' and status='A' order by sl_id";
	$selres = $db_object->get_rsltset($selobj);

//from Config
	$boss=0;
	$conqry = "select person_affected from $config_table";
	$conres = $db_object->get_a_line($conqry);
	$noofperson = $conres['person_affected'];
	$boss = 1;	

//Total rater is 4 (without self),noofperson(the raters we have selected) + boss (boss's rating)
	$totalperson = $noofperson + $boss;	
//from rating
		$ratqry = "select rval from $rating_table where rval='$gbl_met_value'";
		$ratres = $db_object->get_a_line($ratqry);
		$r_val = $ratres['rval'];

//met expectation value;
		$metexpectation = $r_val * $totalperson;
		
	for($i=0;$i<count($selres);$i++)
	{

		$actual="";
		$expected="";
		$count = $count + 1;

		$objective = $selres[$i]['objective'];			
		$o_id = $selres[$i]['o_id'];
		$priority = $selres[$i]['priority'];
		$sl_id = $selres[$i]['sl_id'];
		$checkcumulative = $selres[$i]['percent'];


	//get all  metrics for the given o_id
		$oqry = "select met_id from $approveduser_objective where o_id='$o_id' and 
			user_id='$user_id'";
		$ores = $db_object->get_a_line($oqry);
		$met_id = $ores['met_id'];
		$mqry = "select o_id from $approveduser_objective where met_id='$met_id' and 
			user_id='$user_id'";
		$mres = $db_object->get_single_column($mqry);
		$aver  = count($mres);
		$oid = implode("','",$mres);				
		
	//get the raters rated value
		$Ratervalue = "select sum(r_id) from $approved_feedback where o_id in ('$oid')
				 and user_id='$user_id' and status<>'1' and status<>'2'";
		$Resvalue = $db_object->get_single_column($Ratervalue);
		$actual = $Resvalue[0];
		$actual = @($actual/$aver);
		$actual = @sprintf("%01.2f",$actual);
			
	//calculation for met expectation value
		$expected = @($actual/$metexpectation);
		$expected = $expected * 100;
	//-------			
	$expected = @sprintf("%01.2f",$expected);
	$final_val += $expected;

	}
		
	$final_val = @$final_val/count($selres);
//$array=array("50","100","50","34"); 
$remain = 100 - $final_val;
$array=array("$final_val",$remain); 
$vals= $image->return_Array($array);
 
	$heads = array(
	array($error_msg['cAsemp'], 2, "c"),
	);   
	$image->init(150,150, $vals);//CREATES AN IMAGE
	$image->draw_heading($heads);//FOR HEADING
	//$image->set_legend_percent();//TO SHOW THE PERCENTAGE IN THE RIGHT HAND SIDE
	$image->set_legend_value();//TO SHOW THE REAL VALUES IN THE RHS
	$filename = $graphtest;
	$image->display($filename);
?>
