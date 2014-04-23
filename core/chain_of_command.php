<?php
include("../session.php");
include("header.php");
class Position
{
  function position_display($common,$db_object)
  {
	$path=$common->path;
	$xFile=$path."templates/core/chain_of_command.html";
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

$position_table=$common->prefix_table("position");
$user_table=$common->prefix_table("user_table");
$family_position = $common->prefix_table('family_position');
$family = $common->prefix_table('family');

$qry="select $position_table.pos_id as pos_id,if($user_table.username is null,'EMPTY POSITION',$user_table.username) as position_name,
	$user_table.user_id ,$position_table.position_name as name,
	$user_table.password as password,
	
	$position_table.level_no,$user_table.admin_id
	from $position_table left join $user_table
	on $position_table.pos_id=$user_table.position
	where $position_table.boss_no='$boss_no' ";

	$result=$db_object->get_rsltset($qry);
	
	$present_content="";
		$app.="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

	for($i=0;$i<count($result);$i++)
	{
		$content=$content."&nbsp";
		$values["position_name"]=$result[$i]["position_name"];
		$values["pos_id"]=$result[$i]["name"];




		$values["password"] = $result[$i]["password"];
		$pos_id = $result[$i]["pos_id"];
		$mysql = "select family_id from $family_position where position_id = $pos_id";
		$fam_id_arr = $db_object->get_a_line($mysql);
		$family_id = $fam_id_arr['family_id'];
		$mysql = "select family_name from $family where family_id = '$family_id'";
		$fam_arr = $db_object->get_a_line($mysql);
		$family_name = $fam_arr['family_name'];
		$values["family_name"]= $family_name; 




		
		$values["levelno"]=$result[$i]["level_no"];

		$values['user_id'] = $result[$i]['user_id'];
		
		$pos_id=$result[$i]["pos_id"];
		$admin_id=$result[$i]["admin_id"];
		

$selqry="select username from $user_table where user_id='$admin_id'";
$adminname=$db_object->get_a_line($selqry);
if($adminname["username"]=="")
{
$values["admin_name"]="NO ADMINISTRATOR";
}
else
{
$values["admin_name"]=$adminname["username"];

}
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