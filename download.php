<?
//include_once("session.php");
include_once("includes/database.class");
include_once("includes/common.class");

$db_object=new database();
$common=new common();
class download
{
function download_file($db_object,$common,$user_id,$load)
{
	
	$path=$common->path;
	
	$external=$common->prefix_table("external");
	
	$sql="select $load from $external where user_id='$user_id'";
	//echo $sql;exit;
	$sql_res=$db_object->get_single_column($sql);
	
	$filename=$sql_res[0];
	
		$file = $path."uploads/externalcandidate/$load/$filename";
		
		if(file_exists($file))
		{

			$len  = filesize($file);
			$filename =$load."_".$filename;
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
//echo $str;

			
		}
	
	}
	
}//end class
	$ob  = new download;
	
	$user_id=$_GET["user_id"];
	
	$load=$_GET["load"];
	

$ob->download_file($db_object,$common,$user_id,$load);

?>
