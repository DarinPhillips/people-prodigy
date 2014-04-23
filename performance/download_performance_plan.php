<?
include_once("../session.php");
class download
{
function download_file($db_object,$common,$employee_id,$default,$err,$plan_id)
	{
		$path = $common->path;
		$xTemplate=$path."performance/plan_report/performance_plan.txt";
		
		$content=$common->return_file_content($db_object,$xTemplate);
		
		$plan=$common->prefix_table("plan");
		
		$user_table=$common->prefix_table("user_table");
		
		$performance_setting=$common->prefix_table("performance_setting");
		
		$temp_plan_improvement=$common->prefix_table("temp_plan_improvement");
		
		$plan_improvement=$common->prefix_table("plan_improvement");
		
		$sql="select * from $plan_improvement where plan_id='$plan_id'";
			
		$sql_result=$db_object->get_rsltset($sql);
		
		$qry="select username from $user_table where user_id='$employee_id'";
		
		$res=$db_object->get_single_column($qry);
		
		$qry1="select * from $plan where plan_id='$plan_id'";
		
		$res1=$db_object->get_a_line($qry1);
		
		preg_match("/<{loop_start}>(.*?)<{loop_end}>/s",$content,$match);
		
		$match=$match[0];
		
		for($i=0;$i<count($sql_result);$i++)
		{
			$j=$i+1;
			
			$text=$sql_result[$i][plan_text];
			
			$date=$sql_result[$i][plan_date];
			
			$date=$this->changedate_display($date);
			
			$str.=preg_replace("/<{(.*?)}>/e","$$1",$match);
		}

		$content=preg_replace("/<{loop_start}>(.*?)<{loop_end}>/s",$str,$content);
		
		$xArray[username]=$res[0];
		
		$xArray[requirement]=$res1[requirement];
		
		$xArray[consequences]=$res1[consequences];
		
		$date1=$res1[due_date];
		
		$xArray[date1]=$this->changedate_display($date1);
		
		$content=$common->direct_replace($db_object,$content,$xArray);
		
		$file = $path."performance/plan_report/performance_plan_$employee_id.txt";
		
		$fp=fopen($file,"w");
		
		fwrite($fp,$content);
		
		fclose($fp);
	
		if(file_exists($file))
		{



			$len  = filesize($file);
			$filename = "performance_plan_$employee_id.txt";
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
	function changedate_display($date)
	{
	list($year,$month,$date)=explode("-",$date);

	//$newdate="";

	$newdate=$month.'.'.$date.'.'.$year;


	return ($newdate);

	}
}//end class
	$ob  = new download;
	
	$ob->download_file($db_object,$common,$employee_id,$default,$error_msg,$plan_id);

?>
