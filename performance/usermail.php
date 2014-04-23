<?
include_once("../session.php");
include_once("popupheader.php");
class phonedir
	{
	function view($db_object,$common,$default,$user_id,$emp_id,$name,$email,$sid)
		{
//echo "nam=$name";
			if($emp_id!="")
			{
				$user_id = $emp_id;
			}
			$path = $common->path;
			$filename = $path."templates/performance/usermail.html";
			$file = $common->return_file_content($db_object,$filename);


			$temp['emp_id']	= $val['emp_id'];
			$temp['field_name']	= $name;
			$temp['field_mail'] = $email;
			$temp['field_id'] = $sid;

			$file = $common->direct_replace($db_object,$file,$temp);

			$user_table = $common->prefix_table("user_table");
			$pos = "select position from $user_table where user_id='$user_id'";
			$rrs = $db_object->get_a_line($pos);
			$position = $rrs['position'];
			$rss = $common->get_chain_below($position,$db_object,$twodarr);
			$res = $common->get_user_id($db_object,$rss);
			$pattern="/<{record_loopstart(.*?)<{record_loopend}>/s";
			preg_match($pattern,$file,$arr);
			$match=$arr[0];
			$str="";
			for($i=0;$i<count($res);$i++)
			{
				
				$uid = $res[$i]['user_id'];
				$uname = $res[$i]['username'];
				$email = $res[$i]['email'];
				
				$str.=preg_replace("/\<\{(.*?)\}\>/e","$$1",$match);

			}
			
			$file=preg_replace($pattern,$str,$file);
			echo $file;	
		}//end view
	}//end phonedir
		$ob = new phonedir;
		$ob->view($db_object,$common,$default,$user_id,$emp_id,$name,$email,$sid);
	
include_once("popupfooter.php");
?>
