<?php
include("../session.php");
include("header.php");
class Skill_type
{
  function skill_type($common,$db_object)]
  {
	$filename="../templatess/career/skill_type.html";
	$filecontent=$common->return_file_content($db_object,$filename);
	echo $filecontent;	
   }
}

$skobj=new Skill_type;
$skobj->skill_type($common,$db_object);

