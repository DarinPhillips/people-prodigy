<?
include_once("../session.php");
if(($back=="")&&($reject=="")&&($approval==""))
{
include_once("header.php");
}
class plan3
	{
	function view($db_object,$common,$default,$user_id,$err)
		{
			$path = $common->path;
			$filename = $path."templates/performance/approve_objective3.html";
			$file = $common->return_file_content($db_object,$filename);
		//table declaration
			$user_table = $common->prefix_table("user_table");
			$selected_table = $common->prefix_table("unapproved_selected_objective");
			$affected_table = $common->prefix_table("unapproved_affected");
			$qualify = $common->prefix_table("qualification");
			$help_table = $common->prefix_table("unapproved_help");
			$selected_qualification = $common->prefix_table("unapproved_selected_qualification");
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
			
			$file = $common->direct_replace($db_object,$file,$val);
			echo $file;
		}//end view
	function submit($db_object,$common,$default,$user_id,$post_var)
		{
			$check_array= array();
			$key_array = array();
			
			$sel_qualification = $common->prefix_table("unapproved_selected_qualification");
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
			//$db_object->insert($delqry);			
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
		function save_to_approved($db_object,$common,$default,$user_id)
		{

		//unapproved
			$objective_table = $common->prefix_table("unapproveduser_objective");
			$selected_table	= $common->prefix_table("unapproved_selected_objective");
			$affected_table = $common->prefix_table("unapproved_affected");
			$help_table = $common->prefix_table("unapproved_help");
			$sel_qualification = $common->prefix_table("unapproved_selected_qualification");
			$rejected_category = $common->prefix_table("unapproved_rejected_category");
			$rejected_table = $common->prefix_table("rejected_objective");
			$user_table = $common->prefix_table("user_table");
			$position_table = $common->prefix_table("position");

		//approved
			$uobjective_table = $common->prefix_table("approveduser_objective");
			$uselected_table= $common->prefix_table("approved_selected_objective");
			$uaffected_table = $common->prefix_table("approved_affected");
			$uhelp_table = $common->prefix_table("approved_help");
			$usel_qualification = $common->prefix_table("approved_selected_qualification");
			$urejected_category = $common->prefix_table("approved_rejected_category");
			$comment_table=$common->prefix_table("performance_comments");
			$performance_feedback=$common->prefix_table("performance_feedback");
			$performance_message = $common->prefix_table("performance_message");
			
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
					$o = "objective_".$default;
					$h = "how_to_get_".$default;
					$objective = $sel[$j][$o];
					$priority = $sel[$j]['priority'];
					$committed_no = $sel[$j]['committed_no'];
					$percent = $sel[$j]['percent'];
					$how = $sel[$j][$h];
					$old_slid=$sel[$j]['sl_id'];
					$changed_userid = $sel[$j]['changed_user_id'];
					
					$usel = "insert into $uselected_table set user_id='$user_id',
						o_id='$o_id',objective_$default='$objective',
						priority='$priority',committed_no='$committed_no',percent='$percent',
						how_to_get_$default='$how',changed_user_id='$changed_userid',approved_date=now(),status='A'";
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

			

			$delcat = "delete from $urejected_category where user_id='$user_id'";
			$db_object->insert($delcat);
			
			$inscat = "insert into $urejected_category (cat_id,user_id,category_$default) select cat_id,'$user_id',category_$default
				 from $rejected_category where user_id='$user_id'";
			$db_object->insert($inscat);
			
//--------------setting raters
			$delfeedback = "delete from $performance_feedback where request_from='$user_id' and 
					status='I'";
			$db_object->insert($delfeedback);

			$selslid = "select sl_id from $uselected_table where user_id='$user_id' and status='A' order by sl_id";
			$slidres = $db_object->get_single_column($selslid);

			$bqry = "select position from $user_table where user_id='$user_id'";
			$bres = $db_object->get_a_line($bqry);
			$position = $bres['position'];
			
			$posqry = "select boss_no from $position_table where pos_id='$position'";
			$posres = $db_object->get_a_line($posqry);
			$bossid = $posres['boss_no'];
			
			$uqry ="select user_id from $user_table where position='$bossid'";
			
			$ures = $db_object->get_a_line($uqry);
			$usid = $ures['user_id'];
			
			$admin_id = array($usid);
			
			$posres['boss_no']=$user_id;
			$both_id=array();
			if($admin_id!=$posres['boss_no'])
			{
				$both_id = array_merge($admin_id,$posres['boss_no']);
			}
			else
			{
				$both_id=$user_id;
			}
				
			$statusqry = "select user_id from $performance_feedback 
				where user_id in ('$admin_id','$user_id') and request_from='$user_id' 
				and status='I'";
			$statusres = $db_object->get_single_column($statusqry);
			
			for($s=0;$s<count($slidres);$s++)
			{
				$seluserid = "select user_id from $uaffected_table where sl_id='$slidres[$s]'";
				$seluseridres = $db_object->get_single_column($seluserid);
										
					for($k=0;$k<count($both_id);$k++)
					{							
						if(!@in_array($both_id[$k],$statusres))
						{
							$fedqry = "insert into $performance_feedback set sl_id='$slidres[$s]',user_id='$both_id[$k]',
							request_from='$user_id',s_date=now(),status='I',latest='N'";							
							$db_object->insert($fedqry);
						}				
					}
				for($t=0;$t<count($seluseridres);$t++)
				{
					$rat_id = $seluseridres[$t];
					$insuserid = "insert into $performance_feedback set sl_id='$slidres[$s]',
						user_id='$seluseridres[$t]',request_from='$user_id',s_date=now(),
						status='I'";					
					$db_object->insert($insuserid);

			//mail to selected raters
					$qry = "select username,email from $user_table where user_id='$rat_id'";
					$res = $db_object->get_a_line($qry);
					$path = $common->http_path;
					$path = $path."/performance/selected_for_feedback.php";
					$username = $res['username'];
					$to = $res['email'];
					$fromqry = "select email from $user_table where user_id='$user_id'";
					$fromres = $db_object->get_a_line($fromqry);
					$from = $fromres['email'];
					$mqry = "select verification_submit_sub_$default as subject,verification_submit_message_$default as message
						from $performance_message";
					$mres = $db_object->get_a_line($mqry);
					$subject = $mres['subject'];
					$message = $mres['message'];
					$message = preg_replace("/{{(.*?)}}/e","$$1",$message);
					if($to!="")
					{
						$common->send_mail($to,$subject,$message,$from);
					}
				}//t loop
			}//s loop
//------------------------		
		}//end save_approved

		function rejected($db_object,$common,$default,$user_id,$uid,$post_var)
		{
			while(list($key,$value)=each($post_var))
			{
				$$key=$value;
			}
			$comment = $common->prefix_table("performance_comments");
			$alert = $common->prefix_table("performance_alert");
			$rejected = $common->prefix_table("rejected_objective");
			$user_table = $common->prefix_table("user_table");
			$message_table = $common->prefix_table("performance_message");
			$selmess = "select reject_subject_$default as subject,reject_message_$default as message from 
				$message_table";
			$selres = $db_object->get_a_line($selmess);
			$adqry = "select email from $user_table where user_id='$user_id'";
			$adres = $db_object->get_a_line($adqry);

			$detqry = "select email,username,password from $user_table where user_id='$uid'";
			$detres = $db_object->get_a_line($detqry);
		
			$qry = "update $comment set p_status='I' where user_id='$uid'";
			$db_object->insert($qry);

			$qry = "insert into $comment set user_id='$uid',comment_$default='$fComment',p_status='A'";
			$db_object->insert($qry);
			
			$qry = "insert into $rejected set user_id='$uid',boss_id='$user_id'";
			$db_object->insert($qry);
			
			$del = "delete from $alert where user_id='$uid'";
			$db_object->insert($del);
		//mail
			$to =  $detres['email'];
			$username = $detres['username'];
			$password = $detres['password'];
			$subject = $selres['subject'];
			$message = $selres['message'];
			$from = $adres['email'];
			$path = $common->http_path;
			$path = $path."/performance/performance_alert.php";
			$message = preg_replace("/{{(.*?)}}/e","$$1",$message);			
			$common->send_mail($to,$subject,$message,$from);
		
			
		}//end rejected



		function mail($db_object,$common,$user_id,$uid,$default)
		{
			$config = $common->prefix_table("config");
			$user = $common->prefix_table("user_table");
			$message = $common->prefix_table("performance_message");
			$alert_table = $common->prefix_table("performance_alert");

			$uqry = "select username,password,first_name,last_name,position,email from $user where user_id='$uid'";
			$ures = $db_object->get_a_line($uqry);
			$username = $ures['username'];			
			$password = $ures['password'];
			$uname = $ures['username'];
			$fname = $ures['first_name'];
			$lname = $ures['last_name'];
			$uemail = $ures['email'];

			$aqry = "select username,first_name,last_name,position,email from $user where user_id='$user_id'";
			$ares = $db_object->get_a_line($aqry);
			$aemail = $ares['email'];
					
			$qry = "select appsub_subject_$default ,appsub_message_$default ,
				approved_subject_$default as apsubject,approved_message_$default as apmessage,
				resubmit_subject_$default as resubject,resubmit_message_$default as remessage,
				obj_app_subject_$default as apsubject,obj_app_message_$default as apmessage
				 from $message";
			$res = $db_object->get_a_line($qry);
			$message = $res['apmessage'];
			$subject = $res['apsubject'];
			$path = $common->http_path;
			$path  = $path."/index.php";
			$message = preg_replace("/{{(.*?)}}/e","$$1",$message);	
		
			//delete user
				$delqry = "delete from $alert_table where user_id='$uid'";
				$db_object->insert($delqry);
				$common->send_mail($uemail,$subject,$message,$aemail);
		}//end mail
		
		
	}//end class
	$ob = new plan3;
	if($back!="")
	{
		header("location: approve_objective2.php?emp_id=$uid");
	}
	if($approval!="")
	{
		$ob->submit($db_object,$common,$default,$uid,$post_var);
		$ob->save_to_approved($db_object,$common,$default,$uid);
		$ob->mail($db_object,$common,$user_id,$uid,$default);
		header("location: per_setting.php");
	}
	if($reject!="")
	{
		$ob->rejected($db_object,$common,$default,$user_id,$uid,$post_var);
		header("location: per_setting.php");		
	}
	$ob->view($db_object,$common,$default,$uid,$error_msg);
include_once("footer.php");
?>
