<?php

/*---------------------------------------------
SCRIPT:mobility_graph.php
AUTHOR:info@chrisranjana.com	
UPDATED:9nd Dec

DESCRIPTION:
This script displays the graph for mobility reports.

---------------------------------------------*/
include_once("../session.php");

//GET THE VALUES FOR EACH LEVEL

$xvalsame_level = $post_var['xcosame'];
$yvalsame_level = $post_var['ycosame'];


$xval1_level = $post_var['xco1'];
$yval1_level = $post_var['yco1'];


$xval2_level = $post_var['xco2'];
$yval2_level = $post_var['yco2'];


//EXPLODE THE VARIABLES AND MAKE THEM TO AN ARRAY

$xvalsame_level_arr = @explode(",",$xvalsame_level);
$yvalsame_level_arr = @explode(",",$yvalsame_level);

$xval1_level_arr = @explode(",",$xval1_level);
$yval1_level_arr = @explode(",",$yval1_level);

$xval2_level_arr = @explode(",",$xval2_level);
$yval2_level_arr = @explode(",",$yval2_level);









$model_view_1 			= $common->prefix_table('model_view_1');
$model_view_2			= $common->prefix_table('model_view_2');
$models_percent_fit 		= $common->prefix_table('models_percent_fit');
$position 			= $common->prefix_table('position');
$user_table 			= $common->prefix_table('user_table');
$model_table			= $common->prefix_table('model_table');


//THE KEY IS THE MODEL ID FOR ALL THE X VALUES AND Y VALUES...

$width = 490;
$height = 400;

$image 		= ImageCreate($width, $height);

$bgcolor 	= ImageColorAllocate($image,0xFFFFFF, 0xFFFFFF, 0xFFFFFF);  
$border 	= ImageColorAllocate($image,0x000000, 0x000000, 0x000000);
$red 		= ImageColorAllocate($image,0xFFFFFF, 0x000000, 0x000000);
$blue 		= ImageColorAllocate($image,0x000000, 0x333333, 0xFFFFFF);
$green 		= ImageColorAllocate($image,0x666666, 0xFFFFFF, 0x000000);
 
$color_0	= $green;
$color_1	= $red;
$color_2	= $blue;

$readfont = 3;
$labelfont=1;

ImageString($image,$readfont,155,365,"Interpersonal Skill Fit",$border);
ImageStringUp($image,$readfont,20,265,"Technical Skill Fit",$border);

ImageRectangle($image,45,5,385,345,$border);

ImageLine($image,65,25,65,325,$border);  //y axis
ImageLine($image,65,325,365,325,$border);  //x axis

//PERCENTAGE MARKING IN Y AXIS...

ImageString($image, $labelfont, 55,325, "0%", $border);
ImageString($image, $labelfont, 50,295, "10%", $border);
ImageString($image, $labelfont, 50,265, "20%", $border);
ImageString($image, $labelfont, 50,235, "30%", $border);
ImageString($image, $labelfont, 50,205, "40%", $border);
ImageString($image, $labelfont, 50,175, "50%", $border);
ImageString($image, $labelfont, 50,145, "60%", $border);
ImageString($image, $labelfont, 50,115, "70%", $border);
ImageString($image, $labelfont, 50,85, "80%", $border);
ImageString($image, $labelfont, 50,55, "90%", $border);
ImageString($image, $labelfont, 45,25, "100%", $border);

 
//PERCENTAGE MARKING IN X AXIS...
 
ImageString($image, $labelfont, 90,330, "10%", $border);
ImageString($image, $labelfont, 120,330, "20%", $border);
ImageString($image, $labelfont, 150,330, "30%", $border);
ImageString($image, $labelfont, 180,330, "40%", $border);
ImageString($image, $labelfont, 210,330, "50%", $border);
ImageString($image, $labelfont, 240,330, "60%", $border);
ImageString($image, $labelfont, 270,330, "70%", $border);
ImageString($image, $labelfont, 300,330, "80%", $border);
ImageString($image, $labelfont, 330,330, "90%", $border);
ImageString($image, $labelfont, 360,330, "100%", $border);

ImageString($image,$readfont,415,129,"My Level",$border);
ImageFilledRectangle($image,400,130,407,137,$green);

ImageString($image,$readfont,415,159,"+1 Level",$border);
ImageFilledRectangle($image,400,160,407,167,$red);

ImageString($image,$readfont,415,189,"+2 Level",$border);
ImageFilledRectangle($image,400,190,407,197,$blue);
	
//SAME LEVEL VALUES...

for($i=0;$i<count($xvalsame_level_arr);$i++)
{
$x1same = $xvalsame_level_arr[$i];
$y1same = $yvalsame_level_arr[$i];

$x2same = $x1same + 8;
$y2same = $y1same + 8;
if($x1same != '' && $y1same != '' && $x2same != '' && $y2same != '')
{
ImageFilledRectangle($image,$x1same,$y1same,$x2same,$y2same,$green);
}
}


//ONE LEVEL HIGHER

for($i=0;$i<count($xval1_level_arr);$i++)
{

$x1onelevel = $xval1_level_arr[$i];
$y1onelevel = $yval1_level_arr[$i];

$x2onelevel = $x1onelevel + 8;
$y2onelevel = $y1onelevel + 8;

if($x1onelevel != '' && $y1onelevel!= '' && $x2onelevel != '' && $y2onelevel != '')
{
ImageFilledRectangle($image,$x1onelevel,$y1onelevel,$x2onelevel,$y2onelevel,$red);
}

}

//TWO LEVEL HIGHER

for($i=0;$i<count($xval2_level_arr);$i++)
{

$x1twolevel = $xval2_level_arr[$i];
$y1twolevel = $xval2_level_arr[$i];

$x2twolevel = $x1twolevel + 8;
$y2twolevel = $y1twolevel + 8;
if($x1twolevel != '' && $y1twolevel!= '' && $x2twolevel != '' && $y2twolevel != '')
{
ImageFilledRectangle($image,$x1twolevel,$y1twolevel,$x2twolevel,$y2twolevel,$blue);
}
}


header("Content-type: image/jpeg");  	// or "Content-type: image/png"  
ImageJPEG($image); // or imagepng($image)
ImageDestroy($image);
 
?>
