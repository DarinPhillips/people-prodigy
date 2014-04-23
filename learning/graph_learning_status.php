<?php
	include("../session.php");

class graph_results
{	
	function display($db_object,$common,$user_id,$default,$error_msg,$learning,$post_var)
	{

		$width = 340;
		$height = 220;

	//	$labelfont = '2';
		$labeltitlefont ='3';
		$image = ImageCreate($width, $height); 
		while(list($kk,$vv)=@each($post_var))
		{
			$$kk = $vv;	
		}
		/*
		$to_date=2004-01-02;
		$from_date=2004-01-02;
		$avg_rater=50.23;
		*/

		$bgcolor = ImageColorAllocate($image,0xFFFFFF, 0xFFFFFF, 0xFFFFFF);  
		$border  = ImageColorAllocate($image,0x000000, 0x000000, 0x000000); 
		$border1 = ImageColorAllocate($image,0xcccccc, 0x000000, 0x000000); 
		//$border2 = ImageColorAllocate($image,0x000000, 0xcccccc, 0x000000); 
		ImageRectangle($image,40,20,240,160,$border);

		ImageString($image, $labelfont, 15,20, "100%", $border);
		ImageString($image, $labelfont, 20,55, "75%", $border);
		ImageString($image, $labelfont, 20,90, "50%", $border);
		ImageString($image, $labelfont, 20,125, "25%", $border);
		ImageString($image, $labelfont, 20,155, "0%", $border);
		
		$fdate=$learning->changedate_display($from_date);
		$tdate=$learning->changedate_display($to_date);
	
		$days		 = $error_msg['cDays']." $fdate ".$error_msg['cTo']." $tdate ";	
		$avg_rt = @explode(",",$avg_rater);
		$id = @explode(",",$ids);
		for($i=0;$i<count($avg_rt);$i++)
		{
				$p1 = rand(0,200);
			$p2 = rand(30,250);
			$p3 = rand(100,250);
			$color = imagecolorallocate($image,$p1,$p2,$p3);

			$avg = $avg_rt[$i];
			$avg_comp = 160-((140/100)*$avg);
			$avg = round($avg,2);
			$rid = $id[$i];
			$rname = $common->name_display($db_object,$rid);
			ImageStringUp($image, $labeltitlefont, 5,110, $error_msg['cResults'], $border);
			ImageString($image, $labeltitlefont, 245,20, $error_msg['cCommitment'], $border);
			ImageString($image, $labeltitlefont, 50,170, "$days", $border);
			//ImageString($image, $labeltitlefont, 50,180, "$rname", $color);
			ImageLine($image,240,20,40,160,$border1);   //COMMITMENT LINE
			ImageLine($image,240,$avg_comp,40,160,$color);  //AVERAGE COMPLETION
			header("Content-type: image/png");  	// or "Content-type: image/png"  
			Imagepng($image); // or imagepng($image)  
		}
		ImageDestroy($image);
	}
		
}

$obj=new graph_results;
$obj->display($db_object,$common,$user_id,$default,$error_msg,$learning,$post_var);
include("footer.php");
?>
