<?php
include("../session.php");

class Show_admins
{
	function show_all_admins($common,$db_object,$user_id,$default)
	{
		$path=$common->path;
		$xFile=$path."templates/core/show_administrators.html";
		$xTemplate=$common->return_file_content($db_object,$xFile);


$type="type_".$default;
		
$user_table=$common->prefix_table("user_table");
$position_table=$common->prefix_table("position");
$location_table=$common->prefix_table("location_table");
$family_table=$common->prefix_table("family");
$family_position=$common->prefix_table("family_position");
$employment_type=$common->prefix_table("employment_type");
$admins_table=$common->prefix_table("admins");
preg_match("/{{user_loopstart}}(.*?){{user_loopend}}/s",$xTemplate,$mat);
$replace=$mat[1];

$flds=$common->return_fields($db_object,$admins_table);



$selqry="select distinct($user_table.user_id),$user_table.username from $admins_table left join $user_table on $admins_table.user_id=$user_table.user_id";
$userset=$db_object->get_rsltset($selqry);

$selqry="select family_id,family_name from $family_table";
$familyset=$db_object->get_rsltset($selqry);
$values["family_loop"]=$familyset;

$selqry="select pos_id,position_name from $position_table";
$positionset=$db_object->get_rsltset($selqry);
$values["position_loop"]=$positionset;

$selqry="select distinct(u1.user_id),u1.username from $user_table,$position_table,$user_table as u1,$position_table as p1 where $position_table.pos_id=$user_table.position and u1.position=$position_table.boss_no and p1.pos_id=u1.position order by u1.user_id";
$bossset=$db_object->get_rsltset($selqry);
$values["boss_loop"]=$bossset;

$selqry="select id,$type as type from $employment_type";
$empset=$db_object->get_rsltset($selqry);
$values["employment_loop"]=$empset;

for($i=0;$i<count($userset);$i++)
{
$username=$userset[$i]["username"];
$user_id=$userset[$i]["user_id"];

$selqry="select level_id from $admins_table where user_id='$user_id' and level_id is not null";
$sellevel=$db_object->get_single_column($selqry);
$selqry="select location_id from $admins_table where user_id='$user_id' and location_id is not null";
$selloc=$db_object->get_single_column($selqry);


$levelset=$common->return_levels($db_object);
$replace1=$common->pulldown_replace_multiple($db_object,"level_loopstart","level_loopend",$replace,$levelset,$sellevel);
$locationset=$common->return_location_for_display($db_object);
$replace2=$common->pulldown_replace_multiple($db_object,"location_loopstart","location_loopend",$replace1,$locationset,$selloc);


$selqry="select pos_id from $admins_table where user_id='$user_id' and pos_id is not null";
$selarr["position_loop"]["pos_id"]=$db_object->get_single_column($selqry);

$selqry="select fam_id as family_id from $admins_table where user_id='$user_id' and fam_id is not null";
$selarr["family_loop"]["family_id"]=$db_object->get_single_column($selqry);

$selqry="select boss_id from $admins_table where user_id='$user_id' and boss_id is not null";
$selarr["boss_loop"]["boss_id"]=$db_object->get_single_column($selqry);


$selqry="select emp_type_id as id from $admins_table where user_id='$user_id' and emp_type_id is not null";
$selarr["employment_loop"]["id"]=$db_object->get_single_column($selqry);

//print_r($selarr);

$replaced=$common->multipleselect_replace($db_object,$replace2,$values,$selarr);

$newreplaced.=$replaced;

$newreplaced=preg_replace("/<{username}>/s",$username,$newreplaced);
$newreplaced=preg_replace("/<{user_id}>/s",$user_id,$newreplaced);
}
$xTemplate=preg_replace("/{{user_loopstart}}(.*?){{user_loopend}}/s",$newreplaced,$xTemplate);
$xTemplate=$common->direct_replace($db_object,$xTemplate,$vls);


		echo $xTemplate;
	}
}
$adobj= new Show_admins;
if($fAddnewAdminsitrator)
{
header('Location:new_set_admins.php');

}

include("header.php");
$adobj->show_all_admins($common,$db_object,$user_id,$default);
include("footer.php");
?>