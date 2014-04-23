<?php
include("includes/database.class");
include("includes/common.class");
include("includes/globals.php");
include("includes/learning.class");
include("includes/image.class");

$default=$_COOKIE["lang"];



if($default=="")
{
$default=1;
}

$default=(int)$default;


include("lang/$default/lang.php");




$db_object=new database;
$common=new common;
$learning=new learning;
$image = new piechart;
$xPath=$common->path;
class Session
{
function verify_Login($db_object,$common,$fLogin,$fPassword)
{
$tablename=$common->prefix_table("user");

$mysql="select user_id from $tablename where username='$fLogin' and 

password='$fPassword'";

$arr=$db_object->get_a_line($mysql);

$user_id=$arr[0];

if($arr[0]!="")
{
	$tablename=$common->prefix_table("user_session");

	$mysql="select hash from $tablename where user_id='$user_id'";
	$a=$db_object->get_a_line($mysql);
	$hash=$a[0];

	if($common->login_mode!="test" || $hash=="")
	{
	$hash=$common->hashgen();
	}
	setcookie("Pmsuser",$hash,0,"/");
	$time=time();
	$mysql="replace into $tablename values ('$user_id','$hash','$time')";
	$db_object->insert($mysql);

	$http_path=$common->http_path;


//echo $user_id;
/*if($user_id==1)
{
header("Location:adminsettings_panel.php");
}*/	return $user_id;


}
else
{
$http_path = $common->http_path;
header("Location:$http_path/index.php");
exit;
}



}

//---------------------------------------------------------

}
$obj=new Session;

//include_once("../lang/eng.php");

if($fSubmit=="submit")
{
$user_id=$obj->verify_Login($db_object,$common,$fLogin,$fPassword);
}
else
{
$hash=$_COOKIE["Pmsuser"];
$user_id=$common->check_session_super($hash,$db_object);
//echo "user_id=$user_id<br>";
if($user_id==0)
{

$http_path=$common->http_path;
header("Location:$http_path/index.php");
exit;

}
//--------- generated cookie is read for verification of dashboard change
$user_table=$common->prefix_table("user_table");
$viewasadmin=$_COOKIE["viewasadmin"];


$selqry="select admin_id from $user_table where user_id='$viewasadmin'";
$temp_user_id=$db_object->get_a_line($selqry);

if(($viewasadmin!="" && $temp_user_id["admin_id"]==$user_id)||($viewasadmin!="" && $user_id==1))
{
	
	$user_id=$viewasadmin;
}


}

	// restrictions here 

	$dir = substr(strrchr(getcwd(), "\\"), 1);



	if($dir=="core")
	{
		if($user_id!=1)
		{
			die("Tresspassers will be Prosecuted");
		}
	}

	$form_array["adt"]=$adt;



$filename=$PHP_SELF;
$filename = substr(strrchr($PHP_SELF, "/"), 1);


if($user_id!=1)
{
	$temp_str=in_array($filename,$gbl_admin_files);
	if($temp_str)
	{
		echo "Super Admin alone can view this Webpage";
		exit;
	}
}
	
$user_table=$common->prefix_table("user_table");
$selqry="select user_type from $user_table where user_id='$user_id'";
$emp_type=$db_object->get_a_line($selqry);
if($emp_type=="external")
{

}


	
?>
