<?php

/*===============================================================
    SCRIPT: index.php
    AUTHOR: chrisranjana.com
    UPDATED: 1st September, 2003
    
    DESCRIPTION
       This is the home page of the site.
===============================================================*/


include_once("includes/database.class");
include_once("includes/common.class");
$db_object=new database;
$common=new common;
$xPath=$common->path;
$xTemplate=$xPath."templates/index.html";
$returncontent=$common->return_file_content($db_object,$xTemplate);

$values=array();

$returncontent=$common->direct_replace($db_object,$returncontent,$values);

echo $returncontent;
?>