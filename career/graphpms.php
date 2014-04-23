<?php

/*---------------------------------------------
SCRIPT:graphpms.php
AUTHOR:info@chrisranjana.com	
UPDATED:3rd Nov

DESCRIPTION:
This script displays the graph results.

---------------------------------------------*/


include_once("../session.php");


$skill_id = $_GET['skill_id'];		//THE SKILL FOR WHICH EACH GRAPH IS TO BE SHOWN...
$group_full = $_GET['group_full'];
$id_of_user = $_GET['id_of_user'];

$group_arr = @explode(",",$group_full);

//THE GROUPS WHICH ARE TO BE USED TO FIND THE BAR GRAPH IS OBTAINED...

	while (list($kk,$vv) = @each($group_arr))
	{
	$groupnames = $gbl_grouprater_inter[$vv];
	$arrnew[$vv] = $groupnames;
	}

//print_r($arrnew);exit;

$arrayfirst = $arrnew;

$rater_label_relate = $common->prefix_table('rater_label_relate');
$skill_raters = $common->prefix_table('skill_raters');
$other_raters = $common->prefix_table('other_raters');
$textqsort_rating = $common->prefix_table('textqsort_rating');


//this sql query retrieves the rater label no without the DOESNOT APPLY option...

$mysql = "select $rater_label_relate.rater_labelno 
	from $rater_label_relate , $skill_raters 
	where $skill_raters.rater_id = $rater_label_relate.rater_id 
	and $skill_raters.type_name = 'n' 
	and $skill_raters.skill_type = $rater_label_relate.rater_type 
	and $skill_raters.skill_type = 'i'";


$labels_arr = $db_object->get_single_column($mysql);


$mysql = "select type_name from $skill_raters where type_name = 'd'";
$dna_arr = $db_object->get_single_column($mysql);

if($dna_arr !='')
{
	$labels_arr_dump = $labels_arr;
	
	$labels_arr_new[0] = 'DNA';
	
	for($i=0;$i<count($labels_arr_dump);$i++)
	{
		
	$labels_arr_new[] = $labels_arr_dump[$i];
	}
	$labels_arr = $labels_arr_new;
}



//if the doesnot apply tag is set on by admin then assign the value of 0 to the labelno...

		

//if($dna_arr !='')
//		{
//			$mysql = "select rater_labelno 
//					from $rater_label_relate,$skill_raters 
//					where $skill_raters.rater_id = $rater_label_relate.rater_id 
//					and $skill_raters.type_name='d'";
//					//echo $mysql;exit;
//			$doenst_label_arr	= $db_object->get_a_line($mysql);
//			$doesnt_label = $doenst_label_arr[0];
//			
//		}
		

$labels = @implode(" " , $labels_arr);

$no_of_labels = count($labels_arr);

$graph_start_pos = 150;    // NOTE : this should be the same as $x1...

$graph_end_pos = 400;      //NOTE : this is the max value in the graph...


$length_of_bars = $graph_end_pos - $graph_start_pos;

$distance_between_xcoordinate = round($length_of_bars/($no_of_labels-1));


$career_colors = $common->prefix_table('career_colors');

$mysql = "select career_bgcolor,career_border,career_grp_self,career_grp_team,career_grp_incus,career_grp_boss,career_grp_peer,career_grp_excus,career_grp_topboss,career_grp_dirrep,career_grp_other from $career_colors where color_id = '1'";
			
$color_arr = $db_object->get_a_line($mysql);

//BACKGROUND COLOR
$background = $color_arr['career_bgcolor'];

	$bg_a = substr($background,0,2);
	$bg_a = hexdec($bg_a);
	$bg_b = substr($background,2,2);
	$bg_b = hexdec($bg_b);
	$bg_c = substr($background,4,2);
	$bg_c = hexdec($bg_c);

//BORDER COLOR
$bor_col = $color_arr['career_border'];

	$bor_a = substr($bor_col,0,2);
	$bor_a = hexdec($bor_a);
	$bor_b = substr($bor_col,2,2);
	$bor_b = hexdec($bor_b);
	$bor_c = substr($bor_col,4,2);
	$bor_c = hexdec($bor_c);

//BAR COLORS
$bar1_col = $color_arr['career_grp_self'];

	$bar1_a = substr($bar1_col,0,2);
	$bar1_a = hexdec($bar1_a);
	$bar1_b = substr($bar1_col,2,2);
	$bar1_b = hexdec($bar1_b);
	$bar1_c = substr($bar1_col,4,2);
	$bar1_c = hexdec($bar1_c);

$bar2_col = $color_arr['career_grp_team'];

	$bar2_a = substr($bar2_col,0,2);
	$bar2_a = hexdec($bar2_a);
	$bar2_b = substr($bar2_col,2,2);
	$bar2_b = hexdec($bar2_b);
	$bar2_c = substr($bar2_col,4,2);
	$bar2_c = hexdec($bar2_c);

$bar3_col = $color_arr['career_grp_incus'];

	$bar3_a = substr($bar3_col,0,2);
	$bar3_a = hexdec($bar3_a);
	$bar3_b = substr($bar3_col,2,2);
	$bar3_b = hexdec($bar3_b);
	$bar3_c = substr($bar3_col,4,2);
	$bar3_c = hexdec($bar3_c);
	
$bar4_col = $color_arr['career_grp_boss'];

	$bar4_a = substr($bar4_col,0,2);
	$bar4_a = hexdec($bar4_a);
	$bar4_b = substr($bar4_col,2,2);
	$bar4_b = hexdec($bar4_b);
	$bar4_c = substr($bar4_col,4,2);
	$bar4_c = hexdec($bar4_c);	
	
$bar5_col = $color_arr['career_grp_peer'];

	$bar5_a = substr($bar5_col,0,2);
	$bar5_a = hexdec($bar5_a);
	$bar5_b = substr($bar5_col,2,2);
	$bar5_b = hexdec($bar5_b);
	$bar5_c = substr($bar5_col,4,2);
	$bar5_c = hexdec($bar5_c);
	
$bar6_col = $color_arr['career_grp_excus'];

	$bar6_a = substr($bar6_col,0,2);
	$bar6_a = hexdec($bar6_a);
	$bar6_b = substr($bar6_col,2,2);
	$bar6_b = hexdec($bar6_b);
	$bar6_c = substr($bar6_col,4,2);
	$bar6_c = hexdec($bar6_c);
	
$bar7_col = $color_arr['career_grp_topboss'];

	$bar7_a = substr($bar7_col,0,2);
	$bar7_a = hexdec($bar7_a);
	$bar7_b = substr($bar7_col,2,2);
	$bar7_b = hexdec($bar7_b);
	$bar7_c = substr($bar7_col,4,2);
	$bar7_c = hexdec($bar7_c);
	
$bar8_col = $color_arr['career_grp_dirrep'];

	$bar8_a = substr($bar8_col,0,2);
	$bar8_a = hexdec($bar8_a);
	$bar8_b = substr($bar8_col,2,2);
	$bar8_b = hexdec($bar8_b);
	$bar8_c = substr($bar8_col,4,2);
	$bar8_c = hexdec($bar8_c);
	
$bar9_col = $color_arr['career_grp_other'];

	$bar9_a = substr($bar9_col,0,2);
	$bar9_a = hexdec($bar9_a);
	$bar9_b = substr($bar9_col,2,2);
	$bar9_b = hexdec($bar9_b);
	$bar9_c = substr($bar9_col,4,2);
	$bar9_c = hexdec($bar9_c);
//print_r($arrayfirst);
	
	$count_bars_sel = count($arrayfirst);
	
	
$width = 450;

$height = 20 * ($count_bars_sel+2);  	//200

$image = ImageCreate($width, $height); 



$bgcolor = ImageColorAllocate($image, $bg_a, $bg_b, $bg_c);  
$border = ImageColorAllocate($image, $bor_a, $bor_b, $bor_c);  



$gray = ImageColorAllocate($image, 0xC0, 0xC0, 0xC0);  

ImageRectangle($image,5,5,$width-5,$height-5,$border);



$bar1 = ImageColorAllocate($image, $bar1_a, $bar1_b, $bar1_c);
$bar2 = ImageColorAllocate($image, $bar2_a, $bar2_b, $bar2_c);
$bar3 = ImageColorAllocate($image, $bar3_a, $bar3_b, $bar3_c);
$bar4 = ImageColorAllocate($image, $bar4_a, $bar4_b, $bar4_c);
$bar5 = ImageColorAllocate($image, $bar5_a, $bar5_b, $bar5_c);
$bar6 = ImageColorAllocate($image, $bar6_a, $bar6_b, $bar6_c); 
$bar7 = ImageColorAllocate($image, $bar7_a, $bar7_b, $bar7_c); 
$bar8 = ImageColorAllocate($image, $bar8_a, $bar8_b, $bar8_c); 
$bar9 = ImageColorAllocate($image, $bar9_a, $bar9_b, $bar9_c); 


$colorarray = array("0"=>$bar1,"1"=>$bar2,"2"=>$bar3,"3"=>$bar4,"4"=>$bar5,"5"=>$bar6,"6"=>$bar7,"7"=>$bar8,"8"=>$bar9);


//---------------------------------------

//THE FOLLOWING CODE DISPLAYS THE COORDINATE VALUES OF THE X AXIS

	$pos = $graph_start_pos;

	ImageString($image, $labelfont, 10,10, "Ratings :", $border);

	for($i=0;$i<$no_of_labels;$i++)
	{
	
		$label = $labels_arr[$i];
		ImageString($image, $labelfont, $pos,10, $label, $border);
		$pos += $distance_between_xcoordinate;
	
		
	}
	
//---------------------------------------

$array1=$arrayfirst;

rsort($array1,SORT_NUMERIC);

$highest=$array1[0];

$x1  = 150	;  		//15
$y1  = 20;			//15
$y2 = 30;			// width of the bars
$yname = $y1;


$j=0;
	
//THE FOLLOWING CODE OF WHILE LIST DISPLAYS THE GROUP NAMES ON THE Y AXIS ...
	$label = array();
	while (list($kk,$vv) = @each($arrayfirst))
	{ 
		$title = $vv;
		
		$j++;
	
		ImageString($image, $labelfont, 10,$yname, $title, $border);
		$yname = $yname+20;
		
//DETERMINING THE VALUES OF EACH GROUP...
		

	$mysql = "select $textqsort_rating.rater_label_no 
		from $textqsort_rating,$other_raters
		where $textqsort_rating.rated_user = $other_raters.cur_userid
		and $textqsort_rating.rater_id = $other_raters.rater_userid
		and $textqsort_rating.skill_id = '$skill_id'
		and other_raters.cur_userid = '$id_of_user'
		and other_raters.group_belonging = '$kk'";
		//echo "$mysql<br>";		
		$labelval_arr = $db_object->get_single_column($mysql);
		
		//print_r($labelval_arr);		

		
//CHECK IF THERE ARE MORE THAN 1 USER IN THE RATING LIST,IF SO THEN TAKE THE AVERAGE OF THEM AND DISPLAY THE FLOOR OF THE AVERAGE VAL...
	
	$ratingcount = count($labelval_arr);
	$totalrating = 0;
	
	//if($doesnt_label == $labelval_arr[0])
	//{
	//	
	//	$labelval_arr[0] = 0;
	//}
	
	
		
//if there are more than one person rating from the same group then the average of all the raters are taken as the rating of that group...
	
		if($ratingcount > 1)
		{
			for($n=0;$n<count($labelval_arr);$n++)
			{
				//if the DOESNOT APPLY TAG IS SET THEN SET THE HIGHEST ie LABELNO OF DOESNOT APPLY TO 0
				//if($doesnt_label == $labelval_arr[$n])
				//{
				//	$labelval_arr[$n] = 0;
				//}
				
				$rating = $labelval_arr[$n];
				
				$totalrating += $rating; 
				
			}
			$avg = $totalrating / $ratingcount;
			
			$label[$kk]  = floor($avg);
			
			
		}
		else
		{
			$label[$kk] = $labelval_arr[0];
		}
		
	}
//PRINTING THE VALUES...
	
	$l=0;

	while (list($kk,$vv) = @each($label))
	{
		
	$color = $colorarray[$l];
	
	$value = $label[$kk];
	
	
		if($dna_arr !='')
		{
		$x2 = $x1 + ($distance_between_xcoordinate * ($value));
	
		}
		else
		{
		$x2 = $x1 + ($distance_between_xcoordinate * ($value-1));
		}
	
	ImageFilledRectangle($image, $x1, $y1, $x2, $y2, $color); 
	
	$y1 = $y1 + 20;	//50

	$y2  = $y2 + 20;	//50
	
	$l++;

	}



header("Content-type: image/png");  	// or "Content-type: image/png"  
ImageJPEG($image); // or imagepng($image)  

ImageDestroy($image);

?>
