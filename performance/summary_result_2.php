<?php
include("../session.php");

class graph_results
{	
	function display($db_object,$common,$user_id,$default,$error_msg,$learning,$get_var)
	{
//from table 
//setting color
		$config_table = $common->prefix_table("config");
		$color_qry = "select admin_color,rater1_color,rater2_color,rater3_color,self_color,
				overall_color from $config_table where id='1'";
		$res = $db_object->get_a_line($color_qry);
		$ad_col = $res['admin_color'];
		$rt1_col =$res['rater1_color'];
		$rt2_col = $res['rater2_color'];
		$rt3_col = $res['rater3_color'];
		$self_col = $res['self_color'];
		$over_col = $res['overall_color'];

		$admin = $common->split_color($db_object,$ad_col);
		$rt1 = $common->split_color($db_object,$rt1_col);
		$rt2 = $common->split_color($db_object,$rt2_col);
		$rt3 = $common->split_color($db_object,$rt3_col);
		$slf = $common->split_color($db_object,$self_col);
		$ovr = $common->split_color($db_object,$over_col);
		
	//for raters
	//rater1
		$col1_1 = "0x$rt1[0]";
		$col1_2 = "0x$rt1[1]";
		$col1_3 = "0x$rt1[2]";
		$col1_1 = hexdec($col1_1);
		$col1_2 = hexdec($col1_2);
		$col1_3 = hexdec($col1_3);
	//rater2
		$col2_1 = "0x$rt2[0]";
		$col2_2 = "0x$rt2[1]";
		$col2_3 = "0x$rt2[2]";
		$col2_1 = hexdec($col2_1);
		$col2_2 = hexdec($col2_2);
		$col2_3 = hexdec($col2_3);
	//rater3
		$col3_1 = "0x$rt3[0]";
		$col3_2 = "0x$rt3[1]";
		$col3_3 = "0x$rt3[2]";
		$col3_1 = hexdec($col3_1);
		$col3_2 = hexdec($col3_2);
		$col3_3 = hexdec($col3_3);
	//self
		$col4_1 = "0x$slf[0]";
		$col4_2 = "0x$slf[1]";
		$col4_3 = "0x$slf[2]";
		$col4_1 = hexdec($col4_1);
		$col4_2 = hexdec($col4_2);
		$col4_3 = hexdec($col4_3);
	//for admin
		$col5_1 = "0x$admin[0]";
		$col5_2 = "0x$admin[1]";
		$col5_3 = "0x$admin[2]";
		$col5_1=hexdec($col5_1);
		$col5_2=hexdec($col5_2);
		$col5_3=hexdec($col5_3);
	//overall
		$col6_1 = "0x$ovr[0]";
		$col6_2 = "0x$ovr[1]";
		$col6_3 = "0x$ovr[2]";
		$col6_1 = hexdec($col6_1);
		$col6_2 = hexdec($col6_2);
		$col6_3 = hexdec($col6_3);
		
//color setting ends

		
		$width = 340;
		$height = 270;

		$labelfont = 2;
		$labelfont1 = 1;
		$labeltitlefont ='3';
		$image = ImageCreate($width, $height); 

		while(list($kk,$vv)=@each($get_var))
		{
			$$kk = $vv;	
		}
		$bgcolor = imagecolorallocate($image, 0xFF, 0xFF, 0xFF);
		$border = ImageColorAllocate($image,0,0,0); 
		$rater1 = imagecolorallocate($image, $col1_1, $col1_2, $col1_3);
		$rater2 = imagecolorallocate($image, $col2_1, $col2_2, $col2_3);
		$rater3 = imagecolorallocate($image, $col3_1, $col3_2, $col3_3);
		$self = imagecolorallocate($image, $col4_1, $col4_2, $col4_3);
		$boss = imagecolorallocate($image, $col5_1, $col5_2, $col5_3);
		$over = imagecolorallocate($image, $col6_1, $col6_2, $col6_3);
		/* to check 
		$fromdate="2003-11-13";
		$todate="2003-11-22";
		$ratername="emp,bharathi,admin,karthik";
		$raterval="0,1,2:1,0:1,0,2:1,2,0,2";
		$date="2003-11-13,2003-11-17,2003-11-22:2003-11-13,2003-11-22:2003-11-13,2003-11-20,2003-11-22:2003-11-13,2003-11-15,2003-11-19,2003-11-22";
		*/
			
		$fdate=$fromdate;
		$tdate=$todate;
		if($ratername!="")
		{
			$rname = @split(",",$ratername);
		}

		$rt_val = @split(":",$raterval);
		$rt_date = @split(":",$date);

		$from = @split("-",$fdate);	
		$to = @split("-",$tdate);
		
		$dt1 = mktime(0,0,0,$to[1],$to[2],$to[0]);
		$dt2 = mktime(0,0,0,$from[1],$from[2],$from[0]);
		
		$dt3 = $dt1 - $dt2;
		$daydiff=(($dt3/60)/60)/24;
		
		$avg_array = @split(",",$avgrating);
		$ch_name = @split(":",$combine);
		$combine_array =array();

		for($na=0;$na<count($ch_name);$na++)
		{
			$cl_name = $ch_name[$na];
			$sp_clname = @split("=",$cl_name);
			$ky = trim($sp_clname[0]);
			$vl = $sp_clname[1];
			$combine_array[$ky] = $vl;
		}//na loop
			
		$days = $error_msg['cDays']." $fdate ".$error_msg['cTo']." $tdate";
		ImageRectangle($image,65,24,315,180,$border);
		ImageString($image, $labelfont, 15,19,$error_msg['cExceeds'],$border);
		ImageString($image, $labelfont, 40,94,$error_msg['cMet'], $border);
		ImageString($image, $labelfont, 5,170,$error_msg['cFellshort'],$border);
		ImageString($image, $labeltitlefont, 70,182, "$days", $border);	
		Imageline($image,65,102,315,102,$border);
		ImageStringUp($image, $labeltitlefont,15,140, $error_msg['cExpectation'], $border);		
		$x1 = 65;
		//$y1 = 180;
		$startx_val = $x1;
		$starty_val = 180;
		//$x2 = 130;
		//$y2 = 95;
	
		$min=63;
		$max = 311;
		$tot = $max - $min;
		$count = $daydiff ;
		$interval = @($tot / $count);
		$interval = sprintf("%01.0f", $interval);

		ImageString($image, $labelfont, 15,24, "", $border);
		for($j=0;$j<=$count;$j++)
		{
			if($min <= $max)
			ImageString($image, $labelfont1,$min,176, "|", $border);	
			$min = $min + $interval;
		}

		$d_array = array();
		$inc = 50;
		
		$im_dates = split("-",$fdate);
			//ImageString($image, $labelfont, 50,80,"$im_dates[2]", $border);
		//to calculate the x2 value.
			$odd = array("01","03","05","07","08","10","12");//Odd months
			$even = array("04","06","09","11");//even months
		//ImageString($image, $labelfont, 50,80,"$rt_date[0]", $border);

		for($f=0;$f<count($rt_date);$f++)
		{
			$date_arr = $rt_date[$f];
			$date_array = @split(",",$date_arr);
			
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
			}//d loop 
		}//f loop
	

		$d_inc = 0;
		$color1 = $red;
		$color2 = $blue;
		$color3 = $green;
		$color4 = $rose;
		$n_inc = 200;
		for($i=0;$i<count($rname);$i++)
		{
			
			$name = $rname[$i];
			$avg = $avg_array[$i];
			$rtval = $rt_val[$i];
			$rtdate = $rt_date[$i];
			$rval = @split(",",$rtval);
			$rdate = @split(",",$rtdate);
			$pl = $i +1;
			$colour = $combine_array[$name];												
			$colour = trim($colour);
			$color = $$colour;
									
			for($j=0;$j<count($rval)-1;$j++)
			{
				$ful = $rval[$j];
				$dt_val = $dt_array[$d_inc];
				$dt_val1 = $dt_array[$d_inc + 1];
				//echo $d_inc.($d_inc + 1)."<br>";
				//echo "$dt_val $dt_val1<br>";
				$y1 = $starty_val - (78 * ($ful)) ;
				$x1 =	$startx_val + ($interval * $dt_val);  //cal using No of Days
				$x2 = $startx_val + ($interval * $dt_val1);
				$ful2 = $rval[$j+1];
				$y2 = $starty_val  - (78 * ($ful2));

				ImageLine($image,$x1,$y1,$x2,$y2,$color);		
				
				//ImageString($image, $labelfont1,80,$inc,"$ful" ,$col);						
				$inc = $inc + 10;	
				$x1 = $x2;
				//$y1 = $y2;
				$nor = 1;
				$ct = (count($rval)-1);
				if($j==$ct-1)
				{
					$nor = $nor + 1;
				} 			
				$d_inc = $d_inc + $nor ;
			}//j loop
			ImageString($image, $labelfont1,130,$n_inc,"$colour $name  $error_msg[cAvg]=$avg",$color);
			$n_inc += 10;
		}// i loop

		
		if($overall!="")
		{
			ImageString($image, $labelfont1,130,250,"$error_msg[cOverallavg] = $overall",$over);
		}
		
		header("Content-type: image/png");  				
		Imagepng($image); 		
		ImageDestroy($image);		
	}//graph

	
}//end class

$obj=new graph_results;
$obj->display($db_object,$common,$user_id,$default,$error_msg,$learning,$_GET);
//$obj ->expected($db_object,$common,$user_id,$default,dates);
?>
