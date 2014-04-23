<?
include_once("../session.php");
class download
{
function download_file($db_object,$common,$approved_on,$default,$err,$user_id,$_POST)
	{
		$path = $common->path;
		
		$xTemplate=$path."performance/approved_appraisal/appraisal_admin.txt";
		
		$file=$common->return_file_content($db_object,$xTemplate);
		
		preg_match("/<{objective_loopstart}>(.*?)<{objective_loopend}>/s",$file,$match);
		
		$match=$match[0];

		$fCount=$_POST[fCount];
		$keys=@array_keys($_POST);
		
		$cnt=count($keys);
		
		$cnt=$cnt-3;
		
		$key=$keys[$cnt];
		
		$boss=@explode("_",$key);
		
		$no_boss=$boss[2];//no of bosses
		
		$key=$keys[1];
		
		$obj=@explode("_",$key);
		
		//$no_obj=$obj[1];//no of obj
		
		$b=9;
		
		$cnt=$b+$no_boss*2;
		
		$a=1;
		
		$value=@array_values($_POST);
		
		
		for($i=0;$i<$fCount;$i++)
		{
					
			$c=$a;
			
			$rater_no=$value[$c];
		
			$c++;
			
			$objective=$value[$c];
			
			$c++;
			
			$committed=$value[$c];

			$c++;
			
			$accomplished=$value[$c];
			
			$c++;
			
			$fulfilled=$value[$c];
			
			$c++;
			
			$priority=$value[$c];
			
			$c++;
			
			$expectation_points=$value[$c];
			
			$c++;
			
			$actual_rating=$value[$c];
			
			$c++;
			
			$expectation_met=$value[$c];
			
			$c++;
						
			$comment_boss="";
			
			for($j=$c;$j<($c+$no_boss*2);$j++)
			{
				
				$boss_name=$value[$j];
				
				$j++;
			
				$boss_comment=$value[$j];
				
								
				$boss_comment=stripslashes($boss_comment);
				
				$comment_boss.=$boss_name.":".$boss_comment;
				
	
			}
			
			
			$comment_rater="";
			
			for($k=$j;$k<($j+$rater_no*2);$k++)
			{
				
				$rater_name=$value[$k];
				
				$k++;
				
				$rater_comment=$value[$k];
				
				$rater_comment=stripslashes($rater_comment);
				
				$comment_rater.=$rater_name.":".$rater_comment;
				
				
			}
				
						
				$k=$j+$rater_no*2;
				
				$point1=$value[$k];
				
				$k++;
				
				$point2=$value[$k];
				
				$k++;
				
			for($a=$k;$a<($k+$no_boss*2);$a++)
			{
			
				$rater_exp_name=$value[$a];
				
				$a++;
				
				$rater_exp=$value[$a];
				
				$rater_exp=stripslashes($rater_exp);
				
				$exp_rater.=$rater_exp_name.":".$rater_exp;
				
			
			}
					
				
				
				$str.=preg_replace("/<{(.*?)}>/e","$$1",$match);
				
			}
		
		
				$val[final_point1]=$value[$a];
				
				$a++;
				
				$val[final_point2]=$value[$a];
				
				$a++;
			$final="";	
				
			for($b=$a;$b<($a+$no_boss*2);$b++)
			{
			
				$final_name=$value[$b];
				
				$b++;
				
				$final_exp=$value[$b];
				
				$final_exp=stripslashes($final_exp);
				
				$final.=$final_name.":".$final_exp;
			}
			
								
		$val[username]=$_POST[fName];
		
	
		$val[final]=$final;
		
		$file=preg_replace("/<{objective_loopstart}>(.*?)<{objective_loopend}/s",$str,$file);
		
		$content=$common->direct_replace($db_object,$file,$val);
		
		//echo $content;exit;
		
		$file = $path."performance/approved_appraisal/appraisal_admin_$user_id.txt";
		
		$fp=fopen($file,"w");
		
		fwrite($fp,$content);
	
		fclose($fp);
		
		if(file_exists($file))
		{

			$len  = filesize($file);
			$filename = "appraisal_admin_$user_id.txt";
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
	
	$ob->download_file($db_object,$common,$approved_on,$default,$err,$user_id,$_POST);

?>
