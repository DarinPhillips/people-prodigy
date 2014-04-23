<?php
	include("../session.php");
	include("header.php");

class summary

{	

	function display($db_object,$common,$user_id,$default,$error_msg,$learning,$post_var)
	{

		$solution_table	=$common->prefix_table("assign_solution_builder");
		$result_table 		=$common->prefix_table("learning_result");
		$skill_table 		=$common->prefix_table("skills");
		$plan_table		=$common->prefix_table("approved_devbuilder");
		$feedback_table	=$common->prefix_table("learning_feedback_results");
		$dev_interbasic	=$common->prefix_table("dev_interbasic");
		$dev_basic		=$common->prefix_table("dev_basic");
		$user_table		=$common->prefix_table("user_table");
		
		$path		=$common->path;
	//read html template
		$template		=$path."/templates/learning/progress_summary.html";
		$returncontent	=$common->return_file_content($db_object,$template);


	//read text template
		$flname = $path."templates/learning/progress_summary.txt";
		$return_text = $common->return_file_content($db_object,$flname);
		$open = "report/progress_summary_report_$user_id.txt";	
		$fp=fopen("$open","w");

		preg_match("/<{skill_loopstart}>(.*?)<{skill_loopend}>/s",$returncontent,$match1);
		$skillcon = $match1[1];
	//text
		preg_match("/<{skill_loopstart}>(.*?)<{skill_loopend}>/s",$return_text,$match1_text);
		$skillcon_text = $match1_text[1];


		preg_match("/<{comment_loopstart}>(.*?)<{comment_loopend}>/s",$returncontent,$match2);
		$commentcon = $match2[1];

	//text
		preg_match("/<{comment_loopstart}>(.*?)<{comment_loopend}>/s",$return_text,$match2_text);
		$commentcon_text = $match2_text[1];
				
		$pattern_rating = "/<{ratings_loopstart}>(.*?)<{ratings_loopend}>/s";
		preg_match($pattern_rating,$returncontent,$arr3);
		$match3 = $arr3[1];
	//text
		preg_match($pattern_rating,$return_text,$arr3_text);
		$match3_text = $arr3_text[1];

		$pattern_user = "/<{user_loopstart}>(.*?)<{user_loopend}>/s";
		preg_match($pattern_user,$returncontent,$arr4);
		$match4 = $arr4[1];
	
	//text
		preg_match($pattern_user,$return_text,$arr4_text);
		$match4_text = $arr4_text[1];
		
		$pattern_user1 = "/<{user_loopstart1}>(.*?)<{user_loopend1}>/s";
		preg_match($pattern_user1,$returncontent,$arr5);
		$match5 = $arr5[1];
	//text
		preg_match($pattern_user1,$return_text,$arr5_text);
		$match5_text = $arr5_text[1];

		$pattern_user2 = "/<{user_loopstart2}>(.*?)<{user_loopend2}>/s";
		preg_match($pattern_user2,$returncontent,$arr6);
		$match6 = $arr6[1];
	//text
		preg_match($pattern_user2,$return_text,$arr6_text);
		$match6_text = $arr6_text[1];
			
		$pattern_view_display = "/<{view_rating_for_loopstart}>(.*?)<{view_rating_for_loopend}>/s";

//array declaration
	
		$ratingarr = array();
		$ratingarr1 = array();
		$ratingarr2 = array();
		while(list($kk,$vv)=@each($post_var))
		{
			$$kk = $vv;
			if(ereg("^rating_",$kk))
			{
				list($rating,$skill)=split("_",$kk);
				if($vv != "" )
				{
					$commentarr[$skill]=$vv;
				}
			}
			if(ereg("^fSinceRatingdate_",$kk))
			{
				list($srating,$skill,$buildid)=split("_",$kk);
				if($vv != "" )
				{
					$sinceratingarr[$skill]=$vv;
				}
			}
			if(ereg("^fFromRatingdate_",$kk))
			{
				list($fromrating,$skill,$buildid)=split("_",$kk);
				if($vv != "" )
				{
					$fromratingarr[$skill]=$vv;
				}
			}
			if(ereg("^fToRatingdate_",$kk))
			{
				list($torating,$skill,$buildid)=split("_",$kk);
				if($vv != "" )
				{
					$toratingarr[$skill]=$vv;
				}
			}
			if(ereg("^update_",$kk))
			{
				list($update,$skill)=split("_",$kk);
				if($vv != "" )
				{
					$resultarr[$skill]=$vv;
				}
			}
			if(ereg("^fSinceUpdatedate_",$kk))
			{
				list($supdate,$skill,$buildid)=split("_",$kk);
				if($vv != "" )
				{
					$sinceupdatearr[$skill]=$vv;
				}
			}
			if(ereg("^fFromUpdatedate_",$kk))
			{
				list($fromupdate,$skill,$buildid)=split("_",$kk);
				if($vv != "" )
				{
					$fromupdatearr[$skill]=$vv;
				}
			}
			if(ereg("^fToUpdatedate_",$kk))
			{
				list($toupdate,$skill,$buildid)=split("_",$kk);
				if($vv != "" )
				{
					$toupdatearr[$skill]=$vv;
				}
			}
			if(ereg("^ratingsby_",$kk))
			{
				list($ratingsby,$usertype,$skill)=split("_",$kk);
				if($vv != "" )
				{
					$ratingsbyarr[$skill][]=$vv;
				}
			}
			if(ereg("ratingcomp_1",$kk))
			{

				list($ratings,$usertype,$what,$skill) = split("_",$kk);
				if($vv !="")
				{
					$ratingsbyarr1[$skill][] = $vv;
				}
			}			

			if(ereg("ratingcomp_2",$kk))
			{

				list($ratings,$usertype,$what,$skill) = split("_",$kk);
				if($vv !="")
				{
					$ratingsbyarr2[$skill][] = $vv;
				}
			}			
			if(ereg("^ratingsby_boss_",$kk))
			{				
				$ratingarr[] = $kk;
			}
			if(ereg("^ratingsby_others_",$kk))
			{
				$ratingarr[] = $kk;
			}
			if(ereg("^ratingsby_self_",$kk))
			{
				$ratingarr[] = $kk;
			}
			if(ereg("^ratingsby_avg_",$kk))
			{
				$ratingarr[] = $kk;
			}
			if(ereg("^ratingcomp_1_boss_",$kk))
			{
				$ratingarr1[] = $kk;
			}
			if(ereg("^ratingcomp_1_others_",$kk))
			{
				$ratingarr1[] = $kk;
			}
			if(ereg("^ratingcomp_1_self_",$kk))
			{
				$ratingarr1[] = $kk;
			}
			if(ereg("^ratingcomp_1_avg_",$kk))
			{
				$ratingarr1[] = $kk;
			}


			if(ereg("^ratingcomp_2_boss_",$kk))
			{
				$ratingarr2[] = $kk;
			}
			if(ereg("^ratingcomp_2_others_",$kk))
			{
				$ratingarr2[] = $kk;
			}
			if(ereg("^ratingcomp_2_self_",$kk))
			{
				$ratingarr2[] = $kk;
			}
			if(ereg("^ratingcomp_2_avg_",$kk))
			{
				$ratingarr2[] = $kk;
			}
									
		}
//check for direct reports
		if($dr_id!="")
		{
			$user_id=$dr_id;
		}
		$username=$common->name_display($db_object,$user_id);
		/*$mysql="select skill_name,$feedback_table.skill_id,rated_id,rater_id,skill_description from $feedback_table,
					$plan_table,$skill_table where $feedback_table.rated_id=$plan_table.user_id 
					and $feedback_table.skill_id=$plan_table.skill_id and $feedback_table.status='1'
					and rated_id='$user_id' and $plan_table.pstatus='a' group by $feedback_table.skill_id";*/
		
		$mysql="select skill_name,$feedback_table.skill_id,rated_id,rater_id,skill_description from $feedback_table,
					$plan_table,$skill_table where $feedback_table.rated_id=$plan_table.user_id 
					and $feedback_table.skill_id=$plan_table.skill_id 
					and $feedback_table.skill_id=$skill_table.skill_id and $feedback_table.status='1'
					and rated_id='$user_id' and $plan_table.pstatus='a' group by $feedback_table.skill_id";
		$dFeed= $db_object->get_rsltset($mysql);
		
		for($i=0;$i<count($dFeed);$i++)
		{

				$skill_name		=$dFeed[$i]["skill_name"];
				$skills			=$dFeed[$i]["skill_id"];
				$skill_description	=$dFeed[$i]["skill_description"];
				$commentrepl = "";
				
				$filecontent=str_replace("<{view_mode}>",$view_mode,$filecontent);
	//------------------ For view rater comments loop-----------------	
				
				$Rvalue  			= $commentarr[$skills];
				$Rfrom_date		= $learning->changedate_database($fromratingarr[$skills]);
				$Rto_date			= $learning->changedate_database($toratingarr[$skills]);
				$Rsince_date		= $learning->changedate_database($sinceratingarr[$skills]);
				
				$cmmtqry	= $this->extraquery($db_object,$common,$skills,$Rfrom_date,$Rto_date,$Rsince_date,$user_id,$Rvalue);
				
				$mysql 	= "select username,rater_id,rated_date,feedback_text from $feedback_table,$user_table where 
					$user_table.user_id=$feedback_table.rater_id and $feedback_table.status='1' and 
					$feedback_table.rated_id='$user_id' and $feedback_table.skill_id='$skills' $cmmtqry";
				$dComment= $db_object->get_rsltset($mysql);

			
				if((count($commentarr) == "0") || ($dComment[0][0] == "") || (!array_key_exists($skills, $commentarr)) )
				{
					$skillcon1 =preg_replace("/<{comment_start}>(.*?)<{comment_end}>/s","",$skillcon); 					
					//text
					$skillcon1_text =preg_replace("/<{comment_start}>(.*?)<{comment_end}>/s","",$skillcon_text);
				}
				else
				{
					for($j=0;$j<count($dComment);$j++)
					{
						$rater_name	=$dComment[$j]['username'];
						$feedback_text =$dComment[$j]['feedback_text'];
						$rater_id		=$dComment[$j]['rater_id'];
				
						if($rater_id==$user_id)
						{
							$rater_name=$error_msg['cSelf'];
						}
						$commentrepl.=preg_replace("/<{(.*?)}>/e","$$1",$commentcon);
					//text
						$commentrepl_text.=preg_replace("/<{(.*?)}>/e","$$1",$commentcon_text);
					}

					$skillcon1 =preg_replace("/<{comment_loopstart}>(.*?)<{comment_loopend}>/s",$commentrepl,$skillcon);
				//text
					$skillcon1_text =preg_replace("/<{comment_loopstart}>(.*?)<{comment_loopend}>/s",$commentrepl_text,$skillcon_text);  				
				}
		//------------------ For view results loop (Timely Completed Activities)-----------------
				
				$Uvalue  			= $resultarr[$skills];
				$Ufrom_date		= $learning->changedate_database($fromupdatearr[$skills]);
				$Uto_date			= $learning->changedate_database($toupdatearr[$skills]);
				$Usince_date		= $learning->changedate_database($sinceupdatearr[$skills]);
				
				$rsltqry	= $this->extraquery($db_object,$common,$skills,$Ufrom_date,$Uto_date,$Usince_date,$user_id,$Uvalue);
				$mysql 	= "select cdate,completed_date,plan_approved_date,

								(((to_days(cdate) -

 to_days(plan_approved_date))/(to_days(completed_date) - to_days(plan_approved_date)))*100) 

as per
								from $plan_table,$solution_table,$feedback_table where 
								$plan_table.user_id=$solution_table.user_id and $plan_table.skill_id=$solution_table.skill_id
								and $plan_table.user_id=$feedback_table.rated_id and $plan_table.skill_id=$feedback_table.skill_id
								and $plan_table.pstatus='a' and $plan_table.user_id='$user_id' and $plan_table.skill_id='$skills'
								and $feedback_table.status='1' $rsltqry ";	
				$dResult  =$db_object->get_rsltset($mysql);



				
				$mysql 	= "select	max(completed_date) as maxdate,plan_approved_date from $plan_table,$feedback_table,
									$solution_table where $plan_table.user_id=$feedback_table.rated_id and 
									$plan_table.skill_id=$feedback_table.skill_id and $plan_table.user_id=$solution_table.user_id 
									and $plan_table.skill_id=$solution_table.skill_id and $plan_table.pstatus='a' and
									 $plan_table.user_id='$user_id' and $plan_table.skill_id='$skills'
									and $feedback_table.status='1' $rsltqry group by $plan_table.skill_id";	
				$dMax  	=$db_object->get_a_line($mysql);

				$to_date 	= $dMax['maxdate'];
				$from_date = $dMax['plan_approved_date'];
				
					$mysql 	 = "select max(rated_date) as rateddate,plan_approved_date from $plan_table,$feedback_table,
									$solution_table where $plan_table.user_id=$feedback_table.rated_id and 
									$plan_table.skill_id=$feedback_table.skill_id and $plan_table.user_id=$solution_table.user_id 
									and $plan_table.skill_id=$solution_table.skill_id and $plan_table.pstatus='a' and
									$plan_table.user_id='$user_id' and $plan_table.skill_id='$skills'
									and $feedback_table.status='1' $rsltqry group by $plan_table.skill_id";	
					$dRated  	 =$db_object->get_a_line($mysql);
		
					$last_date = $dRated['rateddate'];
					
					if((@array_key_exists($skills,$fromupdatearr)) && (@array_key_exists($skills,$toupdatearr)) )
					{
						$from_date = $Ufrom_date;
						$last_date  = $Uto_date;
						$to_date  = $Uto_date;
					
					}
					if(@array_key_exists($skills,$sinceupdatearr))
					{
						$from_date = $Usince_date;
						$last_date = $dRated['rateddate'];
						$to_date 	 = $dMax['maxdate'];
					}

				if((count($resultarr) == "0") || ($dResult[0][0] == "")  || (!array_key_exists($skills, $resultarr)) )
				{
					$skillcon2 =preg_replace("/<{result_start}>(.*?)<{result_end}>/s","",$skillcon1);
				//text
					$skillcon2_text =preg_replace("/<{result_start}>(.*?)<{result_end}>/s","",$skillcon1_text); 					
				}
				else
				{
					$count = '0';
					$totalper='0';
					$cnt=count($dResult);
					$totalper='0';
					for($k=0;$k<count($dResult);$k++)
					{
						$per	= $dResult[$k]['per'];	
						if( $per != "" )
						{
							$count=$count+1;	
							$totalper=$totalper+$per;
						}

					}

					if($count != '0')
					{
						$avg=$totalper/$count;
					}
					else
					{
						$avg = '0';	
					}
					$avg_round=round($avg,2);

					$mysql	= "SELECT date_format(rated_date,'%Y-%m-%d') as date,value,rater_id FROM $feedback_table WHERE rated_id='$user_id' AND skill_id='$skills' 
					and date_format(rated_date,'%Y-%m-%d') between '$from_date' and '$to_date' ORDER BY rated_date";
					$dFeedback= $db_object->get_rsltset($mysql);									

					$skll="";
					$skll="";
					for($c=0;$c<count($dFeedback);$c++)
					{
						$rated_date	= $dFeedback[$c]['date'];
						$rated_date = $learning->changedate_display($rated_date);
						$val	= $dFeedback[$c]['value'];
						$val_qry = "select result_$default from $result_table where value='$val'";
						$val_res = $db_object->get_a_line($val_qry);
						$valu = $val_res[0];
						$value = preg_replace("/<{(.*?)}>/e","$$1",$valu);
						$rater_id = $dFeedback[$c]['rater_id'];
						$rater_name = $common->name_display($db_object,$rater_id);
						$skll .= preg_replace("/<{(.*?)}>/e","$$1",$match3);
						$skll_text .= preg_replace("/<{(.*?)}>/e","$$1",$match3_text);
						
					}

					$skillcon1 = preg_replace($pattern_rating,$skll,$skillcon1);

					$skillcon2 = preg_replace("/<{result_start}><{(.*?)}><{result_end}>/s","$$1",$skillcon1); 				
				//text
					$skillcon1_text = preg_replace($pattern_rating,$skll_text,$skillcon1_text);

					$skillcon2_text = preg_replace("/<{result_start}><{(.*?)}><{result_end}>/s","$$1",$skillcon1_text); 				

				}
					
			//------------For ratingsby loop----------------------				
	

		//-------------View rating starts---------
				
			$rating_chk = count($ratingarr);
			$rating_val = array();
			$boss_fl=0;
			$other_fl = 0;
			$self_fl=0;
			$avg_fl=0;
			if($rating_chk >0)
			{
				$avg_key = "ratingsby_avg_".$skills;
				if(@in_array($avg_key,$ratingarr))
				{
					$avg_fl =1;
				}
				
				$bosskey = "ratingsby_boss_".$skills;
				if(@in_array($bosskey,$ratingarr))
				{
					$boss_fl=1;	
				}
				if(($boss_fl=='1')||($avg_fl=='1'))
				{
					$rating_val[] = $common->immediate_boss($db_object,$user_id);
				}

				$others_key = "ratingsby_others_".$skills;
				if(@in_array($others_key,$ratingarr))
				{
					$other_fl = 1;
				}
												
				if(($other_fl=='1')||($avg_fl=='1'))
				{
					$rating_val_qry = "select url from $plan_table where 
								basic_id='5' and interbasic_id='14' and	
								skill_id='$skills' and user_id='$user_id'";
					//echo $rating_val_qry;
					$rating_val_res = $db_object->get_single_column($rating_val_qry);
					
					for($v=0;$v<count($rating_val_res);$v++)
					{
						$rating_val[] = $rating_val_res[$v];
					}//v loop

				}
				$self_key = "ratingsby_self_".$skills;
				if(@in_array($self_key,$ratingarr))
				{
					$self_fl=1;
				}

				if(($self_fl=='1')||($avg_fl=='1'))
				{
					$rating_val[] = $user_id;
				}
								
				$rating_ids = @implode("','",$rating_val);
				
				if($rating_ids!="")
				{
					$final_qry = "select rater_id,value,date_format(rated_date,'%Y-%m-%d') as date,rater_id
					from $feedback_table where rater_id in ('$rating_ids') and 
					rated_id='$user_id' and skill_id='$skills'";					
					$final_res = $db_object->get_rsltset($final_qry);
				}
				
			}
			$str="";
			$str_text="";
			$post_id = array();
			for($f=0;$f<count($final_res);$f++)
			{
				$dates = $final_res[$f]['date'];
				$dates = $learning->changedate_display($dates);
				$rid = $final_res[$f]['rater_id'];
				$post_id[$rid] = $dates;
				$val = $final_res[$f]['value'];
				$val_qry = "select result_$default from $result_table where value='$val'";
				$val_res = $db_object->get_a_line($val_qry);
				$valu = $val_res[0];
				$value = preg_replace("/<{(.*?)}>/e","$$1",$valu);
				$rater_name = $common->name_display($db_object,$rid);
				$str .= preg_replace("/<{(.*?)}>/e","$$1",$match4);
			//text
				$str_text .= preg_replace("/<{(.*?)}>/e","$$1",$match4_text);
				
			}//f loop	

				$skillcon3 = preg_replace($pattern_user,$str,$skillcon2);
			//text			
				$skillcon3_text = preg_replace($pattern_user,$str_text,$skillcon2_text);
					
				$pattern_dis_view_rating = "/<{display_view_rating_start}>(.*?)<{display_view_rating_end}>/s";

				preg_match($pattern_dis_view_rating,$returncontent,$ar);
				$tr_match = $ar[1];
			
			//text
				preg_match($pattern_dis_view_rating,$returncontent_text,$ar_text);
				$tr_match_text = $ar_text[1];

				if(($boss_fl==0)&&($other_fl==0)&&($self_fl==0)&&($avg_fl==0))
				{	
					$skillcon3 = preg_replace($pattern_dis_view_rating,"",$skillcon3);
				//text
					$skillcon3_text = preg_replace($pattern_dis_view_rating,"",$skillcon3_text);
				}


				if((count($ratingsbyarr) == "0")  || (!array_key_exists($skills, $ratingsbyarr)) )
				{
					$skillcon3 =preg_replace("/<{ratingsby_start}>(.*?)<{ratingsby_end}>/s","",$skillcon3);
				//text
					$skillcon3_text =preg_replace("/<{ratingsby_start}>(.*?)<{ratingsby_end}>/s","",$skillcon3_text); 					
				}
				else
				{	
						$mysql 	 = "select max(rated_date) as rateddate,plan_approved_date from $plan_table,$feedback_table,
									$solution_table where $plan_table.user_id=$feedback_table.rated_id and 
									$plan_table.skill_id=$feedback_table.skill_id and $plan_table.user_id=$solution_table.user_id 
									and $plan_table.skill_id=$solution_table.skill_id and $plan_table.pstatus='a' and
									$plan_table.user_id='$user_id' and $plan_table.skill_id='$skills'
									and $feedback_table.status='1' group by $plan_table.skill_id";	

						$dRated  	 =$db_object->get_a_line($mysql);
						$rated_date =  $dRated['rateddate'];
						$approved_date = $dRated['plan_approved_date'];

					for($k=0;$k<4;$k++)
					{
						if($ratingsbyarr[$skills][$k])
						{
							$ratingsarr[]=$ratingsbyarr[$skills][$k];
						}
					}
					$raterid=@implode(",",$ratingsarr);
					
					$skillcon3 = preg_replace("/<{ratingsby_start}><{(.*?)}><{ratingsby_end}>/s","$$1",$skillcon3);
				//text 				
					$skillcon3_text = preg_replace("/<{ratingsby_start}><{(.*?)}><{ratingsby_end}>/s","$$1",$skillcon3_text);
				}




				

		//-------------View rating ends

		//-----------Compare Ratings starts----------------
		//-----------Rater1

		$rating_chk1 = count($ratingarr1);
		$rating_val1 = array();
		$boss_fl1=0;
		$other_fl1 = 0;
		$self_fl1=0;
		$avg_fl1=0;	
		if($rating_chk1 > 0)
		{

			$avg_key1 = "ratingcomp_1_avg_".$skills;
			if(@in_array($avg_key1,$ratingarr1))
			{
				$avg_fl1 = 1;
			}

			$boss_key1 = "ratingcomp_1_boss_".$skills;
			if(@in_array($boss_key1,$ratingarr1))
			{
				$boss_fl1 = 1;
			}

			if(($boss_fl1=='1')||($avg_fl1=='1'))
			{
				$rating_val1[] = $common->immediate_boss($db_object,$user_id);
			}

			$other_key1 = "ratingcomp_1_others_".$skills;
			if(@in_array($other_key1,$ratingarr1))
			{
				$other_fl1 =1;
			}

			if(($other_fl1=='1')||($avg_fl1=='1'))
			{
				$rating_val_qry1 = "select url from $plan_table where 
							basic_id='5' and interbasic_id='14' and	
							skill_id='$skills' and user_id='$user_id'";
				//echo $rating_val_qry;
				$rating_val_res1 = $db_object->get_single_column($rating_val_qry1);				
				for($x=0;$x<count($rating_val_res1);$x++)
				{
					$rating_val1[] = $rating_val_res1[$x];
				}//v loop
			}
			
			$self_key1 = "ratingcomp_1_self_".$skills;
			if(@in_array($self_key1,$ratingarr1))
			{
				$self_fl1 = 1;
			}

			if(($self_fl1=='1')||($avg_fl1=='1'))
			{
				$rating_val1[] = $user_id;
			}

			$rating_ids1 = @implode("','",$rating_val1);
			if($rating_ids1!="")
			{
				$final_qry1 = "select rater_id,value,date_format(rated_date,'%Y-%m-%d') as date,rater_id
				from $feedback_table where rater_id in ('$rating_ids1') and 
				rated_id='$user_id' and skill_id='$skills'";				
				$final_res1 = $db_object->get_rsltset($final_qry1);
			}
		}//chk loop
			$str1="";
			$str1_text="";
			for($o=0;$o<count($final_res1);$o++)
			{
				$dates1 = $final_res1[$o]['date'];
				$dates1 = $learning->changedate_display($dates1);
				$rid1 = $final_res1[$o]['rater_id'];				
				$val1 = $final_res1[$o]['value'];
				$val_qry = "select result_$default from $result_table where value='$val1'";
				$val_res = $db_object->get_a_line($val_qry);
				$valu1 = $val_res[0];
				$value1 = preg_replace("/<{(.*?)}>/e","$$1",$valu1);
				$rater_name1 = $common->name_display($db_object,$rid1);					
				$str1 .= preg_replace("/<{(.*?)}>/e","$$1",$match5);
			//text
				$str1_text .= preg_replace("/<{(.*?)}>/e","$$1",$match5_text);
				
			}//o loop	
			
			$skillcon3 = preg_replace($pattern_user1,$str1,$skillcon3);
		//text
			$skillcon3_text = preg_replace($pattern_user1,$str1_text,$skillcon3_text);

//-------------------compare rating graph --------------
				$mysql 	 = "select max(rated_date) as rateddate,plan_approved_date from $plan_table,$feedback_table,
					$solution_table where $plan_table.user_id=$feedback_table.rated_id and 
					$plan_table.skill_id=$feedback_table.skill_id and $plan_table.user_id=$solution_table.user_id 
					and $plan_table.skill_id=$solution_table.skill_id and $plan_table.pstatus='a' and
					$plan_table.user_id='$user_id' and $plan_table.skill_id='$skills'
					and $feedback_table.status='1' group by $plan_table.skill_id";	
				$dRated  	 =$db_object->get_a_line($mysql);
				$rated_date1 =  $dRated['rateddate'];
				$approved_date1 = $dRated['plan_approved_date'];


				for($u=0;$u<4;$u++)
				{
					if($ratingsbyarr1[$skills][$u])
					{
						$ratingsarrall[]=$ratingsbyarr1[$skills][$u];
					}
				}
		//-------Rater2
		$rating_chk2 = count($ratingarr2);
		$rating_val2 = array();
		$boss_fl2=0;
		$other_fl2 = 0;
		$self_fl2=0;
		$avg_fl2=0;	
		if($rating_chk2 > 0)
		{

			$avg_key2 = "ratingcomp_2_avg_".$skills;
			if(@in_array($avg_key2,$ratingarr2))
			{
				$avg_fl2 = 1;
			}

			$boss_key2 = "ratingcomp_2_boss_".$skills;
			if(@in_array($boss_key2,$ratingarr2))
			{
				$boss_fl2 = 1;
			}

			if(($boss_fl2=='1')||($avg_fl2=='1'))
			{
				$rating_val2[] = $common->immediate_boss($db_object,$user_id);
			}

			$other_key2 = "ratingcomp_2_others_".$skills;
			if(@in_array($other_key2,$ratingarr2))
			{
				$other_fl2 =1;
			}

			if(($other_fl2=='1')||($avg_fl2=='1'))
			{
				$rating_val_qry2 = "select url from $plan_table where 
							basic_id='5' and interbasic_id='14' and	
							skill_id='$skills' and user_id='$user_id'";
				//echo $rating_val_qry;
				$rating_val_res2 = $db_object->get_single_column($rating_val_qry2);				
				for($y=0;$y<count($rating_val_res2);$y++)
				{
					$rating_val2[] = $rating_val_res2[$y];
				}//y loop
			}
			
			$self_key2 = "ratingcomp_2_self_".$skills;
			if(@in_array($self_key2,$ratingarr2))
			{
				$self_fl2 = 1;
			}

			if(($self_fl2=='1')||($avg_fl2=='1'))
			{
				$rating_val2[] = $user_id;
			}

			$rating_ids2 = @implode("','",$rating_val2);

			if($rating_ids2!="")
			{
				$final_qry2 = "select rater_id,value,date_format(rated_date,'%Y-%m-%d') as date,rater_id
				from $feedback_table where rater_id in ('$rating_ids2') and 
				rated_id='$user_id' and skill_id='$skills'";					
				$final_res2 = $db_object->get_rsltset($final_qry2);
			}
		}//chk loop
			$str2="";
			$str2_text="";
			for($z=0;$z<count($final_res2);$z++)
			{
				$dates2 = $final_res2[$z]['date'];
				$dates2 = $learning->changedate_display($dates2);
				$rid2 = $final_res2[$z]['rater_id'];
				$val2 = $final_res2[$z]['value'];
				$val_qry = "select result_$default from $result_table where value='$val2'";
				$val_res = $db_object->get_a_line($val_qry);
				$valu2 = $val_res[0];
				$value2 = preg_replace("/<{(.*?)}>/e","$$1",$valu2);
				$rater_name2 = $common->name_display($db_object,$rid2);
				$str2 .= preg_replace("/<{(.*?)}>/e","$$1",$match6);
			//text
				$str2_text .= preg_replace("/<{(.*?)}>/e","$$1",$match6_text);
				
			}//o loop	

			$skillcon3 = preg_replace($pattern_user2,$str2,$skillcon3);
			//text
			$skillcon3_text = preg_replace($pattern_user2,$str2_text,$skillcon3_text);

			if(($str1=="")&&($str2==""))
			{
				$skillcon3 =preg_replace("/<{rating_compare_start}>(.*?)<{rating_compare_end}>/s","",$skillcon3); 					
				//text
				$skillcon3_text =preg_replace("/<{rating_compare_start}>(.*?)<{rating_compare_end}>/s","",$skillcon3_text);
			}
			else
			{
				
				for($u=0;$u<4;$u++)
				{
					if($ratingsbyarr2[$skills][$u])
					{
						$ratingsarrall[]=$ratingsbyarr2[$skills][$u];
					}
				}
				
				$sel_rater = @array_unique($ratingsarrall);
				$raterid1 = @join(",",$sel_rater);
				$skillcon3 = preg_replace("/<{rating_compare_start}><{(.*?)}><{rating_compare_end}>/s","$$1",$skillcon3);
				//text
				$skillcon3_text = preg_replace("/<{rating_compare_start}><{(.*?)}><{rating_compare_end}>/s","$$1",$skillcon3_text);
				
			}
			$fg1 = 0;
			$fg2 = 0;
		if(($boss_fl1==0)&&($other_fl1==0)&&($self_fl1==0)&&($avg_fl1==0))
		{
			$fg1=1;
		}
		if(($boss_fl2==0)&&($other_fl2==0)&&($self_fl2==0)&&($avg_fl2==0))
		{
			$fg2=1;
		}
		
		$pattern_compare = "/<{compare_display_start}>(.*?)<{compare_display_end}>/s";
		if(($fg1==1)&&($fg2==1))
		{
			$skillcon3 = preg_replace($pattern_compare,"",$skillcon3);
			//text
			$skillcon3_text = preg_replace($pattern_compare,"",$skillcon3_text);
		}
		



		//----------Compare Ratings ends

		//---------This is to display graph----

		$graph = "viewgraphical_".$skills;
		$gr_pattern = "/<{graph_loopstart}>(.*?)<{graph_loopend}>/s";
		$nongr_pattern ="/<{nongraph_loopstart}>(.*)<{nongraph_loopend}>/s";
		if($$graph=="")
		{
			$skillcon3 = preg_replace($gr_pattern,"",$skillcon3);
		//text
			$skillcon3_text= preg_replace($gr_pattern,"",$skillcon3_text);
		}
		else
		{
			$skillcon3 = preg_replace($nongr_pattern,"",$skillcon3);
			//text
			$skillcon3_text = preg_replace($nongr_pattern,"",$skillcon3_text);
		}

		//---------view graph ends----

				$skillrepl.=preg_replace("/<{(.*?)}>/e","$$1",$skillcon3);
				//text
				$skillrepl_text.=preg_replace("/<{(.*?)}>/e","$$1",$skillcon3_text);
		}

		$returncontent =preg_replace("/<{skill_loopstart}>(.*?)<{skill_loopend}>/s",$skillrepl,$returncontent); 		

		//text
		$return_text =preg_replace("/<{skill_loopstart}>(.*?)<{skill_loopend}>/s",$skillrepl_text,$return_text);

		$array['username']=$username;
		$array['dr_id'] = $dr_id;
		$returncontent=$common->direct_replace($db_object,$returncontent,$array);
		echo $returncontent;

		$return_text=$common->direct_replace($db_object,$return_text,$array);
		fwrite($fp,$return_text); 
		fclose ($fp);	
	}

//------------------------------------------------
	
	function extraquery($db_object,$common,$skill_id,$from_date,$to_date,$since_date,$rated_id,$value)
	{
		$feedback_table	=$common->prefix_table("learning_feedback_results");
		switch($value)
		{
			case 1:
				 {
				 	$mysql = "select max(rated_date) as maxdate from $feedback_table where
				 				rated_id='$rated_id' and skill_id='$skill_id'";
					$dLastdate=$db_object->get_a_line($mysql);
					$lastdate = $dLastdate['maxdate'];
					$extraqry = " and rated_date = '$lastdate' ";
					break;
				 }
			case 2:
				 {
				 	$extraqry = " and rated_date > '$since_date' ";
					break;
				 }
			case 3:
				 {
				 	$extraqry = " and rated_date between '$from_date' and '$to_date' ";
					break;
				 }
			case 4:
				{
					$extraqry = "";
					break;
				}
		}
			
		return $extraqry;
		 	 
	}
	

	
	
}
	$obj=new summary;

	$obj->display($db_object,$common,$user_id,$default,$error_msg,$learning,$post_var);

	include("footer.php");
?>

