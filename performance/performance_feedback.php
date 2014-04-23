<?
include_once("../session.php");
if(($save=="")&&($submit==""))
{
	include_once("header.php");
}
class feedback
{
	function view_form($db_object,$common,$default,$user_id,$uid,$techno,$interno,$err)
	{
		$path = $common->path;
		$filename = $path."templates/performance/performance_feedback.html";
		$file = $common->return_file_content($db_object,$filename);

		$file = $common->is_module_purchased($db_object,$path,$file,$common->lfvar);
	
	//table declaration
		$usertable = $common->prefix_table("user_table");
		$fieldtable = $common->prefix_table("name_fields");
		$owner_table = $common->prefix_table("owner_defined_text");
		$app_userobj = $common->prefix_table("approveduser_objective");
		$app_selected= $common->prefix_table("approved_selected_objective");
		$app_affected = $common->prefix_table("approved_affected");
		$app_help = $common->prefix_table("affected_help");
		$app_category = $common->prefix_table("approved_category");
		$app_metrics = $common->prefix_table("approved_metrics");
		$rating =$common->prefix_table("rating");
		$skill = $common->prefix_table("skills");
		$t_feedback = $common->prefix_table("temp_feedback");
		$performance_feedback = $common->prefix_table("performance_feedback");		
		$name = $common->name_display($db_object,$uid);		
		$val['empname'] = $name;
	
	//performance feedback
		$performqry = "select sl_id from $performance_feedback where user_id='$user_id'
				 and request_from='$uid' order by f_id";		
		$performres = $db_object->get_single_column($performqry);
		$ct  = count($performres);
		
	//rating table
		$rqry = "select r_id,rating_$default as rating,rval from $rating order by r_id";
		$rres = $db_object->get_rsltset($rqry);

	//skill table
		$tqry = "select skill_id,skill_name from $skill where skill_type='t'";
		$tech = $db_object->get_rsltset($tqry);
		$iqry = "select skill_id,skill_name from $skill where skill_type='i'";
		$inter = $db_object->get_rsltset($iqry);

	//temp_feedback
		$sel = "select r_id,idelivered,effective,tech1,tech2,inter1,inter2 from 
			$t_feedback where user_id='$uid'  order by fd_id";
		$feed = $db_object->get_rsltset($sel);

	//owner table
		$oqry = "select text_$default as text from $owner_table where context='feedback'";
		$ores = $db_object->get_a_line($oqry);
		$val['feedback'] = $ores['text'];		
		$uqry = "select o_id,sl_id,objective_$default as objective,committed_no,
			how_to_get_$default as how from $app_selected where user_id='$uid' order by sl_id";
		$ures = $db_object->get_rsltset($uqry);
		$pattern="/<{record_loopstart}>(.*?)<{record_loopend}>/s";
		preg_match($pattern,$file,$arr);
				
		$match=$arr[0];
		$str="";

		$pattern1="/<{rating_loopstart(.*?)<{rating_loopend}>/s";
		preg_match($pattern1,$file,$arr1);
		$match1=$arr1[0];
		
		$pattern2="/<{tech_loopstart}>(.*?)<{tech_loopend}>/s";
		preg_match($pattern2,$file,$arr2);
		$match2=$arr2[0];
		
		$pattern3="/<{inter_loopstart}>(.*?)<{inter_loopend}>/s";
		preg_match($pattern3,$file,$arr3);
		$match3=$arr3[0];
		
		$pattern5="/<{techtime_loopstart}>(.*?)<{techtime_loopend}>/s";
		preg_match($pattern5,$file,$arr5);
		$match5=$arr5[0];

		$pattern6="/<{intertime_loopstart}>(.*?)<{intertime_loopend}>/s";
		preg_match($pattern6,$file,$arr6);
		$match6=$arr6[0];
		
		$pattern4="/<!--start-->(.*?)<!--end-->/s";
		$space="";
	for($p=0;$p<count($performres);$p++)
		{//p loop

		$uqry = "select o_id,sl_id,objective_$default as objective,committed_no,
			how_to_get_$default as how from $app_selected where user_id='$uid' and
			 sl_id='$performres[$p]' and status='A' order by sl_id";
		$ures = $db_object->get_rsltset($uqry);

		for($i=0;$i<count($ures);$i++)//count($ures)
		{		

			$str1="";
			//$str2="";
			$str3="";
			$str4="";
			$str5="";
			$objective = $ures[$i]['objective'];
			$committed = $ures[$i]['committed_no'];
			$how = $ures[$i]['how'];
			$count  = $p+1;
			$fEffective = $feed[$i]['effective'];
			$fDelivered = $feed[$i]['idelivered'];
			$oid = $ures[$i]['o_id'];
		//rating loop
			for($j=0;$j<count($rres);$j++)
			{
				$checked = "";
				$rval = $rres[$j]['rval'];
				$frid = $feed[$i]['r_id'];
				if($rval==$frid)
				{
					$checked = "checked";
				}
				$crating = $rres[$j]['rating'];
				$str1.= preg_replace("/\<\{(.*?)\}\>/e","$$1",$match1);
			}//j loop
			$temp=preg_replace($pattern1,$str1,$match);
			
		
		//tech loop
		for($t=0;$t<$techno;$t++)
		{
			$str2="";
			
			for($k=0;$k<count($tech);$k++)
			{
				$selected1 = "";
				$selected2 = "";
				$t1 = $t+1;
				$tec = "tech".$t1;
				$t1skillid = $feed[$i][$tec];
				$skillid = $tech[$k]['skill_id'];
				if($skillid==$t1skillid)
				{	

					//echo "t1 $skillid : $t1skillid<br>";				
					$selected1="selected";					
				}
				
				//echo "sel1 = $selected1 : sel2 = $selected2<br>";
				$skillname = $tech[$k]['skill_name'];
				$index=$t+1;	
				$str2.= preg_replace("/\<\{(.*?)\}\>/e","$$1",$match2);				
			}//k loop
			
			$temp1 = preg_replace($pattern2,$str2,$temp);			
			$chg_match5=preg_replace($pattern2,$str2,$match5);			
			$str4 .= preg_replace("/\<\{(.*?)\}\>/e","$$1",$chg_match5);
						
		}//t loop						
			$temp3=preg_replace($pattern5,$str4,$temp1);					
				
		//inter loop

		for($in=0;$in<$interno;$in++)
		{
			$str3="";
			$inde = $in+1;
			for($l=0;$l<count($inter);$l++)
			{
				
				$interselect= "";
				$n = $in+1;
				$int = "inter".$n;
				$i1skillid = $feed[$i][$int];
				$iskillid = $inter[$l]['skill_id'];
				if($iskillid==$i1skillid)
				{
					$interselect = "selected";
				}
				$iskillname = $inter[$l]['skill_name'];
				$str3.= preg_replace("/\<\{(.*?)\}\>/e","$$1",$match3);
			}//l loop

			$temp2=preg_replace($pattern3,$str3,$temp3);
			$chg_match6 = preg_replace($pattern3,$str3,$match6);
			$str5 .= preg_replace("/\<\{(.*?)\}\>/e","$$1",$chg_match6);			
		}//in loop
			$temp4 = preg_replace($pattern6,$str5,$temp2);

				$str.=preg_replace("/\<\{(.*?)\}\>/e","$$1",$temp4);			
		}//i loop
	}//p loop

		$file=preg_replace($pattern,$str,$file);
		if($str!="")
		{
			$root_path = $common->path;		
			if($user_id!=$uid)
			{
				$pattern="/<!--deliver_start-->(.*?)<!--deliver_end-->/s";
				$file = preg_replace($pattern,"",$file);
			}
			else
			{
				$file = preg_replace("/<!--not_self_start-->(.*?)<!--not_self_end-->/s","",$file);
			}
			$val['uid'] = $uid;
		}
		else
		{
			$pat = "/<!--ifnorecord_start-->(.*?)<!--ifnorecord_end-->/s";
			$file = preg_replace($pat,"<br><center>$err[cEmptyrecords]",$file);
		}
			$file = $common->direct_replace($db_object,$file,$val);		
			echo $file;
	}//view end

	function save_feedback($db_object,$common,$default,$user_id,$post_var)
	{
		$key_array = array();
	//tables
		$t_feedback = $common->prefix_table("temp_feedback");
		while(list($key,$value)=each($post_var))
		{
			$$key=$value;
			//echo "key = $key : value=$value<br>";
			if(ereg("^fEffective_",$key))
			{
				$key_array[] = $key;
			}
				
		}

		$fl=0;

		for($i=0;$i<count($key_array);$i++)
		{
			$rate="";
			$delivered="";
			$key = $key_array[$i];
			list($name,$o_id)=split("_",$key);
			$tech1 = "fTech1_".$o_id;
			$ftech1 = $$tech1;
			$tech2 = "fTech2_".$o_id;
			$ftech2 = $$tech2;
			$inter1 = "fInter1_".$o_id;
			$finter1 = $$inter1;
			$inter2 = "fInter2_".$o_id;
			$finter2 = $$inter2;
			$effect = "fEffective_".$o_id;
			$feffect = $$effect;
			$rating = "fRating_".$o_id;
			$frating = $$rating;
			$deliver = "fDelivered_".$o_id;
			$fdeliver = $$deliver;
			if($frating!="")
			{
				$rate = "r_id = '$frating',";
			}
			if($fdeliver!="")
			{
				$delivered ="idelivered='$fdeliver',";
			}
			
			if($fl==0)
			{
				$update = "update $t_feedback set active='I' where user_id='$uid' and boss_id='$user_id'";
				$db_object->insert($update);
			
			}
			$qry = "insert into $t_feedback set user_id='$uid',boss_id='$user_id',
				o_id='$o_id', $rate $delivered effective='$feffect',tech1='$ftech1',
				tech2='$ftech2',inter1='$finter1',inter2='$finter2',active='A'";
			//echo "insert = $qry<br>";
			$db_object->insert($qry);
			$fl = 1;
			
		}//i loop
	}//end save

	function submit_feedback($db_object,$common,$default,$user_id,$post_var)
	{
		$key_array = array();
	//tables
		$a_feedback = $common->prefix_table("approved_feedback");
		$user_table = $common->prefix_table("user_table");
		$position_table = $common->prefix_table("position");
		$per_feedback = $common->prefix_table("performance_feedback");
		
		while(list($key,$value)=each($post_var))
		{
			$$key=$value;
			//echo "key = $key : value=$value<br>";
			if(ereg("^fEffective_",$key))
			{
				$key_array[] = $key;
			}				
		}
	
		$fl=0;
		for($i=0;$i<count($key_array);$i++)
		{
			$rate="";
			$delivered="";
			$active="";
			$key = $key_array[$i];
			list($name,$o_id)=split("_",$key);
			$tech1 = "fTech1_".$o_id;
			$ftech1 = $$tech1;
			$tech2 = "fTech2_".$o_id;
			$ftech2 = $$tech2;
			$inter1 = "fInter1_".$o_id;
			$finter1 = $$inter1;
			$inter2 = "fInter2_".$o_id;
			$finter2 = $$inter2;
			$effect = "fEffective_".$o_id;
			$feffect = $$effect;
			$rating = "fRating_".$o_id;
			$frating = $$rating;
			$deliver = "fDelivered_".$o_id;
			$fdeliver = $$deliver;
			if($frating!="")
			{
				$rate = "r_id = '$frating',";
			}
			if($fdeliver!="")
			{
				$delivered ="idelivered='$fdeliver',";
			}
			//status  = 0 -if it is not seltrating
			
			if($user_id==$uid)//if he rates himself
			{
				if($fl==0)
				{
					$update = "update $a_feedback set active='I' where user_id='$user_id' and status='1'";					
					$db_object->insert($update);
				}
				$status = ",status='1'";
			
			}
			else
			{
				if($fl==0)
				{
					$update = "update $a_feedback set active='I' where user_id='$uid' and boss_id='$user_id' and status='0'";
					$db_object->insert($update);
				}	
			}
				$userqry = "select position from $user_table where user_id='$user_id'";
				$userres = $db_object->get_a_line($userqry);
				$pos = $userres['position'];
				$posqry = "select boss_no from $position_table where pos_id='$pos'";
				$posres = $db_object->get_a_line($posqry);
				$bosspos = $posres['boss_no'];
				$userqry1 = "select user_id from $user_table where position='$bosspos'";
				$userres1 = $db_object->get_a_line($userqry1);
				$buser_id = $userres1['user_id'];
																							
			$qry = "insert into $a_feedback set user_id='$uid',boss_id='$buser_id',raters_id='$user_id',
				o_id='$o_id', $rate $delivered effective='$feffect',tech1='$ftech1',
				tech2='$ftech2',inter1='$finter1',inter2='$finter2',approved_date=now(),active='A' $status ";
			$db_object->insert($qry);
			if($fl==0)
			{
				$updatelatest = "update $per_feedback set latest='O' where user_id='$user_id' and 
						request_from='$uid' and status='A'";
				$db_object->insert($updatelatest);
				$update = "update $per_feedback set status='A',latest='N' where user_id='$user_id' and 
						request_from='$uid' and status='I'";
				$db_object->insert($update);
			}

			$fl = 1;
		}//i loop
	}//end submit
	function mail_to_boss($db_object,$common,$default,$user_id,$post_var)
	{
		while(list($key,$value)=each($post_var))
		{
			$$key = $value;
		}
	//table declaration	
		$user_table = $common->prefix_table("user_table");	
		$performance_message = $common->prefix_table("performance_message");
	//usertable
		$imm_boss = $common->immediate_boss($db_object,$user_id);		

		$bossqry = "select email from $user_table where user_id='$imm_boss'";
		$bossres = $db_object->get_a_line($bossqry);
		
		$userqry = "select username,email from $user_table where user_id='$uid'";
		$userres = $db_object->get_a_line($userqry);
	//message table
		$messqry = "select verification_submit_sub_$default as subject,verification_submit_message_$default as message
				from $performance_message";
		$messres = $db_object->get_a_line($messqry);
		$to = $bossres['email'];
		$from = $userres['email'];
		$username = $userres['username'];
		$subject = $messres['subject'];
		$message = $messres['message'];
		$path = $common->http_path;
		$path = $path."/performance/verify_progress.php";
		$message = preg_replace("/{{(.*?)}}/e","$$1",$message);
	//send mail to boss
		$common->send_mail($to,$subject,$message,$from);

	}//end mail
}//class end
	$ob = new feedback;
	if($save!="")
	{
		$ob->save_feedback($db_object,$common,$default,$user_id,$post_var);
		header("location:per_setting.php");
	}
	if($submit!="")
	{
		$ob->submit_feedback($db_object,$common,$default,$user_id,$post_var);
		if($user_id==$uid)
		{
			$ob->mail_to_boss($db_object,$common,$default,$user_id,$post_var);	
		}
		header("location:per_setting.php");
	}
	$ob->view_form($db_object,$common,$default,$user_id,$uid,$gbl_tech_skill,$gbl_inter_skill,$error_msg);
	
include_once("footer.php");
?>

