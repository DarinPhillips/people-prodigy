<?php
/*---------------------------------------------
SCRIPT:select_posmodels.php
AUTHOR:info@chrisranjana.com	
UPDATED:09 Dec 03

DESCRIPTION:
This script displays the fourth step of position models created by admin.

---------------------------------------------*/
include("../session.php");
include("header.php");
class personvsposmodel
{
	function show_screen($db_object,$common,$user_id,$default,$error_msg,$post_var)
	{	
		while(list($kk,$vv) = @each($post_var))
		{
			$$kk = $vv;
		}
		
		$xPath		= $common->path;
		$returncontent	= $xPath."/templates/career/select_posmodels.html";
		$returncontent	= $common->return_file_content($db_object,$returncontent);


		$user_table 	= $common->prefix_table('user_table');
		$position 	= $common->prefix_table('position');
		$model_name_table= $common->prefix_table('model_name_table');
		$model_view_1 	= $common->prefix_table('model_view_1');
		$model_view_2 	= $common->prefix_table('model_view_2');
		$model_table	= $common->prefix_table('model_table');

	//echo "empl=$empl";


		
		//USERS FOR WHOM THE PERSON IS ADMIN...

		$mysql = "select $user_table.user_id as user_id 
			from $user_table,$user_table as u1 
			where $user_table.admin_id=u1.user_id and u1.user_id='$user_id' 
			order by u1.position";
		$employees_under_admin_arr = $db_object->get_single_column($mysql);
		//print_r($employees_under_admin_arr);

		//USERS FOR WHOM THE PERSON IS BOSS...

		$mysql = "select $user_table.user_id
				from $user_table,$position,
				$user_table as u1,
				$position as p1 
				where $position.pos_id = $user_table.position 
				and u1.position = $position.boss_no 
				and p1.pos_id=u1.position 
				and u1.user_id = '$user_id'
				order by u1.user_id";
		$employees_under_boss_arr = $db_object->get_single_column($mysql);
		//print_r($employees_under_boss_arr);

		$all_employees_arr = @array_merge($employees_under_admin_arr,$employees_under_boss_arr);
		$all_employees_arr = @array_unique($all_employees_arr);
		
		//print_r($all_employees_arr);

		if($all_employees_arr != '')
		{
		$all_empl_full = @implode("','",$all_employees_arr);
			
		$mysql = "select user_id,username from $user_table where user_id in ('$all_empl_full')";
		$users_arr = $db_object->get_rsltset($mysql);
		$values['employee_loop'] = $users_arr;
		}

/*===================================================================================


//SHOW THE MODELS WHICH THAT PARTICULAR PERSON IS CAPABLE OF VIEWING...
			
		$mysql = "select level_no from $user_table, $position 
				where $user_table.position = $position.pos_id
				and $user_table.user_id = '$user_id'";
		$lev_arr = $db_object->get_a_line($mysql);
		$cur_level = $lev_arr['level_no'];
		
		$mysql = "select model_id from $model_view_1 where levels_to_view = '$cur_level'";
		$models_view1 = $db_object->get_single_column($mysql);
		
//print_r($models_view1);

		$check_boss = $common->is_boss($db_object,$user_id);
		$check_admin = $common->is_admin($db_object,$user_id);
			
		if($check_boss == 1)
		{
			$subqry_boss = "or boss='yes'";
		}
		if($check_admin == 1)
		{
			$subqry_admin = "or admins='yes'";
		}

//MODELS WHICH THE ADMINS OR BOSSES CAN VIEW...
		
		if($check_boss == 1 || $check_admin == 1)
		{
		$mysql = "select model_id from $model_view_2 where model_id<>'0' $subqry_boss $subqry_admin";

		//echo $mysql;
		$models_view2 = $db_object->get_single_column($mysql);

		}
//MODELS WHICH ALL ARE ALLOWED TO VIEW...

		$mysql = "select model_id from $model_view_2 where all1 = 'yes'";
		$models_view2_sub1 = $db_object->get_single_column($mysql);

//MODELS WHICH THE SELF CAN VIEW...
		
		$mysql = "select model_id from $model_view_2 where me = 'yes'";
		$viewcheck = $db_object->get_single_column($mysql);	
		//print_r($viewcheck);
		
		if(@in_array($user_id,$viewcheck))
		{
			$mysql = "select model_id from $model_table where user_id = '$user_id'";
			$models_view2_sub2 = $db_object->get_single_column($mysql);
		}
 


$temp_arr = @array_merge($models_view1,$models_view2,$models_view2_sub1,$models_view2_sub2);

$main_viewmodels = @array_unique($temp_arr);

$modelids = @implode("','",$main_viewmodels);



==========================================================================*/
//DETERMINING THE MODELS THAT A PARTICULAR PERSON IS CAPABLE OF VIEWING... 

	$modelids_arr = $common->viewable_models($db_object,$empl);

	$modelids = @implode("','",$modelids_arr);

	if($modelids != '')
		{
		$mysql = "select model_id, model_name from $model_name_table where model_id in ('$modelids')";
		$model_arr = $db_object->get_rsltset($mysql);
		
		}
	/*else
		{
		$returncontent = preg_replace("/<{model_loopstart}>(.*?)<{model_loopend}>"/s,"",$returncontent);
		$model_arr='';
		$values['model_loop'] = $model_arr;
		}
	*/	
		$values['model_loop'] = $model_arr;
		
		$sel_arr['employee_loop']['user_id'] = $empl;

		$returncontent = $common->multipleloop_replace($db_object,$returncontent,$values,$sel_arr);
		$returncontent = $common->direct_replace($db_object,$returncontent,$values);
		echo $returncontent;
	}
}

$obj = new personvsposmodel;

$obj->show_screen($db_object,$common,$user_id,$default,$error_msg,$post_var);

include("footer.php");

?>
