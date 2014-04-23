<?
include_once("../session.php");
include_once("header.php");
class selperformance
{
	function view_form($db_object,$common,$default,$user_id,$err,$self,$date_format)
	{
		$path=$common->path;
		$filename = $path."templates/performance/selected_for_feedback.html";
		$file = $common->return_file_content($db_object,$filename);
	
	//table declaration
		$feedback = $common->prefix_table("performance_feedback");
		$usertable = $common->prefix_table("user_table");

		$qry = "select request_from,date_format(s_date,'$date_format') as s_date from $feedback where user_id='$user_id' 
		and request_from<>'$user_id' and  status='I'
		 group by request_from order by f_id";

		$res = $db_object->get_rsltset($qry);

		$pattern="/<{record_loopstart(.*?)<{record_loopend}>/s";
			preg_match($pattern,$file,$arr);
			$match=$arr[0];
			$str="";			
			for($i=0;$i<count($res);$i++)
			{
				$uid = $res[$i]['request_from'];
				$sqry = "select username from $usertable where user_id='$uid'";
				$sres = $db_object->get_a_line($sqry);
				$empname = $sres['username'];
				$date = $res[$i]['s_date'];
				$dt = $res[$i]['s_date'];				
				$str.=preg_replace("/\<\{(.*?)\}\>/e","$$1",$match);
			}
			$file=preg_replace($pattern,$str,$file);
			if($str!="")
			{	
				
			}
			else
			{
				$file = preg_replace("/\<table(.*?)\<\/table\>/s",$err['cEmptyrecords'],$file);						

			}
		$file = $common->direct_replace($db_object,$file,$val);
		echo $file;

	}//end view
}//end class
	$ob = new selperformance;
	$ob->view_form($db_object,$common,$default,$user_id,$error_msg,$self,$gbl_date_format);
include_once("footer.php");
?>
