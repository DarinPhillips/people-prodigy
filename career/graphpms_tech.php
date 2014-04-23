<?php

/*---------------------------------------------
SCRIPT:graphpms_tech.php
AUTHOR:info@chrisranjana.com	
UPDATED:10rd Nov

DESCRIPTION:
This script displays the graph results for technical skills.

In this rating the self rating, boss' rating and reference rating are calculated seperately...


---------------------------------------------*/


include_once("../session.php");


$skill_id = $_GET['skill_id'];		//THE SKILL FOR WHICH EACH GRAPH IS TO BE SHOWN...
$id_of_user = $_GET['id_of_user'];
$techgrp_full = $_GET['techgrp_full'];
$techgrp_arr = @explode(",",$techgrp_full);
//print_r($techgrp_arr);

$count=0;

while(list($kk,$vv) = @each($techgrp_arr))
{
}	
	$tech_label_arr[$count] = 'Self';
	$tech_label_arr[$count+1] = 'Boss';
	$tech_label_arr[$count+2] = 'References';
	//$count++;
	

//print_r($tech_label_arr);

$arrayfirst = $techgrp_arr;

//$arrayfirst = array("0"=>"Self","1"=>"Boss","2"=>"References");




$rater_label_relate 	= $common->prefix_table('rater_label_relate');
$skill_raters 		= $common->prefix_table('skill_raters');
$tech_rating 		= $common->prefix_table('tech_rating');
$other_raters_tech	= $common->prefix_table('other_raters_tech');
$user_table			= $common->prefix_table('user_table');
$position			= $common->prefix_table('position');

//this sql query retrieves the rater label no without the DOESNOT APPLY option...

$mysql = "select $rater_label_relate.rater_labelno 
	from $rater_label_relate , $skill_raters 
	where $skill_raters.rater_id = $rater_label_relate.rater_id 
	and $skill_raters.type_name = 'n' 
	and $skill_raters.skill_type = $rater_label_relate.rater_type 
	and $skill_raters.skill_type = 't'";


$labels_arr = $db_object->get_single_column($mysql);
//print_r($labels_arr);


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

$height = 100;  	//20 * ($count_bars_sel+2)

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



//$array1=$arrayfirst;

//rsort($array1,SORT_NUMERIC);

//$highest=$array1[0];

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
	}
//DETERMINING THE VALUES OF EACH GROUP...

if(@in_array("Self",$techgrp_arr))
{
	
 	//$mysql = "select selfrating_labelid";
	
	$mysql = "select $rater_label_relate.rater_labelno 
			from $rater_label_relate,$tech_rating
			where $rater_label_relate.rater_id = $tech_rating.selfrating_labelid
			and $tech_rating.rating_user = '$id_of_user'
			and $tech_rating.skill_id = $skill_id";
			//echo $mysql;
	$self_rating_arr = $db_object->get_single_column($mysql);
	
	//print_r($self_rating_arr);
	
	$self_rating_val = $self_rating_arr[0];
	
	
	$x2 = $x1 + ($distance_between_xcoordinate * ($self_rating_val-1));
	
	ImageFilledRectangle($image, $x1, $y1, $x2, $y2, $colorarray[1]); 
	
	$y1 = $y1 + 20;	//50

	$y2  = $y2 + 20;	//50
	
}

//boss rating

if(@in_array("Boss",$techgrp_arr))
{


	//references rating values	
	
	$mysql = "select $rater_label_relate.rater_labelno 
			from $rater_label_relate,$other_raters_tech
			where $rater_label_relate.rater_id = $other_raters_tech.label_id
			and $other_raters_tech.rated_user = '$id_of_user'
			and $other_raters_tech.skill_id ='$skill_id'
			and $other_raters_tech.rater_id = '$boss_id'";
			//echo $mysql;
	$boss_rating_arr = $db_object->get_single_column($mysql);		
	//print_r($boss_rating_arr);
	
	$boss_rating = $boss_rating_arr[0];
	
	$x2 = $x1 + ($distance_between_xcoordinate * ($boss_rating-1));
	ImageFilledRectangle($image, $x1, $y1, $x2, $y2, $colorarray[3]); 
	
	$y1 = $y1 + 20;	//50

	$y2  = $y2 + 20;	//50
}

//references rating values	

if(@in_array("References",$techgrp_arr))
{


//Determine the boss of the person and eliminate the rating done by him 
//to obtain the rating of the references...
	
//==================================
	//the position of the user is found out...
	
	$boss_id = $common->immediate_boss($db_object,$id_of_user);		
	
	//echo "$boss_id<br>";
		
//====================================
	

	$mysql = "select $rater_label_relate.rater_labelno 
			from $rater_label_relate,$other_raters_tech
			where $rater_label_relate.rater_id = $other_raters_tech.label_id
			and $other_raters_tech.rated_user = '$id_of_user'
			and $other_raters_tech.skill_id ='$skill_id'
			and $other_raters_tech.rater_id <> '$boss_id'";
			//echo "$mysql<br>";
	$ref_rating_arr = $db_object->get_single_column($mysql);		
	//print_r($ref_rating_arr);

$tot_rating = 0;	
	
	for($j=0;$j<count($ref_rating_arr);$j++)
	{
		$ratingval = $ref_rating_arr[$j];
		
		$tot_rating += $ratingval;
		
	}
	$ratingcount  = count($ref_rating_arr);
	
	if($ratingcount !=0)
	{
	$avg = $tot_rating / $ratingcount;
	}
	$reference_ratingval = floor($avg);
	
	$x2 = $x1 + ($distance_between_xcoordinate * ($reference_ratingval-1));
	ImageFilledRectangle($image, $x1, $y1, $x2, $y2, $colorarray[2]); 
	
	$y1 = $y1 + 20;	

	$y2  = $y2 + 20;	

}


//PRINTING THE VALUES...
	
	$l=0;
/*
	while (list($kk,$vv) = @each($label))
	{
		
	$color = $colorarray[$l];
	
	$value = $label[$kk];
	
	
		 
		$x2 = $x1 + ($distance_between_xcoordinate * ($value-1));
	 
	
	ImageFilledRectangle($image, $x1, $y1, $x2, $y2, $color); 
	
	$y1 = $y1 + 20;	//50

	$y2  = $y2 + 20;	//50
	
	$l++;

	}

*/

header("Content-type: image/png");  	// or "Content-type: image/png"  
ImageJPEG($image); // or imagepng($image)  

ImageDestroy($image);

?>
