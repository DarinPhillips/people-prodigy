<?
include_once("../session.php");
include_once("popupheader.php");
class phonedir
	{
	function view($db_object,$common,$default,$user_id,$emp_id)
		{
			if($emp_id!="")
			{
				$user_id = $emp_id;
			}
			$path = $common->path;
			$filename = $path."templates/performance/phonedirectory.html";
			$file = $common->return_file_content($db_object,$filename);
			$file = $common->direct_replace($db_object,$file,$res);
		
			$user_table = $common->prefix_table("user_table");
			$qry = "select user_id,username,office_phone from $user_table where admin_id='$user_id'";
			//$res = $db_object->get_rsltset($qry);
			

			$qry="select position  from $user_table where user_id='$user_id'";		
			$rs  = $db_object->get_a_line($qry);
			$position = $rs['position'];

			$rss = $common->get_chain_below($position,$db_object,$twodarr);
			$res = $common->get_user_id($db_object,$rss);
			$pattern="/<{record_loopstart(.*?)<{record_loopend}>/s";
			preg_match($pattern,$file,$arr);
			$match=$arr[0];
			$str="";
			for($i=0;$i<count($res);$i++)
			{
				//$qry = "select 
				
				$uid = $res[$i]['user_id'];
				$uname = $res[$i]['username'];
				$phone = $res[$i]['office_phone'];				
				$str.=preg_replace("/\<\{(.*?)\}\>/e","$$1",$match);

			}
			
			$file=preg_replace($pattern,$str,$file);
			echo $file;	
		}//end view
	}//end phonedir
		$ob = new phonedir;
		
		$ob->view($db_object,$common,$default,$user_id,$emp_id);
	
include_once("popupfooter.php");
?>
