<?php
include("../session.php");
include("header.php");

class Assign_appraisal1
{
	function assign_appraisal($common,$db_object,$user_id,$form_array,$error_msg)
	{
		
		while(list($kk,$vv)=@each($form_array))
		{
			$$kk=$vv;
		}
		
		$path=$common->path;
		$xFile=$path."templates/performance/assign_appraisal.html";
		$xTemplate=$common->return_file_content($db_object,$xFile);
		
		$position_table=$common->prefix_table("position");
		$user_table=$common->prefix_table("user_table");
		/*$selqry="select distinct($user_table.username),$user_table.position,$user_table.user_id from $user_table
		left join $position_table on $user_table.position=$position_table.boss_no where $user_table.admin_id='$user_id' and $position_table.boss_no is not null";
		echo $selqry;exit;
		$bossset=$db_object->get_rsltset($selqry);*/
		
		//$selres=$common->return_direct_reports($db_object,$user_id);
		
		if($user_id!=1)
		{
			$sql="select user_id from $user_table where admin_id='$user_id'";
			
			$selres=$db_object->get_single_column($sql);
		}
	
		else
		{
			$sql="select user_id from $user_table where user_id<>'$user_id'";
			
			$selres=$db_object->get_single_column($sql);
		}
		$c=0;
		
		$k=0;
		for($i=0;$i<count($selres);$i++)
		{
			$user=$selres[$i];
			
			$check=$common->is_boss($db_object,$user);
			
			if($check==1)
			{
				$qry="select username,position,user_id from $user_table where user_id='$user'";
				
				$bossset[$k]=$db_object->get_a_line($qry);
				
			
				$user=$bossset[$k][user_id];
				
				if($user==$fBoss)
				{
					$bossset[$k][selected]="Selected";
				}
				else
				{
					$bossset[$k][selected]="";
				}

				
				$k++;
			}
		}
		//print_r($bossset);		
		
		if($bossset[0]=="")
		{
			echo $error_msg['cNoBossUnderAdmin'];
			
			include_once("footer.php");
			
			exit;
		}
		
		
		$values["boss_loop"]=$bossset;
		
		if($fBoss)
		{
		
		$sel_arr["boss_loop"]=array($fBoss);
		$selqry="select position from $user_table where user_id='$fBoss'";
		$bossid=$db_object->get_a_line($selqry);
		
		$boss_id=$bossid["position"];
		}
		else
		{
			
		$sel_arr["boss_loop"]=$bossset[0]["user_id"];
		$boss_id=$bossset[0]["position"];
		}
		
		$selqry="select $user_table.user_id,$user_table.username from $user_table,$position_table where $user_table.position=$position_table.pos_id and $position_table.boss_no='$boss_id'";
		
		$user_set=$db_object->get_rsltset($selqry);
		
	
		$values["user_loop"]=$user_set;
	

$xTemplate=$common->multipleloop_replace($db_object,$xTemplate,$values,$sel_arr);

$vals=array();

$xTemplate=$common->direct_replace($db_object,$xTemplate,$vals);
echo $xTemplate;
		
	}
function save_in_thetable($common,$db_object,$form_array,$user_id,$error_msg)
	{
	
		$assign_appraisal=$common->prefix_table("assign_performance_appraisal");
		$user_table=$common->prefix_table("user_table");
		$position_table=$common->prefix_table("position");
		
		$approved_selected_objective=$common->prefix_table("approved_selected_objective");
		while(list($kk,$vv)=@each($form_array))
		{
			$$kk=$vv;
			
		}
		
		if($allboss!="yes")
		{
				
		$boss_id=$fBoss;
		for($i=0;$i<count($fUser);$i++)
		{		
			$users_id=$fUser[$i];
			
			$ch_qry="select o_id from $approved_selected_objective where user_id='$users_id'";
			
			$name=$common->name_display($db_object,$users_id);echo $name;
			$ch_res=$db_object->get_single_column($ch_qry);
			if($ch_res[0]=="")
			{
				
				
				echo $name;
				echo $error_msg['cNoObjective'];
				include_once("footer.php");
				exit;
			}
			else
			{
			$selqry="select username from $user_table where user_id='$fBoss'";
			$bossdetails=$db_object->get_a_line($selqry);
			$selqry="select dummy_id from $assign_appraisal where boss_user_id='$boss_id' and user_id='$users_id' and status<>'h'";
			
			$alreadyassid=$db_object->get_single_column($selqry);
			$selqry="select username from $user_table where user_id='$users_id'";
			$user_name=$db_object->get_a_line($selqry);
			if($alreadyassid[0])
			{
				
				echo $bossdetails["username"];
				echo $error_msg['cAppraisalAssignedAlready'];
				echo $user_name["username"];
				echo "<br>"; 
				continue;
			}
			
			if($alreadyassid[0]=="")
			{
				
			$insqry="insert into $assign_appraisal set boss_user_id='$boss_id',user_id='$users_id',date_added=now(),check_status='n'";
			$db_object->insert($insqry);
			echo $error_msg['cTheUser'];
			echo  $bossdetails["username"];
			echo $error_msg['cUserAssignedToAppraise'];
			echo $user_name["username"];
			echo "<br>";
			}
			}
		}
		
		}
		else
		{
			
		$selres=$common->return_direct_reports($db_object,$user_id);
	
		$c=0;
		
		$k=0;
		for($i=0;$i<count($selres);$i++)
		{
			$user=$selres[$i];
			
			$check=$common->is_boss($db_object,$user);
			
			if($check==1)
			{
				$qry="select username,position,user_id from $user_table where user_id='$user'";
				
				$bossset[$k]=$db_object->get_a_line($qry);
				
				$k++;
			}
		}
	
			for($i=0;$i<count($bossset);$i++)
			{
				$boss_id=$bossset[$i]["position"];
				$asboss_id=$bossset[$i]["user_id"];
				$selqry="select $user_table.user_id,$user_table.username from $user_table,$position_table where $user_table.position=$position_table.pos_id and $position_table.boss_no='$boss_id'";
				$user_set=$db_object->get_rsltset($selqry);
					for($j=0;$j<count($user_set);$j++)
					{
						$users_id=$user_set[$j]["user_id"];
						$selqry="select dummy_id from $assign_appraisal where boss_user_id='$asboss_id' and user_id='$users_id' and status<>'h'";
						
						$alreadyassid=$db_object->get_single_column($selqry);
						
						$ch_qry="select o_id from $approved_selected_objective where user_id='$users_id'";				
						//echo $ch_qry;
						
						$res=$db_object->get_single_column($ch_qry);
						if($res[0]=="")
						{
						
							echo $user_set[$j]["username"];
							echo $error_msg['cNoObjective'];
							
														
						}
						else
						{
						if($alreadyassid[0])
						{

							echo $bossset[$i]["username"];
							echo $error_msg['cUserAssignedToAppraise'];
							echo $user_set[$j]["username"];
							echo "<br>";
							
						}
						if($alreadyassid[0]=="")
						{
						$insqry="insert into $assign_appraisal set boss_user_id='$asboss_id',user_id='$users_id',date_added=now()";
						$db_object->insert($insqry);
							echo $bossset[$i]["username"];
							echo " has been  assigned to appraise the user ";
							echo $user_set[$j]["username"];
							echo "<br>";
						}
					}
					}
			}
		
		}
		
		echo $error_msg["cThank"];
		
	}
}
$assobj=new Assign_appraisal1;



if($fSubmit)
{
$assobj->save_in_thetable($common,$db_object,$post_var,$user_id,$error_msg);
}
else
{
$assobj->assign_appraisal($common,$db_object,$user_id,$post_var,$error_msg);
}

include("footer.php");
?>
