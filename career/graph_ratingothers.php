<?php
/*---------------------------------------------
SCRIPT:graph_ratingothers.php
AUTHOR:info@chrisranjana.com	
UPDATED:Jan 6th

DESCRIPTION:
This script displays the graph of rating others.
---------------------------------------------*/

$width = 70;

$height = 70;  	

$image = ImageCreate($width, $height); 



$bgcolor = ImageColorAllocate($image, 0xFF, 0xFF, 0xFF);  
$border = ImageColorAllocate($image, 0x00, 0x00, 0x00);  


ImageRectangle($image,2,2,$width-2,$height-2,$border);



header("Content-type: image/png");  	// or "Content-type: image/png"  
ImageJPEG($image); // or imagepng($image)  

ImageDestroy($image);

?>
