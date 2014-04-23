<?php
include("../session.php");

include_once("career.class");

include_once("position.class");

include("header.php");

class compare_data_result
{
	function show_results($db_object,$common,$career,$position_obj,$user_id,$gbl_grouprater_inter,$fJob_family,$fJob_family1,$fPosition,$fPosition1,$fLevel,$fLevel1,$fLocation,$fLocation1,$fOrganisation,$fOrganisation1,$fReport,$fReport1,$fIndemp,$fIndemp1,$fEmpstatus,$fEmpstatus1,$fEEO,$fEEO1,$fDate1,$fDate1_c,$fDate2,$fDate2_c,$fPer1,$fPer1_c,$fPer2,$fPer2_c,$gbl_met_value,$fWork_Per1,$fWork_Per1_c,$fWork_Per2,$fWork_Per2_c,$fDate3,$fDate4,$fDate3_c,$fDate4_c,$default,$fCheck_boss1,$fCheck_boss2,$fCheck_self1,$fCheck_self2,$fCheck_others1,$fCheck_others2,$fDate5,$fDate5_c,$fDate6,$fDate6_c,$fDate7,$fDate7_c,$fNo1,$fNo2,$fNo1_c,$fNo2_c,$fNo_app1,$fNo_app2,$fNo_app1_c,$fNo_app2_c,$fTech_skills,$fLabel_T,$fTech_skills_c,$fLabel_T_c,$fPer_skills,$fPer_skills_c,$fLabel_I,$fLabel_I_c,$fOver_used1,$fOver_used1_c,$fOver_used2,$fOver_used1_c,$fCareer_killer1,$fCareer_killer1_c,$fCareer_killer2,$fCareer_killer2_c,$fOver_rated1,$fOver_rated2,$fOver_rated1_c,$fOver_rated2_c,$fUnder_rated1,$fUnder_rated2,$fUnder_rated1_c,$fUnder_rated2_c,$fPosition_model,$fPosition_model_c,$fIMatch_same1,$fIMatch_same2,$fIMatch_same1_c,$fIMatch_same2_c,$fTMatch_same1,$fTMatch_same2,$fTMatch_same1_c,$fTMatch_same1_c,$fIMatch_up1,$fIMatch_up2,$fIMatch_up1_c,$fIMatch_up2_c,$fTMatch_up1,$fTMatch_up2,$fTMatch_up1_c,$fTMatch_up2_c,$fIMatch_up_2_1,$fMatch_up_2_2,$fIMatch_up_2_1_c,$fIMatch_up_2_2_c,$fTMatch_up_2_1,$fTMatch_up_2_2,$fTMatch_up_2_1_c,$fTMatch_up_2_2_c,$fSuccession_plan_1,$fSuccession_plan_1_c,$fSuccession_plan_2,$fSuccession_plan_2_c,$fSuccession_plan_hire,$fSuccession_plan_hire_c,$fISkills,$fISkills_c,$fTSkills,$fTSkills_c,$fDate9,$fDate10,$fDate9_c,$fDate10_c,$fLearning_activity1,$fLearning_activity2,$fLearning_activity1_c,$fLearning_activity2_c,$fApplication_activity1,$fApplication_activity2,$fApplication_activity1_c,$fApplication_activity2_c,$fDate11,$fDate12,$fDate11_c,$fDate12_c,$fDate13,$fDate14,$fDate13_c,$fDate14_c,$fCheck_plan_self1,$fCheck_plan_self2,$fCheck_plan_boss1,$fCheck_plan_boss2,$fCheck_plan_others1,$fCheck_plan_others2,$fImprovement1,$fImprovement2,$fImprovement1_c,$fImprovement2_c,$fCareer_down,$fCareer_down_c,$fCareer_same,$fCareer_same_c,$fCareer_up_1,$fCareer_up_1_c,$fCareer_up_2,$fCareer_up_2_c)
	{
	
		set_time_limit(0);

		
		$user_table=$common->prefix_table("user_table");
		
		$family_position=$common->prefix_table("family_position");
		
		$position_table=$common->prefix_table("position");
		
		$location_table=$common->prefix_table("location");
		
		$user_eeo=$common->prefix_table("user_eeo");
		
		$approved_selected_objective=$common->prefix_table("approved_selected_objective");
		
		$approved_performance_appraisal=$common->prefix_table("approved_performance_appraisal");
		
		$approved_appraisal_results=$common->prefix_table("approved_appraisal_results");
		
		$plan=$common->prefix_table("plan");
		
		$plan_improvement=$common->prefix_table("plan_improvement");
		
		$models_percent_fit=$common->prefix_table("models_percent_fit");
		
		$deployment_plan=$common->prefix_table("deployment_plan");
		
		$position_designee1=$common->prefix_table("position_designee1");
		
		$position_designee2=$common->prefix_table("position_designee2");
		
		$assign_solution_builder=$common->prefix_table("assign_solution_builder");
		
		$dev_interbasic=$common->prefix_table("dev_interbasic");
		
		$approved_devbuilder=$common->prefix_table("approved_devbuilder");
			
		$result_table 		=$common->prefix_table("learning_result");
		
		$plan_table		=$common->prefix_table("approved_devbuilder");
		
		$feedback_table	=$common->prefix_table("learning_feedback_results");
				
		$dev_basic		=$common->prefix_table("dev_basic");
		
		$learning_result=$common->prefix_table("learning_result");
		
		//$learning_feedback_results=$common->prefix_table("learning_feedback_results");
		
		$path=$common->path;
		
		$xTemplate=$path."/templates/career/compare_data_results.html";
		
		$content=$common->return_file_content($db_object,$xTemplate);
						
	      $sql="select * from $user_table left join $user_eeo on
	      
		$user_eeo.user_id=$user_table.user_id left join $position_table
		
		on $position_table.pos_id=$user_table.position
		
		left join $family_position  on
		
		$user_table.position=$family_position.position_id
		
		where $user_table.position<>'' ";
		
		$sql_end="group by $user_table.user_id";
		 		 
		
		//FAMILY CLAUSE
		
		if(count($fJob_family)>0)
		{
			$family=@implode(",",$fJob_family);
			
			$family="(".$family.")";
			
			$family_clause=" and $family_position.position_id=$user_table.position
			
			and $family_position.family_id in $family";
			 	
		}
		
		else
		{
			$family_clause="";
		}//end of family if
				
		if(count($fJob_family1)>0)
		{
			
			$family1=@implode(",",$fJob_family1);
			
			$family1="(".$family1.")";
						
			$family1_clause=" and $family_position.position_id=$user_table.position
			
			and $family_position.family_id in $family1";
			
		}
		else
		{
			$family1_clause="";
		}//end of family if
		//POSITION CLAUSE
		if(count($fPosition)>0)
		{
			$position=@implode(",",$fPosition);
			
			$position="(".$position.")";
			
			$position_clause=" and $user_table.position in $position";
		}
		
		else
		{
			$position_clause="";
		}//end of position if
		
		if(count($fPosition1)>0)
		{
			$position1=@implode(",",$fPosition1);
			
			$position1="(".$position1.")";
			
			$position1_clause=" and $user_table.position in $position1";
		}
		
		else
		{
			$position1_clause="";
		}//end of position if
		
		//LEVEL CLAUSE
		if(count($fLevel)>0)
		{
					
			$level=@implode(",",$fLevel);
			
			$level="(".$level.")";
			
			$level_clause=" and $position_table.level_no in $level";
		}
		
		else
		{
			$level_clause="";
		}//end of level if
		
		if(count($fLevel1)>0)
		{
			$level1=@implode(",",$fLevel1);
			
			$level1="(".$level1.")";
			
			$level1_clause=" and $position_table.level_no in $level1";
		}
		
		else
		{
			$level1_clause="";
		}//end of level if

		if(count($fLocation)>0)
		{
			$location=@implode(",",$fLocation);
			
			$location="(".$location.")";
			
			$location_clause=" and $user_table.location in $location";
		}
		
		else
		{
			$location_clause="";
		}//end of locatiom if
		
		if(count($fLocation1)>0)
		{
			$location1=@implode(",",$fLocation1);
			
			$location1="(".$location1.")";
			
			$location1_clause=" and $user_table.location in $location1";
				
		}
		
		else
		{
			$location1_clause="";
		}//end of location if
	
		//ORGANISATION CLAUSE
		
		$pos_array=array();
		
		if(count($fOrganisation)>0)
		{
		for($i=0;$i<count($fOrganisation);$i++)
		{
			$org=$fOrganisation[$i];
			
			$qry="select position from $user_table where user_id='$org'";
			
			$qry_res=$db_object->get_a_line($qry);
								
			$pos=$qry_res[position];
			
			$sql1="select level_no from $position_table where pos_id='$pos'";
						
			$sql_result=$db_object->get_a_line($sql1);
			
			$res[$i][level_no]=$sql_result[level_no];
			
			$res[$i][position]=$qry_res[position];						
		}//end of for
		
		@sort($res);
	
		for($c=0;$c<count($res);$c++)
		{
		$position=$res[$c][position];
		
			$k=0;
			
			$user_pos[$c]=$common->get_chain_below($position,$db_object,$twodarr);
			
			$user[$c]=$common->get_user_id($db_object,$user_pos[$c]);
			
			$user_arr=array();
			
			for($j=0;$j<count($user[$c]);$j++)
			{
				
				$user_array[$k]=$user[$c][$j][user_id];
			
				$k++;
				
			}//end of inner for
			$pos_array=@array_merge($pos_array,$user_array);
						
		}//end of outer for
		$user_arr=@array_unique($pos_array);
		
		$org=@implode(",",$user_array);
		
		$org="(".$org.")";
			
		$organisation_clause=" and $user_table.user_id in $org";
		
		}
		else
		{
			$organisaton_clause="";
		}//end of outermost if
		
		if(count($fOrganisation1)>0)
		{
		for($i=0;$i<count($fOrganisation1);$i++)
		{
			$org1=$fOrganisation1[$i];
			
			$qry1="select position from $user_table where user_id='$org1'";
			
			$qry_res1=$db_object->get_a_line($qry1);
								
			$pos1=$qry_res1[position];
			
			$sql2="select level_no from $position_table where pos_id='$pos1'";
						
			$sql_result1=$db_object->get_a_line($sql2);
			
			$res1[$i][level_no]=$sql_result1[level_no];
			
			$res1[$i][position]=$qry_res1[position];						
		}//end of for
	
		@sort($res1);
		
		$pos_array1=array();
		
		for($c=0;$c<count($res1);$c++)
		{
		$position1=$res1[$c][position];
		
		
			$k=0;

			$user_pos1[$c]=$common->get_chain_below($position1,$db_object,$twodarr);
			
			$user1[$c]=$common->get_user_id($db_object,$user_pos1[$c]);

			$user_arr1=array();
			
			for($j=0;$j<count($user1[$c]);$j++)
			{
				
				$user_array1[$k]=$user1[$c][$j][user_id];
				
				$k++;
				
			}//inner fro
			
			$pos_array1=@array_merge($pos_array1,$user_array1);
		}//outer for
		
		$user_arr1=@array_unique($pos_array1);
		
			$org1=@implode(",",$user_arr1);
		
		$org1="(".$org1.")";
			
		$organisation1_clause=" and $user_table.user_id in $org1";
		}
	
		else
		{
			$organisation1_clause="";
			
		}//end of if
		
				
	//REPORT CLAUSE
				
		$reg_array=array();
			
		if(count($fReport)>0)
		{
			for($i=0;$i<count($fReport);$i++)
			{
				$rater_id=$fReport[$i];
				
				$rep_user[$i]=$common->return_direct_reports($db_object,$rater_id,$common);
				
				$reg_array=array_merge($reg_array,$rep_user[$i]);
				
				
			}//for end

		$reg_array=array_unique($reg_array);
		
		if(count($reg_array)>0)
		{
			$rep_user=@implode(",",$reg_array);
			
			$rep_user="(".$rep_user.")";
			
			$report_clause=" and $user_table.user_id in $rep_user";	
		}
		}
		else
		{
			$report_clause="";
		}//end of outer if
		
		$reg_array1=array();
		
		if(count($fReport1)>0)
		{
			for($i=0;$i<count($fReport1);$i++)
			{
				$rater_id1=$fReport1[$i];
				
				$rep_user1[$i]=$common->return_direct_reports($db_object,$rater_id1,$common);
				
				$reg_array1=array_merge($reg_array1,$rep_user1[$i]);
		
			}//for end

		$reg_array1=array_unique($reg_array1);
		
		if(count($reg_array1)>0)
		{
			$rep_user1=@implode(",",$reg_array1);
			
			$rep_user1="(".$rep_user1.")";
			
			$report1_clause=" and $user_table.user_id in $rep_user1";	
		}
		}
		else
		{
			$report1_clause="";
		}//end if 
		
		//INDIVIDUAL EMPLOYEES CLAUSE
		
		if(count($fIndemp)>0)
		{
			$indemp=@implode(",",$fIndemp);
			
			$indemp="(".$indemp.")";
			
			$indemp_clause=" and $user_table.user_id in $indemp";
		}
		else
		{
			$indemp_clause="";
		}//if end
		
		if(count($fIndemp1)>0)
		{
			$indemp1=@implode(",",$fIndemp1);
			
			$indemp1="(".$indemp1.")";
			
			$indemp1_clause=" and $user_table.user_id in $indemp1";
		}
		else
		{
			$indemp1_clause="";
		}//if end
		
		// EMPLOYEE STATUS
		
		if(count($fEmpstatus)>0)
		{
			$emp_status=@implode(",",$fEmpstatus);
			
			$emp_status="(".$emp_status.")";
			
			$status_clause=" and employment_type in $emp_status";
						
		}
		else
		{
			$status_clause="";
		}
		if(count($fEmpstatus1)>0)
		{
			$emp_status1=@implode(",",$fEmpstatus1);
			
			$emp_status1="(".$emp_status1.")";
			
			$status1_clause=" and employment_type in $emp_status1";
		}
		else
		{
			$status1_clause="";
		}
		
		//EEO
		
		if(count($fEEO)>0)
		{
			$EEO=@implode(",",$fEEO);
			
			$EEO="(".$EEO.")";
			
			$EEO_clause=" and $user_eeo.tag_id in $EEO";
				
		}
		
		else
		{
			$EEO_clause="";
		}
		if(count($fEEO1)>0)
		{
			$EEO1=@implode(",",$fEEO1);
			
			$EEO1="(".$EEO1.")";
			
			$EEO1_clause=" and $user_eeo.tag_id in $EEO1";
				
		}
		
		else
		{
			$EEO1_clause="";
		}
		
		
					
		//SQL
			
		$sql1=$sql.$family_clause.$position_clause.$level_clause.$location_clause.$organisation_clause.$report_clause.$indemp_clause.$status_clause.$EEO_clause.$sql_end;

		
				
		
		$sql2=$sql.$family1_clause.$position1_clause.$level1_clause.$location1_clause.$organisation1_clause.$report1_clause.$indemp1_clause.$status1_clause.$EEO1_clause.$sql_end;
		
		$sql_result=$db_object->get_rsltset($sql1);
		
		$sql_result1=$db_object->get_rsltset($sql2);
		
		$root_path=$common->path;
		
		$module=md5(performance);
		
		$ch_performance=$common->is_module_purchased_check($db_object,$root_path,$module);

if($ch_performance==1)
{

	//Date calculation
		
		$current_date=date("Y-m-d H:i:s",time());
		
		$current_date=@explode(" ",$current_date);
		
		$date=@explode("-",$current_date[0]);
		
		$timestamp1=mktime($date[1],$date[2],$date[0]);
		
		//PERFORMANCE
		
		$a=0;
		
		$result1=array();
		
		for($i=0;$i<count($sql_result);$i++)
		{
		
		//$user_id=$sql_result[$i][user_id];
			
			$user_id=$sql_result[$i][0];
		
		$perform_qry ="select o_id from $approved_selected_objective 

		where user_id='$user_id'";
		
		$per_res= $db_object->get_a_line($perform_qry);
		
		$o_id=$per_res[o_id];
		
		
		if($o_id!="")
		{

		$fsince = $this->date_format($fDate1);
		
		$today =  $this->date_format($fDate2);
			
		
		if($fDate1=="")
		{
		$dates = "and date_format(approved_date,'%Y-%m-%d') <='$today'";	
		}
		else
		{
			$dates="and date_format(approved_date,'%Y-%m-%d') between '$fsince' and '$today'";
		
		}//end of date if
		
		$perform_result=$common->get_fullfilled($db_object,$o_id,$user_id,$dates);
		
		$cfullfilled1=$perform_result[Cfulfill];

		$perf1=$cfullfilled1;
		
		if($cfullfilled1=="")
		{
			$perf1=0;
		}
		if($perf1>=$fPer1 and $perf1<=$fPer1_c)
		{
			$result1[$a][user_id]=$user_id;
			
			$result1[$a][username]=$sql_result[$i][username];
			
			$result1[$a][workdone]=$perf1;
			
			$result1[$a][date1]=$fDate1;
			
			$result1[$a][date2]=$fDate2;
			
			
						
		}
		else
		{
			
			$result1[$a][user_id]=$user_id;
			
			$result1[$a][username]=$sql_result[$i][username];
			
			$result1[$a][workdone]=$perf1;
			
			$result1[$a][date1]=$fDate1;
			
			$result1[$a][date2]=$fDate2;
			
				
		}//end of perf if
	
		}
	else//IF THE USER DOESNOT HAVE ANY OBJECTIVE.ASK
		{
			$result1[$a][workdone]=0;
			
			$result1[$a][user_id]=$user_id;
			
			$result1[$a][username]=$sql_result[$i][username];
			
			$result1[$a][workdone]="doesnot have any objective";
			
			$result1[$a][date1]=$fDate1;
			
			$result1[$a][date2]=$fDate2;	
		}//end of oid
		$a++;
		}//end of for
		
		$a=0;
	
		$result2=array();
		
		for($i=0;$i<count($sql_result1);$i++)
		{
		
		//$user_id1=$sql_result1[$i][user_id];
			$user_id1=$sql_result1[$i][0];
		
				
		$perform_qry1 ="select o_id from $approved_selected_objective 

		where user_id='$user_id1'";
		
		$per_res1= $db_object->get_a_line($perform_qry1);
			
		
		$o_id1=$per_res1[o_id];
	
	
		if($o_id1!="")
		{
		
		$fsince1= $this->date_format($fDate1_c);
		
		$today1 =  $this->date_format($fDate2_c);
		
	
		
		if($fDate3=="")
		{
			$dates1 = "and date_format(approved_date,'%Y-%m-%d') <='ftoday1'";
		}
		else
		{
			$dates1 = "and date_format(approved_date,'%Y-%m-%d') between '$fsince1' and 'ftoday1'";
		}
	
		$perform_result1=$common->get_fullfilled($db_object,$o_id1,$user_id1,$dates1);
		
		
		$cfullfilled=$perform_result1[Cfulfill];

		$perf2=$cfullfilled;
		
		if($perf2=="")
		{
			$perf2=0;
		}
		if($perf2>=$fPer2 and $perf2<=$fPer2_c)
		{
			$result2[$a][user_id]=$user_id1;
					
			$result2[$a][username]=$sql_result1[$i][username];
			
			$result2[$a][workdone]=$perf2;
			
			$result2[$a][date1]=$fDate1_c;
			
			$result2[$a][date2]=$fDate2_c;
			
			$a++;
		}
		else
		{
			
		$result2[$a][user_id]=$user_id1;
					
		$result2[$a][username]=$sql_result1[$i][username];
			
		$result2[$a][workdone]=$perf2;
			
		$result2[$a][date1]=$fDate1_c;
			
		$result2[$a][date2]=$fDate2_c;
	}
		}
		else//IF THE USER DOESNOT HAVE ANY OBJECTIVE.ASK
		{
			$result2[$a][user_id]=$user_id1;
					
			$result2[$a][username]=$sql_result1[$i][username];
			
			$result2[$a][workdone]="doesnot have any objective";
			
			$result2[$a][date1]=$fDate1_c;
			
			$result2[$a][date2]=$fDate2_c;
		}//end of oid
		$a++;
		}//end of for
	
			$sql_result=$result1;
				
			$sql_result1=$result2;
		
		
		$k=0;

	$result=array();

		$keys=@array_keys($sql_result);
		
		for($i=0;$i<count($sql_result);$i++)
		{
			$userid=$sql_result[$i][user_id];
			
			$qry1 ="select o_id,sl_id from $approved_selected_objective 

			where user_id='$userid'";
			
			$res_qry1=$db_object->get_a_line($qry1);
			
			$o_id=$res_qry1[o_id];
			
			$sl_id=$res_qry1[sl_id];

			if($o_id!="")
			{
				$fsince2=$this->date_format($fDate3);
				
				$ftoday1=$this->date_format($fDate4);
				
				
				if($fDate3=="")
				{
					$dates2 = "and date_format(approved_date,'%Y-%m-%d') <='$ftoday1'";
				}
				else
				{
					$dates2 = "and date_format(approved_date,'%Y-%m-%d') between '$fsince2' and '$ftoday1'";
				}
				
				//echo $gbl_met_value;

				$expected1=$common->expectation_met($db_object,$default,$userid,$gbl_met_value,$o_id,$dates2);
			

			}
			else
			{
				$expected1=0;
			}//end of oid
			$k=0;
			
			
			if($fCheck_boss1=='on')
			{
				$rater[$k]="{{cCboss}}";
				
				$k++;
			}
			if($fCheck_self1=="on")
			{
				$rater[$k]="{{cCself}}";
				
				$k++;
			}
			if($fCheck_others1=="on")
			{
				$rater[$k]="{{cAllothers}}";
				
				$k++;
			}
			if($rater[0]=="")
			{
				$result[$i][rater]="none";
			}
			else
			{
				$result[$i][rater]=@implode(" and ",$rater);
			}
				$key=$keys[$i];
			
				$result[$i][user_id]=$sql_result[$key][user_id];
				
				$result[$i][username]=$sql_result[$key][username];
				
				$result[$i][completed]=$expected1;
				
				$result[$i][date3]=$fDate3;
				
				$result[$i][date4]=$fDate4;
				
				$result[$i][date1]=$sql_result[$key][date1];
				
				$result[$i][date2]=$sql_result[$key][date2];
				
				$result[$i][workdone]=$sql_result[$key][workdone];
			
			
		}//end of for


			
		$result1=array();
		
		$keys1=@array_keys($sql_result1);
		
		for($i=0;$i<count($sql_result1);$i++)
		{
			
			$key1=$keys1[$i];
			
			
			
			$expected2="";
			
			//$userid1=$sql_result1[$i][user_id];
			
			$userid1=$sql_result1[$key1][user_id];

			$qry2 ="select o_id,sl_id from $approved_selected_objective 

			where user_id='$userid1'";
			
			$res_qry2=$db_object->get_a_line($qry2);
			
			$o_id1=$res_qry2[o_id];
			
			$sl_id1=$res_qry2[sl_id];
			
			

			if($o_id1!="")
			{
				
				$fsince2=$this->date_format($fDate3_c);
				
				$ftoday1=$this->date_format($fDate4_c);
				
				
				if($fDate3=="")
				{
					$dates2 = "and date_format(approved_date,'%Y-%m-%d') <='$ftoday1'";
				}
				else
				{
					$dates2 = "and date_format(approved_date,'%Y-%m-%d') between '$fsince2' and '$ftoday1'";
				}
				

				$expected2=$common->expectation_met($db_object,$default,$userid1,$gbl_met_value,$o_id1,$dates2);
				
				
				
			}
			else
			{
				$expected2=0;
			}//end of if
			$k=0;
			if($fCheck_boss2=="on")
			{
				$rater1[$k]="{{cCboss}}";
				
				$k++;
			}
			if($fCheck_self2=="on")
			{
				$rater1[$k]="{{cCself}}";
				
				$k++;
			}

			if($fCheck_others2=="on")
			{
				$rater1[$k]="{{cAllothers}}";
				
				$k++;
			}
			if($rater1[0]=="")
			{
				$result1[$i][rater]="none";
			}
			else
			{
				$result1[$i][rater]=@implode(" and ",$rater1);
			}
			
				$key1=$keys1[$i];
				
			
			
				$result1[$i][user_id]=$sql_result1[$key1][user_id];	
				
				$result1[$i][username]=$sql_result1[$key1][username];
				
				
				//$result1[$i][user_id]=$sql_result1[$i][user_id];	
				
				//$result1[$i][username]=$sql_result1[$i][username];
				
//				$xyz=$sql_result1[$key1][user_id];
			
				$result1[$i][completed]=$expected2;
				
				$result1[$i][date3]=$fDate3_c;
				
				$result1[$i][date4]=$fDate4_c;
				
				$result1[$i][date1]=$sql_result1[$key1][date1];
				
				$result1[$i][date2]=$sql_result1[$key1][date2];
				
				$result1[$i][workdone]=$sql_result1[$key1][workdone];
				
			
			
		}//end of for


			$sql_result=$result;
		
			$sql_result1=$result1;
			
			$today=time();
			
			$date=date("m/d/Y",time());
			
			if($fDate5!="" and $fDate6!="")
			{

				$date5=$this->date_format($fDate5);
				
				$date6=$this->date_format($fDate6);
		
		for($i=0;$i<count($sql_result);$i++)
		{
			$user_id=$sql_result[$i][user_id];
			
			$qry="select $plan.added_on,$plan.employee_id as user_id,$plan.plan_id 
			
			from $plan,$plan_improvement where $plan.plan_id=$plan_improvement.plan_id 
			
			and $plan_improvement.status='a' and added_on
			
			between '$date5' and '$date6' and plan.employee_id='$user_id' group by $plan_improvement.plan_id ";
			
			//$qry="select plan_id from $plan_improvement where employee_id='$user_id' group by plan_id";
			
			$res=$db_object->get_single_column($qry);
			
			$sql_result[$i][plans]=count($res);
			
		
		}//end of for
		$b=0;
				if(fNo1!="" and $fNo2!="")
				{
					for($i=0;$i<count($sql_result);$i++)
					{
						$no=$sql_result[$i][plans];
						
						if($no<=$fNo2 and $no>=$fNo1)
						{
						
							$result[$b]=$sql_result[$i];
							
							$b++;
						}
					}
				
				}
				
				$sql_result=$result;
			}
			else
			{
				$today=$this->date_format($date);
				
		for($i=0;$i<count($sql_result);$i++)
		{
			$user_id=$sql_result[$i][user_id];
			
			$qry="select $plan.added_on,$plan.employee_id as user_id,$plan.plan_id 
			
			from $plan,$plan_improvement where $plan.plan_id=$plan_improvement.plan_id 
			
			and $plan_improvement.status='a' and 
			
			added_on <'$today' and $plan.employee_id='$user_id' group by $plan_improvement.plan_id";
			
			//$qry="select plan_id from $plan_improvement where employee_id='$user_id' group by plan_id";
			
			$res=$db_object->get_single_column($qry);
			
			$sql_result[$i][plans]=count($res);
			
		
		}
		$b=0;
				if(fNo1!="" and $fNo2!="")
				{
					for($i=0;$i<count($sql_result);$i++)
					{
						$no=$sql_result[$i][plans];
						
						if($no<=$fNo2 and $no>=$fNo1)
						{
						
							$result[$b]=$sql_result[$i];
							
							$b++;
						}
					}
				
				}
				
				$sql_result=$result;
			}//end of if
	//===================================================================================
			if($fDate5_c!="" and $fDate6_c!="")
			{
				
				$date5=$this->date_format($fDate5_c);
				
				$date6=$this->date_format($fDate6_c);
		
		for($i=0;$i<count($sql_result1);$i++)
		{
			$user_id1=$sql_result1[$i][user_id];
			
			$qry1="select $plan.added_on,$plan.employee_id as user_id,$plan.plan_id 
			
			from $plan,$plan_improvement where $plan.plan_id=$plan_improvement.plan_id 
			
			and $plan_improvement.status='a' and added_on
			
			between '$date5' and '$date6' and plan.employee_id='$user_id1' group by $plan_improvement.plan_id ";
			
			//$qry="select plan_id from $plan_improvement where employee_id='$user_id' group by plan_id";
			
			$res1=$db_object->get_single_column($qry);
			
			$sql_result1[$i][plans]=count($res1);
			
		
		}
		$b=0;
				if(fNo1_c!="" and $fNo2_c!="")
				{
					for($i=0;$i<count($sql_result1);$i++)
					{
						$no1=$sql_result1[$i][plans];
						
						if($no1<=$fNo2_c and $no1>=$fNo1_c)
						{
							$result1[$b]=$sql_result1[$i];
							
							$b++;
						}
					}
				}
				$sql_result1=$result1;
			}
			else
			{
				$today=$this->date_format($date);
				
		for($i=0;$i<count($sql_result1);$i++)
		{
			$user_id1=$sql_result1[$i][user_id];
			
			$qry="select $plan.added_on,$plan.employee_id as user_id,$plan.plan_id 
			
			from $plan,$plan_improvement where $plan.plan_id=$plan_improvement.plan_id 
			
			and $plan_improvement.status='a' and 
			
			added_on <'$today' and $plan.employee_id='$user_id1' group by $plan_improvement.plan_id";
			
			//$qry="select plan_id from $plan_improvement where employee_id='$user_id' group by plan_id";
			
			$res1=$db_object->get_single_column($qry);
			
			$sql_result1[$i][plans]=count($res1);
			
		
		}
			$b=0;
				if(fNo1_c!="" and $fNo2_c!="")
				{
					for($i=0;$i<count($sql_result1);$i++)
					{
						$no1=$sql_result1[$i][plans];
						
						if($no1<=$fNo2_c and $no1>=$fNo1_c)
						{
							$result1[$b]=$sql_result1[$i];
							
							$b++;
						}
					}
				}
				$sql_result1=$result1;
			}//end of if
			
		if($fDate7!="" and $fDate8!="")
		{
			$result=array();
			
			$date7=$this->date_format($fDate7);
			
			$date8=$this->date_format($fDate8);
			
			for($j=0;$j<count($sql_result);$j++)
			{
				$user_id=$sql_result[$j][user_id];
						
				$qry="select appraisal_id from $approved_performance_appraisal where 
			
				user_id='$user_id' and status='h' and approved_on between '$date7' and '$date8' group by approved_on";
			
				$res=$db_object->get_single_column($qry);
							
				$sql_result[$j][appraisal]=count($res);
			}
			$b=0;
			if($fNo_app1!="" and $fNo_app2!="")
			{
				for($i=0;$i<count($sql_result);$i++)
				{
					$app=$sql_result[$i][appraisal];
					
					if($app<=$fNo_app2 and $app>=$fNo_app1)
					{
						$result[$b]=$sql_result[$i];
						
						$b++;
					}
				}
				$sql_result=$result;
			}
			
			
		}
		else
		{
			$today=date("m/d/Y",time());
			
			$date=$this->date_format($today);
			
			for($j=0;$j<count($sql_result);$j++)
			{
				$user_id=$sql_result[$j][user_id];
						
				$qry="select appraisal_id from $approved_performance_appraisal where 
			
				user_id='$user_id' and status='h' and approved_on <'$date' group by approved_on";
			
				$res=$db_object->get_single_column($qry);
							
				$sql_result[$j][appraisal]=count($res);
			}
			$b=0;
			if($fNo_app1!="" and $fNo_app2!="")
			{
				for($i=0;$i<count($sql_result);$i++)
				{
					$app=$sql_result[$i][appraisal];
					
					if($app<=$fNo_app2 and $app>=$fNo_app1)
					{
						$result[$b]=$sql_result[$i];
						
						$b++;
					}
				}
				$sql_result=$result;
			}
			
		}//end of if
		
		if($fDate7_c!="" and $fDate8_c!="")
		{
			$result1=array();
			
			$date7=$this->date_format($fDate7_c);
			
			$date8=$this->date_format($fDate8_c);
			
			for($j=0;$j<count($sql_result1);$j++)
			{
				$user_id1=$sql_result1[$j][user_id];
						
				$qry1="select appraisal_id from $approved_performance_appraisal where 
			
				user_id='$user_id1' and status='h' and approved_on between '$date7' and '$date8' group by approved_on";
			
				$res1=$db_object->get_single_column($qry1);
							
				$sql_result1[$j][appraisal]=count($res1);
			}
			$b=0;
			if($fNo_app1_c!="" and $fNo_app2_c!="")
			{
				for($i=0;$i<count($sql_result1);$i++)
				{
					$app1=$sql_result1[$i][appraisal];
					
					if($app<=$fNo_app2_c and $app>=$fNo_app1_c)
					{
						$result1[$b]=$sql_result1[$i];
						
						$b++;
					}
				}
				$sql_result1=$result1;
			}
			
			
		}
		else
		{
			$today=date("m/d/Y",time());
			
			$date=$this->date_format($today);
			
			for($j=0;$j<count($sql_result1);$j++)
			{
				$user_id1=$sql_result1[$j][user_id];
						
				$qry1="select appraisal_id from $approved_performance_appraisal where 
			
				user_id='$user_id1' and status='h' and approved_on <'$date' group by approved_on";
			
				$res1=$db_object->get_single_column($qry1);
							
				$sql_result1[$j][appraisal]=count($res1);
			}
			$b=0;
			if($fNo_app1_c!="" and $fNo_app2_c!="")
			{
				for($i=0;$i<count($sql_result1);$i++)
				{
					$app1=$sql_result1[$i][appraisal];
					
					if($app1<=$fNo_app2_c and $app1>=$fNo_app1_c)
					{
						$result1[$b]=$sql_result1[$i];
						
						$b++;
					}
				}
				$sql_result1=$result1;
			}
			
		}//end of if
}//end of performance MODULE

	//	$sql_result=$result;
		
	//	$sql_result1=$result1;

$root_path=$common->path;

$module=md5(career);

$ch_career=$common->is_module_purchased_check($db_object,$root_path,$module);

if($ch_career==1)
{
			//SUCCESSION DEPLOYMENT FACTORS
		$other_raters_tech=$common->prefix_table("other_raters_tech");
		
		for($i=0;$i<count($sql_result);$i++)
		{
			$id_arr[$i]=$sql_result[$i][user_id];
					
		}
		if(count($id_arr)>0)
		{
			$id=@implode(",",$id_arr);
			
			$id="(".$id.")";
			
			$user_clause=" and rated_user in $id";
		}
		for($i=0;$i<count($sql_result1);$i++)
		{
			$id_arr1[$i]=$sql_result1[$i][user_id];
					
		}
		
		if(count($id_arr1)>0)
		{
			
			$id1=@implode(",",$id_arr1);
			
			$id1="(".$id1.")";
			
			$user_clause1=" and rated_user in $id1";
		}
		
		if(count($fTech_skills)>0 and count($sql_result)>0)
		{
			$skills=@implode(",",$fTech_skills);
			
			$skill="(".$skills.")";
			
			if(count($fLabel_T)>0)
			{
				$label=@implode(",",$fLabel_T);
				
				$label="(".$label.")";
				
				$label_clause=" and label_id in $label";
				
				
			}
			else
			{
				$label_clause="";
			}
			$res_qry="select rated_user from $other_raters_tech where 
				
			skill_id in $skill".$label_clause.$user_clause." group by rated_user";
			
			$res=$db_object->get_rsltset($res_qry);
			
			
			
			$c=0;
			
			for($a=0;$a<count($sql_result);$a++)
			{
				$id=$sql_result[$a][user_id];

				for($b=0;$b<count($res);$b++)
				{
					if($id==$res[$b][rated_user])
					{
						
						$res[$c]=$sql_result[$a];
												
						$c++;
					}
				}
			}
			
			$sql_result=$res;
			
		}//end of if

	

		if(count($fTech_skills_c)>0 and count($sql_result1)>0)
		{

			$skills1=@implode(",",$fTech_skills_c);
			
			$skill1="(".$skills1.")";
			
			if(count($fLabel_T_c)>0)
			{
				$label1=@implode(",",$fLabel_T_c);
				
				$label1="(".$label1.")";
				
				$label1_clause=" and label_id in $label1";
				
				
			}
			else
			{
				$label1_clause="";
			}
			$res1_qry="select rated_user from $other_raters_tech where 
				
			skill_id in $skill".$label1_clause.$user_clause1." group by rated_user";
			
			$res1=$db_object->get_rsltset($res1_qry);
		
			
			$c=0;
			
			for($a=0;$a<count($sql_result1);$a++)
			{
				$id=$sql_result1[$a][user_id];

				for($b=0;$b<count($res1);$b++)
				{
					if($id==$res1[$b][rated_user])
					{
						
						$res1[$c]=$sql_result1[$a];
												
						$c++;
					}
				}
			}

			$sql_result1=$res1;
		}//end of if 
//print_r($sql_result);echo "<hr><br>";print_r($sql_result1);exit;
			for($a=0;$a<count($sql_result);$a++)
			{
				$id_array[$a]=$sql_result[$a][user_id];
								
			}
			if(count($id_array)>0)
			{
				$id=@implode(",",$id_array);
				
				$id="(".$id.")";
				
				$usr_clause=" and rated_user in $id";
			}
			else
			{
				$usr_clause="";
			}
			
			for($a=0;$a<count($sql_result1);$a++)
			{
				$id1_array[$a]=$sql_result1[$a][user_id];
				
			
								
			}
			
			
			if(count($id1_array)>0)
			{
				$id1=@implode(",",$id1_array);
				
				$id1="(".$id1.")";
				
				$usr1_clause=" and rated_user in $id1";
				
			}
			else
			{
				$usr1_clause="";
			}

			if(count($fPer_skills)>0)
			{
				
				$per_skill=@implode(",",$fPer_skills);
				
				$per="(".$per_skill.")";
				
				$per_clause=" and skill_id in $per";
				
				if(count($fLabel_I)>0)
				{
					$label_id=@implode(",",$fLabel_I);
					
					$label="(".$label_id.")";
					
					$lab_clause=" and rater_label_no in $label";
				}
				else
				{
					$lab_clause="";
				}
				
			}
			else
			{
				$per_clause="";
			}//end of if
			
			$textqsort_rating=$common->prefix_table("textqsort_rating");
			
			$qry="select * from $textqsort_rating where 1".$per_clause.$lab_clause.$usr_clause." group by rated_user";


			$res=$db_object->get_rsltset($qry);
			
			if(count($fPer_skills_c)>0)
			{
				
				$per_skill1=@implode(",",$fPer_skills_c);
				
				$per1="(".$per_skill1.")";
				
				$per_clause1=" and skill_id in $per1";
				
				if(count($fLabel_I_c)>0)
				{
					$label_id1=@implode(",",$fLabel_I_c);
					
					$label1="(".$label_id1.")";
					
					$lab_clause1=" and rater_label_no in $label1";
				}
				else
				{
					$lab_clause1="";
				}
				
			}
			else
			{
				$per_clause1="";
			}
			
			$textqsort_rating=$common->prefix_table("textqsort_rating");
						
			$qry1="select * from $textqsort_rating where 1".$per_clause1.$lab_clause1.$usr1_clause." group by rated_user";
			
			
			$res1=$db_object->get_rsltset($qry1);
			
			$c=0;
			
			for($a=0;$a<count($sql_result);$a++)
			{
				$id=$sql_result[$a][user_id];

				for($b=0;$b<count($res);$b++)
				{
					if($id==$res[$b][rated_user])
					{
				
						$res11[$c]=$sql_result[$a];
										
						$c++;
					}
				}
			}
			
				$c=0;
			for($a=0;$a<count($sql_result1);$a++)
			{
				$id1=$sql_result1[$a][user_id];

				for($b=0;$b<count($res1);$b++)
				{
					if($id1==$res1[$b][rated_user])
					{
				
						$res12[$c]=$sql_result1[$a];
										
						$c++;
					}
				}
			}
		
			$sql_result=$res11;
			
			$sql_result1=$res12;
			
		
		//print_r($sql_result);echo "<hr><br>";print_r($sql_result1);exit;
		

			for($a=0;$a<count($sql_result);$a++)
			{
				$users[$a]=$sql_result[$a][user_id];
			}
			
			for($a=0;$a<count($sql_result1);$a++)
			{
				$id_arr1[$a]=$sql_result1[$a][user_id];
			}
			$keys=@array_keys($users);$keys1=@array_keys($id_arr1);
			
			$result=array();
				
				$career_kill=$career->career_killers($db_object,$common,$users,$gbl_grouprater_inter);
				
				$d=0;
				
				//for($b=0;$b<count($career_kill);$b++)
				for($b=0;$b<count($sql_result);$b++)
				{
				
					$count=$career_kill[$b][count];
									
					$user=$career_kill[$b][user_id];
					
					if($fCareer_killer1!="" and $fCareer_killer2!="")
					{
					if($count<=$fCareer_killer2 and $count>=$fCareer_killer1)
					{
						
						$ch_key=@array_search($user,$users);
						
						$result[$d]=$sql_result[$ch_key];
						
						$result[$d][count]=$count;
										
						$d++;
					}
					}
					if($fCareer_killer1=="" and $fCareer_killer2=="")
					{
						$ch_key=@array_search($user,$users);
						
						$result[$d]=$sql_result[$ch_key];
						
						$result[$d][count]=$count;
						
						$d++;
						
					
					}
					
				}//end of for
				
				$sql_result=$result;
	
					
			$result1=array();
		
			
				$career_kill1=$career->career_killers($db_object,$common,$id_arr1,$gbl_grouprater_inter);
				
				$d=0;
				
				//for($b=0;$b<count($career_kill1);$b++)
				for($b=0;$b<count($sql_result1);$b++)
				{
					$count1=$career_kill1[$b][count];
					
					$user1=$career_kill1[$b][user_id];
					if($fCareer_killer1_c!="" and $fCareer_killer2_c!="")
					{
					if($count1<=$fCareer_killer2_c and $count1>=$fCareer_killer1_c)
					{
						$ch_key1=@array_search($user,$id_arr1);
						
						$result1[$d]=$sql_result1[$ch_key1];
						
						$result1[$d][count]=$count1;
						
						$d++;
					}
					}
					if($fCareer_killer1_c=="" and $fCareer_killer2_c=="")
					{
						$result1[$b]=$sql_result1[$b];
						
						$result1[$b][count]=$count1;
					}
				}//end of for
				$sql_result1=$result1;


			//OVER RATED
			$users=array();$id_arr1=array();
			for($a=0;$a<count($sql_result);$a++)
			{
				$users[$a]=$sql_result[$a][user_id];
			}
			
			for($a=0;$a<count($sql_result1);$a++)
			{
				$id_arr1[$a]=$sql_result1[$a][user_id];
			}
			$result=array();$result1=array();$count="";$count1="";$user="";$user1="";
			
			$ch_key="";$ch_key1="";

			
				
				$over_rated=$career->over_rate($db_object,$common,$users,$gbl_grouprater_inter);
			
				$d=0;
				
				//for($b=0;$b<count($over_rated);$b++)
				for($b=0;$b<count($sql_result);$b++)
				{
					$count=$over_rated[$b][count];
					
					$user=$over_rated[$b][user_id];
					if($fOver_rated1!="" and $fOver_rated2!="")
					{
					if($count<=$fOver_rated2 and $count>=$fOver_rated1)
					{
						$ch_key=@array_search($user,$users);
						
						$result[$d]=$sql_result[$ch_key];
						
						$result[$d][count1]=$count;
						
						$d++;
					}
					}
					if($fOver_rated1=="" and $fOver_rated2=="")
					{
						$result[$b]=$sql_result[$b];
						
						$result[$b][count1]=$count;
					}
				}
				$sql_result=$result;	
			
			$over_rated1=$career->over_rate($db_object,$common,$id_arr1,$gbl_grouprater_inter);
				$d=0;
				
				//for($b=0;$b<count($over_rated1);$b++)
				for($b=0;$b<count($sql_result1);$b++)
				{
					$count1=$over_rated1[$b][count];
					
					$user1=$over_rated1[$b][user_id];
						
					if($fOver_rated1_c!="" and $fOver_rated2_c!="")
					{
					if($count1<=$fOver_rated2_c and $count1>=$fOver_rated1_c)
					{
						$ch_key1=@array_search($user1,$id_arr1);
						
						$result1[$d]=$sql_result1[$ch_key1];
						
						$result1[$d][count1]=$count1;
						
						$d++;
					}
					}
					if($fOver_rated1_c=="" and $fOver_rated2_c=="")
					{
						$result1[$b]=$sql_result1[$b];
						
						$result1[$b][count1]=$count1;
					}
				}
				$sql_result1=$result1;	
			
			//print_r($sql_result1);exit;
			//UNDER RATED
			$users=array();$id_arr1=array();
			for($a=0;$a<count($sql_result);$a++)
			{
				$users[$a]=$sql_result[$a][user_id];
			}
			
			for($a=0;$a<count($sql_result1);$a++)
			{
				$id_arr1[$a]=$sql_result1[$a][user_id];
			}
			$result=array();$result1=array();$count="";$count1="";$user="";$user1="";
			
			$ch_key="";$ch_key1="";

			
				
				$under_rated=$career->under_rate($db_object,$common,$users,$gbl_grouprater_inter);
			
				$d=0;
				
				//for($b=0;$b<count($under_rated);$b++)
				for($b=0;$b<count($sql_result);$b++)
				{
					$count=$under_rated[$b][count];
					
					$user=$under_rated[$b][user_id];
					if($fUnder_rated1!="" and $fUnder_rated2!="")
					{
					if($count<=$fUnder_rated2 and $count>=$fUnder_rated1)
					{
						$ch_key=@array_search($user,$users);
						
						$result[$d]=$sql_result[$ch_key];
						
						$result[$d][count2]=$count;
						
						$d++;
					}
					}
					if($fUnder_rated1=="" and $fUnder_rated2=="")
					{
						$result[$b]=$sql_result[$b];
						
						$result[$b][count2]=$count;
					}
				}
				$sql_result=$result;	
			
			
				$under_rated1=$career->under_rate($db_object,$common,$users,$gbl_grouprater_inter);
				
				$d=0;
				
				//for($b=0;$b<count($under_rated1);$b++)
				for($b=0;$b<count($sql_result1);$b++)
				{
					$count1=$under_rated1[$b][count];
					
					$user1=$under_rated1[$b][user_id];
					
					if($fUnder_rated1_c!="" and $fUnder_rated2_c!="")
					{
					if($count1<=$fUnder_rated2_c and $count1>=$fUnder_rated1_c)
					{
						$ch_key1=@array_search($user1,$id_arr1);
						
						$result1[$d]=$sql_result[$ch_key1];
						
						$result1[$d][count2]=$count1;
						
						$d++;
					}
					}
					if($fUnder_rated1_c=="" and $fUnder_rated2_c=="")
					{
						$result1[$b]=$sql_result1[$b];
						
						$result1[$b][count2]=$count1;
					}
				}
				$sql_result1=$result1;
				
	//print_r($sql_result);echo "<hr><br>";print_r($sql_result1);exit;
		//POSITION MODELS
		
		if(count($fPosition_model)>0)
		{
		$result=array();$result1=array();$b=0;$c=0;
				
		for($i=0;$i<count($sql_result);$i++)
		{
			
			$user=$sql_result[$i][user_id];$b=0;
			
			$view_models=$common->viewable_models($db_object,$user);
		
			
			$ch_arr=array();
			
			for($a=0;$a<count($view_models);$a++)
			{
				
				$mod_id=$view_models[$a];$ch="";
				
				$ch=@in_array($mod_id,$fPosition_model);
				
				if($ch==1)
				{
					$ch_arr[$b]=$mod_id;
					
					$b++;
					
				}
			}
			
			
			if(count($ch_arr)>0)
			{
				$result[$c]=$sql_result[$i];
				
				$result[$c][model]=$ch_arr;
			
				$c++;
			}
		}
		
		$sql_result=$result;
		}
		else
		{
			
			$view_model=$common->viewable_models($db_object,$user_id);
			
			for($i=0;$i<count($sql_result);$i++)
			{
				$sql_result[$i][model]=$view_model;
			}
		}

		if(count($fPosition_model_c)>0)
		{
			$b=0;$c=0;
			
		for($i=0;$i<count($sql_result1);$i++)
		{
			$ch1="";
			
			$ch_arr1=array();$b=0;
			
			$user1=$sql_result1[$i][user_id];
			
			$view_models1=$common->viewable_models($db_object,$user1);
			
			for($a=0;$a<count($view_models1);$a++)
			{
				$mod_id1=$view_models1[$a];
				
				$ch1=@in_array($mod_id1,$fPosition_model_c);
				
				if($ch1==1)
				{
					$ch_arr1[$b]=$mod_id1;
					
					$b++;
					
				}
			}
			if(count($ch_arr1)>0)
			{
				$result1[$c]=$sql_result1[$i];
				
				$result1[$c][model]=$ch_arr1;
								
				$c++;
			}
		}
				
		$sql_result1=$result1;
		}
		else
		{
			$view_model1=$common->viewable_models($db_object,$user_id);
			
			for($i=0;$i<count($sql_result1);$i++)
			{
				$sql_result1[$i][model]=$view_model1;
			}
		}

	
		
			$result=array();$b=0;
			
			for($i=0;$i<count($sql_result);$i++)
			{
					
				$user=$sql_result[$i][user_id];
				
				$same_level_model=$position_obj->return_my_level($db_object,$common,$user);
				
				$model=$sql_result[$i][model];
			
				if($fCareer_same!="")
				{
					$sql="select same_level from career_goals where user_id='$user'";
					
					$res=$db_object->get_single_column($sql);
					
					$same_level_model=@array_merge($same_level_model,$res);
				}
				$check=@array_intersect($model,$same_level_model);
				
				if(count($check)>0)
				{
				
				for($a=0;$a<count($model);$a++)
				{
					$mod_id=$model[$a];
					
					$sql="select percent_fit from $models_percent_fit where user_id='$user'
					
					and model_id='$mod_id' and skill_type='i'";
					
					$sql_res=$db_object->get_a_line($sql);
					
					$percent=$sql_res[percent_fit];
					
					if($fIMatch_same1!="" and $fIMatch_same2!="")
					{
				
					if(($percent<$fIMatch_same2) and ($percent>$fIMatch_same1))
					{
						$result[$b]=$sql_result[$i];
						
						$result[$b][percent]=$percent;
						
						$b++;
					}
					}
					else
					{
						$sql_result[$i][percent]=$percent;
					}
				}
				}
			}
			if($fIMatch_same1 !="" and $fIMatch_same2!="")
			{
			$sql_result=$result;
			}
			
	
		
		$check=array();
	
		
			$result1=array();$b=0;
			
			for($i=0;$i<count($sql_result1);$i++)
			{
				
				$user1=$sql_result1[$i][user_id];
								
				$same_level_model1=$position_obj->return_my_level($db_object,$common,$user1);
				
				$model1=$sql_result1[$i][model];
					
				if($fCareer_same_c!="")
				{
					$sql="select same_level from career_goals where user_id='$user1'";
					
					$res1=$db_object->get_single_column($sql);
					
					$same_level_model1=@array_merge($same_level_model1,$res1);
				}
				
				$check1=@array_intersect($model1,$same_level_model1);
				
				if(count($check1)>0)
				{
				
				for($a=0;$a<count($model1);$a++)
				{
					$mod_id1=$model1[$a];
					
					$sql1="select percent_fit from $models_percent_fit where user_id='$user1'
					
					and model_id='$mod_id1' and skill_type='i'";
					
					$sql_res1=$db_object->get_a_line($sql1);
					
					$percent1=$sql_res1[percent_fit];
					
					if($fIMatch_same1_c!="" and  $fIMatch_same2_c!="")
					{
					if(($percent1<$fIMatch_same2_c) and ($percent1>$fIMatch_same1_c))
					{
						$result1[$b]=$sql_result1[$i];
						
						$result1[$b][percent]=$percent1;
						
						$b++;
					}
					}
					else
					{
						$sql_result1[$i][percent]=$percent1;
					}
				}
				}
			}
		if($fIMatch_same1_c!="" and  $fIMatch_same2_c!="")
		{

			$sql_result1=$result1;
		}
			
		$check1=array();
	
					
			$result=array();$b=0;$user="";$model=array();$check=array();$percent="";$b=0;
			
			for($i=0;$i<count($sql_result);$i++)
			{
				$user=$sql_result[$i][user_id];

				//$c=$sql_result[0][user_id];echo $c;
				$level_model=$position_obj->return_my_level($db_object,$common,$user);
				
				
				
				if($fCareer_same!="")
				{
					$sql="select same_level from career_goals where user_id='$user'";
					
					$res=$db_object->get_single_column($sql);
					
					$level_model=@array_merge($level_model,$res);
				}
				
				$model=$sql_result[$i][model];

						
				$check=@array_intersect($model,$level_model);
										
				if($check[0]!="")
				{
				
				for($a=0;$a<count($model);$a++)
				{
					$mod_id=$model[$a];
					
					
						
					$sql="select percent_fit from $models_percent_fit where user_id='$user'
					
					and model_id='$mod_id' and skill_type='t'";
					
					$sql_res=$db_object->get_a_line($sql);
					
					$percent=$sql_res[percent_fit];
					
					if($fTMatch_same1!="" and $fTMatch_same2!="")
					{

					if(($percent<$fTMatch_same2) and ($percent>$fTMatch_same1))
					{
						
						$result[$b]=$sql_result[$i];
						
						$result[$b][percent1]=$percent;
						
						$b++;
					}
					}
					else
					{
						
						$sql_result[$i][percent1]=$percent;
					}
				}
				}
			}
			
		if($fTMatch_same1!="" and $fTMatch_same2!="")
		{
			$sql_result=$result;
		}
		
		
	
			$result1=array();$b=0;$user1="";$model1=array();$check1=array();$percent1="";
			
			for($i=0;$i<count($sql_result1);$i++)
			{
				$user1=$sql_result1[$i][user_id];
				
				$same_level_model1=$position_obj->return_my_level($db_object,$common,$user1);
				
				if($fCareer_same_c!="")
				{
					$sql="select same_level from career_goals where user_id='$user'";
					
					$res1=$db_object->get_single_column($sql);
					
					$same_level_model1=@array_merge($same_level_model1,$res1);
				}
				
				
				
				$model1=$sql_result1[$i][model];
				
				$check1=@array_intersect($model1,$same_level_model1);
				
				if($check1[0]!="")
				{
				
				for($a=0;$a<count($model1);$a++)
				{
					$mod_id1=$model1[$a];
					
					$sql1="select percent_fit from $models_percent_fit where user_id='$user1'
					
					and model_id='$mod_id1' and skill_type='t'";
					
					$sql_res1=$db_object->get_a_line($sql1);
					
					$percent1=$sql_res1[percent_fit];
					
					if($fTMatch_same1_c!="" and  $fTMatch_same2_c!="")
					{
					
					if(($percent1<$fTMatch_same2_c) and ($percent1>$fTMatch_same1_c))
					{
						$result1[$b]=$sql_result1[$i];
						
						$result1[$b][percent1]=$percent1;
						
						$b++;
					}
					}
					else
					{
						$sql_result1[$i][percent1]=$percent1;
						
					}
				}
				}
			}
		if($fTMatch_same1_c!="" and  $fTMatch_same2_c!="")
		{
			$sql_result1=$result1;
		}

	//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
		//print_r($sql_result);echo "<hr><br>";print_r($sql_result1);exit;
		
		
			$result=array();$b=0;$user="";$model=array();$check=array();$percent="";
			
			for($i=0;$i<count($sql_result);$i++)
			{
					
				$user=$sql_result[$i][user_id];
				
				$higher_level_model=$position_obj->return_1higher_level($db_object,$common,$user);
				
				if($fCareer_up_1!="")
				{
					$sql="select onelevel_up from career_goals where user_id='$user'";
					
					$res=$db_object->get_single_column($sql);
					
					$higher_level_model=@array_merge($higher_level_model,$res);
				}
				
				$model=$sql_result[$i][model];
				
				$check=@array_intersect($model,$higher_level_model);
				
				
				if(count($check)!=0)
				{

				for($a=0;$a<count($model);$a++)
				{
				
					$mod_id=$model[$a];
					
					$sql="select percent_fit from $models_percent_fit where user_id='$user'
					
					and model_id='$mod_id' and skill_type='i'";
					
					$sql_res=$db_object->get_a_line($sql);
			
					$percent=$sql_res[percent_fit];
					
				if($fIMatch_up1!="" and $fIMatch_up2!="")
				{
					
					if(($percent<=$fIMatch_up2) and ($percent>=$fIMatch_up1))
					{
						
						$result[$b]=$sql_result[$i];

						$result[$b][percent2]=$percent;
						
						$b++;
					}
				}
				else
				{
					
					$sql_result[$i][percent2]=$percent;
					
					
				}
				}
				}
			}
		
		if($fIMatch_up1!="" and $fIMatch_up2!="")
		{
	
			$sql_result=$result;
				
		}
	
		$check=array();
		
	
			$result1=array();$b=0;$check1=array();$model1=array();$user1="";$percent1="";
			
			for($i=0;$i<count($sql_result1);$i++)
			{
				$user1=$sql_result1[$i][user_id];
				
				$higher_level_model1=$position_obj->return_1higher_level($db_object,$common,$user1);
				
					
				if($fCareer_up_1_c!="")
				{
					$sql="select onelevel_up from career_goals where user_id='$user1'";
					
					$res1=$db_object->get_single_column($sql);
					
					$higher_level_model1=@array_merge($higher_level_model1,$res1);
				}
				
				$model1=$sql_result1[$i][model];
				
				$check1=@array_intersect($model1,$higher_level_model1);
				
				if(count($check1)!=0)
				{
				
				for($a=0;$a<count($model1);$a++)
				{
					$mod_id1=$model1[$a];
					
					$sql1="select percent_fit from $models_percent_fit where user_id='$user1'
					
					and model_id='$mod_id1' and skill_type='i'";
					
					$sql_res1=$db_object->get_a_line($sql1);
					
					$percent1=$sql_res1[percent_fit];
					
					if($fIMatch_up1_c!="" and $fIMatch_up2_c!="")
					{
					
					if(($percent1<=$fIMatch_up2_c) and ($percent1>=$fIMatch_up1_c))
					{
						$result1[$b]=$sql_result1[$i];
						
						$result1[$b][percent2]=$percent1;
						
						$b++;
					}
					}
					else
					{
						$sql_result1[$i][percent2]=$percent1;
					}
				}
				}
			}
			
		if($fIMatch_up1_c!="" and $fIMatch_up2_c!="")
		{
			$sql_result1=$result1;
		}
		$check1=array();
	
			
		
		$result=array();$b=0;$user="";$model=array();$check=array();$percent="";
			
			for($i=0;$i<count($sql_result);$i++)
			{
				$user=$sql_result[$i][user_id];
				
				$level_model=$position_obj->return_1higher_level($db_object,$common,$user);
				
				if($fCareer_up_1!="")
				{
					$sql="select onelevel_up from career_goals where user_id='$user'";
					
					$res=$db_object->get_single_column($sql);
					
					$level_model=@array_merge($level_model,$res);
				}
				
				$model=$sql_result[$i][model];
				
				$check=@array_intersect($model,$level_model);

				if(count($check)>0)
				{
				
				for($a=0;$a<count($model);$a++)
				{
					$mod_id=$model[$a];
					
					$sql="select percent_fit from $models_percent_fit where user_id='$user'
					
					and model_id='$mod_id' and skill_type='t'";
					
					$sql_res=$db_object->get_a_line($sql);
					
					$percent=$sql_res[percent_fit];
					
			if($fTMatch_up1!="" and $fTMatch_up2!="")
			{
					
					if(($percent<=$fTMatch_up2) and ($percent>=$fTMatch_up1))
					{
						$result[$b]=$sql_result[$i];
						
						$result[$b][percent3]=$percent;
						
						$b++;
					}
			}
			else
			{
				$sql_result[$i][percent3]=$percent;
			}
				}
				}
			}
				
			if($fTMatch_up1!="" and $fTMatch_up2!="")
			{
			$sql_result=$result;
			}
		
		
			
		
			$result1=array();$b=0;$user1="";$model1=array();$check1=array();$percent1="";
			
			for($i=0;$i<count($sql_result1);$i++)
			{
				$user1=$sql_result1[$i][user_id];
				
				$higher_level_model1=$position_obj->return_1higher_level($db_object,$common,$user1);
				
				if($fCareer_up_1_c!="")
				{
					$sql="select onelevel_up from career_goals where user_id='$user1'";
					
					$res1=$db_object->get_single_column($sql);
					
					$higher_level_model1=@array_merge($higher_level_model1,$res1);
				}
				
				$model1=$sql_result1[$i][model];
				
				$check1=@array_intersect($model1,$higher_level_model1);
				
				if(count($check1)!=0)
				{
				
				for($a=0;$a<count($model1);$a++)
				{
					$mod_id1=$model1[$a];
					
					$sql1="select percent_fit from $models_percent_fit where user_id='$user1'
					
					and model_id='$mod_id1' and skill_type='t'";
					
					$sql_res1=$db_object->get_a_line($sql1);
					
					$percent1=$sql_res1[percent_fit];

		if($fTMatch_up1_c!="" or $fTMatch_up2_c!="")
		{
					
					if(($percent1<$fTMatch_up2_c) and ($percent1>$fTMatch_up1_c))
					{
						$result1[$b]=$sql_result1[$i];
						
						$result1[$b][percent3]=$percent1;
						
						$b++;
					}
		}
		else
		{
			$sql_result1[$i][percent3]=$percent1;
		}
				}
				}
			}
		
		if($fTMatch_up1_c!="" and $fTMatch_up2_c!="")
		{
			$sql_result1=$result1;
		}

	
		//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
	
		
			$result=array();$b=0;$user="";$model=array();$check=array();$higher_level_model=array();
			$percent="";
			for($i=0;$i<count($sql_result);$i++)
			{
					
				$user=$sql_result[$i][user_id];
				
				$higher_level_model=$position_obj->return_2higher_level($db_object,$common,$user);
				
				if($fCareer_up_2!="")
				{
					$sql="select onelevel_up from career_goals where user_id='$user'";
					
					$res=$db_object->get_single_column($sql);
					
					$higher_level_model=@array_merge($higher_level_model,$res);
				}
				
				
				$model=$sql_result[$i][model];
					
				$check=@array_intersect($model,$higher_level_model);
				
				if(count($check)!=0)
				{

				for($a=0;$a<count($model);$a++)
				{
				
					$mod_id=$model[$a];
					
					$sql="select percent_fit from $models_percent_fit where user_id='$user'
					
					and model_id='$mod_id' and skill_type='i'";
					
					$sql_res=$db_object->get_a_line($sql);
				
					$percent=$sql_res[percent_fit];
				
				
		if($fIMatch_up_2_1!="" and $fIMatch_up_2_2!="")
		{
		
					if(($percent<=$fIMatch_up_2_2) and ($percent>=$fIMatch_up_2_1))
					{
						
						$result[$b]=$sql_result[$i];

						$result[$b][percent4]=$percent;
						
						$b++;
					}
		}
		else
		{
			
			$sql_result[$i][percent4]=$percent;
		}
				}
				}
			}

		if($fIMatch_up_2_1!="" and $fIMatch_up_2_2!="")
		{
			$sql_result=$result;
				
		}
		
		$check=array();
		
		
			$result1=array();$b=0;$check1=array();$model1=array();$user1="";$higher_level_model1=array();
			$percent1="";
			for($i=0;$i<count($sql_result1);$i++)
			{
				$user1=$sql_result1[$i][user_id];
				
				$higher_level_model1=$position_obj->return_2higher_level($db_object,$common,$user1);
				
				if($fCareer_up_2_c!="")
				{
					$sql="select onelevel_up from career_goals where user_id='$user1'";
					
					$res1=$db_object->get_single_column($sql);
					
					$higher_level_model1=@array_merge($higher_level_model1,$res1);
				}
				
				$model1=$sql_result1[$i][model];
				
				$check1=@array_intersect($model1,$higher_level_model1);
				
				if(count($check1)>0)
				{
				
				for($a=0;$a<count($model1);$a++)
				{
					
					$mod_id1=$model1[$a];
					
					$sql1="select percent_fit from $models_percent_fit where user_id='$user1'
					
					and model_id='$mod_id1' and skill_type='i'";
					
					$sql_res1=$db_object->get_a_line($sql1);
					
					$percent1=$sql_res1[percent_fit];
					
					if($fIMatch_up_2_1_c!="" or $fIMatch_up_2_2_c!="")
				{
					
					if(($percent1<=$fIMatch_up_2_2_c) and ($percent1>=$fIMatch_up_2_1_c))
					{
						$result1[$b]=$sql_result1[$i];
						
						$result1[$b][percent4]=$percent1;
						
						$b++;
					}
				}
				else
				{
					$sql_result1[$i][percent4]=$percent1;
				}
				}
				}
			}
		
		if($fIMatch_up_2_1_c!="" and  $fIMatch_up_2_2_c!="")
		{
			$sql_result1=$result1;
		}
		$check1=array();
	
			
			
			$result=array();$b=0;$user="";$model=array();$check=array();$percent="";$level_model=array();
			
			for($i=0;$i<count($sql_result);$i++)
			{
				$user=$sql_result[$i][user_id];
				
				$level_model=$position_obj->return_2higher_level($db_object,$common,$user);
				
				if($fCareer_up_2!="")
				{
					$sql="select onelevel_up from career_goals where user_id='$user'";
					
					$res=$db_object->get_single_column($sql);
					
					$level_model=@array_merge($level_model,$res);
				}
				
				$model=$sql_result[$i][model];
				
				$check=@array_intersect($model,$level_model);

				if(count($check)>0)
				{
				
				for($a=0;$a<count($model);$a++)
				{
					$mod_id=$model[$a];
					
					$sql="select percent_fit from $models_percent_fit where user_id='$user'
					
					and model_id='$mod_id' and skill_type='t'";
					
					$sql_res=$db_object->get_a_line($sql);
					
					$percent=$sql_res[percent_fit];
					
					if($fTMatch_up_2_1!="" or $fTMatch_up_2_2!="")
					{
					
					if(($percent<=$fTMatch_up_2_2) and ($percent>=$fTMatch_up_2_1))
					{
						$result[$b]=$sql_result[$i];
						
						$result[$b][percent5]=$percent;
						
						$b++;
					}
					}
					else
					{
						$sql_result[$i][percent5]=$percent;
					}
				}
				}
			}
			
			if($fTMatch_up_2_1!="" and $fTMatch_up_2_2!="")
			{
			$sql_result=$result;
			}
		
		
		
	
			$result1=array();$b=0;$user1="";$model1=array();$check1=array();$percent1="";$higher_level_model1=array();
			
			for($i=0;$i<count($sql_result1);$i++)
			{
				$user1=$sql_result1[$i][user_id];
				
				$higher_level_model1=$position_obj->return_2higher_level($db_object,$common,$user1);
				
				if($fCareer_up_2_c!="")
				{
					$sql="select onelevel_up from career_goals where user_id='$user1'";
					
					$res1=$db_object->get_single_column($sql);
					
					$higher_level_model1=@array_merge($higher_level_model1,$res1);
				}
			
				$model1=$sql_result1[$i][model];
				
				$check1=@array_intersect($model1,$higher_level_model1);
				
				if(count($check1)!=0)
				{
				
				for($a=0;$a<count($model1);$a++)
				{
					$mod_id1=$model1[$a];
					
					$sql1="select percent_fit from $models_percent_fit where user_id='$user1'
					
					and model_id='$mod_id1' and skill_type='t'";
					
					$sql_res1=$db_object->get_a_line($sql1);
					
					$percent1=$sql_res1[percent_fit];
					
		if($fTMatch_up_2_1_c!="" and $fTMatch_up_2_2_c!="")
		{
					
					if(($percent1<$fTMatch_up_2_2_c) and ($percent1>$fTMatch_up_2_1_c))
					{
						$result1[$b]=$sql_result1[$i];
						
						$result1[$b][percent5]=$percent1;
						
						$b++;
					}
		}
		else
		{
			$sql_result1[$i][percent5]=$percent1;
		}
				}
				}
			}
		if($fTMatch_up_2_1_c!="" and $fTMatch_up_2_2_c!="")
		{
			$sql_result1=$result1;
		}
		//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
		
	//print_r($sql_result);echo "<hr><br>";print_r($sql_result1);exit;
		
		//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
		
		if($fSuccession_plan_1!="" or $fSuccession_plan_2!="" or $fSuccession_plan_hire!="")
		{
		$res1=array();$user=array();$users_set=array();$users_id="";$res1=array();
		
		$result=array();
		
		for($i=0;$i<count($sql_result);$i++)
		{
			$users_set[$i]=$sql_result[$i][user_id];
			
			$user[$i]=$sql_result[$i][position];
		}
		if(count($user)>0)
		{
			$users=@implode(",",$user);
			
			$users_id="(".$users.")";
			
			$sql="select plan_id from $deployment_plan where position in $users_id";
			
			$res_sql=$db_object->get_single_column($sql);
			
			if(count($res_sql)>0)
			{
				$plans=@implode(",",$res_sql);
				
				$plans_id="(".$plans.")";
			
			if($fSuccession_plan_1!="")
			{
				$sql1="select designated_user as user_id from $position_designee1 where plan_id in $plans_id group by designated_user";
				
				$res1=$db_object->get_single_column($sql1);
			}
			if($fSuccession_plan_2!="")
			{
				$sql2="select designated_user as user_id from $position_designee2 where plan_id in $plans_id group by designated_user";
				
				$res2=$db_object->get_single_column($sql2);
				
				$res1=@array_merge($res1,$res2);
			}
			
			if($fSuccession_plan_hire!="")
			{
				if(count($res1)>0)
				{
					$users=@implode(",",$res1);
					
					$users_set="(".$users.")";
					
					$sql="select user_id from $user_table where user_type='external' and user_id in $users_set";
				
					$res1=$db_object->get_single_column($sql);
				}
				if($fSuccession_plan_1=="" and $fSuccession_plan_2=="")
				{
				
					$users=@implode(",",$users_set);
					
					$users_set="(".$users.")";
					
					$sql="select user_id from $user_table where user_type='external' and user_id in $users_set";
					
					$res1=$db_object->get_single_column($sql);
				}
			}
			}
		}
		
		$a=0;
		for($i=0;$i<count($sql_result);$i++)
		{
			$user=$sql_result[$i][user_id];
			
			$ch=@in_array($user,$res1);
			
			if($ch==1)
			{
				$result[$a]=$sql_result[$i];
				
				$a++;
			}
		}
		$sql_result=$result;
		}
		
		
		
			//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
		
		if($fSuccession_plan_1_c!="" or $fSuccession_plan_2_c!="" or $fSuccession_plan_hire_c!="")
		{
		$res1=array();$users_set=array();$user=array();$users="";$users_id="";$res1=array();$res_sql=array();
		$result1=array();
		for($i=0;$i<count($sql_result1);$i++)
		{
			$users_set[$i]=$sql_result1[$i][user_id];
			
			$user[$i]=$sql_result1[$i][position];
		}
		if(count($user)>0)
		{
			$users=@implode(",",$user);
			
			$users_id="(".$users.")";
			
			$sql="select plan_id from $deployment_plan where position in $users_id";
		
			$res_sql=$db_object->get_single_column($sql);
			
			if(count($res_sql)>0)
			{
				$plans=@implode(",",$res_sql);
				
				$plans_id="(".$plans.")";
			
			if($fSuccession_plan_1_c!="")
			{
				$sql1="select designated_user as user_id from $position_designee1 where plan_id in $plans_id group by designated_user";
			
				$res1=$db_object->get_single_column($sql1);
				
			}
			if($fSuccession_plan_2_c!="")
			{
				$sql2="select designated_user as user_id from $position_designee2 where plan_id in $plans_id group by designated_user";
				
				$res2=$db_object->get_single_column($sql2);
				
				$res1=@array_merge($res1,$res2);
			}
			
			if($fSuccession_plan_hire_c!="")
			{
				if(count($res1)>0)
				{
					$users=@implode(",",$res1);
					
					$users_set="(".$users.")";
					
					$sql="select user_id from $user_table where user_type='external' and user_id in $users_set";
					
					$res1=$db_object->get_single_column($sql);
				}
				if($fSuccession_plan_1_c=="" and $fSuccession_plan_2_c=="")
				{
				
					$users=@implode(",",$users_set);
					
					$users_set="(".$users.")";
					
					$sql="select user_id from $user_table where user_type='external' and user_id in $users_set";
				
					$res1=$db_object->get_single_column($sql);
				}
			}
			}
		}
		$a=0;
		for($i=0;$i<count($sql_result1);$i++)
		{
			$user1=$sql_result1[$i][user_id];
			
			$ch=@in_array($user1,$res1);
			
			if($ch==1)
			{
				$result1[$a]=$sql_result1[$i];
				
				$a++;
			}
		}
		$sql_result1=$result1;
		}
					
}//END OF CAREER MODULE
	
		//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
		

	$skills_table=$common->prefix_table("skills");
	
		//LEARNING FACTORS
		$root_path=$common->path;
		
		$module=md5(learning);
		
		$ch_learn=$common->is_module_purchased_check($db_object,$root_path,$module);
$ch_learn=2;
if($ch_learn==1)
{
	if(count($fISkills)==0)//IF SKILLS NOT SELECTED
	{
		$sql="select skill_id from $skills_table where skill_type='i'";
		
		$fISkills=$db_object->get_single_column($sql);
	}
	
		if(count($fISkills)>0)
		{
		$a=0;
		$result=array();$users="";$res=array();
		
			$users=$this->get_userid($db_object,$sql_result);
			
			if($users!="")
			{
			
				$skills=@implode(",",$fISkills);
				
				$skills_id="(".$skills.")";
				
				$sql="select user_id from $assign_solution_builder where skill_id in $skills_id and type='i'
				
				and user_id in "."(". $users.")";

				$res=$db_object->get_single_column($sql);
								
				for($i=0;$i<count($sql_result);$i++)
				{
					$user=$sql_result[$i][user_id];
					
					if(@in_array($user,$res))
					{
						$result[$a]=$sql_result[$i];
						
						$a++;
					}
				}
						
			}
			
		
		$sql_result=$result;	
		}	

		//-------------------------------------------------------------------------
		if(count($fISkills_c)==0)
		{
			$sql="select skill_id from $skills_table where skill_type='i'";
			
			$fISkills_c=$db_object->get_single_column($sql);
		}

		if(count($fISkills_c)>0)
		{
		$a=0;
		$result1=array();$users1="";$res1=array();
		
			$users1=$this->get_userid($db_object,$sql_result1);

			if($users1!="")
			{
			
				$skills1=@implode(",",$fISkills_c);
				
				$skills_id1="(".$skills1.")";
				
				$sql1="select user_id from $assign_solution_builder where skill_id in $skills_id1 and type='i'
				
				and user_id in "."(". $users1.")";
			
				$res1=$db_object->get_single_column($sql1);
								
				for($i=0;$i<count($sql_result1);$i++)
				{
					$user1=$sql_result1[$i][user_id];
					
					if(@in_array($user1,$res1))
					{
						$result1[$a]=$sql_result1[$i];
						
						$a++;
					}
				}
						
			}
			
		$sql_result1=$result1;
			
		}	
		
		//----------------------------------------------------------------------------
		
		if(count($fTSkills)==0)
		{
			$sql="select skill_id from $skills_table where skill_type='t'";
			
			$fTSkills=$db_object->get_single_column($sql);
		}

		if(count($fTSkills)>0)
		{
		$a=0;
		$result=array();$users="";$res=array();
		
			$users=$this->get_userid($db_object,$sql_result);
			
			if($users!="")
			{
			
				$skills=@implode(",",$fTSkills);
				
				$skills_id="(".$skills.")";
				
				$sql="select user_id from $assign_solution_builder where skill_id in $skills_id and type='t'
				
				and user_id in "."(". $users.")";

				$res=$db_object->get_single_column($sql);
								
				for($i=0;$i<count($sql_result);$i++)
				{
					$user=$sql_result[$i][user_id];
					
					if(@in_array($user,$res))
					{
						$result[$a]=$sql_result[$i];
						
						$a++;
					}
				}
						
			}
			
		$sql_result=$result;
			
		}				
		//-------------------------------------------------------------------------
					
		if(count($fTSkills_c)==0)
		{
			$sql="select skill_id from $skills_table where skill_type='t'";
			
			$fTSkills_c=$db_object->get_single_column($sql);
		}
		
		if(count($fTSkills_c)>0)
		{
		$a=0;
		$result1=array();$users1="";$res1=array();
		
			$users1=$this->get_userid($db_object,$sql_result1);

			if($users1!="")
			{
			
				$skills1=@implode(",",$fTSkills_c);
				
				$skills_id1="(".$skills1.")";
				
				$sql1="select user_id from $assign_solution_builder where skill_id in $skills_id1 and type='t'
						
				and user_id in "."(". $users1.")";

				$res1=$db_object->get_single_column($sql1);
							
				for($i=0;$i<count($sql_result1);$i++)
				{
					$user1=$sql_result1[$i][user_id];
					
					if(@in_array($user1,$res1))
					{
						$result1[$a]=$sql_result1[$i];
											
						$a++;
					}
				}
						
			}
			
		$sql_result1=$result1;
			
		}

			for($i=0;$i<count($sql_result);$i++)
			{
				$user=$sql_result[$i][user_id];
				
					$sql="select skill_id from $assign_solution_builder where user_id='$user' and skill_id<>'0'";
			
								
				$res=$db_object->get_single_column($sql);
				
				$sql_result[$i][skill]=$res;
				
			}
			
			
			for($i=0;$i<count($sql_result1);$i++)
			{
				$user1=$sql_result1[$i][user_id];
				
				
				$sql1="select skill_id from $assign_solution_builder where user_id='$user1' and skill_id<>'0'";
				
					$res1=$db_object->get_single_column($sql1);
				
				$sql_result1[$i][skill]=$res1;
				
			}
	
		//----------------------------------------------------------------------------

		//LEARNING ACTIVITY
		
			//$content = preg_replace("/<{iflear_loop(.*?)}>/s","",$content);
			
			$result=array();$c=0;
			
			$qry="select interbasic_id from $dev_interbasic where basic_id in (1,2,3)";
			
			$id_res=$db_object->get_single_column($qry);
			
			if(count($id_res)>0)
			{
						
				$ids=@implode(",",$id_res);
				
				$id_set="(".$ids.")";
			}//end of if
			$e=0;
			$date9=$this->date_format($fDate9);
			
			$date10=$this->date_format($fDate10);
			
			$res=array();
			
			for($a=0;$a<count($sql_result);$a++)
			{
				$percent=0;$percentage=array();	
				
				$skill=$sql_result[$a][skill];
				
				$sql_result[$a][date9]=$date9;
				
				$sql_result[$a][date10]=$date10;
						
				$user=$sql_result[$a][user_id];
				
				
				if(count($skill)>0)
				{
			
					$skills=@implode(",",$skill);
					
					$skill_set="(".$skills.")";
					
					if($fDate9!="" and $fDate10!="")
					{
				$sql="select user_id,skill_id,cdate,completed_date from $approved_devbuilder
				
				where user_id='$user' and skill_id in $skill_set and completed_date between '$date9' and 
				
				'$date10' and basic_id in (1,2,3)";
					}
					else
					{
				$sql="select user_id,skill_id,cdate,completed_date from $approved_devbuilder
				
				where user_id='$user' and skill_id in $skill_set and basic_id in (1,2,3)";
						
					}//end of if else

				
				$res=$db_object->get_rsltset($sql);
				
				$percent=0;
					
						
				for($b=0;$b<count($res);$b++)
				{
					
				
					$skill_id=$res[$b][skill_id];
				}//end of inner for
					if(count($skill_id)>0)
					{
						$skills=@implode(",",$skill_id);
						
						$skills_set="(".$skills.")";
					}//end of if
					
					
					
				
					$mysql 	= "select cdate,completed_date,plan_approved_date,

					(((to_days(cdate) - to_days(plan_approved_date))/(to_days(completed_date) - to_days(plan_approved_date)))*100) 

					as per from $approved_devbuilder,$assign_solution_builder,$feedback_table where 
					
					$approved_devbuilder.user_id=$assign_solution_builder.user_id and $approved_devbuilder.skill_id=$assign_solution_builder.skill_id
					
					and $approved_devbuilder.user_id=$feedback_table.rated_id and $approved_devbuilder.skill_id=$feedback_table.skill_id
					
					and $approved_devbuilder.pstatus='a' and $approved_devbuilder.user_id='$user' and $approved_devbuilder.skill_id in $skill_set
					
					and $feedback_table.status='1' ";
				
				$percentage=$db_object->get_rsltset($mysql);
			
			for($j=0;$j<count($percentage);$j++)
			{

				$percent+=$percentage[$j][per];
				
			}//end of for
				


			
				if(count($percentage)>0)
				{
				
				$d_result[$e]=$sql_result[$a];

				$d_result[$e][lpercent]=$percent/$j;
								
				$e++;
				
				$sql_result[$a][lpercent]=$percent/$j;
				}//end of if 
				
						
				}//end of if
				$per=$sql_result[$a][lpercent];
		
				if($fLearning_activity1!="" and $fLearning_activity2 !="")
				{
					
					if($per<=$fLearning_activity2 and $per>=$fLearning_activity1)
					{
						
						$result[$c]=$sql_result[$a];
						
						$c++;
					}
				}
				
			
			}//end of for
		
				if($fLearning_activity1!="" and $fLearning_activity2!="")
				{
					
					$sql_result=$result;
				}
				else
				{
					
					$sql_result=$d_result;
				}

		//-----------------------------------------------------------------------------------------
	
		
			$e=0;
			
			$result1=array();$c=0;$id_res=array();
			
			$qry="select interbasic_id from $dev_interbasic where basic_id in (1,2,3)";
			
			$id_res=$db_object->get_single_column($qry);
			
			if(count($id_res)>0)
			{
						
				$ids=@implode(",",$id_res);
				
				$id_set="(".$ids.")";
			}//end of if
			
			$date9=$this->date_format($fDate9_c);
			
			$date10=$this->date_format($fDate10_c);
			
			$res=array();
			
			for($a=0;$a<count($sql_result1);$a++)
			{
				$percent1=0;$percentage1=array();	
				
				$skill1=$sql_result1[$a][skill];
		
				$user1=$sql_result1[$a][user_id];
				
				if(count($skill1)>0)
				{
			
					$skills1=@implode(",",$skill1);
					
					$skill_set1="(".$skills1.")";
					
					if($fDate9_c!="" and $fDate10_c!="")
					{
					
				$sql1="select user_id,skill_id,cdate,completed_date from $approved_devbuilder
				
				where user_id='$user1' and skill_id in $skill_set1 and completed_date between '$date9' and 
				
				'$date10' and basic_id in (1,2,3)";
					}
					else
					{
				$sql1="select user_id,skill_id,cdate,completed_date from $approved_devbuilder
				
				where user_id='$user1' and skill_id in $skill_set1 and basic_id in (1,2,3)";		
					}
				
				$res1=$db_object->get_rsltset($sql1);
				
				$percent1=0;
					
						
				for($b=0;$b<count($res1);$b++)
				{
					
				
					$skill_id1=$res1[$b][skill_id];
				}
					if(count($skill_id1)>0)
					{
						$skills1=@implode(",",$skill_id1);
						
						$skills_set1="(".$skills1.")";
					}
					
					
					
				
					$mysql 	= "select cdate,completed_date,plan_approved_date,

					(((to_days(cdate) - to_days(plan_approved_date))/(to_days(completed_date) - to_days(plan_approved_date)))*100) 

					as per from $approved_devbuilder,$assign_solution_builder,$feedback_table where 
					
					$approved_devbuilder.user_id=$assign_solution_builder.user_id and $approved_devbuilder.skill_id=$assign_solution_builder.skill_id
					
					and $approved_devbuilder.user_id=$feedback_table.rated_id and $approved_devbuilder.skill_id=$feedback_table.skill_id
					
					and $approved_devbuilder.pstatus='a' and $approved_devbuilder.user_id='$user1' and $approved_devbuilder.skill_id in $skill_set1
					
					and $feedback_table.status='1' ";
				
				$percentage1=$db_object->get_rsltset($mysql);
			
			for($j=0;$j<count($percentage1);$j++)
			{
				$percent1+=$percentage1[$j][per];
				
			}
			
				if(count($percentage1)>0)
				{
					$d_result1[$e]=$sql_result1[$a];
					
					$d_result1[$e][lpercent]=$percent1/$j;
					
					$e++;
			
				$sql_result1[$a][lpercent]=$percent1/$j;
				}
				
				$sql_result1[$a][date9]=$date9;
				
				$sql_result1[$a][date10]=$date10;
				
				
			}//end of if
			
				if($fLearning_activity1_c!="" and $fLearning_activity2_c !="")
				{
					$per1=$sql_result1[$a][lpercent];

					if($per1<=$fLearning_activity2_c and $per1>=$fLearning_activity1_c)
					{
						
						$result1[$c]=$sql_result1[$a];
						
						$c++;
					}
				}
			
			}//end of for
		
				if($fLearning_activity1_c!="" and $fLearning_activity2_c!="")
				{
					
					$sql_result1=$result1;
				}
				else
				{
					$sql_result1=$d_result1;
				}
			
		
		


		//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
		
		//APPLICATION ACTIVITY
		
		$content = preg_replace("/<{ifactivity_(.*?)}>/s","",$content);
			
			$result=array();$c=0;$id_res=array();$d_result=array();
			
			$qry="select interbasic_id from $dev_interbasic where basic_id='4'";
			
			$id_res=$db_object->get_single_column($qry);
			
			if(count($id_res)>0)
			{
						
				$ids=@implode(",",$id_res);
				
				$id_set="(".$ids.")";
			}
			$e=0;
			$date11=$this->date_format($fDate11);
			
			$date12=$this->date_format($fDate12);
			
			$res=array();
			
			for($a=0;$a<count($sql_result);$a++)
			{
				$percent=0;$percentage=array();	
				$skill=$sql_result[$a][skill];
				
				$sql_result[$a][date11]=$date11;
				
				$sql_result[$a][date12]=$date12;
						
				$user=$sql_result[$a][user_id];
				
				if(count($skill)>0)
				{
			
					$skills=@implode(",",$skill);
					
					$skill_set="(".$skills.")";
					
				if($fDate11!="" and $fDate12!="")
				{
					
				$sql="select user_id,skill_id,cdate,completed_date from $approved_devbuilder
				
				where user_id='$user' and skill_id in $skill_set and completed_date between '$date11' and 
				
				'$date12' and basic_id in (4)";
				}
				else
				{
					$sql="select user_id,skill_id,cdate,completed_date from $approved_devbuilder
				
				where user_id='$user' and skill_id in $skill_set and basic_id in (4)";
					
				}
				
				$res=$db_object->get_rsltset($sql);
				
				$percent=0;
					
						
				for($b=0;$b<count($res);$b++)
				{
					
				
					$skill_id=$res[$b][skill_id];
				}
					if(count($skill_id)>0)
					{
						$skills=@implode(",",$skill_id);
						
						$skills_set="(".$skills.")";
					}
					
					
					
				
					$mysql 	= "select cdate,completed_date,plan_approved_date,

					(((to_days(cdate) - to_days(plan_approved_date))/(to_days(completed_date) - to_days(plan_approved_date)))*100) 

					as per from $approved_devbuilder,$assign_solution_builder,$feedback_table where 
					
					$approved_devbuilder.user_id=$assign_solution_builder.user_id and $approved_devbuilder.skill_id=$assign_solution_builder.skill_id
					
					and $approved_devbuilder.user_id=$feedback_table.rated_id and $approved_devbuilder.skill_id=$feedback_table.skill_id
					
					and $approved_devbuilder.pstatus='a' and $approved_devbuilder.user_id='$user' and $approved_devbuilder.skill_id in $skill_set
					
					and $feedback_table.status='1' ";
				
				$percentage=$db_object->get_rsltset($mysql);
			
			for($j=0;$j<count($percentage);$j++)
			{

				$percent+=$percentage[$j][per];
				
			}
				
				
			
				if(count($percentage)>0)
				{
				
				$d_result[$e]=$sql_result[$a];

				$d_result[$e][apercent]=$percent/$j;
								
				$e++;
				
				$sql_result[$a][apercent]=$percent/$j;
				}
				
						
				}
		
				if($fApplication_activity1!="" and $fApplication_activity2 !="")
				{
					$per=$sql_result[$a][apercent];

					if($per<=$fApplication_activity2 and $per>=$fApplication_activity1)
					{
						
						$result[$c]=$sql_result[$a];
						
						$c++;
					}
				}
			
			}
		
				if($fApplication_activity1!="" and $fApplication_activity2!="")
				{
					
					$sql_result=$result;
				}
				else
				{
					
					$sql_result=$d_result;
				}
			
			
		//-----------------------------------------------------------------------------------------
			$content = preg_replace("/<{ifactivity1_(.*?)}>/s","",$content);
			
			$result1=array();$c=0;$id_res1=array();$d_result1=array();
			
			$qry="select interbasic_id from $dev_interbasic where basic_id='4'";
			
			$id_res1=$db_object->get_single_column($qry);
			
			if(count($id_res1)>0)
			{
						
				$ids1=@implode(",",$id_res1);
				
				$id_set1="(".$ids1.")";
			}
			$e=0;
			$date11=$this->date_format($fDate11_c);
			
			$date12=$this->date_format($fDate12_c);
			
			$res=array();
			
			for($a=0;$a<count($sql_result1);$a++)
			{
				$percent1=0;$percentage1=array();	
				$skill1=$sql_result1[$a][skill];
				
				$sql_result1[$a][date11]=$date11;
				
				$sql_result1[$a][date12]=$date12;
						
				$user1=$sql_result1[$a][user_id];
				
				if(count($skill1)>0)
				{
			
					$skills1=@implode(",",$skill1);
					
					$skill_set1="(".$skills1.")";
					
				if($fDate11_c!="" and $fDate12_c!="")
				{
		
					
				$sql1="select user_id,skill_id,cdate,completed_date from $approved_devbuilder
				
				where user_id='$user' and skill_id in $skill_set and completed_date between '$date11' and 
				
				'$date12' and basic_id in (4)";
				}
				else
				{
					$sql1="select user_id,skill_id,cdate,completed_date from $approved_devbuilder
				
				where user_id='$user' and skill_id in $skill_set and basic_id in (4)";
					
				}
				
				$res1=$db_object->get_rsltset($sql);
				
				$percent1=0;
					
						
				for($b=0;$b<count($res1);$b++)
				{
					
				
					$skill_id1=$res1[$b][skill_id];
				}
					if(count($skill_id1)>0)
					{
						$skills1=@implode(",",$skill_id1);
						
						$skills_set1="(".$skills1.")";
					}
					
					
					
				
					$mysql 	= "select cdate,completed_date,plan_approved_date,

					(((to_days(cdate) - to_days(plan_approved_date))/(to_days(completed_date) - to_days(plan_approved_date)))*100) 

					as per from $approved_devbuilder,$assign_solution_builder,$feedback_table where 
					
					$approved_devbuilder.user_id=$assign_solution_builder.user_id and $approved_devbuilder.skill_id=$assign_solution_builder.skill_id
					
					and $approved_devbuilder.user_id=$feedback_table.rated_id and $approved_devbuilder.skill_id=$feedback_table.skill_id
					
					and $approved_devbuilder.pstatus='a' and $approved_devbuilder.user_id='$user1' and $approved_devbuilder.skill_id in $skill_set1
					
					and $feedback_table.status='1' ";
				
				$percentage1=$db_object->get_rsltset($mysql);
			
			for($j=0;$j<count($percentage1);$j++)
			{

				$percent1+=$percentage1[$j][per];
				
			}
				
				
			
				if(count($percentage1)>0)
				{
				
				$d_result1[$e]=$sql_result1[$a];

				$d_result1[$e][apercent]=$percent1/$i;
								
				$e++;
				
				$sql_result1[$a][apercent]=$percent1/$i;
				}
				
						
				}
		
				if($fApplication_activity1_c!="" and $fApplication_activity2_c !="")
				{
					$per1=$sql_result1[$a][apercent];

					if($per1<=$fApplication_activity2_c and $per1>=$fApplication_activity1_c)
					{
						
						$result1[$c]=$sql_result1[$a];
						
						$c++;
					}
				}
			
			}
		
				if($fApplication_activity1_c!="" and $fApplication_activity2_c!="")
				{
					
					$sql_result1=$result1;
				}
				else
				{
					
					$sql_result1=$d_result1;
				}
			
	

		//------------------------------------------------------------------------------
	
		$qry="select max(value) from $learning_result";
		
		$res_qry=$db_object->get_single_column($qry);
		
		$max=$res_qry[0];
			$result=array();
				
		$content = preg_replace("/<{ifimp_(.*?)}>/s","",$content);
			
			$res=array();

			$date13=$this->date_format($fDate13);

			$date14=$this->date_format($fDate14);
			
			for($i=0;$i<count($sql_result);$i++)
			{
				$skills=array();
				
				$c=0;
				
				$sql_result[$i][date13]=$date13;
				
				$sql_result[$i][date14]=$date14;
				
				$user=$sql_result[$i][user_id];
				
				$skills=$sql_result[$i][skill];
			
				if($skills[0]!="")
				{
					
					$skills_id=@implode(",",$skills);
					
					$skill="(".$skills_id.")";
					
					if($fCheck_plan_self1 and $fCheck_plan_boss1)
					{
						$imm_boss=$common->return_immediate_boss($db_object,$user);
						
					if($fDate13!="" and $fDate14!="")
					{
						
					$sql="select value from $feedback_table where rated_id='$user'
					
					and skill_id in $skill and rated_date between '$date13' and '$date14' and 
					
					rater_id in ('$user','$imm_boss')";
					}
					else
					{
						$sql="select value from $feedback_table where rated_id='$user'
					
					and skill_id in $skill and rater_id in ('$user','$imm_boss')";
					}
					$res=$db_object->get_single_column($sql);
					}
					
					if($fCheck_plan_self1 and !$fCheck_plan_self2)
					{
					if($fDate13!="" and $fDate14!="")
					{
					$sql="select value from $feedback_table where rated_id='$user'
					
					and skill_id in $skill and rated_date between '$date13' and '$date14'
					
					and rated_id=rater_id";
					}
					else
					{
						$sql="select value from $feedback_table where rated_id='$user'
					
					and skill_id in $skill and rated_id=rater_id";
					}
					
					$res=$db_object->get_single_column($sql);
					}
					if($fCheck_plan_boss1 and !$fCheck_plan_boss2)
					{
						
						$imm_boss=$common->immediate_boss($db_object,$user);
						
						
					if($fDate13!="" and $fDate14!="")
					{
		
						
					$sql="select value from $feedback_table where rated_id='$user'
					
					and skill_id in $skill and rated_date between '$date13' and '$date14' and rater_id='$imm_boss'";
					
					}
					else
					{
						$sql="select value from $feedback_table where rated_id='$user'
					
					and skill_id in $skill and rated_date between and rater_id='$imm_boss'";
					}
					
					$res=$db_object->get_single_column($sql);	
					}
					if(!$fCheck_plan_self1 and !$fCheck_plan_boss1)
					{
					$imm_boss=$common->immediate_boss($db_object,$user);
					
					if($fDate13!="" and $fDate14!="")
					{
						
					$sql="select value from $feedback_table where rated_id='$user'
					
					and skill_id in $skill and rated_date between '$date13' and '$date14'"; 
					}
					else
					{
						$sql="select value from $feedback_table where rated_id='$user'
					
					and skill_id in $skill"; 
					}
					
					$res=$db_object->get_single_column($sql);
					}
					
					
					$val=0;
					
					for($a=0;$a<count($res);$a++)
					{
						$val+=$res[$a];
					}
					if(count($res)>0)
					{
						$imp=($val/count($res));
						
						$imp=($imp*100)/$max;
						
						$i_result[$c]=$sql_result[$i];
						
						$i_result[$c][imp]=$imp;
						
						$sql_result[$i][imp]=$imp;
						
						$c++;
					}
					
										
				}
			}
		
			$b=0;
			
			if($fImprovement1!="" and $fImprovement2!="")
			{
				
				for($i=0;$i<count($sql_result);$i++)
				{
					$imp=$sql_result[$i][imp];
					
								
					if($imp<=$fImprovement2 and $imp>=$fImprovement1)
					{
						$result[$b]=$sql_result[$i];
						
						$b++;
					}
					
				}
			}
		
			if($fImprovement1!="" and $fImprovement2!="")
			{
				$sql_result=$result;
			}
			else
			{
				$sql_result=$i_result;
			}
			
	
		//-----------------------------------------------------------------------------------------
		
			
			
			$result1=array();
				
		$content = preg_replace("/<{ifimp1_(.*?)}>/s","",$content);
			
			$res1=array();

			$date13=$this->date_format($fDate13_c);

			$date14=$this->date_format($fDate14_c);
			
			for($i=0;$i<count($sql_result1);$i++)
			{
				$skills1=array();
				
				$c=0;
				
				$sql_result1[$i][date13]=$date13;
				
				$sql_result1[$i][date14]=$date14;
				
				$user1=$sql_result1[$i][user_id];
				
				$skills1=$sql_result1[$i][skill];
			
				if($skills1[0]!="")
				{
					
					$skills_id1=@implode(",",$skills1);
					
					$skill1="(".$skills_id1.")";
					
					if($fCheck_plan_self1_c and $fCheck_plan_boss1_c)
					{
						$imm_boss1=$common->return_immediate_boss($db_object,$user1);
						
					if($fDate13_c!="" and $fDate14_c!="")
					{
						
					$sql="select value from $feedback_table where rated_id='$user1'
					
					and skill_id in $skill1 and rated_date between '$date13' and '$date14' and 
					
					rater_id in ('$user1','$imm_boss1')";
					}
					else
					{
						$sql="select value from $feedback_table where rated_id='$user1'
					
					and skill_id in $skill1 and rater_id in ('$user1','$imm_boss1')";
					}
					
					$res1=$db_object->get_single_column($sql);
					}
					
					if($fCheck_plan_self1_c and !$fCheck_plan_self2_c)
					{
					if($fDate13_c!="" and $fDate14_c!="")
					{
		
						
					$sql="select value from $feedback_table where rated_id='$user1'
					
					and skill_id in $skill1 and rated_date between '$date13' and '$date14'
					
					and rated_id=rater_id";
					
					}
					else
					{
						$sql="select value from $feedback_table where rated_id='$user1'
					
					and skill_id in $skill1 and rated_id=rater_id";
					}
					
					$res1=$db_object->get_single_column($sql);
					}
					if($fCheck_plan_boss1_c and !$fCheck_plan_boss2_c)
					{
						
						$imm_boss1=$common->immediate_boss($db_object,$user1);
						
					if($fDate13_c!="" and $fDate14_c!="")
					{	
						
					$sql1="select value from $feedback_table where rated_id='$user1'
					
					and skill_id in $skill1 and rated_date between '$date13' and '$date14' and rater_id='$imm_boss1'";
					}
					else
					{
						$sql1="select value from $feedback_table where rated_id='$user1'
					
					and skill_id in $skill1 and rater_id='$imm_boss1'";
					}
					
					$res1=$db_object->get_single_column($sql);	
					}
					if(!$fCheck_plan_self1_c and !$fCheck_plan_boss1_c)
					{
					$imm_boss1=$common->immediate_boss($db_object,$user1);
					
					if($fDate13_c!="" and $fDate14_c!="")
					{
						
					$sql="select value from $feedback_table where rated_id='$user1'
					
					and skill_id in $skill1 and rated_date between '$date13' and '$date14'"; 
					}
					else
					{
					$sql="select value from $feedback_table where rated_id='$user1'
					
					and skill_id in $skill1"; 
						
					}
					
					$res1=$db_object->get_single_column($sql);
					}
					
					
					$val=0;
					
					for($a=0;$a<count($res1);$a++)
					{
						$val+=$res1[$a];
					}
					if(count($res1)>0)
					{

						$imp1=($val/count($res1));
						
						$imp1=($imp1*100)/$max;
						
						$i_result1[$c]=$sql_result1[$i];
						
						$i_result1[$c][imp]=$imp1;
						
						$sql_result1[$i][imp]=$imp1;
						
						$c++;
					}
					
					
				}
			}
		
			$b=0;
			
			if($fImprovement1_c!="" and $fImprovement2_c!="")
			{
				
				for($i=0;$i<count($sql_result1);$i++)
				{
					$imp1=$sql_result1[$i][imp];
					
								
					if($imp1<=$fImprovement2_c and $imp1>=$fImprovement1_c)
					{
						$result1[$b]=$sql_result1[$i];
						
						$b++;
					}
					
				}
			}
		
			if($fImprovement1_c!="" and $fImprovement2_c!="")
			{
				$sql_result1=$result1;
			}
			else
			{
				$sql_result1=$i_result1;
			}
			
		
}
		//-----------------------------------------------------------------------------------------
		
		if(count($sql_result)!=0 or count($sql_result1)!=0)
		{
		
		$content=$common->return_file_content($db_object,$xTemplate);
			
		$values["result_loop"]=$sql_result;
		
		$values["result1_loop"]=$sql_result1;

	
		$content=$common->simpleloopprocess($db_object,$content,$values);
		
		$content=$common->direct_replace($db_object,$content,$array);
		
		echo $content;
		
		}
		else
		{
			echo "no result available";
		}
	
	}
	
	
	function date_format($date)
	{
		$date1=@explode("/",$date);
		
		$date=$date1[2]."-".$date1[0]."-".$date1[1];
		
		return($date);
	}
	
	function get_userid($db_object,$sql_result)
	{

			for($i=0;$i<count($sql_result);$i++)
			{
				$user_set[$i]=$sql_result[$i][user_id];
				//$user_set[$i]=$sql_result[$i][0];
				
			}
		
			
			
				if(count($user_set)>0)
				{
					$users=@implode(",",$user_set);
				}
				else
				{
					$users="";
				}
				
				
				//echo "users=$users<br>";
				return($users);
	}

	



}


if($fShow1)
{
	//echo "here";
	$action="show";

}
if($fCompare1)
{
	$action="compare";
}

$obj=new compare_data_result();

$career=new career();

$position=new position();

switch($action)
{
	case "show":
	
	echo "<font color='red'><b>I want to know the format for displaying the results</b></font>";

	$obj->show_results($db_object,$common,$career,$position,$user_id,$gbl_grouprater_inter,$fJob_family,$fJob_family1,$fPosition,$fPosition1,$fLevel,$fLevel1,$fLocation,$fLocation1,$fOrganisation,$fOrganisation1,$fReport,$fReport1,$fIndemp,$fIndemp1,$fEmpstatus,$fEmpstatus1,$fEEO,$fEEO1,$fDate1,$fDate1_c,$fDate2,$fDate2_c,$fPer1,$fPer1_c,$fPer2,$fPer2_c,$gbl_met_value,$fWork_Per1,$fWork_Per1_c,$fWork_Per2,$fWork_Per2_c,$fDate3,$fDate4,$fDate3_c,$fDate4_c,$default,$fCheck_boss1,$fCheck_boss2,$fCheck_self1,$fCheck_self2,$fCheck_others1,$fCheck_others2,$fDate5,$fDate5_c,$fDate6,$fDate6_c,$fDate7,$fDate7_c,$fNo1,$fNo2,$fNo1_c,$fNo2_c,$fNo_app1,$fNo_app2,$fNo_app1_c,$fNo_app2_c,$fTech_skills,$fLabel_T,$fTech_skills_c,$fLabel_T_c,$fPer_skills,$fPer_skills_c,$fLabel_I,$fLabel_I_c,$fOver_used1,$fOver_used1_c,$fOver_used2,$fOver_used2_c,$fCareer_killer1,$fCareer_killer1_c,$fCareer_killer2,$fCareer_killer2_c,$fOver_rated1,$fOver_rated2,$fOver_rated1_c,$fOver_rated2_c,$fUnder_rated1,$fUnder_rated2,$fUnder_rated1_c,$fUnder_rated2_c,$fPosition_model,$fPosition_model_c,$fIMatch_same1,$fIMatch_same2,$fIMatch_same1_c,$fIMatch_same2_c,$fTMatch_same1,$fTMatch_same2,$fTMatch_same1_c,$fTMatch_same1_c,$fIMatch_up1,$fIMatch_up2,$fIMatch_up1_c,$fIMatch_up2_c,$fTMatch_up1,$fTMatch_up2,$fTMatch_up1_c,$fTMatch_up2_c,$fIMatch_up_2_1,$fMatch_up_2_2,$fIMatch_up_2_1_c,$fIMatch_up_2_2_c,$fTMatch_up_2_1,$fTMatch_up_2_2,$fTMatch_up_2_1_c,$fTMatch_up_2_2_c,$fSuccession_plan_1,$fSuccession_plan_1_c,$fSuccession_plan_2,$fSuccession_plan_2_c,$fSuccession_plan_hire,$fSuccession_plan_hire_c,$fISkills,$fISkills_c,$fTSkills,$fTSkills_c,$fDate9,$fDate10,$fDate9_c,$fDate10_c,$fLearning_activity1,$fLearning_activity2,$fLearning_activity1_c,$fLearning_activity2_c,$fApplication_activity1,$fApplication_activity2,$fApplication_activity1_c,$fApplication_activity2_c,$fDate11,$fDate12,$fDate11_c,$fDate12_c,$fDate13,$fDate14,$fDate13_c,$fDate14_c,$fCheck_plan_self1,$fCheck_plan_self2,$fCheck_plan_boss1,$fCheck_plan_boss2,$fCheck_plan_others1,$fCheck_plan_others2,$fImprovement1,$fImprovement2,$fImprovement1_c,$fImprovement2_c,$fCareer_down,$fCareer_down_c,$fCareer_same,$fCareer_same_c,$fCareer_up_1,$fCareer_up_1_c,$fCareer_up_2,$fCareer_up_2_c);
	
	break;
	
	case "compare":
	
	echo "<font color='red'><b>I want to know the format for displaying the results</b></font>";
	
	//$obj->compare($db_object,$common,$fJob_family,$fJob_family1,$fPosition,$fPosition1,$fLevel,$fLevel1,$fLocation,$fLocation1,$fOrganisation,$fOrganisation1,$fReport,$fReport1,$fIndemp,$fIndemp1,$fEmpstatus,$fEmpstatus1,$fEEO,$fEEO1,$fDate1,$fDate1_c,$fDate2,$fDate2_c,$fPer1,$fPer1_c,$fPer2,$fPer2_c,$gbl_met_value,$fWork_Per1,$fWork_Per1_c,$fWork_Per2,$fWork_Per2_c,$default,$fCheck_boss1,$fCheck_boss2,$fCheck_self1,$fCheck_self2,$fCheck_others1,$fCheck_others2);
	
	break;
}

include_once("footer.php");
?>
