<?php

include_once("../session.php");

include_once("header.php");

class raters
{
	function have_raters_assigned($db_object,$common,$user_id)
	{
		$user_table=$common->prefix_table("user_table");
		
		$otherraters_table = $common->prefix_table("other_raters");	
		
		$tech_references = $common->prefix_table('tech_references');
				
		$sql="select user_id from $user_table where admin_id='$user_id'";
		
		$result=$db_object->get_single_column($sql);
		
		if($user_id==1)
		{
			$sql="select user_id from $user_table where user_id<>'$user_id'";
			
			$result=$db_object->get_single_column($sql);
		}
		
		if(count($result)>0)
		{
		
			$users=@implode(",",$result);
			
			$users_id="(".$users.")";
		//INTERPERSONAL...

		$mysql = "select cur_userid as user_id from $otherraters_table where status = 'a' and rating_over = 'n' and cur_userid in $users_id";
		
		$arr = $db_object->get_rsltset($mysql);
		
	
		
//TECHNICAL...
		
		$mysql = "select user_to_rate as user_id from $tech_references where  rating_over = 'n' and user_to_rate in $users_id";
	
		$tech_arr = $db_object->get_rsltset($mysql);

		$arr=@array_merge($arr,$tech_arr);
		
		for($a=0;$a<count($arr);$a++)
		{
			$arr1[$a]=$arr[$a][user_id];
		}
		
		$result_arr=@array_unique($arr1);
		
		$keys=@array_keys($result_arr);

		for($i=0;$i<count($keys);$i++)
		{
			$key=$keys[$i];
			
			$val=$result_arr[$key];
			
			$a=0;
			
			for($j=0;$j<count($arr);$j++)
			{
				if($val==$arr[$j][user_id])
				{
					$a++;
				}
			}
			$count[$i]=$a;
			
			$user[$i]=$key;
						
		}

		@arsort($count);

		$keys_count=@array_keys($count);

		$path=$common->path;
		
		$xtemplate=$path."templates/career/have_raters_assigned.html";
		
		$file=$common->return_file_content($db_object,$xtemplate);

		preg_match("/<{user_loopstart}>(.*?)<{user_loopend}>/s",$file,$match);
		
		$match=$match[0];

	
		for($i=0;$i<count($keys_count);$i++)
		{
			$key=$keys_count[$i];
			
			$count1=$count[$key];
			
			$key1=$user[$key];
			
			$userid=$arr1[$key1];
			
			$username=$common->name_display($db_object,$userid);
			
			$str.=preg_replace("/<{(.*?)}>/e","$$1",$match);
		}

		$file=preg_replace("/<{user_loopstart}>(.*?)<{user_loopend}>/s",$str,$file);

		
		$file=$common->direct_replace($db_object,$file,$xArray);
								
		echo $file;
		}
		
		
		
	}
}

$obj=new raters();

$obj->have_raters_assigned($db_object,$common,$user_id);

include_once("footer.php");
?>
