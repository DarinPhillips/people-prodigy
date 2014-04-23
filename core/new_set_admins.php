<?php
include("../session.php");
include("header.php");
class New_set_admin
{
 function display_admins($common,$db_object,$user_id,$default)
 {
	$path=$common->path;
	$xFile=$path."templates/core/new_set_admins.html";
	$returncontent=$common->return_file_content($db_object,$xFile);



	$type="type_".$default;
		
		$admins_table = $common->prefix_table("admins");
		$user_table = $common->prefix_table("user_table");
		$family_table = $common->prefix_table("family");
		$location_table = $common->prefix_table("location");
		$position_table = $common->prefix_table("position");
		$employment_table = $common->prefix_table("employment_type");
		$orgmain_table = $common->prefix_table("org_main");
		
		$mysql = "select email,user_id from $user_table where user_id='$user_id'";
		$selemail=$db_object->get_a_line($mysql);


//-------------here the access rights is checked for 1 which is equal  to the admins access rights
//-----------the users  with the admins access   rights will be listed.And admin access rights should always be 1
		
		$mysql = "select $user_table.email,$user_table.user_id from $user_table left join $admins_table on $user_table.user_id=$admins_table.user_id  where $user_table.user_type='employee' and $user_table.position is not null and $user_table.access_rights=1  and $admins_table.user_id is null ";
		$mail_arr = $db_object->get_rsltset($mysql);


	$selqry="select user_id,username from $user_table where user_type='employee' and position is not null";
	$username_arr=$db_object->get_rsltset($selqry);




		$mysql = "select family_name,family_id from $family_table";
		$family_arr = $db_object->get_rsltset($mysql);
		
		
//		$mysql="select loc_name,loc_id from $location_table";
//		$loc_arr = $db_object->get_rsltset($mysql);
			

		$loc_arr=$common->return_location($db_object);


		$mysql="select position_name,pos_id from $position_table";
		$pos_arr = $db_object->get_rsltset($mysql);
		
		$mysql="select $type as type,id from $employment_table";
		$emp_arr = $db_object->get_rsltset($mysql);
		
		$mysql="select levels,higher_order from $orgmain_table";
		$level_arr = $db_object->get_a_line($mysql);


		$mysql="select distinct($position_table.position_name) as boss_name,$position_table.pos_id as boss_id from $position_table,$position_table as temp_position_table where temp_position_table.boss_no=$position_table.pos_id";
		$boss_arr = $db_object->get_rsltset($mysql);
		
	
		$levels = $level_arr["levels"];

		
		$values['family_loop']=$family_arr;
		$values['location_loop']=$loc_arr;
		$values['position_loop']=$pos_arr;
		$values['employment_loop']=$emp_arr;
		$values['email_loop']=$mail_arr;
		$values['boss_loop']=$boss_arr;
		$values['username_loop']=$username_arr;
		
	/*	$fields = $common->return_fields($db_object,$admins_table);
		$mysql = "select $fields from $admins_table where user_id='$user_id'";
		$sel_arr = $db_object->get_rsltset($mysql);
		
		for($i=0;$i<count($sel_arr);$i++)
		{
			$selfam_arr[] = $sel_arr[$i]["fam_id"];
			$selloc_arr[] = $sel_arr[$i]["location_id"];
			$selpos_arr[] = $sel_arr[$i]["pos_id"];		
			$selemp_arr[] = $sel_arr[$i]["emp_type_id"];
			$sellev_arr[] = $sel_arr[$i]["level_id"];
			$selboss_arr[]= $sel_arr[$i]["boss_id"];


		}
		
		$sel_arr['family_loop']		= $selfam_arr;
		$sel_arr['location_loop']	= $selloc_arr;
		$sel_arr['position_loop']	= $selpos_arr;
		$sel_arr['employment_loop']	= $selemp_arr;
		$sel_arr['level_loop']		= $sellev_arr;
		$sel_arr['boss_loop']		= $selboss_arr;
		$sel_arr['email_loop']["email"]	=$selemail["email"];
		
		$str = "";
		
		
		for($i=0;$i<$levels;$i++)
		{
			$x = $i+1;
			$level_value[$i]["level_value"] = $x;
		}*/
		$levels=$common->return_levels($db_object);
		while(list($kk,$vv)=@each($levels))
		{
			$level_value[]["level_value"] = $levels[$kk];
		}
	$values['level_loop'] = $level_value;
		
	$returncontent	= $common->multipleloop_replace($db_object,$returncontent,$values,$sel_arr);
	
	$values["directreplace"]["user_id"]=$user_id;

	$returncontent = $common->direct_replace($db_object,$returncontent,$values);
		

	echo $returncontent;
	

  }
}
$newobj= new New_set_admin;


$newobj->display_admins($common,$db_object,$user_id,$default);
include("footer.php");
?>