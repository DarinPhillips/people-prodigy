<?php

include_once("../session.php");

include_once("header.php");

class model
{
	function model_notin_carrer_goals($db_object,$common,$user_id,$error_msg)
	{
		$user_table=$common->prefix_table("user_table");
		
		$family_position=$common->prefix_table("family_position");
		
		$position_table=$common->prefix_table("position");
		
		$career_goals=$common->prefix_table("career_goals");
		
		if($user_id!=1)
		{
			$mysql = "select user_id,position from $user_table where admin_id='$user_id'";
		}
		else
		{
			$mysql = "select $user_table.user_id,$user_table.position
				from $user_table,$position_table 
				where $user_table.position=$position_table.pos_id 
				and ($user_table.position<>NULL or $user_table.position<>0) 
				and $user_table.user_id!=1 
				order by $position_table.level_no desc";			

		}
	
		$user_underadmin_arr = $db_object->get_rsltset($mysql);
		
		for($i=0;$i<count($user_underadmin_arr);$i++)
		{
			$user_pos[$i]=$user_underadmin_arr[$i][position];	
			
			$users_id[$i]=$user_underadmin_arr[$i][user_id];
		}
		
		if(count($user_pos)>0)
		{
			$userspos=@implode(",",$user_pos);
			
			$users="(".$userspos.")";
			
			$users_id=@implode(",",$users_id);
			
			$usersid="(".$users_id.")";
			
			$sql="select family_id from $family_position where position_id in $users group by family_id";
			
			$sql_result=$db_object->get_single_column($sql);
		
			if(count($sql_result)>0)
			{
				$fam=@implode(",",$sql_result);
				
				$fam=$fam.",0";
				
				$fam_arr="(".$fam.")";
				
				$mysql="select user_id from $career_goals where onelevel_low in $fam_arr or 
				
				same_level in $fam_arr or onelevel_up in $fam_arr or twolevel_up in $fam_arr
				
				and user_id in $usersid";
				
				$result=$db_object->get_single_column($mysql);
			}
		}
		if(count($result==0))
		{
			echo $error_msg['cEmptyrecords'];
			
			include_once("footer.php");
			
			exit;
		}
		
		$path=$common->path;
		
		$xtemplate=$path."/templates/career/models_notin_career_goals.html";
		
		$returncontent=$common->return_file_content($db_object,$xtemplate);
		
		preg_match("/<{position_loopstart}>(.*?)<{position_loopend}>/s",$returncontent,$match);
		
	$match = $match[0];

	for($i=0;$i<count($result);$i++)
	{
		$pos_id=$result[$i];

		$mysql = "select position_name from $position_table where pos_id='$pos_id'";
		
		$pos_res=$db_object->get_a_line($mysql);
		
		$position_name=$pos_res[position_name];
		
		$replace.= preg_replace("/<{(.*?)}>/e","$$1",$match);
		
		
	}	
	$returncontent = preg_replace("/<{position_loopstart}>(.*?)<{position_loopend}>/s",$replace,$returncontent);

		$returncontent = $common->direct_replace($db_object,$returncontent,$values);
		
		echo $returncontent;
		
		
		
		
	}
}

$obj=new model();

$obj->model_notin_carrer_goals($db_object,$common,$user_id,$error_msg);

include_once("footer.php");
?>
