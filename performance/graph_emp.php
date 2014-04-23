<?
include("../session.php");

$approved_selected_objective = $common->prefix_table("approved_selected_objective");
//Get all the Objectives of the user
	$selobj = "select sl_id,o_id,objective_$default as objective,priority,
		committed_no,percent from $approved_selected_objective 
		where user_id='$user_id' and status='A' order by sl_id";
	$selres = $db_object->get_rsltset($selobj);
	for($i=0;$i<count($selres);$i++)
	{
			
		$fulfilled ="";
		$sl_id = $selres[$i]['sl_id'];
		$o_id = $selres[$i]['o_id'];
		$objective = $selres[$i]['objective'];

		$get = $common->get_fullfilled($db_object,$o_id,$user_id,$dates);
		$fulfilled = $get['Cfulfill'];
		$final_val +=  $fulfilled;
	}
	$final_val = @$final_val/count($selres);
		

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
