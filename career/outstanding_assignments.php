<?php
include("../session.php");
include("header.php");

class assignments
{
	function outstanding_assignments($db_object,$common,$user_id)
	{
		$users=$common->return_direct_reports($db_object,$user_id);
		
		$user_table=$common->prefix_table("user_table");
		
		$position=$common->prefix_table("position");

		if($users[0]!="")
		{
			if(count($users)>1)
			{
				$users_id=@implode(",",$users);
					
				$users_id="(".$users_id.")";
			}
			else
			{
				$users_id="(".$users[0].")";
			}
		
		$qry="select $user_table.user_id,$user_table.username,date_format($position.date_added,'%m.%d.%Y.%i:%s'),
		
		$position.position_name,$user_table.email from $user_table,$position where 
		
		$user_table.position=$position.pos_id and $user_table.user_id in $users_id order by 
		
		$position.date_added asc";
		
		$res=$db_object->get_rsltset($qry);
		
		$path=$common->path;
				
		$xtemplate=$path."templates/career/outstanding_assignments.html";
		
		$file=$common->return_file_content($db_object,$xtemplate);
				
		$pattern="/<{user_loopstart}>(.*?)<{user_loopend}>/s";
			
		preg_match($pattern,$file,$match);
		
		$match=$match[0];

		for($a=0;$a<count($res);$a++)
		{
			$user=$res[$a][user_id];
			
			$name=$common->name_display($db_object,$user);
			
			$date=$res[$a][2];

			$pos=$res[$a][position_name];
			
			$email=$res[$a][email];
			
			$str.=preg_replace("/<{(.*?)}>/e","$$1",$match);
		}
					
		$file=preg_replace($pattern,$str,$file);

		$file=$common->direct_replace($db_object,$file,$values);	
		
		echo $file;
		}
		
			
	}
}
$obj=new assignments();

$obj->outstanding_assignments($db_object,$common,$user_id);

include_once("footer.php");

?>
