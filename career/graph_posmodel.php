<?php
/*---------------------------------------------
SCRIPT:graph_posmodel.php
AUTHOR:info@chrisranjana.com	
UPDATED:2nd Dec

DESCRIPTION:
This script displays the graph which shows just the data for the admin while selecting the position model.

---------------------------------------------*/


include_once("../session.php");

$users_of_model = $_GET['users_of_model'];
$usersofmodel_arr = @explode(",",$users_of_model);


$width = 430;
$height = 200;

$image = ImageCreate($width, $height); 

$bgcolor = ImageColorAllocate($image,0xFFFFFF, 0xFFFFFF, 0xFFFFFF);  
$border = ImageColorAllocate($image,0x000000, 0x000000, 0x000000); 

ImageRectangle($image,5,5,425,195,$border);

ImageLine($image,15,30,15,180,$border);  //x axis  - left 
ImageLine($image,15,180,415,180,$border); //y axis
ImageLine($image,415,180,415,30,$border); // x axis - right

ImageLine($image,215,30,215,180,$border);  //center line divide

ImageLine($image,115,30,115,180,$border);   //quarter 1
ImageLine($image,315,30,315,180,$border);   //quarter 2

ImageString($image, $labelfont, 10,185, "0%", $border);
ImageString($image, $labelfont, 115,185, "25%", $border);
ImageString($image, $labelfont, 215,185, "50%", $border);
ImageString($image, $labelfont, 315,185, "75%", $border);
ImageString($image, $labelfont, 400,185, "100%", $border);


$textqsort_rating = $common->prefix_table('textqsort_rating');
$user_tests = $common->prefix_table('user_tests');
$user_test_grade = $common->prefix_table('user_test_grade');
$other_raters_tech = $common->prefix_table('other_raters_tech');
$rater_label_relate = $common->prefix_table('rater_label_relate');

$mysql = "select count($user_test_grade.skill_id) as c_rated_user ,sum(grade) as sumoflabels,user_id as rated_user
		from $user_tests,$user_test_grade
		where $user_tests.user_testid = $user_test_grade.user_testid
		and user_tests.test_type = 'i'
		and test_completed='y' and $user_tests.user_id in ($users_of_model) group by user_id";
		
$tests_arr = $db_object->get_rsltset($mysql);

$mysql ="select count(rated_user) as c_rated_user
		,sum(rater_label_no) as sumoflabels
		,rated_user,max(rater_label_no) as max_label
		from $textqsort_rating
		where rated_user <> rater_id
		and rated_user in ($users_of_model)group by rated_user";
$arr = $db_object->get_rsltset($mysql);


//FIND THE MAXIMUM VALUE OF LABEL...
$mysql = "select max(rater_label_no) as max_val from $textqsort_rating";
$query_arr = $db_object->get_a_line($mysql);
$max_label = $query_arr['max_val'];

$merged_array = @array_merge($arr,$tests_arr);


for($i=0;$i<count($merged_array);$i++)
{
	$c_rated_user = $merged_array[$i]['c_rated_user'];
	$sum_of_labels = $merged_array[$i]['sumoflabels'];
	//$max_label = $merged_array[$i]['max_label'];
	$rated_user = $merged_array[$i]['rated_user'];

	$percent_user = ($sum_of_labels / ($c_rated_user * $max_label)) * 100;
	$users_i_data[$rated_user] = round($percent_user,2);
		
	
	
}



//Technical averages...

$mysql = "select max(rater_labelno) as max_t from $rater_label_relate where rater_type='t'";
$max_arr = $db_object->get_a_line($mysql);

$max_label_t = $max_arr['max_t'];

$mysql = "select count($user_test_grade.skill_id) as c_rated_user ,sum(grade) as sumoflabels,user_id as rated_user
		from $user_tests,$user_test_grade
		where $user_tests.user_testid = $user_test_grade.user_testid
		and user_tests.test_type = 't'
		and test_completed='y' and $user_tests.user_id in ($users_of_model) group by user_id";
		
$teststech_arr = $db_object->get_rsltset($mysql);


$mysql = "select sum(label_id) as sumoflabels
		,rated_user 
		,count(rater_id) as c_rated_user
		from $other_raters_tech
		where rated_user in ($users_of_model)
		group by rated_user";

$tech_arr = $db_object->get_rsltset($mysql);

$arr_tech = @array_merge($teststech_arr,$tech_arr);

for($j=0;$j<count($arr_tech);$j++)
{
	
	$c_rated_user = $arr_tech[$j]['c_rated_user'];
	$sum_of_labels = $arr_tech[$j]['sumoflabels'];
	$rated_user = $arr_tech[$j]['rated_user'];

	$percent_user = ($sum_of_labels / ($c_rated_user * $max_label_t)) * 100;
	$users_t_data[$rated_user] = round($percent_user,2);
	
}


//TAKE THE PERCENTAGE OF BOTH THE I & T VALUES AND TAKE THE AVERAGE TO DISPLAY
while(list($kk1,$vv1) = @each($users_i_data))
{
	@reset($users_t_data);
	while(list($kk2,$vv2) = @each($users_t_data))
	{
		if($kk1 == $kk2)
		{
			$val = ($vv1 + $vv2) /2;
			$newarr[$kk1] = $val;
		}
		else
		{
			$newarr[$kk1] = $vv1;
		}
	}
}


$full_data_percent = $newarr;
@asort($newarr);

$xvalues = 9;
$pos = 175;
$distance_inbetween = @round($pos / $xvalues);
for($i=0;$i<$xvalues;$i++)
{
	ImageString($image, $labelfont, 10,$pos, $i, $border);
	$pos -= $distance_inbetween;

}

$graph_start_pos = 15;
$graph_end_pos = 415;

while(list($kk,$vv) = @each($full_data_percent))
{
	$chart_values[] = $vv;
	
}

$x=1;
for($i=0;$i<count($chart_values);$i++)
{
	if($chart_values[$i] == $chart_values[$i+1])
	{
		
		$no_of_users[$chart_values[$i]] = $x++;
		
	}
	else
	{
		
		$no_of_users[$chart_values[$i]] = $x;
		$x=1;
		
	}
	
}



//$no_of_users = array ( "0"=>"2","20"=>"3","66" => "2" ,"70" => "3", "100" => "1") ;






$users_arr_keys = @array_keys($no_of_users);


//PRINTING THE VALUES ON THE CHART

for($i=0;$i<count($users_arr_keys);$i++)
{
	$key = $users_arr_keys[$i];
	$val = $no_of_users[$key];
	
	$x1 = ($key * 4) + 15;
	$y1 = 180 - ($distance_inbetween * $val);
	

	$key1 = $users_arr_keys[$i+1];
	$val1 = $no_of_users[$key1];
	

	$x2 = (($key1) * 4) + 15;
	$y2 = 180 - ($distance_inbetween * $val1);

	

// imagearc ( $image, $x1, $y1, $x2, $y2, $x1, $x2, $border);
//echo "$y1<br>";
	if($key1!=NULL)
		{
		ImageLine($image,$x1,$y1,$x2,$y2,$border);
		}
		
	ImageChar ($image,$labelfont,$x1,$y1-5,".",$border);
	
}

header("Content-type: image/png");  	// or "Content-type: image/png"  
ImageJPEG($image); // or imagepng($image)  

ImageDestroy($image);


?>
