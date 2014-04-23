<?php
include_once("../session.php");

include_once("header.php");

class alert_appraisal
{
	function alert_approved_appraisal($db_object,$common,$user_id)
	{
		$approved_performance_appraisal=$common->prefix_table("approved_performance_appraisal");
		
		$approved_appraisal_results=$common->prefix_table("approved_appraisal_results");
		
		$path=$common->path;
		
		$xtemplate=$path."templates/performance/approved_appraisal.html";
		
		$file=$common->return_file_content($db_object,$xtemplate);
		
		$qry="select approved_on,appraisal_id from $approved_appraisal_results where user_id='$user_id'
		
		and status='h' group by approved_on ";
		
		$result=$db_object->get_rsltset($qry);
		
		preg_match("/<{alert_loopstart}>(.*?)<{alert_loopend}>/s",$file,$match);
		
		$match=$match[0];
		
		for($i=0;$i<count($result);$i++)
		{
			$approved_on=$result[$i][approved_on];	
			
			$date=@explode(" ",$approved_on);
			
			$date1=@explode("-",$date[0]);
				
			$approved_date1[0]=@implode(".",$date1);
			
			$date2=@explode(":",$date[1]);
			
			$approved_date1[1]=$date2[0].":".$date2[1];
			
			$approved_date=@implode(".",$approved_date1);
					
			$appraisal_id=$result[$i][appraisal_id];
	
			$str.=preg_replace("/<{(.*?)}>/e","$$1",$match);
		}
		$file=preg_replace("/<{alert_loopstart}>(.*?)<{alert_loopend}>/s",$str,$file);
		
		$file=$common->direct_replace($db_object,$file,$xArray);
		
		echo $file;
	}
	
	
	
	
	function show_approved_appraisal($db_object,$common,$user_id,$approved_on,$default)
	{
		$fUser=$user_id;
		$approved_performance_appraisal=$common->prefix_table("approved_performance_appraisal");
		
		$approved_appraisal_results=$common->prefix_table("approved_appraisal_results");
		
		$sql="select * from $approved_performance_appraisal where user_id='$user_id' and approved_on='$approved_on'";
		
		$result=$db_object->get_rsltset($sql);
		
		$boss_id=$result[0][boss_id];
		
		$path=$common->path;
		
		$xtemplate=$path."templates/performance/view_approved_appraisal.html";
		
		$file=$common->return_file_content($db_object,$xtemplate);
		
		$file1=$file;
		
		$ret_array=$this->calculation($db_object,$common,$default,$user_id,$boss_id,$gbl_met_value,$err);
		
		$metexpectation=$ret_array[0];
		
		$object=$ret_array[1];
		
		$obj_id=$ret_array[2];
			
		$prior=$ret_array[3];
		
		$act=$ret_array[4];
		
		$expect=$ret_array[5];
				
		$fulfill=$ret_array[6];
		
		$committ=$ret_array[7];
		
		$accomplished=$ret_array[8];
			
		preg_match("/<{objective_loopstart}>(.*?)<{objective_loopend}>/s",$file1,$match1);
		
		preg_match("/<{rater_loopstart}>(.*?)<{rater_loopend}>/s",$file1,$match);
		
		$match=$match[0];
		
		
		$match1=$match1[0];
			
		$cnt=$ret_array[9];
		
			
			for($j=0;$j<$cnt;$j++)
			{
				
				
				$perres1[$j]=$ret_array[10][$j];
				
				$count=$j+1;
				
				$objective=$object[$j];
				
				$o_id=$obj_id[$j];
				
				$metexpectation=$metexpectation;
				
				$actual=$act[$j];
				
				$expected=$expect[$j];
							
				$committed=$committ[$j];
				
				$accomplish=$accomplished[$j];
				
				$fulfilled=$fulfill[$j];
				
				$priority=$prior[$j];
				
				$str="";
				
				$boss_qry="select rater_id,rater_comment from $approved_performance_appraisal where user_id='$user_id'
				
				and o_id='$o_id' and who='b' and approved_on='$approved_on' and status='h'";

				$boss_res=$db_object->get_rsltset($boss_qry);

				$boss_comment="";
				
				for($a=0;$a<count($boss_res);$a++)
				{
					$b=$a+1;
					
					$ct=$j+1;
					
					$id=$boss_res[$a][rater_id];
					
					$name=$common->name_display($db_object,$id);
					
					$comment=$boss_res[$a][rater_comment];
									
					$fRater_name="fRatername_".$b."_".$ct;
					
					$fRater_comment="fRatercomment_".$b."_".$ct;
					
					$boss_comment.="<tr><td class=code>{{cBye}}<input type=text name=$fRater_name value=$name readonly></td></tr>
					<tr><td class=code><TEXTAREA WRAP=PHYSICAL ROWS=3 COLS=50 name=$fRater_comment READONLY>$comment</TEXTAREA></td></tr>";
					
				}
				
				for($k=0;$k<count($perres1[$j]);$k++)
				{
					
					
					$Rcount=$k+1;
					
					$ct=$j+1;
													
					$rater_id=$perres1[$j][$k];
					
					$rater_qry="select rater_comment from $approved_performance_appraisal where
					
					 rater_id='$rater_id' and user_id='$user_id' and o_id='$o_id' and who='r'
					 
					 and approved_on='$approved_on' and status='h'";


				$rater_res=$db_object->get_single_column($rater_qry);
				
				if(count($rater_res)>1)
				{
					$comment_rater=@implode(",",$rater_res);
				}
				else if(count(rater_res)==1)
				{
					$comment_rater=$rater_res[0];
				}
					//echo $comment_rater;exit;											
					$ratername=$common->name_display($db_object,$rater_id);
					
					$qry="select rater_comment from $approved_performance_appraisal where rater_id='$rater_id' and user_id='$user_id' and approved_on='$approved_on' and status='h'";
									
					$result=$db_object->get_a_line($qry);
					
					$rater_comment=$result[rater_comment];
				
											
					$str.=preg_replace("/<{(.*?)}>/e","$$1",$match);
					
					
					
				}//end of for
			
				$sql="select rater_point,final_point2,rater_summary,final_summary from $approved_appraisal_results where o_id='$o_id' 
				
				and user_id='$user_id' and approved_on='$approved_on' and status='h'";
			
				$res=$db_object->get_a_line($sql);
				
				
				$final_summary=$res[final_summary];
								
				$explanation=$res[rater_summary];
				
				$points=$metexpectation;
				
				$points1=$res[rater_point];
				
				$total_points1=$res[final_point2];
				
				$qry2="select rater_comment,rater_id,user_id from $approved_performance_appraisal where
				
				 who='b' and user_id='$user_id' and approved_on='$approved_on' and o_id='$o_id'";
			
				
				$res2=$db_object->get_a_line($qry2);
				
				$comment_boss=$res2[rater_comment];
				
				
				$boss_id=$res2[rater_id];
				
				//$user_id=$res2[user_id];
				$user_id=$fUser;
								
				$boss_name=$common->name_display($db_object,$boss_id);
				
				$username=$common->name_display($db_object,$user_id);

				$Rcount=0;
				
				$rater=$k;
				
				$exp_qry="select rater_summary,rater_point from $approved_appraisal_results where
				
				  user_id='$user_id' and approved_on='$approved_on' and status='h' and o_id='$o_id' group by appraisal_id";
				
				$exp_res=$db_object->get_rsltset($exp_qry);
				
				$c=count($exp_res)-1;
				
				$exp="";
			
					for($i=0;$i<count($exp_res);$i++)
					{
						$id=$boss_res[$i][rater_id];
						$c=$i+1;
						
						$ct=$j+1;
						$name=$common->name_display($db_object,$id);
						$explain=$exp_res[$i][rater_summary];
						$fRater_exp_name="fRater_exp_name".$c."_".$ct;
						
						$fRater_exp="fRater_exp".$c."_".$ct;
						$exp.="<tr><td class=code>{{cBye}}<input type=text name=$fRater_exp_name value=$name readonly></td></tr>
						<tr><td class=code><TEXTAREA WRAP=PHYSICAL ROWS=3 COLS=50 name=$fRater_exp readonly>$explain</TEXTAREA></td></tr>";
					}
					
					$rater_no=$k;
					

					//$points1=$exp_res[$c][rater_point];
											
				$str1.=preg_replace("/<{(.*?)}>/e","$$1",$match1);
				
				$obj_replace=preg_replace("/<{rater_loopstart}>(.*?)<{rater_loopend}>/s",$str,$match1);
				
				$obj_replace_full.=preg_replace("/<{(.*?)}>/e","$$1",$obj_replace);
			
					
				
		}//end of for
		
		$total_points=$metexpectation*$cnt;
		
		$file=preg_replace("/<{objective_loopstart}>(.*?)<{objective_loopend}>/s",$obj_replace_full,$file);
		
		$final_qry="select final_summary,final_point2,boss_id from $approved_appraisal_results where
				
		user_id='$user_id' and approved_on='$approved_on' group by appraisal_id";
				
				$final_res=$db_object->get_rsltset($final_qry);
				
					$final="";
								

					for($i=0;$i<(count($final_res));$i++)
					{
						
						//$id=$boss_res[$i][rater_id];
						
						$id=$final_res[$i][boss_id];
						
						$d=$i+1;
						
						$name=$common->name_display($db_object,$id);
						
																
						$final_summary=$final_res[$i][final_summary];
						
						$final_name="fFinal_name_".$d;
						
						$final_summary_name="fFinal_summary_".$d;
						
						$final.="<tr><td class=code>{{cBye}}<input type=text name=$final_name value=$name readonly></td></tr>
						<tr><td class=code><TEXTAREA WRAP=PHYSICAL ROWS=3 COLS=50 name=$final_summary_name readonly>$final_summary</TEXTAREA></td></tr>";
						
						$final_point2=$final_res[0][final_point2];
					}
					
			
				
		$xArray=array("count"=>$cnt,"total_points"=>$total_points,"total_points1"=>$total_points1,"user_id"=>$user_id,"boss_id"=>$boss_id,"username"=>$username,"final_explanation"=>$final_exp,"appraisal_id"=>$dummy_id,"final"=>$final);
		
		$file = $common->direct_replace($db_object,$file,$xArray);
		
		$file = $common->direct_replace($db_object,$file,$val);
		
		echo $file;
	
	
	}
	
	function calculation($db_object,$common,$default,$user_id,$boss_id,$gbl_met_value,$err)
	{

		$app_sel_objective = $common->prefix_table("approved_selected_objective");
		
		$user_table = $common->prefix_table("user_table");
		
		$approved_feedback = $common->prefix_table("approved_feedback");
		
		$config_table = $common->prefix_table("config");
		
		$rating_table = $common->prefix_table("rating");
		
		$performance_feedback = $common->prefix_table("performance_feedback");
		
		$approved_affected = $common->prefix_table("approved_affected");
		
		$position_table = $common->prefix_table("position");
		
		$approveduser_objective = $common->prefix_table("approveduser_objective");
		
		$verified_user = $common->prefix_table("verified_user");
		
		$assign_performance_appraisal = $common->prefix_table("assign_performance_appraisal");
		
		$sql="select * from $assign_performance_appraisal where dummy_id='$dummy_id'";
		
		$res=$db_object->get_a_line($sql);
	
		$uid=$user_id;
		
		$user_id=$boss_id;
		
		if($uid!="")
		{
			
			$psel = "select position from $user_table where user_id='$user_id'";
			
			$pres = $db_object->get_a_line($psel);
			
			$position = $pres['position'];
			
			$below = $common->get_chain_below($position,$db_object,$arr);
			
			$below_user = $common->get_user_id($db_object,$below);
			
			$spt_user = array();
			
			for($c=0;$c<count($below_user);$c++)
			{
				$spt_user[] = $below_user[$c]['user_id'];
			}
	
			$split = @implode("','",$spt_user);

			if($user_id!=1)
			{
				$selqry="select user_id from $user_table where admin_id='$user_id' and $user_table.user_id not in('$split')";
				
				$userset=$db_object->get_single_column($selqry);
			}
			else
			{
				$selqry="select $user_table.user_id from $user_table,$position_table where
				
					 $user_table.position=$position_table.pos_id and ($user_table.position<>NULL or $user_table.position<>0) and
					 
					 $user_table.user_id!=1 and $user_table.user_id not in('$split')
					 
					 order by $position_table.level_no desc";			
					 
					 $userset=$db_object->get_single_column($selqry);
			}
		
			

			if(!is_array($userset))
			{
				$userset = array();
			}
			
			for($b=0;$b<count($below_user);$b++)
			{	
				$userset[] = $below_user[$b]['user_id'];		
			}		

		}

		if($uid!="")
		{
			$user_id  = $uid;
		}
		else
		{
			
			$userset[] = $user_id;
		}	
		
		if(in_array($user_id,$userset))
		{

		
			$val['uid']  = $uid;
			
			$path = $common->path;
			
	
		

		$name = $common->name_display($db_object,$user_id);
		
		$userqry = "select username,position from $user_table where user_id='$user_id'";
		
		$userres = $db_object->get_a_line($userqry);
		
		$position = $userres['position'];

		$bossid = $boss_id;
		
		
		$bossqry = "select username from $user_table where user_id = '$bossid'";
		
		$boss = $db_object->get_a_line($bossqry);
		
		$val['bossname'] = $boss['username'];
		

		$selobj = "select sl_id,o_id,objective_$default as objective,priority,committed_no,percent from $app_sel_objective 
		
				where user_id='$user_id' and status='A' order by sl_id";
				
		$selres = $db_object->get_rsltset($selobj);
		
		
		$countselres = count($selres);

		$boss=0;
		
		$conqry = "select person_affected from $config_table";
		
		$conres = $db_object->get_a_line($conqry);
		
		$noofperson = $conres['person_affected'];
		
		$boss = 1;	
		
		$totalperson = $noofperson + $boss;	
			
		$ratqry = "select rval from $rating_table where rval='$gbl_met_value'";
		
		$ratres = $db_object->get_a_line($ratqry);
		
		$r_val = $ratres['rval'];

		$metexpectation = $r_val * $totalperson;
		
		$points=$metexpectation;
		
		$total_points=0;
		
		$c=count($selres);
		
			preg_match("/<{rater_loopstart}>(.*?)<{rater_loopend}>/s",$file1,$match);
			
			$match=$match[0];
				
		for($i=0;$i<count($selres);$i++)
		{
			
			$total_points=$total_points+$points;
			
			$str1="";
			
			$actual="";
			
			$Cfulfill = "";
			
			$count = $count + 1;
			
			$objective = $selres[$i]['objective'];			
			
			$object[$i]=$objective;
			
			$o_id = $selres[$i]['o_id'];
			
			$obj_id[$i]=$o_id;
			
			$priority = $selres[$i]['priority'];
			
			$prior[$i]=$priority;
					
			$sl_id = $selres[$i]['sl_id'];
			
			$checkcumulative = $selres[$i]['percent'];
			
	
			$oqry = "select met_id from $approveduser_objective where o_id='$o_id' and 
				user_id='$user_id'";
			
			$ores = $db_object->get_a_line($oqry);
			
			$met_id = $ores['met_id'];
			
			$mqry = "select o_id from $approveduser_objective where met_id='$met_id' and 
				user_id='$user_id'";
			
			$mres = $db_object->get_single_column($mqry);
			
			$aver  = count($mres);
		
			$oid = implode("','",$mres);				
		
			$Ratervalue = "select sum(r_id) from $approved_feedback where o_id in ('$oid')
			 and user_id='$user_id' and status<>'1' and status<>'2'";
		
			$Resvalue = $db_object->get_single_column($Ratervalue);
			
			$actual = $Resvalue[0];
			
			$actual= @($actual/$aver);
			
			$act[$i]=$actual;
			
			$get = $common->get_fullfilled($db_object,$o_id,$user_id,$dates);
			
			$fulfilled = $get['Cfulfill'];

			$expected = @($actual/$metexpectation);
			
			$expected = $expected * 100;
			
			$expectation = @sprintf("%01.2f",$expected);
			
			$expect[$i]=$expectation;
			
			$fulfilled = @sprintf("%01.2f",$fulfilled);
			
			
			$fulfill[$i]=$fulfilled;
			
			$committed = $get['Ccommit'];
			
			$committ[$i]=$committed;
			
			$accomplish = $get['Caccomplish'];
			
			$accomplished[$i]=$accomplish;
			
			$boss_name=$common->name_display($db_object,$boss_id);

			$perqry1 = "select user_id from $performance_feedback where request_from='$user_id' and 
				status='A' and latest='N' and sl_id='$sl_id' and user_id<>'$user_id' and user_id<>'$bossid'";
				
			$perres1[$i] = $db_object->get_single_column($perqry1);
			
			$str="";
			
			
			
		
	}//end of outer for
	
		
		}//end of if
		$ret_arr=array($points,$object,$obj_id,$prior,$act,$expect,$fulfill,$committ,$accomplished,$countselres,$perres1);

		
		return($ret_arr);
	}
	
	function show_employees_appraisal($db_object,$common,$user_id)
	{
		
		$approved_performance_appraisal=$common->prefix_table("approved_performance_appraisal");
		
		$approved_appraisal_results=$common->prefix_table("approved_appraisal_results");
		
		$user_table=$common->prefix_table("user_table");
		
		//$users=$common->return_direct_reports($db_object,$user_id);
		
	if($user_id!=1)	
	{
		
		$sql="select position from $user_table where user_id='$user_id'";
		
		$res=$db_object->get_a_line($sql);
		
		$pos=$res[position];
		
		$users_under=$common->get_chain_below($position,$db_object,$twodarr);
		
		$users_id=$common->get_user_id($db_object,$users_under);
		
		for($i=0;$i<count($users_id);$i++)
		{
			$users[$i]=$users_id[$i][user_id];
						
		}
	}
	else
	{
		$sql="select user_id from $user_table where user_id<>'1'";
		
		$users=$db_object->get_single_column($sql);
	}
		if(count($users)>1)
		{
			$user=@implode(",",$users);
			
			$user="(".$user.")";
			
		}
		else
		{
			$user="(".$users[0].")";
		}
		
		$sql="select $user_table.username,$approved_performance_appraisal.approved_on from
		
		 $user_table,$approved_performance_appraisal where 
		
		$approved_performance_appraisal.user_id=$user_table.user_id 
		
		and $approved_performance_appraisal.user_id in $user group by approved_on";
		
		$sql_result=$db_object->get_rsltset($sql);
		
		$path=$common->path;
		
		$xtemplate=$path."templates/performance/view_employees_approved_appraisal.html";
		
		$file=$common->return_file_content($db_object,$xtemplate);
		
		preg_match("/<{app_loopstart}>(.*?)<{app_loopend}>/s",$file,$match);
		
		$match=$match[0];
		
		for($i=0;$i<count($sql_result);$i++)
		{
			$approved_on=$sql_result[$i][approved_on];
			
			$approved=@explode(" ",$approved_on);
			
			$app1=@explode("-",$approved[0]);
			
			$date1[0]=@implode(".",$app1);
			
			$app2=@explode(":",$approved[1]);
			
			$date1[1]=$app2[0].":".$app2[1];
			
			$date=@implode(".",$date1);
			
			$name=$sql_result[$i][username];
			
			$str.=preg_replace("/<{(.*?)}>/e","$$1",$match);
		}
		$file=preg_replace("/<{app_loopstart}>(.*?)<{app_loopend}>/s",$str,$file);
		
		$file=$common->direct_replace($db_object,$file,$xArray);
		
		echo $file;
	
	}
}

$obj=new alert_appraisal();


switch($action)
{

case NULL:

	$obj->alert_approved_appraisal($db_object,$common,$user_id);
	
	break;
	
case "show":
	
	$obj->show_approved_appraisal($db_object,$common,$user_id,$date,$default);
	
	break;
	
case "boss":

	$obj->show_employees_appraisal($db_object,$common,$user_id);
	
	break;
	

}
include_once("footer.php");
?>
