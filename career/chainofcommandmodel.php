<?php
include("../session.php");

class Position
{
  function position_display($common,$db_object,$user_id,$post_var,$default,$error_msg,$gbl_files)
  {
	while(list($kk,$vv) = @each($post_var))
		{
			$$kk = $vv;
			if(ereg("^eeosubcat_",$kk))
			{
				$eid=ereg_replace("eeosubcat_","",$kk);
				$eeotagsarr[] = $eid;
			}
			if(ereg("^empltype_",$kk))
			{
				$typeid = ereg_replace("empltype_","",$kk);
				$emptypesarr[] = $typeid; 
			}
		}


	$path=$common->path;
	$xFile=$path."templates/career/chainofcommandmodel.html";
	$xTemplate=$common->return_file_content($db_object,$xFile);


//THE CONDITIONS SELECTED IN THE FIRST SCREEN OF SUCCESSION & DEPLOYMENT PLAN
//ARE TO BE PASSED ON TO NEXT SCREEN ON CLICKING A LINK IN THE CHART POPULATED IN THIS PAGE
//THIS IS ACHIEVED USING A COOKIE

	$eeotagsall = @implode(",",$eeotagsarr);
	$emptypesall = @implode(",",$emptypesarr); 


	$matchperpos 	= $fMatch_per_pos;
	$lh1 		= $levelhigher1;
	$lsame 		= $levelsame;
	$ll1 		= $levellower1;
	$ll2 		= $levellower2;
	$ilot		= $interest_lot;
	$isome		= $interest_some;
	$ino		= $interest_no;
	$ilplan		= $interest_learnplan;
	$etags		= $eeotagsall;
	$etypes		= $emptypesall;	


	$components = "matchperpos:$matchperpos||lh1:$lh1||lsame:$lsame||ll1:$ll1||ll2:$ll2||ilot:$ilot||isome:$isome||ino:$ino||ilplan:$ilplan||etags:$etags||etypes:$etypes";

	setcookie("successionplans",$components,0,"/");

	include("header.php");


	preg_match("/<{position_loopstart}>(.*?)<{position_loopend}>/s",$xTemplate,$match);
	$replacecontent=$match[1];
	
	$user_table = $common->prefix_table('user_table');		
	$mysql = "select username from $user_table where user_id = '$user_id'";
	$usrname_arr = $db_object->get_a_line($mysql);

	$username = $usrname_arr['username'];
	

//FIND THE POSISION OF THE USER...
	$mysql = "select position from $user_table where user_id = '$user_id'";	
	$pos_arr = $db_object->get_a_line($mysql);
		
	$pos_of_user = $pos_arr['position'];

	//$pos_of_user
	
	$xTemp=$this->arrange($db_object,$common,$pos_of_user,$content,$replacecontent,$app,$post_var,$user_id);
 
	$xTemp=preg_replace("/<{position_loopstart}>(.*?)<{position_loopend}>/s",$xTemp,$xTemplate);
 
	$vals['username'] = $username;
	$xTemp=$common->direct_replace($db_object,$xTemp,$vals);

	echo $xTemp;
   }

function arrange($db_object,$common,$boss_no,$content,$replacecontent,$app,$post_var,$user_id)
{

	set_time_limit(0);
	$cur_userid = $user_id;

	$position_table			= $common->prefix_table("position");
	$user_table			= $common->prefix_table("user_table");
	$family_position 		= $common->prefix_table('family_position');
	$family 			= $common->prefix_table('family');
	$position_designee1 		= $common->prefix_table('position_designee1');
	$position_designee2 		= $common->prefix_table('position_designee2');
	$deployment_plan 		= $common->prefix_table('deployment_plan');
	$assign_succession_plan_sub 	= $common->prefix_table('assign_succession_plan_sub');


	$qry="select $position_table.pos_id as pos_id,if($user_table.username is null,' ',$user_table.username) as position_name,
		$user_table.user_id ,$position_table.position_name as name,
		$user_table.password as password,
	
		$position_table.level_no,$user_table.admin_id
		from $position_table left join $user_table
		on $position_table.pos_id=$user_table.position
		where $position_table.boss_no='$boss_no' ";

	$result=$db_object->get_rsltset($qry);
	
	$present_content="";

	$app.="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

	for($i=0;$i<count($result);$i++)
	{
		$content=$content."&nbsp";

	 

		$position_name = $result[$i]["position_name"]; 	//username in this position...
		$pos_name = $result[$i]["name"];

		 
		$pos_id = $result[$i]["pos_id"];
		
		 
		
		$levelno = $result[$i]["level_no"];

		$user_id = $result[$i]['user_id'];
		
		//$pos_id=$result[$i]["pos_id"];
		 

//CHECK IF THE PERSON CURRENTLY IN THIS POSITION IS A DESIGNEE FOR ANOTHER POSITON

$mysql = "select $position_designee1.designated_user as designee1,$position_designee2.designated_user as designee2
	from $position_designee1,$position_designee2,$deployment_plan
	where $position_designee1.plan_id = $position_designee2.plan_id
	and $position_designee1.plan_id = $deployment_plan.plan_id
	and ($position_designee1.designated_user = '$user_id' or $position_designee2.designated_user = '$user_id')";

$designeeuserid_arr = $db_object->get_a_line($mysql);

$designee1_userid = $designeeuserid_arr['designee1'];
$designee2_userid = $designeeuserid_arr['designee2'];


$redcolorstart = "<FONT COLOR=BLACK>";
$redcolorend = "</FONT>";

	if($designee1_userid != '')
	{
		
		$mysql = "select position_designee1.designated_user as designee1 
				from position_designee1,deployment_plan 
				where position_designee1.plan_id = deployment_plan.plan_id 
				and deployment_plan.position='$pos_id'";
		$check_designee_arr = $db_object->get_a_line($mysql);

		$check_designee = $check_designee_arr['designee1'];
		
		if($check_designee == '')
		{
		$redcolorstart = "<FONT COLOR=RED>";
		$redcolorend = "</FONT>";
		}
	}
 	elseif($designee2_userid != '')
	{
		
		$mysql = "select position_designee2.designated_user as designee2 
				from position_designee2,deployment_plan 
				where position_designee2.plan_id = deployment_plan.plan_id 
				and deployment_plan.position='$pos_id'";

		$check_designee_arr = $db_object->get_a_line($mysql);

		$check_designee = $check_designee_arr['designee2'];

		if($check_designee == '')
		{
		$redcolorstart = "<FONT COLOR=RED>";
		$redcolorend = "</FONT>";
		}
	}




//IF THE PERSON IS A DESIGNEE FOR ANOTHER POSITION THEN CHECK IF THERE ARE ANY PERSON
//DESIGNATED FOR THIS POSITION
//IF NO DESIGNEE THEN THAT POSITION IS RED...


//CHECK IF THE ADMIN OR BOSS HAS ASSIGNED TO CREATE SUCCESSION PLAN FOR THE 
//PARTICULAR POSITION REQUIRED... IF ASSIGNED, MAKE IT A LINK 
		
		$mysql = "select position from $assign_succession_plan_sub where assigned_to = '$cur_userid' and status = 'n'";

		$pos_assigned_arr = $db_object->get_single_column($mysql);
$user_table=$common->prefix_table("user_table");
if($cur_userid!="")
{
$pos_qry="select position from $user_table where user_id='$cur_userid'";
$pos_res=$db_object->get_a_line($pos_qry);
$pos=$pos_res[position];
$chain_below=$common->get_chain_below($pos,$db_object,$twodarr);
$pos_assigned_arr=@array_merge($chain_below,$pos_assigned_arr);
}
		//$position_assigned = $pos_assigned_arr['position'];

		//echo "position assigned is $position_assigned<br>";
		//echo "Cur Position is $pos_id <br><br>";


		if(@in_array($pos_id,$pos_assigned_arr) or $cur_userid==1)
		{
 
			//$linkstart = "<a href='show_models.php?posid={{pid}}' title='Employee is {{position_name}} Level is {{levelno}}'> ";
			$linkstart = "<a href='show_models.php?posid=$pos_id' title='Employee is $position_name Level is $levelno'> ";
			$linkend = "</a>";
		}
		else
		{	
			$linkstart = "";
			$linkend = "";
		}
		

		$name=$result[$i]["position_name"];
		$pos_id=$result[$i]["pos_id"];

		$repl_content = preg_replace("/<{(.*?)}>/e","$$1",$replacecontent);
		$content .=$app.$repl_content;

		$user_id = $cur_userid;

		$content = $this->arrange($db_object,$common,$pos_id,$content,$replacecontent,$app,$post_var,$user_id);

   	}

	$content = preg_replace("/<{(.*?)}>/","$$1",$content);
   	return $content;
 }


}
$obj=new Position;

//THIS FUNCTION WILL DISPLAY THE CHAIN OF COMMAND CHART	
$obj->position_display($common,$db_object,$user_id,$post_var,$default,$error_msg,$gbl_files);


include("footer.php");
?>
