<?php

include_once("../session.php");

include_once("header.php");

class inter
{
	function position_model($db_object,$common,$user_id,$default)
	{
		$path=$common->path;
		
		$xtemplate=$path."templates/career/employee_report_inter.html";
		
		$file=$common->return_file_content($db_object,$xtemplate);
		
		$model_factor_1=$common->prefix_table("model_factors_1");
		
		$model_name_table=$common->prefix_table("model_name_table");
		
		$user_table=$common->prefix_table("user_table");
		
		$family_position=$common->prefix_table("family_position");
				
		$sql="select position from $user_table where user_id='$user_id'";
		
		$sql_res=$db_object->get_a_line($sql);
		
		$pos=$sql_res[position];
			
		$sql="select family_id from $family_position where position_id='$pos'";
		
		$res=$db_object->get_single_column($sql);

		if(count($res)>0)
		{
			$fam=@implode(",",$res);
			
			$family_ids="(".$fam.")";
			
			$sql="select $model_factor_1.model_id,model_name from $model_name_table,$model_factor_1 where 
			
			$model_name_table.model_id=$model_factor_1.model_id and family in $family_ids group by $model_factor_1.model_id";
			
			$sql_res=$db_object->get_rsltset($sql);

			preg_match("/<{model_loopstart}>(.*?)<{model_loopend}>/s",$file,$match);
			
			$match=$match[0];
			
			for ($i=0;$i<count($sql_res);$i++)
			{
				$model_id=$sql_res[$i][model_id];
				
				$model_name=$sql_res[$i][model_name];
				
				$str.=preg_replace("/<{(.*?)}>/e","$$1",$match);
			}
			$file=preg_replace("/<{model_loopstart}>(.*?)<{model_loopend}>/s",$str,$file);
			
			
			$file=$common->direct_replace($db_object,$file,$xArray);

			echo $file;
			

		//$this->show_screen($db_object,$common,$user_id,$default,$error_msg,$model_id);
		}
		else
		{
			echo "No Models for the employees current position";
		}
			
		
	}
	
	
	
	
	function show_screen($db_object,$common,$empl,$default,$error_msg,$model_sel)
	{
		
		while(list($kk,$vv) = @each($post_var))
		{
			$$kk = $vv;
		}
	
		$xPath		= $common->path;
		$returncontent	= $xPath."/templates/career/person_position_inter.html";
		$returncontent	= $common->return_file_content($db_object,$returncontent);

		$skill_raters 		= $common->prefix_table('skill_raters');
		$model_skills 		= $common->prefix_table('model_skills');
		$skills 		= $common->prefix_table('skills');
		$textqsort_rating 	= $common->prefix_table('textqsort_rating');
		$rater_label_relate 	= $common->prefix_table('rater_label_relate');
		$model_name_table	= $common->prefix_table('model_name_table');
		$posmodel_colors 	= $common->prefix_table('posmodel_colors');
 		$user_table 		= $common->prefix_table('user_table');
		$other_raters_tech 	= $common->prefix_table('other_raters_tech');			

		
		$mysql = "select username from $user_table where user_id = '$empl'";
		$username_arr = $db_object->get_a_line($mysql);
		$username = $username_arr['username'];
		$mysql = "select model_name,model_id from $model_name_table where model_id = '$model_sel'";

		$model_arr = $db_object->get_a_line($mysql);
		$model_name = $model_arr['model_name'];
		$values['username'] = $username;
		$values['model_name'] = $model_name;
		$values['model_id'] = $model_sel;
		$values['empl'] = $empl;
		
		
	
	//$model_id = '3';
		
	$model_id = $model_sel;

	$mysql = "select $model_skills.skill_id,
		round(avg(rater_label_no)) as label_got,
		level_chosen as level_required
		from $model_skills,$textqsort_rating 
		where $model_skills.skill_id = $textqsort_rating.skill_id
		and $model_skills.model_id = '$model_id'
		and $textqsort_rating.rated_user = '$empl'
		and $textqsort_rating.rater_type = 'i'
		group by $textqsort_rating.skill_id";
		//echo $mysql;
	$datareq_arr = $db_object->get_rsltset($mysql);

//CREATING THE REQUIRED DATA IN AN ARRAY...

$newarray=array();


		for($i=0;$i<count($datareq_arr);$i++)
		{
	
		$label_got = $datareq_arr[$i]['label_got'];
		$level_required = $datareq_arr[$i]['level_required'];
		$skill_id = $datareq_arr[$i]['skill_id'];

		$newarray[$level_required][$label_got][]= $skill_id;
	
		}



	$mysql = "select count(*) as cnt from $skill_raters where skill_type = 'i'";
	$cnt_arr = $db_object->get_a_line($mysql);
	$cnt = $cnt_arr['cnt'];
	
	$mysql = "select rater_level_$default as rating_label ,rater_labelno from $skill_raters,$rater_label_relate where $skill_raters.rater_id = $rater_label_relate.rater_id and skill_type = 'i'";
//echo $mysql;
	$raterlabel_arr = $db_object->get_rsltset($mysql);
	

//TABLE START POSITIONS
	preg_match("/<{trdisplay_start}>(.*?)<{tddisplay_start}>/s",$returncontent,$trmatch_old);
	$trmatch = $trmatch_old[1];
	
//TR ENDING
	preg_match("/<{tddisplay_end}>(.*?)<{trdisplay_end}>/s",$returncontent,$trendmatchold);
	$trendmatch = $trendmatchold[1];
	
	preg_match("/<{tddisplay_start}>(.*?)<{tddisplay_end}>/s",$returncontent,$tdmatch_old);
	$tdmatch = $tdmatch_old[1];

//SKILLS TO DISPLAY INSIDE THE TD'S
	preg_match("/<{showallskills_loopstart}>(.*?)<{showallskills_loopend}>/s",$tdmatch,$skillsold);
	$skill_displaymatch = $skillsold[1];

//DISPLAY OF THE LABEL NAMES ... 
	preg_match("/<{labeldisplay_loopstart}>(.*?)<{labeldisplay_loopend}>/s",$returncontent,$labelold);
	$labelmatch = $labelold[1];
	
	preg_match("/<{positionlabeldisplay_loopstart}>(.*?)<{positionlabeldisplay_loopend}>/s",$returncontent,$poslabelold);
	$poslabelmatch = $poslabelold[1];
	
//RETRIEVING THE COLORS FROM THE DATABASE...

	$mysql = "select key_1,key_2,key_3,key_4,key_5 from $posmodel_colors where posmodel_id = '1'";
	$color_arr = $db_object->get_a_line($mysql);
	//print_r($color_arr);

	$color1 = $color_arr['key_1'];
	$color2 = $color_arr['key_2'];
	$color3 = $color_arr['key_3'];
	$color4 = $color_arr['key_4'];
	$color5 = $color_arr['key_5'];
			
	$nrows=$cnt;
	$ncolumns=1;
	$percent_fit_inter = array();


	$no_of_skills_inter1 = 1;
	$no_of_skills_inter2 = 1;
	$no_of_skills_inter3 = 1;
	$no_of_skills_inter4 = 1;
	$no_of_skills_inter5 = 1;


	for($i=$cnt;$i>0;$i--)
	{
		$mysql = "select rater_level_$default as rating_label
				from $skill_raters,$rater_label_relate
				where $skill_raters.rater_id = $rater_label_relate.rater_id
				and skill_type = 'i'
				and rater_labelno = $nrows";
		
			$label_arr = $db_object->get_a_line($mysql);
			$labelname = $label_arr['rating_label'];
			
			
			
		$newstring .= preg_replace("/<{(.*?)}>/e","$$1",$trmatch);

	
		for($j=$cnt;$j>0;$j--)
		{
 
			$skill_str="";
			$specific_color = '';

			for($l=0;$l<count($newarray[$nrows][$ncolumns]);$l++)
			{
			
				$skill_id_dis = $newarray[$nrows][$ncolumns][$l];
				$mysql = "select skill_name,skill_id from $skills where skill_id = '$skill_id_dis'";
				$skills_arr = $db_object->get_a_line($mysql);
				$skill_display = $skills_arr['skill_name'];
				
				$key_array[$keyid][] = $skill_display;


//COLOR DISPLAY				
				if($nrows == $cnt && $ncolumns == '1')
				{
					//echo "row $nrows and col $ncolumns <br>";
					$specific_color = $color1; //"red"
					$keyid = 1;
					$percent_fit_inter[$keyid] = $no_of_skills_inter1++;
					
					 
				}
				
				elseif($nrows == '1' && $ncolumns == $cnt)
				{
					//echo "row $nrows and col $ncolumns <br>";
					$specific_color = $color5; //"black"
					$keyid = 5;
					$percent_fit_inter[$keyid] = $no_of_skills_inter5++; 
				}
				elseif($nrows == $ncolumns)
				{
					$specific_color = $color3;  //"green"
					$keyid = 3;
					$percent_fit_inter[$keyid] = $no_of_skills_inter3++;
				}
				elseif($nrows < $cnt && $ncolumns >= $nrows)
				{
					$specific_color = $color4;  //"cyan"
					$keyid = 4;
					$percent_fit_inter[$keyid] = $no_of_skills_inter4++;
				}
				else //if($nrows == 1 && $ncolumns == 1)
				{
					$specific_color = $color2;  //"yellow"
					$keyid = 2;
					$percent_fit_inter[$keyid] = $no_of_skills_inter2++; 
				}
				
//COLOR DISPLAY END
							



				//echo "<a href=link.php>$skill_display</a> in row $nrows and col $ncolumns<br>";

				$skill_str .= preg_replace("/<{(.*?)}>/e","$$1",$skill_displaymatch);
			
			}
			
			//echo "row is $nrows and col is $ncolumns<br>";
		
			if(count($newarray[$nrows][$ncolumns]) < 1)
			{
								
				$skill_str = preg_replace("/<{showallskills_loopstart}>(.*?)<{showallskills_loopend}>/s","",$skill_str);
			
			}
		 
			
			
			
			$tdmatch1 = preg_replace("/<{showallskills_loopstart}>(.*?)<{showallskills_loopend}>/s",$skill_str,$tdmatch);
			
			if($j == 1) //$cnt-1
			{
			
				$mysql = "select rater_level_$default as rating_label
					from $skill_raters,$rater_label_relate
					where $skill_raters.rater_id = $rater_label_relate.rater_id
					and skill_type = 'i'
					and rater_labelno = $nrows";

				$label_arr = $db_object->get_a_line($mysql);
				$labelname = $label_arr['rating_label'];

				
//if it is the end of columns then add the last column and move on to the next row ...
		 
				$newstring .= preg_replace("/<{(.*?)}>/e","$$1",$tdmatch1);
				//$ncolumns++;
				//echo "<br><b>$ncolumns<br>";
				
				$newstring .= preg_replace("/<{(.*?)}>/e","$$1",$trendmatch);
				
				$nrows--;
				$ncolumns=1;
				
			}
			else
			{
				//echo "<br><b>$ncolumns<br>";

		 		$mysql = "select rater_level_$default as rating_label
					from $skill_raters,$rater_label_relate
					where $skill_raters.rater_id = $rater_label_relate.rater_id
					and skill_type = 'i'
					and rater_labelno = $nrows";

				$label_arr = $db_object->get_a_line($mysql);
				$labelname = $label_arr['rating_label'];
				
				$newstring .= preg_replace("/<{(.*?)}>/e","$$1",$tdmatch1);
				
				$ncolumns++;
				
			}
			

		}


	}


//PERCENT FIT CALCULATION...

//print_r($percent_fit_inter);	

	$total_skills_fit_inter = 0;
	while(list($kk,$vv) = @each($percent_fit_inter))
	{
		if($kk == 1)
		{
			$fit_1_inter = $vv * 0;
		}
		if($kk == 2)
		{
			$fit_2_inter = $vv * 50;
		}
		if($kk == 3)
		{
			$fit_3_inter = $vv * 100;
		}
		if($kk == 4)
		{
			$fit_4_inter = $vv * 100;
		}
		if($kk == 5)
		{
			$fit_5_inter = $vv * 100;
		}

		$total_skills_fit_inter += $vv;

	}

	$fit_full_inter = $fit_1_inter + $fit_2_inter + $fit_3_inter + $fit_4_inter + $fit_5_inter;

	if($fit_full_inter != 0 || $fit_full_inter != '')
	{
	$fit_inter = round($fit_full_inter / $total_skills_fit_inter,2);

	}

	$values['fit_inter'] = $fit_inter;

	


//KEY AND SKILL VALUES FOR THE INTERPERSONAL FIT PERCENTAGE...
//print_r($key_array);

	$mysql = "select rater_level_$default as rating_label,rater_labelno from $skill_raters,$rater_label_relate where $skill_raters.rater_id = $rater_label_relate.rater_id and skill_type = 'i'";

	$label_myrating_arr = $db_object->get_rsltset($mysql);
	for($i=0;$i<count($label_myrating_arr);$i++)
	{
		$labelname = $label_myrating_arr[$i]['rating_label'];
		$labelid = $label_myrating_arr[$i]['rater_labelno'];
		$label_str .= preg_replace("/<{(.*?)}>/e","$$1",$labelmatch);
		$poslabel_str .= preg_replace("/<{(.*?)}>/e","$$1",$poslabelmatch);
	
	}


	//DISPLAY OF THE MODEL NAME...

	$mysql = "select model_name from $model_name_table where model_id = '$model_id'";
	$modelname_arr = $db_object->get_a_line($mysql);
	$modelname = $modelname_arr['model_name'];
	$values['modelname'] = $modelname;



	$returncontent = preg_replace	("/<{labeldisplay_loopstart}>(.*?)<{labeldisplay_loopend}>/s",$label_str,$returncontent);
	$returncontent = preg_replace	("/<{positionlabeldisplay_loopstart}>(.*?)<{positionlabeldisplay_loopend}>/s",$poslabel_str,$returncontent);	

	$returncontent = preg_replace("/<{trdisplay_start}>(.*?)<{trdisplay_end}>/s",$newstring,$returncontent);
	
	$returncontent=$common->direct_replace($db_object,$returncontent,$values);
	
	echo $returncontent;


	}
	
}

$obj=new inter();

if($model_sel)
{

	$obj->show_screen($db_object,$common,$user_id,$default,$error_msg,$model_sel);
}
else
{
	$obj->position_model($db_object,$common,$user_id,$default);
}

include_once("footer.php");

?>
