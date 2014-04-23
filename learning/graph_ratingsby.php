<?php
	include("../session.php");
class graph_results
{	
	function display($db_object,$common,$user_id,$default,$error_msg,$learning,$post_var,$gbl_result)
	{

		while(list($kk,$vv)=@each($post_var))
		{
			$$kk = $vv;
			//echo "$kk = $vv<br>";
		}
		
			
		$width = 340;
		$height = 220;

		$labeltitlefont ='3';
		$image = ImageCreate($width, $height); 
	
		$bgcolor = ImageColorAllocate($image,0xFFFFFF, 0xFFFFFF, 0xFFFFFF);  
		$border = ImageColorAllocate($image,0x000000, 0x000000, 0x000000); 
		$border1 = ImageColorAllocate($image,0xcccccc, 0x000000, 0x000000); 
		$border2 = ImageColorAllocate($image,0x000000, 0xcccccc, 0x000000); 
		$border3 = ImageColorAllocate($image,0x000000, 0x000000, 0xcccccc); 
		$border4 = ImageColorAllocate($image,0x000000, 0xcccccc, 0xcccccc); 

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
		
		ImageLine($image,110,90,310,90,$border);		/* MIDDLE LINE  */
		ImageStringUp($image, $labeltitlefont, 15,130, $error_msg['cLImprovement'], $border);
		$rater_disp='175';
		$raterarr	= @explode(",",$rater_id);
		ImageString($image, $labelfont, 50,175, $error_msg['cRatingsBy'], $border);

		for($j=0;$j<count($raterarr);$j++)
		{
			$extra_qry=$learning->return_qry($db_object,$common,$raterarr[$j],$user_id);			
			$rater	=$this->return_rater($db_object,$common,$raterarr[$j],$error_msg);
			$color	="border".$raterarr[$j];
			ImageString($image, $labelfont, 120,$rater_disp, $rater, $$color);
			$rater_disp =$rater_disp+8;
			$mysql	= "SELECT * FROM $feedback_table WHERE rated_id='$user_id' AND 
							skill_id='$skillid' $extra_qry ORDER BY rated_date";
			$dFeedback= $db_object->get_rsltset($mysql);	
			$total_div	= (($learning->changedate_timestamp($last_date))-($learning->changedate_timestamp($from_date)));
			$div		 = 200/$total_div;
			$x1=110;
			$y1=90;
			$color	="border".$raterarr[$j];
						
			for($i=0;$i<count($dFeedback);$i++)
			{
				$rated_date	= $dFeedback[$i]['rated_date'];
				$value		=$dFeedback[$i]['value'];
				//echo "$rated_date $value i = $i<br>";
				$r_date	= (($learning->changedate_timestamp($rated_date))-($learning->changedate_timestamp($from_date)));
		
				$x2=((200/$total_div)*$r_date)+110;

				$yval	="yvalue_".$value;
				$y2		=$$yval;			
				ImageLine($image,$x1,$y1,$x2,$y2,$$color);
				$x1=$x2;
				$y1=$y2;		
			}
		
		}
		
		if($dFeedback[0][0] == "")
		{
				ImageString($image, $labeltitlefont, 50,190, $error_msg['cEmptyrecords'], $border1);
		}

		header("Content-type: image/png");  	// or "Content-type: image/png"  
		ImageJPEG($image); // or imagepng($image)  

		ImageDestroy($image);
	}
	
	//-------------------------------------------------------
	
	function return_rater($db_object,$common,$arrvalue,$error_msg)
	{
		switch($arrvalue)
		{
			case 1:
				$rater = $error_msg['cBosss'];
				break;
			case 2:
				$rater = $error_msg['cOthers'];
				break;
			case 3:
				$rater = $error_msg['cSelf'];
				break;				
			case 4:
				$rater = $error_msg['cOverallavg'];
				break;				
		}
		return $rater;	
	}	
}

$obj=new graph_results;
$obj->display($db_object,$common,$user_id,$default,$error_msg,$learning,$post_var,$gbl_result);
include("footer.php");
?>
