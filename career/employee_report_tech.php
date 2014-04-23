<?php

include_once("../session.php");

include_once("header.php");

class inter
{
	function position_model($db_object,$common,$user_id,$default)
	{
		$path=$common->path;
		
		$xtemplate=$path."templates/career/employee_report_tech.html";
		
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
		$returncontent	= $xPath."/templates/career/person_position_tech.html";
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
		
		$model_id=$model_sel;
	
	//TECHNICAL
		
			$mysql = "select $model_skills.skill_id as skill_id,
			round(avg(label_id)) as label_got,
			level_chosen as level_required
			from $other_raters_tech,$model_skills 
			where $model_skills.skill_id = $other_raters_tech.skill_id
			and $model_skills.model_id = '$model_id'
			and $other_raters_tech.rated_user = '$empl'
			group by $other_raters_tech.skill_id";	

	$datareqtech_arr = $db_object->get_rsltset($mysql);

//CREATING THE REQUIRED DATA IN AN ARRAY...

	$newarraytech=array();


		for($i=0;$i<count($datareqtech_arr);$i++)
		{
	
		$label_got = $datareqtech_arr[$i]['label_got'];
		$level_required = $datareqtech_arr[$i]['level_required'];
		$skill_id = $datareqtech_arr[$i]['skill_id'];

		$newarraytech[$level_required][$label_got][]= $skill_id;
	
		}



	$mysql = "select count(*) as cnt_tech from $skill_raters where skill_type = 't'";
	$cnt_arr = $db_object->get_a_line($mysql);
	$cnt_tech = $cnt_arr['cnt_tech'];
	
	$mysql = "select rater_level_$default as rating_label ,rater_labelno from $skill_raters,$rater_label_relate where $skill_raters.rater_id = $rater_label_relate.rater_id and skill_type = 't'";
	$raterlabel_arr = $db_object->get_rsltset($mysql);
	

//TABLE START POSITIONS
	preg_match("/<{trdisplaytech_start}>(.*?)<{tddisplaytech_start}>/s",$returncontent,$trmatchtech_old);
	$trmatchtech = $trmatchtech_old[1];
	
//TR ENDING
	preg_match("/<{tddisplaytech_end}>(.*?)<{trdisplaytech_end}>/s",$returncontent,$trendmatcholdtech);
	$trendmatchtech = $trendmatcholdtech[1];
	
	preg_match("/<{tddisplaytech_start}>(.*?)<{tddisplaytech_end}>/s",$returncontent,$tdmatchtech_old);
	$tdmatchtech = $tdmatchtech_old[1];

//SKILLS TO DISPLAY INSIDE THE TD'S
	preg_match("/<{showallskillstech_loopstart}>(.*?)<{showallskillstech_loopend}>/s",$tdmatchtech,$skillsoldtech);
	$skilltech_displaymatch = $skillsoldtech[1];

//DISPLAY OF THE LABEL NAMES ... 
	preg_match("/<{labeldisplaytech_loopstart}>(.*?)<{labeldisplaytech_loopend}>/s",$returncontent,$labeloldtech);
	$labelmatchtech = $labeloldtech[1];
	
	preg_match("/<{positionlabeldisplaytech_loopstart}>(.*?)<{positionlabeldisplaytech_loopend}>/s",$returncontent,$poslabeloldtech);
	$poslabelmatchtech = $poslabeloldtech[1];
	

		
	$nrows=$cnt_tech;
	$ncolumns=1;
	$percent_fit = array();

	$no_of_skills_tech1 = 1;
	$no_of_skills_tech2 = 1;
	$no_of_skills_tech3 = 1;
	$no_of_skills_tech4 = 1;
	$no_of_skills_tech5 = 1;

	for($i=$cnt_tech;$i>0;$i--)
	{
		$mysql = "select rater_level_$default as rating_label
				from $skill_raters,$rater_label_relate
				where $skill_raters.rater_id = $rater_label_relate.rater_id
				and skill_type = 't'
				and rater_labelno = $nrows";
			 	
			$label_arr = $db_object->get_a_line($mysql);
			$labelname_tech = $label_arr['rating_label'];
			 
		$newstring_tech .= preg_replace("/<{(.*?)}>/e","$$1",$trmatchtech);

	
		for($j=$cnt_tech;$j>0;$j--)
		{
 
			$skilltech_str="";
		 
			for($l=0;$l<count($newarraytech[$nrows][$ncolumns]);$l++)
			{
				
				$skilltech_id_dis = $newarraytech[$nrows][$ncolumns][$l];
				
				$mysql = "select skill_name,skill_id from $skills where skill_id = '$skilltech_id_dis'";
				$skills_arr = $db_object->get_a_line($mysql);
				$skilltech_display = $skills_arr['skill_name'];
				$specifictech_color = "";
			
			
//COLOR DISPLAY START
				
				if($nrows == $cnt_tech && $ncolumns == '1')
				{
					//echo "row $nrows and col $ncolumns <br>";
					$specifictech_color = $color1;  //"red"
					$keyid_tech = 1;
					$percent_fit[$keyid_tech] = $no_of_skills_tech1++;
					 
				}
				elseif($nrows == '1' && $ncolumns == $cnt_tech)
				{
					//echo "row $nrows and col $ncolumns <br>";
					$specifictech_color = $color5;  //"black"
					$keyid_tech = 5;
					$percent_fit[$keyid_tech] =  $no_of_skills_tech5++;
				}
				elseif($nrows == $ncolumns)
				{
					$specifictech_color = $color3;  //"green"
					$keyid_tech = 3;
					$percent_fit[$keyid_tech] =  $no_of_skills_tech3++; 
				}
				elseif($nrows < $cnt_tech && $ncolumns >= $nrows)
				{
					$specifictech_color = $color4;  //"cyan"
					$keyid_tech = 4;
					$percent_fit[$keyid_tech] =  $no_of_skills_tech4++;
				}
				else //if($nrows == $cnt_tech && $ncolumns == $cnt_tech)
				{
					$specifictech_color = $color2;  //"yellow"
					$keyid_tech = 2;
					
					$percent_fit[$keyid_tech] =  $no_of_skills_tech2++;
					 
				}

//===============COLOR DISPLAY END


				$skilltech_str .= preg_replace("/<{(.*?)}>/e","$$1",$skilltech_displaymatch);
			
			}
		
			if(count($newarraytech[$nrows][$ncolumns]) < 1)
			{
								
				$skilltech_str = preg_replace("/<{showallskillstech_loopstart}>(.*?)<{showallskillstech_loopend}>/s","",$skilltech_str);
			
			}
		  
			$tdmatch1tech = preg_replace("/<{showallskillstech_loopstart}>(.*?)<{showallskillstech_loopend}>/s",$skilltech_str,$tdmatchtech);
			
			if($j == 1) //$cnt_tech-1
			{
			
				$mysql = "select rater_level_$default as rating_label
					from $skill_raters,$rater_label_relate
					where $skill_raters.rater_id = $rater_label_relate.rater_id
					and skill_type = 't'
					and rater_labelno = $nrows";

				$label_arr = $db_object->get_a_line($mysql);
				$labelname_tech = $label_arr['rating_label'];

				
//if it is the end of columns then add the last column and move on to the next row ...
		 
				$newstring_tech .= preg_replace("/<{(.*?)}>/e","$$1",$tdmatch1tech);
				$ncolumns++;
				
				$newstring_tech .= preg_replace("/<{(.*?)}>/e","$$1",$trendmatchtech);
				
				$nrows--;
				$ncolumns=1;
				
			}
			else
			{

		 		$mysql = "select rater_level_$default as rating_label
					from $skill_raters,$rater_label_relate
					where $skill_raters.rater_id = $rater_label_relate.rater_id
					and skill_type = 't'
					and rater_labelno = $nrows";

				$label_arr = $db_object->get_a_line($mysql);
				$labelname_tech = $label_arr['rating_label'];
				
				$newstring_tech .= preg_replace("/<{(.*?)}>/e","$$1",$tdmatch1tech);
				
				$ncolumns++;
			}
			

		}
	}


//PERCENT FIT CALCULATION...

//print_r($percent_fit);	

	$total_skills_fit = 0;
	while(list($kk,$vv) = @each($percent_fit))
	{
		if($kk == 1)
		{
			$fit_1 = $vv * 0;
		}
		if($kk == 2)
		{
			$fit_2 = $vv * 50;
		}
		if($kk == 3)
		{
			$fit_3 = $vv * 100;
		}
		if($kk == 4)
		{
			$fit_4 = $vv * 100;
		}
		if($kk == 5)
		{
			$fit_5 = $vv * 100;
		}

		$total_skills_fit += $vv;

	}
		$fit_full = $fit_1 + $fit_2 + $fit_3 + $fit_4 + $fit_5;
		if($fit_full != 0)
		{
		$fit_tech = round($fit_full / $total_skills_fit,2);
		}



	$values['fit_tech'] = $fit_tech;




	$mysql = "select rater_level_$default as rating_label,rater_labelno from $skill_raters,$rater_label_relate where $skill_raters.rater_id = $rater_label_relate.rater_id and skill_type = 't'";
	$label_myratingtech_arr = $db_object->get_rsltset($mysql);
	
	for($i=0;$i<count($label_myratingtech_arr);$i++)
	{
		$labelname_tech = $label_myratingtech_arr[$i]['rating_label'];
		$labelid_tech = $label_myratingtech_arr[$i]['rater_labelno'];
		$labeltech_str .= preg_replace("/<{(.*?)}>/e","$$1",$labelmatchtech);
		$poslabeltech_str .= preg_replace("/<{(.*?)}>/e","$$1",$poslabelmatchtech);
	
	}


	//DISPLAY OF THE MODEL NAME...

	$mysql = "select model_name from $model_name_table where model_id = '$model_id'";
	$modelname_arr = $db_object->get_a_line($mysql);
	$modelname = $modelname_arr['model_name'];
	$values['modelname'] = $modelname;



	$returncontent = preg_replace	("/<{labeldisplaytech_loopstart}>(.*?)<{labeldisplaytech_loopend}>/s",$labeltech_str,$returncontent);
	$returncontent = preg_replace	("/<{positionlabeldisplaytech_loopstart}>(.*?)<{positionlabeldisplaytech_loopend}>/s",$poslabeltech_str,$returncontent);	

	$returncontent = preg_replace("/<{trdisplaytech_start}>(.*?)<{trdisplaytech_end}>/s",$newstring_tech,$returncontent);
	
	$returncontent = $common->direct_replace($db_object,$returncontent,$values);
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
