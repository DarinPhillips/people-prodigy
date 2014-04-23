<?php
/*===============================================================
    SCRIPT: dev_solution.php
    AUTHOR: chrisranjana.com
    UPDATED: 25th of September, 2003
    
    DESCRIPTION
     This deals with the analyses Developmental Solution.
===============================================================*/

include("../session.php");
	
	$file_name=$common->help();
	


	$path=$common->path;

	$xFile=$path."/lang/learning/$file_name.html";

	$returncontent=$common->return_file_content($db_object,$xFile);

	echo $returncontent;
?>
