<?php
	include("../session.php");

class graph_results
{	
	function display($db_object,$common,$user_id,$default,$error_msg,$learning,$post_var,$gbl_result)
	{

		while(list($kk,$vv)=@each($post_var))
		{
			$$kk = $vv;
		}

		$width = 340;
		$height = 220;

		$labeltitlefont ='3';
		$image = ImageCreate($width, $height); 
	
		$bgcolor = ImageColorAllocate($image,0xFFFFFF, 0xFFFFFF, 0xFFFFFF);  
		$border = ImageColorAllocate($image,0x000000, 0x000000, 0x000000); 
		$border1 = ImageColorAllocate($image,0x000000, 0xcccccc, 0x000000); 
		ImageRectangle($image,110,20,310,160,$border);

		/* FOR Y AXIS  LABELS*/
		$feedback_table	=$common->prefix_table("learning_feedback_results");
		$result_table 		=$common->prefix_table("learning_result");
		
		$mysql	= "SELECT count(*) FROM $result_table";
		$dRsltcnt	= $db_object->get_a_line($mysql);
		$rsltcount=$dRsltcnt[0];
		$cnt	= (160-20)/($rsltcount-1);
		
		$mysql	= "SELECT * FROM $result_table ORDER BY value DESC";
		$dbResult= $db_object->get_rsltset($mysql);
		
		$y=20;
		for($i=0;$i<$rsltcount;$i++)
		{
			$yval	="yvalue_".$dbResult[$i]['value'];
			$yvalue	= $gbl_result[$dbResult[$i]['value']];	//Y AXIS LABELS
			ImageString($image, $labeltitlefont, 50,$y, "$yvalue", $border);
			$$yval	=$y;
			$y=$y+$cnt;
		}
		
		ImageLine($image,110,90,310,90,$border);		
		ImageStringUp($image, $labeltitlefont, 15,130, $error_msg['cLImprovement'], $border);
		
						
		$mysql	= "SELECT * FROM $feedback_table WHERE rated_id='$usrid' AND skill_id='$skillid' ORDER BY rated_date";
		$dFeedback= $db_object->get_rsltset($mysql);	
//echo "sql=$mysql<br>";
		$mysql	= "SELECT count(*) FROM $feedback_table WHERE rated_id='$usrid' AND skill_id='$skillid'";
		$dFeedcount= $db_object->get_a_line($mysql);
		$feedcount = $dFeedcount[0];

		$total_div	= (($learning->changedate_timestamp($last_date))-($learning->changedate_timestamp($from_date)));
		$div		 = 200/$total_div;
		$x1=110;
		$y1=90;

		for($i=0;$i<$feedcount;$i++)
		{
			$rated_date	= $dFeedback[$i]['rated_date'];
				
			$value		=$dFeedback[$i]['value'];
			$r_date	= (($learning->changedate_timestamp($rated_date))-($learning->changedate_timestamp($from_date)));
			$x2=((200/$total_div)*$r_date)+110;
		
			$yval	="yvalue_".$value;
			$y2		=$$yval;
		
			ImageLine($image,$x1,$y1,$x2,$y2,$border1);
		
			$x1=$x2;
			$y1=$y2;
		
		}

		$fdate=$learning->changedate_display($from_date);
		$tdate=$learning->changedate_datetime($last_date);

		$days		 = $error_msg['cDays']." $fdate ".$error_msg['cTo']." $tdate ";	
		
		ImageString($image, $labeltitlefont, 110,170, "$days", $border);
		ImageString($image, $labeltitlefont, 120,200, $error_msg['cObservedImprovement'], $border);

		header("Content-type: image/png");  	// or "Content-type: image/png"  
		ImageJPEG($image); // or imagepng($image)  

		ImageDestroy($image);
	}
		
}

$obj=new graph_results;
$obj->display($db_object,$common,$user_id,$default,$error_msg,$learning,$post_var,$gbl_result);
include("footer.php");
?>
