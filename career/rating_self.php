<?php
//GAUGES1

include_once("../session.php");

$user_table=$common->prefix_table("user_table");

$other_raters=$common->prefix_table("other_raters");

$tech_references=$common->prefix_table("tech_references");

$user_tests=$common->prefix_table("user_tests");

if($user_id!=1)
{
	$sql="select user_id from $user_table where admin_id='$user_id'";
}
else
{
	$sql="select user_id from $user_table where user_id<>'$user_id'";
}
$users_id=$db_object->get_single_column($sql);

$count=0;

$one_week=7*24*60*60;

if(count($users_id)>0)
{
	$users=@implode(",",$users_id);
	
	$user_clause="and $other_raters.cur_userid in "."(". $users.")";

$sql1="select count(rater_userid) as count
	
from $other_raters,$user_table where $user_table.user_id=$other_raters.cur_userid and 
	 
cur_userid=rater_userid and rating_over='y' ".$user_clause." and (date_rating_over-date_rating_requested)<='$one_week'";

$res1=$db_object->get_single_column($sql1);

$count+=$res1[0];

$sql2="select count($user_tests.user_id) as count from $user_table,$user_tests
		
where $user_table.user_id=$user_tests.user_id and test_completed='y' and $user_tests.user_id in "."(".$users.")";

$res2=$db_object->get_single_column($sql2);

$count+=$res2[0];

$sql3="select count(ref_userid) as count from $tech_references,$user_table
		
where $tech_references.user_to_rate=$user_table.user_id and ref_userid in "."(".$users.") and
		
user_to_rate=ref_userid and rating_over='y' "." and (date_rating_over-date_rating_requested)<='$one_week'";

$res3=$db_object->get_single_column($sql3);

$count1+=$res3[0];

$sql1="select count(rater_userid) as count 
	
from $other_raters,$user_table where $user_table.user_id=$other_raters.cur_userid and 
	 
cur_userid=rater_userid ".$user_clause;

$res1=$db_object->get_single_column($sql1);

$count1+=$res1[0];

$sql2="select count($user_tests.user_id) as count from $user_table,$user_tests
		
where $user_table.user_id=$user_tests.user_id and $user_tests.user_id in "."(".$users.")";

$res2=$db_object->get_single_column($sql2);

$count1+=$res2[0];

$sql3="select count(ref_userid) as count
		
from $tech_references,$user_table where $tech_references.user_to_rate=$user_table.user_id and
		
user_to_rate=ref_userid and ref_userid in "."(".$users.")";

$res3=$db_object->get_single_column($sql3);

$count1+=$res3[0];

}
$path=$common->path;
if($count1!=0)
{
	$percent=$count/$count1;
	
	$array=array($count,$count1);
	
			$heads = array(
    array("Rating Self", 3, "c"),  
    );
	
$vals=$image->return_Array($array);

$image->init(150,150, $vals);

$image->draw_heading($heads);

$image->set_legend_percent();

$image->display($filename);
}
else
{
	
	$heads = 
	array(
	    			array("No employee",3,"c"),
	    			array("under this admin", 3, "c")
    		);



	$image = ImageCreate(150, 150); 

	$white = ImageColorAllocate($image,255,255,255);
	
	$black = ImageColorAllocate($image,0,0,0);
	
	
	ImageString($image, $heads[0][1],10,0, $heads[0][0],
	
   	$black);
	
	ImageString($image, $heads[1][1],10,15, $heads[1][0],
	
   	$black);
	
    
   	//ImageString($image,3,50,50,'heading',16);

	ImagePng($image);

}
?>
