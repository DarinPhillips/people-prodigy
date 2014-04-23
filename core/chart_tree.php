<?php
include("../session.php");
include("header.php");
class Position
{
  function position_display($common,$db_object)
  {
	$path=$common->path;
	$xFile=$path."templates/core/chart_tree.html";
	$xTemplate=$common->return_file_content($db_object,$xFile);
//	$qry="select pos_id,position_name from $position where boss_no=0";
	preg_match("/<{position_loop}>(.*?)<{position_loop}>/s",$xTemplate,$match);
	$replacecontent=$match[1];
	$xTemp=$this->arrange($db_object,$common,0,$content,$replacecontent,$app);

	$xTemp=preg_replace("/<{position_loop}>(.*?)<{position_loop}>/s",$xTemp,$xTemplate);
	$vals=array();
	$xTemp=$common->direct_replace($db_object,$xTemp,$vals);

	echo $xTemp;
   }

function arrange($db_object,$common,$boss_no,$content,$replacecontent,$app)
{


	$qry="select pos_id,position_name from position where boss_no='$boss_no'";
	$result=$db_object->get_rsltset($qry);
	
	$present_content="";
		$app.="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

	for($i=0;$i<count($result);$i++)
	{
		$content=$content."&nbsp";
		$values["position_name"]=$result[$i]["position_name"];
		$values["pos_id"]=$result[$i]["pos_id"];
		$pos_id=$result[$i]["pos_id"];
		
		$name=$result[$i]["position_name"];
		$pos_id=$result[$i]["pos_id"];
		$repl_content=$common->direct_replace($db_object,$replacecontent,$values);
		$content.=$app.$repl_content;
		$content=$this->arrange($db_object,$common,$pos_id,$content,$replacecontent,$app);

   	}
  	
   	return $content;
 }
}
$obj=new Position;
if($fDelete)
{
$common->delete_position($db_object);
}
	
$obj->position_display($common,$db_object);
include("footer.php");
?>