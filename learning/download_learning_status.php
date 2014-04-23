<?
include_once("../session.php");
class download
{
function download_file($db_object,$common,$user_id,$uid,$default,$err)
	{
		if($uid!="")
		{
			$user_id =$uid;
		}

		$path = $common->path;
		$file = $path."learning/status_report/learning_status_report_$user_id.txt";
		if(file_exists($file))
		{
			$len  = filesize($file);
			$filename = "learning_status_report_$user_id.txt";
			header("content-type: application/stream");
			header("content-length: $len");
			header("content-disposition: attachment; filename=$filename");
			$fp=fopen($file,"r");			
			fpassthru($fp);
			exit;
		}
		else
		{
			
$str=<<<EOD
		<script>
			alert( '$err[cEmptyrecords]' );
			window.location=document.referrer;
		</script>
EOD;
echo $str;

			
		}
	}
}//end class
	$ob  = new download;
	$ob->download_file($db_object,$common,$user_id,$uid,$default,$error_msg);

?>
