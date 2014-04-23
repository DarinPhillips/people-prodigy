<?
include_once("../session.php");
if(($back=="")&&($approval=="")&&($approved==""))
{
include_once("header.php");
}
class plan3
	{
	function view($db_object,$common,$default,$user_id,$err)
		{
			$path = $common->path;
			$filename = $path."templates/performance/userplan3_input.html";
			$file = $common->return_file_content($db_object,$filename);
		//table declaration
			$user_table = $common->prefix_table("user_table");
			$selected_table = $common->prefix_table("temp_selected_objective");
			$affected_table = $common->prefix_table("temp_affected");
			$qualify = $common->prefix_table("qualification");
			$help_table = $common->prefix_table("temp_help");
			$selected_qualification = $common->prefix_table("temp_selected_qualification");
			$priority_table = $common->prefix_table("priority");
			$xSetTable	= $common->prefix_table("config");
		//user_table
			$qry = "select username from $user_table where user_id='$user_id'";
			$res = $db_object->get_a_line($qry);
			$val['uid'] = $user_id;
			$val['fName'] = $res['username'];
		//selected_table
			$selqry = "select sl_id,objective_$default as objective ,how_to_get_$default as how,committed_no,priority
				from $selected_table where user_id='$user_id' order by sl_id";
			$selres = $db_object->get_rsltset($selqry);
		//qualify table
			$qqry = "select q_id,qualification_$default as qualification from $qualify";
			$qres = $db_object->get_rsltset($qqry);
				
		//selected_qualification
			$qid_array = array();
			$id_array = array();
			$ssqry = "select sl_qid,sl_id,q_id from $selected_qualification where user_id='$user_id'";
			$ssres = $db_object->get_rsltset($ssqry);

			for($i=0;$i<count($ssres);$i++)
			{
				$slid = $ssres[$i]['sl_id'];
				$qid_array[$slid][] = $ssres[$i]['q_id'];
				$id = $ssres[$i]['q_id'];
				$id_array[$slid][$id] = $ssres[$i]['sl_qid'];				
			}
		
		//objective loop
			$pattern = "/<{objective_loopstart}>(.*?)<{objective_loopend}>/s";	
			preg_match($pattern,$file,$arr);
			$match=$arr[0];
			$str="";
		//Qualification loop

		preg_match("/<{tablerow_loopstart}>(.*?)<{tablerow_loopstop}>/s",$file,$xRowmatches);
		$xRowmatch	= $xRowmatches[0];

		preg_match("/<{tablecol_loopstart}>(.*?)<{tablecol_loopstop}>/s",$file,$xColmatches);
		$xColmatch	= $xColmatches[0];

		
		$xQ	 = "SELECT no_of_qualification FROM $xSetTable";
		$xCols	= $db_object->get_a_line($xQ);
		$xCols	= $xCols['no_of_qualification'];

		$pattern1 = "/<!--approval_start/s";
		$pattern2 = "/approval_end-->/s";
		$pattern3 = "/<!--approval_start(.*?)approval_end-->/s";
		$space="";
	//to display approval button
		$selcount = count($selres);
		if($selcount!=0)
		{
			$file = preg_replace($pattern1,$space,$file);
			$file = preg_replace($pattern2,$space,$file);
		}
		else
		{
			$val["norecords"] = $err["cEmptyrecords"];
			$file = preg_replace($pattern3,$space,$file);
		}
			
				for($i=0;$i<count($selres);$i++)
				{
					$str1="";
					$xRowstr="";
					$fObjective = $selres[$i]['objective'];
					$fNumber =$selres[$i]['committed_no'];
					$pid = $selres[$i]['priority'];

					$psel = "select priority_$default as prior from $priority_table where pval='$pid'";
					$pres = $db_object->get_a_line($psel);
					$fPriority = $pres['prior'];
					$fHow = $selres[$i]['how'];
					$slid = $selres[$i]['sl_id'];																			
					for($j=0;$j<=count($qres);$j++)
					{	
						//echo $xRowstr;						
						$dt = $j%($xCols);
						//echo"$j mod $xCols = $dt<br> ";
						$cnt = count($qres);
						$cnt = $cnt-1;
						if($j%($xCols)==0 || $j==count($qres))//-----Controls the rows
						{	
							
							
							$xTemp_rowstr = preg_replace("/<{tablecol_loopstart}>(.*?)<{tablecol_loopstop}>/s",$xColstr,$xRowmatch);
							$xRowstr.= preg_replace("/<{(.*?)}>/e","$$1",$xTemp_rowstr);
							$xTemp_rowstr="";
							$xColstr="";							
							//echo $xRowstr;
							//echo"<hr>";
						}
						$checked = "";
						$edit = "";
						$slqid="";
						$fQualification = $qres[$j]['qualification'];	
						$sid = $slid;
						$qid = $qres[$j]['q_id'];	
						$chek = $qid_array[$sid];						
						if(@in_array($qid,$chek))
						{
							//$edt = $id_array[$sid][$qid];
							//$slqid = "_".$edt;
							$checked = "checked";
							//$edit = "edit";							
						}
						if($j!=count($qres))
						{
							$xColstr.= preg_replace("/<{(.*?)}>/e","$$1",$xColmatch);
						}
					}		
					$temp = preg_replace("/<{tablerow_loopstart}>(.*?)<{tablerow_loopstop}>/s",$xRowstr,$match);
					$str .= preg_replace("/\<\{(.*?)\}\>/e","$$1",$temp);
				}	
			$file=preg_replace($pattern,$str,$file);
			if($user_id==1)
			{
				$val['approval'] = $err['capproved'];
			}
			else
			{
				$val['approval'] = $err['capproval'];
			}
			$file = $common->direct_replace($db_object,$file,$val);
			echo $file;
		}//end view
	function submit($db_object,$common,$default,$user_id,$post_var)
		{
			$check_array= array();
			$key_array = array();
			
			$sel_qualification = $common->prefix_table("temp_selected_qualification");
			$comments = $common->prefix_table("performance_comments");
			$feedback_table  = $common->prefix_table("performance_feedback");
			$user_table = $common->prefix_table("user_table");
			$position_table = $common->prefix_table("position");
			
			while(list($key,$value)=each($post_var))
			{
				$$key = $value;
				if(ereg("^fCheck_",$key))
				{
					list($n,$new,$sid,$qid,$slqid)=split("_",$key);
					$check_array[$sid][$qid] = $value;
					$key_array[] = $key;
				}
			}
			$delqry = "delete from $sel_qualification where user_id='$user_id'";
			$db_object->insert($delqry);
			//echo $delqry;
			for($i=0;$i<count($key_array);$i++)
			{
				$key = $key_array[$i];

				if(ereg("^fCheck__",$key))
				{
					list($n,$new,$sid,$qid) = split("_",$key);
					$check = "fCheck__".$sid."_".$qid;
					$checkval = $$check;
					$qry = "insert into $sel_qualification set sl_id='$sid',
						q_id='$qid',user_id='$user_id'";
					$db_object->insert($qry);
					//echo "insert = $qry<br>";					
				}
				
			}
			
			
																
		}//end submit

		function save_to_unapproved($db_object,$common,$default,$user_id,$post_var)
		{
			while(list($key,$value)=each($post_var))
				{
						$$key=$value;
				}
			$changed_user = $user_id;
			if($uid!="")
			{
				$user_id = $uid;
			}				
		//temp
			$objective_table = $common->prefix_table("tempuser_objective");
			$selected_table	= $common->prefix_table("temp_selected_objective");
			$affected_table = $common->prefix_table("temp_affected");
			$help_table = $common->prefix_table("temp_help");
			$sel_qualification = $common->prefix_table("temp_selected_qualification");
			$rejected_category = $common->prefix_table("rejected_category");
			$rejected_table = $common->prefix_table("rejected_objective");

		//unapproved
			$uobjective_table = $common->prefix_table("unapproveduser_objective");
			$uselected_table= $common->prefix_table("unapproved_selected_objective");
			$uaffected_table = $common->prefix_table("unapproved_affected");
			$uhelp_table = $common->prefix_table("unapproved_help");
			$usel_qualification = $common->prefix_table("unapproved_selected_qualification");
			$urejected_category = $common->prefix_table("unapproved_rejected_category");
			
		//delete all
			$obj = "select o_id from $uobjective_table where user_id='$user_id'";
			$objres = $db_object->get_single_column($obj);
			$sp = @implode("','",$objres);
			$del_obj = "delete from $uobjective_table where user_id='$user_id'";
			$db_object->insert($del_obj);


			
			$sel = "select sl_id from $uselected_table where user_id='$user_id'";
			$selres = $db_object->get_single_column($sel);
			$sp1 = @implode("','",$selres);
			$del_sel = "delete from $uselected_table where o_id in ('$sp')";
			$db_object->insert($del_sel);

			$del_aff = "delete from $uaffected_table where sl_id in ('$sp1')";
			$db_object->insert($del_aff);
			
			$del_help = "delete from $uhelp_table where sl_id in ('$sp1')";
			$db_object->insert($del_help);

			$del_qual = "delete from $usel_qualification where sl_id in ('$sp1')";
			$db_object->insert($del_qual);
			
			$qry = "select * from $objective_table where user_id='$user_id' order by o_id";
			$tobj = $db_object->get_rsltset($qry);
			
			for($i=0;$i<count($tobj);$i++)
			{
				$met_id = $tobj[$i]['met_id'];
				$old_oid = $tobj[$i]['o_id'];
				$uobj = "insert into $uobjective_table set met_id='$met_id',user_id='$user_id'";
				$o_id = $db_object->insert_data_id($uobj);

				$qry = "select * from $selected_table where o_id='$old_oid'";
				$sel = $db_object->get_rsltset($qry);

				for($j=0;$j<count($sel);$j++)
				{
					$o = "objective_".$default;
					$h = "how_to_get_".$default;
					$objective = $sel[$j][$o];
					$priority = $sel[$j]['priority'];
					$committed_no = $sel[$j]['committed_no'];
					$percent = $sel[$j]['percent'];
					$how = $sel[$j][$h];
					$old_slid=$sel[$j]['sl_id'];
					
					$usel = "insert into $uselected_table set user_id='$user_id',
						o_id='$o_id',objective_$default='$objective',
						priority='$priority',committed_no='$committed_no',percent='$percent',
						how_to_get_$default='$how',submit_date=now(),changed_user_id='$changed_user'";
					$sl_id=$db_object->insert_data_id($usel);

					$afqry="insert into $uaffected_table (sl_id,user_id,from_date,to_date,frequency) select '$sl_id',
						user_id,from_date,to_date,frequency 
						from $affected_table where sl_id='$old_slid'";
					
					$db_object->insert($afqry);
		
					$hlpqry = "insert into $uhelp_table (sl_id,user_id) select '$sl_id',
						user_id from $help_table where sl_id='$old_slid'";
					$db_object->insert($hlpqry);
			
					$selobjqry = "insert into $usel_qualification (sl_id,user_id,q_id) 
						select '$sl_id',user_id,q_id from $sel_qualification where sl_id='$old_slid'";
					$db_object->insert($selobjqry);
				}//j loop
			}//i loop

			$rejqry = "delete from $rejected_table where user_id='$user_id'";
			$db_object->insert($rejqry);

			$delcat = "delete from $urejected_category where user_id='$user_id'";
			$db_object->insert($delcat);
			
			$inscat = "insert into $urejected_category (cat_id,user_id,category_$default) select cat_id,'$user_id',category_$default
				 from $rejected_category where user_id='$user_id'";
			$db_object->insert($inscat);

		}//end save_temp
		function mail($db_object,$common,$user_id,$default)
		{
			$config = $common->prefix_table("config");
			$user = $common->prefix_table("user_table");
			$position_table = $common->prefix_table("position");
			$message = $common->prefix_table("performance_message");
			$alert_table = $common->prefix_table("performance_alert");

			$uqry = "select username,password,first_name,last_name,position,email from $user where user_id='$user_id'";
			$ures = $db_object->get_a_line($uqry);
			
			$uname = $ures['username'];
			$fname = $ures['first_name'];
			$lname = $ures['last_name'];
			$position = $ures['position'];
			$uemail = $ures['email'];
			if($position!=0)
			{
				$pqry = "select boss_no from $position_table where pos_id='$position'";				
				$pres = $db_object->get_a_line($pqry);
				$boss = $pres['boss_no'];

				$pqry1 = "select boss_no from $position_table where pos_id='$boss'";
				$pres1 = $db_object->get_a_line($pqry1);
				$b_boss = $pres1['boss_no'];

				$mqry = "select email,username,password from $user where position='$boss'";				
				//echo "boss = $mqry<br>";
				$mres = $db_object->get_a_line($mqry);
				$i_mail = $mres['email'];
			
				$userqry = "select username from $user where user_id='$user_id'";
				$userres = $db_object->get_a_line($userqry);
				
				$mqry1 = "select email,username,password from $user where position='$b_boss'";
				//echo "bboss = $mqry1<br>";
				$mres1 = $db_object->get_a_line($mqry1);
				$b_mail = $mres1['email'];
				//echo "bmail = $b_mail<br>";
				$qry ="select i_boss,b_boss from $config";
				$res = $db_object->get_a_line($qry);
	
				$iboss = $res['i_boss'];
				$bboss = $res['b_boss'];
					
				$qry = "select appsub_subject_$default as appsubject ,appsub_message_$default as appmessage,
				approved_subject_$default as apsubject,approved_message_$default as apmessage,
				resubmit_subject_$default as resubject,resubmit_message_$default as remessage,
				obj_subject_$default as objsub,obj_message_$default as objmes
				from $message";
				$res = $db_object->get_a_line($qry);
				$subject = $res['objsub'];
				$message = $res['objmes'];
				$path = $common->http_path;
				$path = $path."/performance/approve_objective_list.php";
				$username = $userres['username'];
								
				//delete user
					$delqry = "delete from $alert_table where user_id = '$user_id'";
					$db_object->insert($delqry);
				if($iboss=='Y')
				{
					$bossname = $mres['username'];
					$password =$mres['password'];
					$message = preg_replace("/{{(.*?)}}/e","$$1",$message);
					//echo "iboss = $i_mail message=$message<BR>";
					if($i_mail!="")
					{
						$common->send_mail($i_mail,$subject,$message,$uemail);
					}
				
					$altqry = "insert into $alert_table set user_id='$user_id',boss_id='$boss',submit_date=now()";
					$db_object->insert($altqry);
				}
				if(($bboss=='Y')&&($b_boss!=0))
				{
					$bossname = $mres1['username'];
					$password =$mres1['password'];
					$message = preg_replace("/{{(.*?)}}/e","$$1",$message);
					//echo "bboss = $b_mail message=$message<br>";
					if($b_mail!="")
					{
						$common->send_mail($b_mail,$subject,$message,$uemail);
					}
					$altqry = "insert into $alert_table set user_id='$user_id',boss_id='$b_boss,submit_date=now()'";
					$db_object->insert($altqry);
				}										
			}//if position 0
		}//end mail
		
		function save_to_approved($db_object,$common,$default,$user_id,$post_var)
		{
				while(list($key,$value)=each($post_var))
				{
						$$key=$value;
				}
			$changed_user = $user_id;
			if($uid!="")
			{
				$user_id = $uid;
			}

		//temp
						
			$objective_table = $common->prefix_table("tempuser_objective");
			$selected_table	= $common->prefix_table("temp_selected_objective");
			$affected_table = $common->prefix_table("temp_affected");
			$help_table = $common->prefix_table("temp_help");
			$sel_qualification = $common->prefix_table("temp_selected_qualification");
			$rejected_category = $common->prefix_table("rejected_category");
			$rejected_table = $common->prefix_table("rejected_objective");
		//unapproved
			$uobjective_table = $common->prefix_table("approveduser_objective");
			$uselected_table= $common->prefix_table("approved_selected_objective");
			$uaffected_table = $common->prefix_table("approved_affected");
			$uhelp_table = $common->prefix_table("approved_help");
			$usel_qualification = $common->prefix_table("approved_selected_qualification");
			$urejected_category = $common->prefix_table("approved_rejected_category");
			$comment_table=$common->prefix_table("performance_comments");
			$performance_feedback=$common->prefix_table("performance_feedback");
			
		//delete all 
			
			$obj = "select o_id from $uobjective_table where user_id='$user_id'";
			$objres = $db_object->get_single_column($obj);
			$sp = @implode("','",$objres);
		
			$del_obj = "update $uobjective_table set status='I' where user_id='$user_id'";
			$db_object->insert($del_obj);
			
			$sel = "select sl_id from $uselected_table where user_id='$user_id'";
			$selres = $db_object->get_single_column($sel);
			$sp1 = @implode("','",$selres);

			$del_sel = "update $uselected_table set status='I' where user_id='$user_id'";
			$db_object->insert($del_sel);
		

			$qry = "select * from $objective_table where user_id='$user_id' order by o_id";
			$tobj = $db_object->get_rsltset($qry);

			$flg=0;
			for($i=0;$i<count($tobj);$i++)
			{
				$met_id = $tobj[$i]['met_id'];
				$old_oid = $tobj[$i]['o_id'];
				$uobj = "insert into $uobjective_table set met_id='$met_id',user_id='$user_id',status='A'";
				$o_id = $db_object->insert_data_id($uobj);

				$qry = "select * from $selected_table where o_id='$old_oid'";
				$sel = $db_object->get_rsltset($qry);

				for($j=0;$j<count($sel);$j++)
				{
					$flg=0;
					$o = "objective_".$default;
					$h = "how_to_get_".$default;
					$objective = $sel[$j][$o];
					$priority = $sel[$j]['priority'];
					$committed_no = $sel[$j]['committed_no'];
					$percent = $sel[$j]['percent'];
					$how = $sel[$j][$h];
					$old_slid=$sel[$j]['sl_id'];
					
					$usel = "insert into $uselected_table set user_id='$user_id',
						o_id='$o_id',objective_$default='$objective',
						priority='$priority',committed_no='$committed_no',percent='$percent',
						how_to_get_$default='$how',changed_user_id='$changed_user',approved_date=now(),
						status='A'";
					$sl_id=$db_object->insert_data_id($usel);

					$afqry="insert into $uaffected_table (sl_id,user_id,from_date,to_date,frequency) select '$sl_id',
						user_id,from_date,to_date,frequency 
						from $affected_table where sl_id='$old_slid'";
					$db_object->insert($afqry);
					
					$hlpqry = "insert into $uhelp_table (sl_id,user_id) select '$sl_id',
						user_id from $help_table where sl_id='$old_slid'";
					$db_object->insert($hlpqry);
			
					$selobjqry = "insert into $usel_qualification (sl_id,user_id,q_id) 
						select '$sl_id',user_id,q_id from $sel_qualification where sl_id='$old_slid'";
					$db_object->insert($selobjqry);
				$flg=1;
				}//j loop
			}//i loop

			

			$delcat = "delete from $urejected_category where user_id='$user_id'";
			$db_object->insert($delcat);
			
			$inscat = "insert into $urejected_category (cat_id,user_id,category_$default) select cat_id,'$user_id',category_$default
				 from $rejected_category where user_id='$user_id'";
			$db_object->insert($inscat);

		}//end save_approved

	}//end class
	$ob = new plan3;
	if($back!="")
	{
		header("location: userplan2_input.php?emp_id=$uid");
	}
	
	if($approval!="")
	{		
		$ob->submit($db_object,$common,$default,$uid,$post_var);
		$ob->save_to_unapproved($db_object,$common,$default,$user_id,$post_var);
		$ob->mail($db_object,$common,$user_id,$default);		
		header("location: per_setting.php");
	}
	if($approved!="")
	{
		$ob->submit($db_object,$common,$default,$uid,$post_var);
		$ob->save_to_approved($db_object,$common,$default,$user_id,$post_var);
		header("location: per_setting.php");
	}

	$ob->view($db_object,$common,$default,$uid,$error_msg);
include_once("footer.php");
?>
