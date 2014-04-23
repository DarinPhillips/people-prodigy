<?php
include("session.php");
include("header.php");

class Fix_employee
{
	function display($common,$db_object,$user_id,$default,$sort_arr,$fPage,$error_msg)
	{
		$path=$common->path;
		$xFile=$path."templates/fix_employee.html";
		$xTemplate=$common->return_file_content($db_object,$xFile);

		$user_table=$common->prefix_table("user_table");
		$config=$common->prefix_table("config");
		$selqry="select page_limit from config";
		$pagelimit=$db_object->get_a_line($selqry);
		$page_limit=$pagelimit["page_limit"];
		if($user_id!=1)
		{
		$countqry="select count(*) from $user_table where admin_id='$user_id'";
		}
		else
		{
			$countqry="select count(*) from $user_table";
		}
		$count=$db_object->get_a_line($countqry);
		$cnt=$count[0];

	if($cnt==0)
	{
		echo $error_msg["cNoEmpUnderUrControl"];
		include("footer.php");
		exit;
	}

if($cnt<=5)
{

	$xTemplate=preg_replace("/<{page_by_loopstart}>(.*?)<{page_by_loopend}>/s","",$xTemplate);
	$lim1=0;
	$lim2=$page_limit;
}
else
{
		preg_match("/<{page_by_loopstart}>(.*?)<{page_by_loopend}>/s",$xTemplate,$mats1);
		$toshow=$mats1[1];
		$lnk="fix_employee.php?";


if($fPage=="" || $fPage==1)
{
	$fPage=1;
	$slimit=0;
}
else
{
	$slimit=($fPage-1)*$page_limit;//limit
}
		if($slimit==0)
		{	
			$slimit=0;
			$rep=preg_replace("/<{Front}>(.*?)<{Front}>/s","",$toshow);

		//	$rep=preg_replace("/<{(.*?)}>/s","",$rep);
			$lim1=$slimit;
			$lim2=$lim1+$page_limit;
			$check1=$sort_arr[1];
			$check2=$sort_arr[2];
			$check3=$sort_arr[3];
			$pageprev=$fPage-1;
			$pagenext=$fPage+1;

			$rep=preg_replace("/{{(.*?)}}/e","$$1",$rep);
			$rep=$common->print_pgbreak($db_object,$lnk,$cnt,$rep,$page_limit);
			$xTemplate=preg_replace("/<{page_by_loopstart}>(.*?)<{page_by_loopend}>/s",$rep,$xTemplate);
			
		}
		else
		{	
			$lim1=$slimit;
			$lim2=$page_limit;
			//limit
			$ex=$lim1+$page_limit;	
			if($lim1==0)
			{
				$rep=preg_replace("/<{Front}>(.*?)<{Front}>/s","",$toshow);
			}
			else if($ex>=$cnt)
			{
				
				preg_match("/<{Front}>(.*?)<{Front}>/s",$toshow,$mat2);
				$rep=$mat2[1];
			}
			else
			{
				$rep=preg_replace("/<{(Front)}>/s","",$toshow);
			}		
			$check1=$sort_arr[1];
			$check2=$sort_arr[2];
			$check3=$sort_arr[3];
			$pageprev=$fPage-1;
			$pagenext=$fPage+1;
		
			$rep=preg_replace("/{{(.*?)}}/e","$$1",$rep);
			$rep=$common->print_pgbreak($db_object,$lnk,$cnt,$rep,$page_limit);
			$xTemplate=preg_replace("/<{page_by_loopstart}>(.*?)<{page_by_loopend}>/s",$rep,$xTemplate);
		}
		
}

//--------------prelims--------------
		$type="type_".$default;
		$nametype="name_".$default;
		

//------------tables--------------------------

		$location_table=$common->prefix_table("location_table");
		$position_table=$common->prefix_table("position");
		$family_table=$common->prefix_table("family");
		$user_table=$common->prefix_table("user_table");
		$access_rights_table=$common->prefix_table("access_rights");
		$employment_type_table=$common->prefix_table("employment_type");
		$eeo_tags_table=$common->prefix_table("eeo_tags");
		$namefields_table=$common->prefix_table("name_fields");
		$family_position_table=$common->prefix_table("family_position");
		$user_eeo_table=$common->prefix_table("user_eeo");
		$temp_user_table=$common->prefix_table("temp_user_table");
		
		preg_match("/<{link_loopstart}>(.*?)<{link_loopend}>/s",$xTemplate,$last_bit);
		$lastbit=$last_bit[1];
					
		if($user_id!=1)
		{
		$sel_qry="select user_id,first_name,last_name,username,nick_name,mid_name,
			  sec_last_name,email,password,office_phone,cell_phone,pager,
		          fax,office_mail_address,office_physical_address,location,
		          position,access_rights,employment_type from $user_table where 
		          admin_id='$user_id'";
		}
		else
		{

			$sel_qry="select user_id,first_name,last_name,username,nick_name,mid_name,
			  sec_last_name,email,password,office_phone,cell_phone,pager,
		          fax,office_mail_address,office_physical_address,location,
		          position,access_rights,employment_type from $user_table";

		}

//print_r($sort_arr);

		if(count($sort_arr)!=0)
		{
		
			$sel_qry="select $user_table.user_id,$user_table.first_name,$user_table.last_name,$user_table.username,$user_table.nick_name,$user_table.mid_name,
			  $user_table.sec_last_name,$user_table.email,$user_table.password,$user_table.office_phone,$user_table.cell_phone,$user_table.pager,
		          $user_table.fax,$user_table.office_mail_address,$user_table.office_physical_address,$user_table.location,
		          $user_table.position,$user_table.access_rights,$user_table.employment_type from $user_table ";
		          if($user_id!=1)
		          {
		          	$condi=" where $user_table.admin_id='$user_id'";
		          }
		          else
		          {
		          	$condi=" where 1";
		          }
		      $r=0;
			for($i=1;$i<=count($sort_arr);$i++)
			{
				
				
				$temp_val=$sort_arr[$i];
				
				if($temp_val!="level" && $temp_val!="family" && $temp_val!="boss" && $temp_val!="location" && $temp_val!="")
				{					
				$sorted_arr.=$user_table.".".$sort_arr[$i].",";
				}
				else
				{
				//	echo $temp_val;
					if($temp_val=="location")
					{
						$app3="left join $location_table on $location_table.location_id=$user_table.location ";
						//=====>$sorted_arr.=$location_table."."."loc_name,";
					//	$joincond3=" and $location_table.loc_id=$user_table.location ";
						
					}
					else if($temp_val!="family")
					{
						$app1="left join $position_table on $position_table.pos_id=$user_table.position ";
						$sorted_arr.=$position_table.".".$temp_val."_no,";
					//	$joincond1=" and $position_table.pos_id=$user_table.position ";
						
					}
					else
					{
		/*if you need to sort by the family name them add this( and $family_table.family_id=$family_position_table.family_id )  in the
		  join condition2 and  change the sorted_arr to ($family_table.family_name) and just add the $family_table to app2
		  */
						$app2="left join $family_position_table on $family_position_table.position_id=$user_table.position ";
						$sorted_arr.=$family_position_table."."."family_id".",";
						//$joincond2=" and $family_position_table.position_id=$user_table.position ";						
					}
				}
				
			}
			$sorted_arr=substr($sorted_arr,0,-1);
			$comma=",";
		$orderby=" order by ";
		
/*		$app=$app1.",".$app2;
		
		$joincond=$joincond1.$joincond2.$joincond3;
		if($app2=="" && $app1!="")
		{
		$app=substr($app,0,-1);
		}
		else if($app2!="" && $app1=="")
		{
			$app=substr($app,1);
		//	$orderby="group by $user_table.user_id".$orderby;
		}
		else if($app2=="" && $app1=="")
		{
			$app="";
			$comma="";
		}
		if($app3!="")
		{		
		$app=$app.",".$app3;
		}*/


		$app=$app1.$app2.$app3;

		if($app2!="")
		{
			$orderby="group by $user_table.user_id".$orderby;
		}
		$sel_qry.=$app.$condi.$joincond.$orderby.$sorted_arr;
		
		}
	

		$xlimit=" limit $lim1,$lim2";
		$sel_qry.=$xlimit;

		//echo $sel_qry;exit;
	
		$user_set=$db_object->get_rsltset($sel_qry);
					
		
		$sel_qry="select $nametype,field_name from $namefields_table where status='YES'";
		$dynafield_arr=$db_object->get_rsltset($sel_qry);

				
	
		$sel_qry="select distinct(level_no) from $position_table";
		$level_arr=$db_object->get_rsltset($sel_qry);

		$sel_qry="select location_id from $location_table";
		$location_arr=$db_object->get_rsltset($sel_qry);
	
		$loc_arr=$common->return_location_for_display($db_object);
		for($i=0;$i<count($location_arr);$i++)
		{
			$j=$i+1;
			
			$location_arr[$i][location_name]=$loc_arr[$j];
		}
		
		
		$sel_qry="select pos_id,position_name from $position_table";
		$position_arr=$db_object->get_rsltset($sel_qry);

		$sel_qry="select family_id,family_name from $family_table";
		$family_arr=$db_object->get_rsltset($sel_qry);

/*		$sel_qry="select $user_table.username as bossname,$position_table.boss_no as boss_id from $position_table,$user_table where $position_table.boss_no=$user_table.position";
		$boss_arr=$db_object->get_rsltset($sel_qry);*/

		$sel_qry="select id as access_id,$type as access_type from $access_rights_table where rights='yes'";
		$access_arr=$db_object->get_rsltset($sel_qry);

		$sel_qry="select id as emptype_id, $type as emp_type from $employment_type_table where status='Yes'";
		$employment_type_arr=$db_object->get_rsltset($sel_qry);

		$sel_qry="select tag_id,tag_name from $eeo_tags_table";
//		$eeo_arr=$db_object->get_rsltset($sel_qry);
		
$eeo_arr=$common->return_eeo_status($db_object);

		$values["level_loop"]=$level_arr;
		$values["location_loop"]=$location_arr;
		$values["position_loop"]=$position_arr;
		$values["family_loop"]=$family_arr;
	//	$values["boss_loop"]=$boss_arr;
		$values["access_loop"]=$access_arr;
		$values["empstatus_loop"]=$employment_type_arr;
		$values["eeo_loop"]=$eeo_arr;
		
//-----------------------users display starts here   -------------------------------------


preg_match("/<{parent_table_loopstart}>(.*?)<{parent_table_loopend}>/s",$xTemplate,$mats);
$subTemp=$mats[1];


for($set_id=0;$set_id<count($user_set);$set_id++)
{

		$id=$user_set[$set_id]["user_id"];	

		$temp_pos_id=$user_set[$set_id]["position"];;
		$sel_qry="select family_id from $family_position_table where position_id='$temp_pos_id'";
		$famid=$db_object->get_a_line($sel_qry);


		
		
//		$sel_qry="select $position_table.boss_no as boss_no from $position_table,$user_table where $user_table.user_id=$position_table.pos_id and $position_table.pos_id='$temp_pos_id'";
//		$bosid=$db_object->get_a_line($sel_qry);
		
		$sel_qry="select level_no from $position_table where pos_id='$temp_pos_id'";
		$levelq=$db_object->get_a_line($sel_qry);

		$templevel=$levelq["level_no"];

		$sel_qry="select pos_id as boss_no,position_name as bossname from position where level_no>'$templevel'";
		$boss_arr=$db_object->get_rsltset($sel_qry);

	
//		$sel_qry="select $position_table.position_name as bossname,$position_table.pos_id as boss_no from $position_table where $position_table.level_no='$templevel'";
		$sel_qry="select boss_no as boss_no from position where pos_id ='$temp_pos_id'";
		$bosid=$db_object->get_a_line($sel_qry);
		$values["boss_loop"]=$boss_arr;

		$sel_qry="select tag_id as tag_id from $user_eeo_table where user_id='$id'";
		$usereeo_arr=$db_object->get_single_column($sel_qry);
//print_r($bosid);
/*		
		$sel_arr["level_loop"]["level_no"]=$levelq["level_no"];
		$sel_arr["location_loop"]["loc_id"]=$user_set[$set_id]["location"];
		$sel_arr["position_loop"]["pos_id"]=$user_set[$set_id]["position"];
		$sel_arr["family_loop"]["family_id"]=$famid["family_id"];
		$sel_arr["boss_loop"]["boss_no"]=$bosid["boss_no"];
		$sel_arr["boss_loop"]["bossname"]=$bosid["bossname"];
		$sel_arr["empstatus_loop"]["emptype_id"]=$user_set[$set_id]["employment_type"];
		$sel_arr["access_loop"]["access_id"]=$user_set[$set_id]["access_rights"];
		$sel_arr["eeo_loop"]=$usereeo_arr;
*/

//-----------Here the selected variables are send by inserting in an array so that the function multiplereplace  works correctly
		$sel_arr["level_loop"]	= array('level_no'=>array($levelq["level_no"]));
		$sel_arr["location_loop"]["loc_id"]=array($user_set[$set_id]["location"]);
		$sel_arr["position_loop"]["pos_id"]=array($user_set[$set_id]["position"]);
		$sel_arr["family_loop"]["family_id"]=array($famid["family_id"]);
		$sel_arr["boss_loop"]["boss_no"]=array($bosid["boss_no"]);
		$sel_arr["boss_loop"]["bossname"]=array($bosid["bossname"]);
		$sel_arr["empstatus_loop"]["emptype_id"]=array($user_set[$set_id]["employment_type"]);
		$sel_arr["access_loop"]["access_id"]=array($user_set[$set_id]["access_rights"]);
		$sel_arr["eeo_loop"]["tag_id"]=$usereeo_arr;

//	print_r($sel_arr);
//	exit;

		$xSubtemp=$common->multipleselect_replace($db_object,$subTemp,$values,$sel_arr);
		preg_match("/<{dynamic_fields_start}>(.*?)<{dynamic_fields_end}>/s",$xTemplate,$match);
		$sub=$match[1];
		for($i=0;$i<count($dynafield_arr);$i++)
		{
			$field_name_dis=$dynafield_arr[$i][$nametype];
			$field_name=$dynafield_arr[$i]["field_name"];
			$field_value=$user_set[$set_id][$field_name];

			
			$subreplaced.=preg_replace("/<{(.*?)}>/e","$$1",$sub);
		}
		$xSubtemp=preg_replace("/<{dynamic_fields_start}>(.*?)<{dynamic_fields_end}>/s",$subreplaced,$xSubtemp);
		$subreplaced="";

				
		$vals["email"]=$user_set[$set_id]["email"];
		//$vals["id"]=$user_set[$set_id]["user_id"];
		$vals["username"]=$user_set[$set_id]["username"];
		$vals["password"]=$user_set[$set_id]["password"];
		$vals["offno"]=$user_set[$set_id]["office_phone"];
		$vals["cellno"]=$user_set[$set_id]["cell_phone"];
		$vals["pager"]=$user_set[$set_id]["pager"];
		$vals["faxno"]=$user_set[$set_id]["fax"];
		$vals["email"]=$user_set[$set_id]["email"];
		$vals["mailaddrs"]=$user_set[$set_id]["office_mail_address"];
		$vals["physicaladdr"]=$user_set[$set_id]["office_physical_address"];
		$vals["id"]=$id;

		$xSubtemp=$common->direct_replace($db_object,$xSubtemp,$vals);
	

		$sel_qry="select user_id from $temp_user_table where user_id='$id'";
		$temp_id=$db_object->get_a_line($sel_qry);
		if($temp_id["user_id"])
		{
			$lastbit=preg_replace("/<{id}>/s",$id,$lastbit);
			$xSubtemp=$lastbit.$xSubtemp;
		}
		
		$replaced.=$xSubtemp."<hr>";

}
$xTemplate=preg_replace("/<{parent_table_loopstart}>(.*?)<{parent_table_loopend}>/s",$replaced,$xTemplate);


preg_match("/<{dyna_loopstart}>(.*?)<{dyna_loopend}>/s",$xTemplate,$match1);
$repla=$match1[1];

	for($i=0;$i<count($dynafield_arr);$i++)
		{
			$field_name_dis=$dynafield_arr[$i][$nametype];
			$field_name=$dynafield_arr[$i]["field_name"];
			$replaced1.=preg_replace("/<{(.*?)}>/e","$$1",$repla);
		}
$xTemplate=preg_replace("/<{dyna_loopstart}>(.*?)<{dyna_loopend}>/s",$replaced1,$xTemplate);
$xTemplate=preg_replace("/<{link_loopstart}>(.*?)<{link_loopend}>/s","",$xTemplate);

$xTemplate=$common->direct_replace($db_object,$xTemplate,$vals);
		echo $xTemplate;
	}


//--------------this function arranges the default fields to be sorted
//-even when the user comes first in the page ie every radio box

function check_it($common,$db_object,$post_var1)
{

$f=0;
	while(list($kk,$vv)=@each($post_var1))
	{
		$$kk=$vv;
		if($vv!="")
		{

			$rdi=split("##;##",$kk);
		
			$termil=$rdi[1];
			if($termil=="radio")
			{
				$temp=$rdi[0];
				$sortarr[$vv]=$temp;
				$termil="";
			}
			
			
		}
	}

	return $sortarr;
	

}

function save_the_employeedeatils($common,$db_object,$user_id,$default,$error_msg,$form_array,$gbl_delete_table)
{
	$f=0;
//	print_r($form_array);
//	exit;
	while(list($kk,$vv)=@each($form_array))
	{
		$$kk=$vv;
		if($vv!="")
		{
			$fldn=split("_",$kk);
			$id=$fldn[1];
			$temp="fCheck_".$id;
			
			
			if($$temp!="delete" || $$temp=="update")
			{
				if(ereg("^fLevel_",$kk))
				{
					$othertables[$id]["level"]=$vv;
					
					
				}
				else if(ereg("^fLocation_",$kk))
				{
					$properties[$id]["location"]=$vv;
					
				}
				else if(ereg("^fPosition_",$kk))
				{
					$properties[$id]["position"]=$vv;	
				}
				else if(ereg("^fFamily_",$kk))
				{
					$othertables[$id]["fam_id"]=$vv;	
				}
				else if(ereg("^fBoss_",$kk))
				{
					$othertables[$id]["boss_no"]=$vv;	
				}
				else if(ereg("^fEmployeestatus_",$kk))
				{
					$properties[$id]["employment_type"]=$vv;	
				}
				else if(ereg("^fAccessrights_",$kk))
				{
					$properties[$id]["access_rights"]=$vv;	
				}
				else if(ereg("^fEEOtags_",$kk))
				{
					$eeotables[$id]["eeo_tags"]=$vv;	
				}
				else if(ereg("^fLogin_",$kk))
				{
					$properties[$id]["username"]=$vv;	

				}
				else if(ereg("^fPassword_",$kk))
				{
					$properties[$id]["password"]=$vv;	
				}
				else if(ereg("^fOffno_",$kk))
				{
					$properties[$id]["office_phone"]=$vv;	
				}
				else if(ereg("^fCellno_",$kk))
				{
					$properties[$id]["cell_phone"]=$vv;	
				}
				else if(ereg("^fPager_",$kk))
				{
					$properties[$id]["pager"]=$vv;	
				}
				else if(ereg("^fFax_",$kk))
				{
					$properties[$id]["fax"]=$vv;	
				}
				else if(ereg("^fMailaddrs_",$kk))
				{
					$properties[$id]["office_mail_address"]=$vv;	
				}
				else if(ereg("^fPhyaddrs_",$kk))
				{
					$properties[$id]["office_physical_address"]=$vv;	
				}
				else
				{
					$fldn=split("#;#",$kk);
					$id=$fldn[1];
					if($id!="")
					{
						$fieldname=$fldn[0];
						$fieldvalue=$vv;
						$properties[$id][$fieldname]=$fieldvalue;
					//	$sub[$id]="$fieldname=$fieldvalue";
					}
				}
				
			
			}
			else
			{
				$user_to_be_deleted[$f++]=$id;
			}

			
		}
		
	}


$user_table=$common->prefix_table("user_table");


//-----------	THIS DELETES THE EMPLOYEES WHO ARE CHECKED FROM THE FORM TO DELETE

	
	if($user_to_be_deleted[0]!="")
	{
		
		//echo $error_msg["cEmp_Del"];
		
		for($i=0;$i<count($user_to_be_deleted);$i++)
		{
			$emp_id=$user_to_be_deleted[$i];
			$selqry="select username from $user_table where user_id='$emp_id'";
			$user=$db_object->get_a_line($selqry);
			
			$path=$common->path;
			
						
			$mod_l=md5("learning");
			
			$mod_c=md5("career");
			
			$mod_p=md5("performance");
			
			$mod_co=md5("core");
			
			$gbl_del_table=array();
			
			$check=$common->is_module_purchased_check($db_object,$path,$mod_l);						
			
			
			if($check==1)
			{
				$gbl_del_table=@array_merge($gbl_del_table,$gbl_delete_table['career']);
			}
				
			$check=$common->is_module_purchased_check($db_object,$path,$mod_p);						
			if($check==1)
			{
			
				$gbl_del_table=@array_merge($gbl_del_table,$gbl_delete_table[performance]);
			}
		
			$check=$common->is_module_purchased_check($db_object,$path,$mod_l);						
			if($check==1)
			{
			
				$gbl_del_table=@array_merge($gbl_del_table,$gbl_delete_table[learning]);
			}
			
			
			$delstatus=$common->delete_employee($db_object,$emp_id,$gbl_del_table);
			
			if($delstatus==true)
			{
				echo $user["username"];
				echo $error_msg['cDeleted'];
			}
			else
			{
				echo $user["username"];
				echo $error_msg["cUserisbosscantdel"];
			}
		}
		
	}



$mysql="update table $user_table set ";



//--------------UPDATES THE EMPLOYE INFORMATION GOES IN LOOP WITHIN A LOOP

	while(list($kk,$vv)=@each($properties))
	{
		$id=$kk;
		$mysql="update $user_table set ";
		$temparr=$properties[$id];
		//print_r($properties);exit;
		while(list($k,$v)=@each($temparr))
		{
			$submysql.="$k='$v',";
			
		}
		$endsql=" where user_id='$id'";
		$submysql=substr($submysql,0,-1);
		$mysql.=$submysql.$endsql;
		//echo $mysql;
		$db_object->insert($mysql);
		$submysql="";
	}
	

			
//	echo $error_msg["cUserDetailsUpdates"];

//print_r($eeotables);
$user_eeo=$common->prefix_table("user_eeo");
	while(list($kk,$vv)=@each($eeotables))
	{
		$mysql="delete from $user_eeo where user_id='$kk'";
		$db_object->insert($mysql);

		$temp_arr=$eeotables[$kk]["eeo_tags"];
		while(list($k,$v)=@each($temp_arr))
		{
			$mysql="insert into $user_eeo set tag_id='$v',user_id='$kk'";
			$db_object->insert($mysql);
			//echo "$mysql<br>";
		}

	
	}
echo $error_msg["cUserDetailsUpdates"];

	

}

//-------------I think this function
//------------get the headings according to which they are
//sorted and arrages them in order so that it will be useful in the display fn query


function presort($common,$db_object,$check1,$check2,$check3,$default)
{
	if($check1)
	{
	$sortarr[1]=$check1;
	$sortarr[2]=$check2;
	$sortarr[3]=$check3;
	}
	$name="name_".$default;
$namefields=$common->prefix_table("name_fields");
$selqry="select field_name from $namefields where status='YES'";
$namarr=$db_object->get_single_column($selqry);

	for($i=1;$i<count($sortarr);$i++)
	{
		$fldname=$sortarr[$i];
		if(in_array($fldname,$namarr))
		{
			$key=array_search($fldname,$namarr);
			$sortarr[$i]=$namarr[$key];
		}
		else
		{
			switch($fldname)
			{
				case "level":
				$sortarr[$i]="level";
				break;
				case "location":
				$sortarr[$i]="location";
				break;
				case "position":
				$sortarr[$i]="position";
				break;
				case "family":
				$sortarr[$i]="family";
				break;
				case "boss":
				$sortarr[$i]="boss";
				break;
				case "employment_type":
				$sortarr[$i]="employment_type";
				break;
				case "access_rights":
				$sortarr[$i]="access_rights";
				break;
				default:
				$sortarr[$i]="user_id";
				break;
			}
		}
	}
			

	return $sortarr;
	
}
	
}
$fixobj= new Fix_employee;

//print_r($post_var);exit;
$post_var1=$post_var;
if($fSave)
{
	$fixobj->save_the_employeedeatils($common,$db_object,$user_id,$default,$error_msg,$post_var,$gbl_delete_table);
//	exit;
}
else
{
	if($Sort)
	{ 
	$sortarr=$fixobj->check_it($common,$db_object,$post_var);
	$fixobj->display($common,$db_object,$user_id,$default,$sortarr,$fPage,$error_msg);	
	}
	else
	{

	$sortarr=$fixobj->presort($common,$db_object,$check1,$check2,$check3,$default);	
	$fixobj->display($common,$db_object,$user_id,$default,$sortarr,$fPage,$error_msg);
	}
}
include("footer.php");
?>
