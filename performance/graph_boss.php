<?
include("../session.php");

	$user_table = $common->prefix_table("user_table");
	$approved_selected_objective = $common->prefix_table("approved_selected_objective");
	$approveduser_objective = $common->prefix_table("approveduser_objective");
	$approved_feedback = $common->prefix_table("approved_feedback");
	$verified_user = $common->prefix_table("verified_user");


	$posqry = "select position from $user_table where user_id='$user_id'";
	$posres = $db_object->get_a_line($posqry);
	$position = $posres['position'];

//Get all the Users under the users chain of command

	$down = $common->get_chain_below($position,$db_object,$arr);
	$allusers = $common->get_user_id($db_object,$down);
	$alluserid = array();
	for($i=0;$i<count($allusers);$i++)
	{
		$alluserid[] = $allusers[$i][user_id];			
	}
	$alluserid[] = $user_id;
	$sl_userids = implode("','",$alluserid);					

//Get all the Objectives of the user
	$selobj = "select sl_id,o_id,objective_$default as objective,priority,
			committed_no,percent from $approved_selected_objective 
			where user_id='$user_id' and status='A' order by sl_id";
	$selres = $db_object->get_rsltset($selobj);
//get raters
	$vqry = "select sl_id,for_user_id from $verified_user where verified_user_id in('$sl_userids') group by verified_user_id ";
	$vres = $db_object->get_rsltset($vqry);

//mainloop starts here					

	for($i=0;$i<count($selres);$i++)
	{
		$sno = $i+1;
		$ful_val="";

		$fulfilled1="";
		$accomplish1="";
		$committed1="";

		$sl_id = $selres[$i]['sl_id'];
		$o_id = $selres[$i]['o_id'];
		$objective = $selres[$i]['objective'];
	
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

	//Get all verified users				
		for($j=0;$j<count($vres);$j++)
		{

			$username = "";
			$uid="";
			$rfulfilled="";			
			$sl = $vres[$j]['sl_id'];
			$usid = $vres[$j]['for_user_id'];
			
			$slqry = "select o_id from $approved_selected_objective where sl_id='$sl'";
			$slres = $db_object->get_a_line($slqry);
			
			$s_oid = $slres['o_id'];				
			$metqry = "select met_id from $approveduser_objective where o_id='$s_oid' and 
			user_id='$usid'";
			$metres = $db_object->get_a_line($metqry);
			$metric_id = $metres['met_id'];
			
			$mqry = "select o_id from $approveduser_objective where met_id='$met_id' and 
			user_id='$usid'";								
			$mres = $db_object->get_single_column($mqry);
			$aver  = count($mres);
			$oid1 = @implode("','",$mres);		
			
			if($metric_id==$met_id)
			{
			$username = $common->name_display($db_object,$usid);				
			$uid = $usid;		
		//oid - diff o_id with same metrics
				$rget = $common->get_fullfilled($db_object,$s_oid,$uid,$dates);

				$rfulfilled = $rget['Cfulfill'];
		//All form display value
				$rfulfilled = @sprintf("%01.2f",$rfulfilled);
				$ful_val += $rfulfilled;			
			}
		}//j loop

		$ful_val = @($ful_val/count($vres));
		$final_val += $ful_val;
	}//i loop
	
//$array=array("50","100","50","34"); 
$remain = 100 - $final_val;
$array=array("$final_val",$remain); 
$vals= $image->return_Array($array);
 
	$heads = array(
	array($error_msg['cAsboss'], 2, "c"),
	);   
	$image->init(150,150, $vals);//CREATES AN IMAGE
	$image->draw_heading($heads);//FOR HEADING
	//$image->set_legend_percent();//TO SHOW THE PERCENTAGE IN THE RIGHT HAND SIDE
	$image->set_legend_value();//TO SHOW THE REAL VALUES IN THE RHS
	$filename = $graphtest;
	$image->display($filename);
?>
