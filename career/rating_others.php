<?php

include_once("../session.php");

$user_table=$common->prefix_table("user_table");

$other_raters=$common->prefix_table("other_raters");

$tech_references=$common->prefix_table("tech_references");

$user_tests=$common->prefix_table("user_tests");

$current_date=time()-(7*24*60*60);

$one_week=7*24*60*60;

$today = date("Y-m-d H:i:s ",$current_date);    

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

if(count($users_id)>0)
{
		$users=@implode(",",$users_id);
		
$sql1="select count(rater_userid) as count
		
 from $other_raters,$user_table where $other_raters.cur_userid=$user_table.user_id and
		 
  cur_userid<>rater_userid and 
		  
 rating_over='y' and rater_userid in"."(".$users.")"." and (date_rating_requested-date_rating_over)<='$one_week'";
 
 $res1=$db_object->get_single_column($sql1);

 $o_count+=$res1[0];
 
$sql2="select count(ref_userid) as count

from $tech_references,$user_table where $tech_references.user_to_rate=$user_table.user_id and

user_to_rate<>ref_userid and 

rating_over='y' and ref_userid in "."(".$users.")"." and (date_rating_requested-date_rating_over)<='$one_week'";

$res2=$db_object->get_single_column($sql2);

$o_count+=$res2[0];

$sql1="select count(rater_userid) as count
		
 from $other_raters,$user_table where $other_raters.cur_userid=$user_table.user_id and
		 
  cur_userid<>rater_userid and 
		  
  rater_userid in"."(".$users.")";
 
 $res1=$db_object->get_single_column($sql1);
 
 $ot_count+=$res1[0];
 
$sql2="select count(ref_userid) as count

from $tech_references,$user_table where $tech_references.user_to_rate=$user_table.user_id and

user_to_rate<>ref_userid and 

ref_userid in "."(".$users.")";

$res2=$db_object->get_single_column($sql2);

$ot_count+=$res2[0];
$pie=new piechart();

$path=$common->path;

$array=array($o_count,$ot_count);

if($ot_count!=0)
{
	
			$heads = array(
    array("Rating Others", 3, "c"),  
    );

$vals=$pie->return_Array($array);

$image->init(150,150, $vals);

$image->draw_heading($heads);

$image->set_legend_percent();

 $image->display($filename);

}


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
