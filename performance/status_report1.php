<?php
	include("../session.php");

class graph_results
{	
	function display($db_object,$common,$user_id,$default,$error_msg,$learning,$get_var)
	{


		$width = 345;
		$height = 300;

		$labelfont = 2;
		$labelfont1 = 1;
		$labeltitlefont ='3';
		$image = ImageCreate($width, $height); 

		while(list($kk,$vv)=@each($get_var))
		{
			$$kk = $vv;	
		}
	
		$bgcolor = ImageColorAllocate($image,0xFFFFFF, 0xFFFFFF, 0xFFFFFF);  
		$border = ImageColorAllocate($image,0,0,0); 
		$blue = imagecolorallocate($image, 0x33, 0x00, 0xFF);
		$red = imagecolorallocate($image, 0xFF, 0x00, 0x00);
		$green = imagecolorallocate($image, 0x33, 0xCC, 0x00);
		$rose = imagecolorallocate($image, 0xFF, 0x33, 0xFF);
		$color1 = $red;
		$color2 = $blue;
		$color3 = $green;
		$color4 = $rose;

		/* to check 
		$date_array = "2003-12-01,2003-12-02,2003-12-02,2003-12-12:2003-12-12:2003-12-20,2003-12-22:2003-12-23:2003-12-19";
		$fulfilled_array = "0.00,0.00,30.00,0.00:20.00:50.00,80.00:55.00:60.00";
		$ratername = "p,anish,naren,mano,jacob";
		$avgval = "6.00,20.00,50.00,45.00,75.00";
		$fromdate = "2003-12-01";
		$todate	="2003-12-25";*/
		

		$fdate=$fromdate;
		$tdate=$todate;
		
		$mgfulfill = @split(":",$fulfilled_array);
		$mgdate = @split(":",$date_array);
		$mgname = @split(",",$ratername);
		$mgavg = @split(",",$avgval);
					

		$to = split("-",$tdate);
		$from = split("-",$fdate);
		
		$dt1 = mktime(0,0,0,$to[1],$to[2],$to[0]);
		$dt2 = mktime(0,0,0,$from[1],$from[2],$from[0]);

		$dt3 = $dt1 - $dt2;
		$daydiff=(($dt3/60)/60)/24;
		$days = $error_msg['cDays']." $fdate ".$error_msg['cTo']." $tdate ";	
	
		ImageRectangle($image,40,24,305,180,$border);
		ImageString($image, $labelfont, 15,19, "100%", $border);
		ImageString($image, $labelfont, 20,58, "75%", $border);
		ImageString($image, $labelfont, 20,97, "50%", $border);
		ImageString($image, $labelfont, 20,136, "25%", $border);
		ImageString($image, $labelfont, 20,175, "0%", $border);
		ImageString($image, $labeltitlefont, 70,182, "$days", $border);	
		ImageStringUp($image, $labeltitlefont,5,140, $error_msg['cResults'], $border);		
		Imageline($image,40,180,305,24,$border);
	
		$im_dates = split("-",$fdate);
		$n_inc = 200;

		/*$p1 = "33";
		$p2 = "CC";
		$p3 = "CC";

		$p1 = hexdec($p1);
		$p2 = hexdec($p2);
		$p3 = hexdec($p3);*/



		for($e=0;$e<count($mgname);$e++)
		{
			$p1 = rand(0,200);
			$p2 = rand(30,250);
			$p3 = rand(100,250);
			
			$name = $mgname[$e];
			$fulfilled_array = @split(",",$mgfulfill[$e]);
			$date_array = @split(",",$mgdate[$e]);
			$pl = $e+1;
			$col = "color".$pl;
			$x1 = 40;
			$y1 = 180;
			$startx_val = $x1;
			$starty_val = $y1;

			//echo "p1=$p1 p2=$p2 p3=$p3<br>";
			//echo "hp1=$p1 hp2=$p2 hp3=$p3<br>";

			$color = imagecolorallocate($image,$p1,$p2,$p3);			
			

			$min=38;
			$max = 305;
			$tot = $max - $min;
		
			$count = $daydiff   ;
			$interval = @($tot / $count);
			$interval = sprintf("%01.0f", $interval);

		//ImageString($image, $labelfont, 15,40, "$fulfilled_array", $border);
			for($j=0;$j<=$count;$j++)
			{
				if($min <= $max)
				ImageString($image, $labelfont1,$min,176, "|", $border);	
				$min = $min + $interval;
			}

			$dt_array = array();
			$inc = 40;
		
			

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

				}		
			}



		for($i=0;$i<count($fulfilled_array);$i++)
		{
			$ful = $fulfilled_array[$i];
			$dt_val = $dt_array[$i];			
			$x2 = $startx_val + ($interval * $dt_val);  //cal using No of Days						
			$div= $ful/25;			
			$y2 = $starty_val - (39 * $div) ;			
			ImageLine($image,$x1,$y1,$x2,$y2,$color);
			//imagefill($image,$x1,$y1,$color);
			//imagefill($image,$x2,$y2,$color);
			//echo "x1 = $x1 : y1 = $y1 x2 = $x2 y2 = $y2<br>";
			$x1 = $x2;
			$y1 = $y2;
		}
			$avg = $mgavg[$e];
			ImageString($image, $labelfont1,130,$n_inc,"$name  $error_msg[cAvg]=$avg",$color);
			$n_inc += 10;	
		}//e loop
	
			//imagefill($image,59,123,$green);

		header("Content-type: image/png");  				
		Imagepng($image); 		
		ImageDestroy($image);

		
	}//graph
}//end class

$obj=new graph_results;
$obj->display($db_object,$common,$user_id,$default,$error_msg,$learning,$_GET);
//$obj ->expected($db_object,$common,$user_id,$default,dates);
?>
