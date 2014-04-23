<?php
include("../session.php");
include("header.php");
class View_Objectives
{
	
		
	function view_plan($db_object,$common,$default,$user_id,$uid,$gblfreq,$gblpercent,$s_id,$post_var,$error_msg)
		{
				
				
			$path = $common->path;
			if($uid!="")
			{
				$user_id=$uid;
			}
			while(list($key,$value)=each($post_var))
			{
				$$key = $value;
				$user_array[$key] = $value;
			}	
			$filename = $path."/templates/performance/view_objectives.html";
			$file = $common->return_file_content($db_object,$filename);
		//Table Prefix
			$user_table = $common->prefix_table("user_table");
			$objective = $common->prefix_table("tempuser_objective");
			$approved_cat = $common->prefix_table("approved_category");
			$approved_met = $common->prefix_table("approved_metrics");
			$config = $common->prefix_table("config");
			$selected = $common->prefix_table("temp_selected_objective");
			$affected_table = $common->prefix_table("temp_affected");
			$help_table=$common->prefix_table("temp_help");
			$comment_table=$common->prefix_table("performance_comments");
			$priority_table = $common->prefix_table("priority");
			$rejected_table = $common->prefix_table("rejected_objective");

		//from performance_comments
			$perqry = "select comment_$default as comment from $comment_table where user_id='$user_id' and p_status='A'";
			$perres = $db_object->get_a_line($perqry);
			$comment = $perres['comment'];

		//from config table;
			$conqry = "select person_affected,person_help_needed from $config";
			$rescon = $db_object->get_a_line($conqry);
			$affected = $rescon["person_affected"];
			$help = $rescon["person_help_needed"];
			
		//from tempuser_objective table
			$uqry = "select met_id,o_id from $objective where user_id='$user_id' order by met_id";
			$ures = $db_object->get_rsltset($uqry);
		//from priority table
			$pqry = "select p_id,priority_$default,pval from $priority_table order by p_id";
			$pres = $db_object->get_rsltset($pqry);
		//from rejected table
			$rejected="";
			$rejqry = "select user_id from $rejected_table where user_id='$user_id'";	
			$rej = $db_object->get_a_line($rejqry);
			$rejected = $rej['user_id'];

		//from temp_selected_objective table			
			$sqry = "select o_id from $selected where user_id='$user_id'";
			$sres = $db_object->get_single_column($sqry);			
			$maxqry = "select max(sl_id) from $selected ";
			$maxres = $db_object->get_single_column($maxqry);
			$maxslid = $maxres[0];

			$indexval = @array_keys($gblfreq);
			$perindex = @array_keys($gblpercent);
				
		
			
			$pattern="/<{category_loopstart(.*?)<{category_loopend}>/s";
			preg_match($pattern,$file,$arr);
			$match=$arr[0];
			$str="";
	
			$pattern1="/<{percentage_loopstart}>(.*?)<{percentage_loopend}>/s";
			preg_match($pattern1,$file,$arr1);
			$match1 = $arr1[0];

			$pattern2 = "/<{affected_loopstart}>(.*?)<{affected_loopend}>/s";
			preg_match($pattern2,$file,$arr2);
			$match2 = $arr2[0];
	
				
			$pattern3 = "/<{frequency_loopstart}>(.*?)<{frequency_loopend}>/s";
			preg_match($pattern3,$file,$arr3);
			$match3 = $arr3[0];
			
			$pattern4 = "/<{help_loopstart}>(.*?)<{help_loopend}>/s";
			preg_match($pattern4,$file,$arr4);
			$match4 = $arr4[0];

			$pattern5 = "/<{priority_loopstart}>(.*?)<{priority_loopend}>/s";
			preg_match($pattern5,$file,$arr5);
			$match5 = $arr5[0];
			$dt = count($ures);

			$pattern6 ="/<!--save_start/";
			$pattern7 = "/save_end-->/";
			$space="";
			$fg = count($ures);

			
			//to display save & next button
			if($fg!=0)
			{
				$file = preg_replace("$pattern6",$space,$file);
				$file = preg_replace("$pattern7",$space,$file);
			}
			else
			{
				$user_array["Norecords"] = $error_msg["cNorecords"];
			}
				

			for($i=0;$i<count($ures);$i++)
			{
								
				$count = $i+1;
				$str1="";
				$str2="";
				$str4="";
				$str5="";
				$allnew="";
				$flag =0 ;
				$rs = $ures[$i]['met_id'];
				$oid = $ures[$i]['o_id'];
				
				$mqry = "select cat_id,metrics_$default from $approved_met where 
					met_id='$rs'";				
				$mres = $db_object->get_a_line($mqry);
				$cqry = "select category_$default from $approved_cat where cat_id='$mres[cat_id]'";
				$cres = $db_object->get_a_line($cqry);
				$cval =  $cres[0];
				$mval = $mres[1];
							
				if(@in_array($oid,$sres))
				{
					$dqry = "select sl_id,objective_$default as obj ,priority,committed_no,percent,
						how_to_get_$default as how from $selected where o_id='$oid'";
					$dres = $db_object->get_a_line($dqry);
					$affected_id = $dres['sl_id'];
					$fObjective = $dres['obj'];
					$fPrior = $dres['priority'];
					$fCommitted = $dres['committed_no'];
					$fPercent = $dres['percent'];
					$fGet = $dres['how'];
					$flag = 1;
					$c = $dres['sl_id'];										
				}
				else
				{
					$maxslid = $maxslid + 1;
					$c = $maxslid ;
					$allnew = "new";
					$affected_id = "";
					$fObjective = "";
					$fPrior = "";
					$fCommitted = "";
					$fPercent = "";
					$fGet = "";
				}			
			//percentage loop
				for($j=0;$j<count($gblpercent);$j++)
				{
					$perselect ="";
					$percentindex = $perindex[$j];
					$percent = $gblpercent[$percentindex];
										
					if($percentindex==$fPercent)
					{
						$perselect = "selected";
					}
					$str1.=preg_replace("/\<\{(.*?)\}\>/e","$$1",$match1);										
				}
				$temp = preg_replace($pattern1,$str1,$match);


			//Affected loop	

				for($k=0;$k<$affected;$k++)
				{	
					$str3="";
					$frqcheck="";
					$to="";
					$from="";
					if($flag=='1')
					{
						
						$affqry = "select aff_id,user_id,from_date,to_date,frequency from 
							$affected_table where sl_id='$affected_id' order by aff_id";
						$affres = $db_object->get_rsltset($affqry);

						$userid = $affres[$k]['user_id'];
						$userqry = "select username,email from $user_table where user_id='$userid'";
						$userres = $db_object->get_a_line($userqry);
						$fUsername = $userres['username'];
						$fEmail = $userres['email'];
			
						$fFrm = $affres[$k]['from_date'];

						if(($fFrm!="")&&($fFrm!="0000-00-00"))
						{
							$sp = split("-",$fFrm);
							$fFrom = $sp[1]."/".$sp[2]."/".$sp[0];
						}
						else
						{
							$fFrom="";
							$fTo="";
						}							
							$to = $affres[$k]['to_date'];
						if(($to!="")&&($to!="0000-00-00"))
						{
							$sp = split("-",$to);
							$fTo = $sp[1]."/".$sp[2]."/".$sp[0];
						}
					
						$frqcheck = $affres[$k]['frequency'];
						$ct = $affres[$k]['aff_id'];
						if($ct=="")
						{
							$ct = $k+1;
							$fFrom="";
							$fTo="";
							$insert = "ins";	
						}
						
						
						
					}
					else
					{
						$fFrom="";
						$fTo="";
						$fUsername="";
						$fEmail="";
						$userid="";
						$ct = $k+1;
					}						
			//$str3=$common->singleloop_replace($db_object,"<{frequency_loopstart}>","<{frequency_loopend}>",$temp,$error_msg["Frequency"],$sel_val);

			//frequency loop
					for($l=0;$l<count($gblfreq);$l++)	
					{		
						$freselect="";				
						$index = $indexval[$l];
						if($index==$frqcheck)
						{
							$freselect = "selected";
						}
						$frequency  = $gblfreq[$index];
						$str3.=preg_replace("/\<\{(.*?)\}\>/e","$$1",$match3);
					}							
					$str_int = preg_replace($pattern3,$str3,$match2);
					$str2.=preg_replace("/\<\{(.*?)\}\>/e","$$1",$str_int);
				}			
				$temp2 = preg_replace($pattern2,$str2,$temp);

			//helploop
				for($m=0;$m<$help;$m++)
				{
					
					if($flag==1)
					{
						$helpqry = "select h_id,user_id from $help_table where sl_id='$affected_id' order by h_id";
						$helpres = $db_object->get_rsltset($helpqry);						
						$huserid = $helpres[$m]['user_id'];
						$huserqry = "select username,email from $user_table where user_id='$huserid'";
						$huserres = $db_object->get_a_line($huserqry);
						$fHusername = $huserres['username'];
						$fHemail = $huserres['email'];
						
					}
					else
					{
						$cnt = $m + 1;
						$fHusername="";
						$fHemail="";
						$huserid="";
					}
					$cnt = $m+1;
					$str4.=preg_replace("/\<\{(.*?)\}\>/e","$$1",$match4);
				}
					$temp3 = preg_replace($pattern4,$str4,$temp2);	
			//priority loop
				$def_priority = $error_msg["defaultpriority"];
				if($fPrior=="")
				{
				$mysql="select pval ,min(pval) as m  from $priority_table group by pval";
				$a_check=$db_object->get_a_line($mysql);
				//print_r($a_check);
				$fPrior=$a_check['pval'];				
				}
				for($n=0;$n<count($pres);$n++)						
					{	
						$priorcheck = "";
						$pindex = $pres[$n]['pval'];						
						if($pindex==$fPrior)
						{	
							$priorcheck = "checked";
						}				
						$pri = 	"priority_".$default;
						$pval  = $pres[$n][$pri];						
						$str5.=preg_replace("/\<\{(.*?)\}\>/e","$$1",$match5);
					}							 			
					$temp4 = preg_replace($pattern5,$str5,$temp3);										
				$str.=preg_replace("/\<\{(.*?)\}\>/e","$$1",$temp4); 
				
			}//end i loop


			$file=preg_replace($pattern,$str,$file);
		
			if(empty($str))
			{
				
				$file=$error_msg["cEmptyrecords"];				
			}

			$qry = "select username,admin_id from $user_table where user_id='$user_id'";
			$res = $db_object->get_a_line($qry);
			$user = $res['username'];
			$boss = $res['admin_id'];
			$user_array['user'] = $user;
			$user_array['emp_id'] = $uid;


		//comments
			$pattern6 = "/<!--start-->(.*?)<!--end-->/s";
			$space="";
			if($rejected=="")
			{
				$file = preg_replace($pattern6,$space,$file);
			}
			$user_array['fComment'] = $comment;
			$file = $common->direct_replace($db_obect,$file,$user_array);
			echo $file;

		}//end view
	
}
$vobj=new View_Objectives;
$user_table=$common->prefix_table("user_table");
$selqry="select admin_id from $user_table where user_id='$fUser_id'";
$getid=$db_object->get_a_line($selqry);


if($getid['admin_id']==$user_id||$user_id==1)
{
$vobj->view_plan($db_object,$common,$default,$fUser_id,$uid,$gbl_freq_array,$gbl_percent_array,$s_id,$post_var,$error_msg);
}
else
{
echo "This Employee is not under  Your Control";	
}
include("footer.php");
?>
