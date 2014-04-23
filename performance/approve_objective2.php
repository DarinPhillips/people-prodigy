<?
include_once("../session.php");
if(($back=="")&&($save=="")&&($next==""))
{
include_once("header.php");
}
class userplan2
	{
	function view_plan($db_object,$common,$default,$user_id,$uid,$gbl_freq_array,$gbl_percent_array,$s_id,$post_var)
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
			$filename = $path."/templates/performance/approve_objective2.html";
			$file = $common->return_file_content($db_object,$filename);
		//Table Prefix
			$user_table = $common->prefix_table("user_table");
			$objective = $common->prefix_table("unapproveduser_objective");
			$approved_cat = $common->prefix_table("approved_category");
			$approved_met = $common->prefix_table("approved_metrics");
			$config = $common->prefix_table("config");
			$selected = $common->prefix_table("unapproved_selected_objective");
			$affected_table = $common->prefix_table("unapproved_affected");
			$help_table=$common->prefix_table("unapproved_help");
			$priority_table = $common->prefix_table("priority");

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

		//from unapproved_selected_objective table			
			$sqry = "select o_id from $selected where user_id='$user_id'";
			$sres = $db_object->get_single_column($sqry);			
			$maxqry = "select max(sl_id) from $selected ";
			$maxres = $db_object->get_single_column($maxqry);
			$maxslid = $maxres[0];

			$indexval = @array_keys($gbl_freq_array);
			$perindex = @array_keys($gbl_percent_array);
				
		
			
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
					$dqry = "select sl_id,objective_$default as obj ,priority,committed_no,percent as percent,
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

				for($j=0;$j<count($gbl_percent_array);$j++)
				{
					$perselect ="";
					$percentindex = $perindex[$j];
					
					$percent = $gbl_percent_array[$percentindex];
					
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
					for($l=0;$l<count($gbl_freq_array);$l++)	
					{		
						$freselect="";				
						$index = $indexval[$l];
						if($index==$frqcheck)
						{
							$freselect = "selected";
						}
						$frequency  = $gbl_freq_array[$index];
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
						if($fPrior=="")
						{
							$mysql="select pval ,min(pval) as m  from $priority_table group by pval";
							$a_check=$db_object->get_a_line($mysql);
							//print_r($a_check);
							$fPrior=$a_check['pval'];				
						}
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
				
			}
			
			$file=preg_replace($pattern,$str,$file);							
			$qry = "select username,admin_id from $user_table where user_id='$user_id'";
			$res = $db_object->get_a_line($qry);
			$user = $res['username'];
			$boss = $res['admin_id'];
			$user_array['user'] = $user;
			$user_array['emp_id'] = $uid;

		
			$pattern6 = "/<!--start-->(.*?)<!--end-->/s";
			$space="";
			$comment = "";
			if($comment=="")
			{
				$file = preg_replace($pattern6,$space,$file);
			}
			$file = $common->direct_replace($db_obect,$file,$user_array);
			echo $file;

		}//end view
		
		function save_objective($db_object,$common,$default,$user_id,$emp_id,$post_var)
		{
			if($emp_id!="")
			{
				$user_id=$emp_id;
			}

		//Table Prefix
		
			$selected = $common->prefix_table("unapproved_selected_objective");
			$affected_table = $common->prefix_table("unapproved_affected");
			$help_table = $common->prefix_table("unapproved_help");
			$user_table = $common->prefix_table("user_table");
		
		//array declaration
			$newoid = array();
			$newobjective = array();
			$objectivekey = array();
			$affectedarray = array();
			$helparray = array();
			$del_array = array();
			
		$posqry = "select position from $user_table where user_id='$user_id'";
		$posres = $db_object->get_a_line($posqry);
		$pos = $posres['position'];
		$positions = $common->get_chain_below($pos,$db_object,$twodarr);
		$retres  = $common->get_user_id($db_object,$positions);
		$ct = count($positions);

		$posid=array();
		for($p=0;$p<count($retres);$p++)
		{
			$posid[] = $retres[$p]['user_id'];
		}

			while(list($key,$value)=each($post_var))
			{
				$$key = $value;
				//echo "key=$key : val=$value<br>";
					if(ereg("^metric_",$key))
					{
						list($name,$n,$nod,$nid)=split("_",$key);	
						$newoid["$nid"]["$nod"] = $value;
					}
					if(ereg("^fObjective_",$key))
					{
						list($name,$n,$id)=split("_",$key);
						//if($value!="")
						{
						$newobjective["$id"] = $value;
						$objectivekey[] = $key;							
						}
					}
					if(ereg("^fAff_id_",$key))
					{
						list($name,$n,$n1,$sid,$aid)=split("_",$key);
						if($value!="")
						{
							//$affectedarray["$sid"]["$aid"] = $value;
						}
					}
					if(ereg("fAff_name_",$key))
					{
						list($name,$n,$n1,$sid,$aid)=split("_",$key);
						if($value!="")
						{
							$affectedarray["$sid"]["$aid"] = $value;
						}
					}
					if(ereg("^fHelp_name_",$key))
					{
						list($name,$n,$n1,$sid,$aid)=split("_",$key);
						if($value!="")
						{
							$helparray["$sid"]["$aid"] = $value;
						}
					}								
																		
			}	
				

		for($i=0;$i<count($newobjective);$i++)
		{		
			$key=$objectivekey[$i];
			
			
			if(ereg("^fObjective_new_",$key))
			{
				list($name,$n,$id)=split("_",$key);
				$objective = $newobjective[$id];
								
				$noid = $newoid[$id];				
				$idval = @array_keys($noid);				
				$sel_id = $idval[0];

				$priority_name="fPriority_new_".$id;
				$priority = $$priority_name;
			
				$commit = "fCommitted_new_".$id;
				$committed = $$commit;
				
				$per = "fPercent_new_".$id;
				$percent = $$per;

				$how = "fHow_new_".$id;
				$show = $$how;
					
				$del_array[]= $sel_id;
				if(($objective!="")||(($committed!="")&&($committed!='0'))||($show!=""))
				{//check null loop
				$objchk = "select o_id from $selected where user_id='$user_id' and o_id='$sel_id'";
					$objres = $db_object->get_a_line($objchk);
					$objcheck = $objres['o_id'];
					if($objcheck==0)
					{//check id loop					
						$qry = "insert into $selected set user_id='$user_id',o_id='$sel_id',
						objective_$default='$objective',priority='$priority',
						committed_no='$committed',percent ='$percent',how_to_get_$default='$show'";
					$sl_id = $db_object->insert_data_id($qry);
				
				
	
					//echo "qry = $qry<br>";
					$affected = $affectedarray[$id];				
					$affectedkey = @array_keys($affected);

					for($j=0;$j<count($affected);$j++)
					{
						$kval = $affectedkey[$j];
						$aname = "fAff_name_new_".$id."_".$kval;
						$affname = $$aname;
						$nqry = "select user_id from $user_table where username='$affname'";
						$nres = $db_object->get_a_line($nqry);
						$aff_id = $nres['user_id'];
					

						$fromdt = "fFromdate_new_".$id."_".$kval;
						$from = $$fromdt;
						$sp = split("/",$from);
						$from = $sp[2]."-".$sp[0]."-".$sp[1];
		
						$todt = "fTodate_new_".$id."_".$kval;
						$to = $$todt;
						$sp = split("/",$to);
						$to = $sp[2]."-".$sp[0]."-".$sp[1];

						$freq = "fFrequency_new_".$id."_".$kval;
						$frequenc = $$freq;
						if(!(in_array($aff_id,$posid)))
						{
							$aff_id='';
						}
					

						$qry1 = "insert into $affected_table set sl_id='$sl_id',
						user_id='$aff_id',from_date='$from',to_date='$to',frequency='$frequenc'";
						$db_object->insert($qry1);
					//	echo "qry1 = $qry1<br>";
					
					}
					$helpar = $helparray[$id];
					$helpkey = @array_keys($helpar);
					for($k=0;$k<count($helpar);$k++)
					{
					
						$hval = $helpkey[$k];
						$hname = "fHelp_name_new_".$id."_".$hval;
						$helpname = $$hname;
	
						$hqry = "select user_id from $user_table where username='$helpname'";
						$hres = $db_object->get_a_line($hqry);
						$help_id = $hres['user_id'];
						if(!(in_array($help_id,$posid)))
						{
							$help_id='';
						}
						$qry2 = "insert into $help_table set sl_id='$sl_id',user_id='$help_id'";
						$db_object->insert($qry2);
						//echo "qry2 = $qry2<br>";
						}
					}//check id loop
					}//check null loop end
				}
				else
				{//update data
					if(ereg("^fObjective__",$key))
					{
						list($name,$n,$id)=split("_",$key);
						$objective = $newobjective[$id];	
																
						$noid = $newoid[$id];				
						$idval = @array_keys($noid);				
						$sel_id = $idval[0];
	
						$priority_name="fPriority__".$id;
						$priority = $$priority_name;
			
						$commit = "fCommitted__".$id;
						$committed = $$commit;
						
						$per = "fPercent__".$id;
						$percent = $$per;
	
						$how = "fHow__".$id;
						$show = $$how;	
						if(($objective!="")||($committed!="")||($committed=='0')||($show!=""))
						{
							$del_array[] = $sel_id;
							$qry = "update $selected set o_id='$sel_id',objective_$default='$objective',priority='$priority',
							committed_no='$committed',percent ='$percent',how_to_get_$default='$show' where sl_id='$id'";
							$db_object->insert($qry);
						}
					}
				
					$affected = $affectedarray[$id];				
					$affectedkey = @array_keys($affected);
					$delaffected = "delete from $affected_table where sl_id='$id'";
					$db_object->insert($delaffected);			
					//echo "delaff = $delaffected<br>";
										
					for($j=0;$j<count($affected);$j++)
					{
						$kval = $affectedkey[$j];
						//$aff_name = $affected[$kval];
						$aname = "fAff_name__".$id."_".$kval;
						$affname = $$aname;
						$nqry = "select user_id from $user_table where username='$affname'";
						$nres = $db_object->get_a_line($nqry);
						$aff_id = $nres['user_id'];
					
			
						$fromdt = "fFromdate__".$id."_".$kval;						
						$from = $$fromdt;
						$sp = split("/",$from);
						$from = $sp[2]."-".$sp[0]."-".$sp[1];
		
						$todt = "fTodate__".$id."_".$kval;
						$to = $$todt;
						$sp = split("/",$to);
						$to = $sp[2]."-".$sp[0]."-".$sp[1];

						$freq = "fFrequency__".$id."_".$kval;
						$frequenc = $$freq;
						//echo "affa = $aff_id";
						if(!(in_array($aff_id,$posid)))
						{
							$aff_id='';
						}	
				
						$qry1 = "insert into $affected_table set sl_id='$id',
						user_id='$aff_id',from_date='$from',to_date='$to',frequency='$frequenc'";
						$db_object->insert($qry1);
						//echo "qry1 = $qry1<br>";
						
					}
					$helpar = $helparray[$id];
					$helpkey = @array_keys($helpar);
					$delhelp = "delete from $help_table where sl_id='$id'";
					$db_object->insert($delhelp);	
					//echo "delhelp = $delhelp<br>";		
					for($k=0;$k<count($helpar);$k++)
					{
						
						$hval = $helpkey[$k];					
						//$help_id = $helpar[$hval];
						$hname = "fHelp_name__".$id."_".$hval;
						$helpname = $$hname;
					
						$hqry = "select user_id from $user_table where username='$helpname'";
						$hres = $db_object->get_a_line($hqry);
						$help_id = $hres['user_id'];
						if(!(in_array($help_id,$posid)))
						{
							$help_id='';
						}
					
						$qry2 = "insert into $help_table set sl_id='$id',user_id='$help_id'";
						$db_object->insert($qry2);
						//echo "qry2 = $qry2<br>";
					}
					
				}															
			
			}
			$sp = implode("','",$del_array);
			$delqry = "delete from $selected where o_id not in('$sp') and user_id='$user_id'";
			$db_object->insert($delqry);
		}//end save
	
	}//end userclass
	
	$ob = new userplan2;
	if($back!="")
	{
		header("location: approve_objective.php?uid=$emp_id");
	}
	if($save!="")
	{
		$ob->save_objective($db_object,$common,$default,$user_id,$emp_id,$post_var);
		header("location:per_setting.php");
	}
	if($next!="")
	{
		$ob->save_objective($db_object,$common,$default,$user_id,$emp_id,$post_var);
		header("location: approve_objective3.php?uid=$emp_id");
	}

	$ob->view_plan($db_object,$common,$default,$user_id,$emp_id,$gbl_freq_array,$gbl_percent_array,$s_id,$post_var);
include_once("footer.php");
?>
