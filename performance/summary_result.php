<?php
	include("../session.php");

class graph_results
{	
	function display($db_object,$common,$user_id,$default,$error_msg,$learning,$get_var)
	{

		$width = 340;
		$height = 220;

	//	$labelfont = '2';
		$labeltitlefont ='3';
		$image = ImageCreate($width, $height); 

		while(list($kk,$vv)=@each($get_var))
		{
			$$kk = $vv;	
		}
//table 		
		$config_table = $common->prefix_table("config");
		$color_qry = "select commit_color,accomplish_color from $config_table where id='1'";
		$res = $db_object->get_a_line($color_qry);
		$ccolor = $res['commit_color'];
		$acolor = $res['accomplish_color'];
		$com = $common->split_color($db_object,$ccolor);
		$accom = $common->split_color($db_object,$acolor);


		
		$bgcolor = ImageColorAllocate($image,0xFFFFFF, 0xFFFFFF, 0xFFFFFF);  
		$border = ImageColorAllocate($image,0x000000, 0x000000, 0x000000); 
		$p1 = "0x$com[0]";
		$p2 = "0x$com[1]";
		$p3 = "0x$com[2]";
		
		$p1=hexdec($p1);
		$p2=hexdec($p2);
		$p3=hexdec($p3);
		
		$commit_color = imagecolorallocate($image,$p1 ,$p2 , $p3);
//		$commit_color = $commit;
		$p1 = "0x$accom[0]";
		$p2 = "0x$accom[1]";
		$p3 = "0x$accom[2]";
		$p1=hexdec($p1);
		$p2=hexdec($p2);
		$p3=hexdec($p3);
		$accomplish_color = imagecolorallocate($image, $p1, $p2, $p3);




		/* to check 
		$date_array = "2003-12-13,2003-12-21,2003-12-27";
		$fulfilled_array = "25,75,80";
		$fromdate = "2003-12-10";
		$todate	="2003-12-29";	*/
		

		$fulfilled_array = @explode(",",$fulfilled_array);
		$date_array = @explode(",",$date_array);

		ImageRectangle($image,40,24,293,180,$border);
		ImageString($image, $labelfont, 15,19, "100%", $border);
		ImageString($image, $labelfont, 20,58, "75%", $border);
		ImageString($image, $labelfont, 20,97, "50%", $border);
		ImageString($image, $labelfont, 20,136, "25%", $border);
		ImageString($image, $labelfont, 20,175, "0%", $border);
		
		$accomplishment = $error_msg['cAccomplishment'];
		$results	 = $error_msg['cResults'];
		$commitment	 = $error_msg['cCommitment'];
	
		$fdate=$fromdate;
		$tdate=$todate;

		$to = split("-",$tdate);
		$from = split("-",$fdate);
		
		$dt1 = mktime(0,0,0,$to[1],$to[2],$to[0]);
		$dt2 = mktime(0,0,0,$from[1],$from[2],$from[0]);

		$dt3 = $dt1 - $dt2;
		$daydiff=(($dt3/60)/60)/24;


		
		$days = $error_msg['cDays']." $fdate ".$error_msg['cTo']." $tdate ";	

		
		ImageStringUp($image, $labeltitlefont, 5,110, $error_msg['cResults'], $border);		
		//ImageString($image, $labeltitlefont, 260,6, $error_msg['cCommitment'], $border);
		ImageString($image, $labeltitlefont, 50,182, "$days", $border);	
		ImageLine($image,293,24,40,180,$commit_color);   //COMMITMENT LINE		
				
		$x1 = 40;
		$y1 = 180;
		$startx_val = $x1;
		$starty_val = $y1;
		//$x2 = 130;
		//$y2 = 95;

		$min=38;
		$max = 292;
		$tot = $max - $min;

		$count = $daydiff   ;
		$interval = @($tot / $count);
		$interval = sprintf("%01.0f", $interval);

		//ImageString($image, $labelfont, 15,40, "$fulfilled_array", $border);
		for($j=0;$j<=$count;$j++)
		{
			if($min <= $max)
			ImageString($image, $labelfont,$min,176, "|", $border);	
			$min = $min + $interval;
		}

		$d_array = array();
		$inc = 40;
		
		$im_dates = split("-",$fdate);

		//to calculate the x2 value
			$odd = array("01","03","05","07","08","10","12");//31 days
			$even = array("04","06","09","11");//30 days
			for($d=0;$d<count($date_array) ;$d++)
			{
				$start_date = $im_dates[2];
				$ar = $date_array[$d];
				$im_da = split("-",$ar);
				$dte = $im_da[2];
				$m = $im_da[1];
				
				for($c=0;$c<=$daydiff;$c++)
				{

					if($dte==$start_date)
					{	
					
						$dt_array[] = $c;

					}

					$start_date = $start_date + 1;
					if(in_array($m,$odd))
					{
						if($start_date ==32)
						{
							$start_date = 1;
							$m = $m + 1;
							if($m ==13)
							{
								$m=1;
							}
						}
					}
					elseif(in_array($m,$even))
					{
						if($start_date==31)
						{
							$start_date = 1;
							$m = $m  + 1;
							if($m==13)
							{
								$m=1;
							}
						}
					}
					else
					{
						if($start_date==29)
						{
							$start_date=1;
							$m = $m + 1;
						}
					}
			//ImageString($image, $labelfont, 50,$inc,"$start_date", $border);
			$inc = $inc+ 15;					
				}		
			}				
		for($i=0;$i<count($fulfilled_array);$i++)
		{
			$ful = $fulfilled_array[$i];
			$dt_val = $dt_array[$i];			
			$x2 =	$startx_val + ($interval * $dt_val);  //cal using No of Days						
			$div= $ful/25;			
			$y2 = $starty_val - (39 * $div) ;				
			ImageLine($image,$x1,$y1,$x2,$y2,$accomplish_color);						
			$x1 = $x2;
			$y1 = $y2;
			$d_array="";						
		}
			
		ImageFilledRectangle($image,40,200,60,202,$commit_color);
		ImageString($image, $labeltitlefont, 65,195, $error_msg['cCommitment'], $border);
		ImageFilledRectangle($image,180,200,200,202,$accomplish_color);
		ImageString($image, $labeltitlefont,205,195, $error_msg['cAccomplished'], $border);
		header("Content-type: image/png");  	
		Imagepng($image); 		
		ImageDestroy($image);
		
	}
		
}

$obj=new graph_results;
$obj->display($db_object,$common,$user_id,$default,$error_msg,$learning,$_GET);
include("footer.php");
?>
