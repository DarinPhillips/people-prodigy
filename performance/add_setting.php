<?
include_once("../session.php");
include_once("header.php");
class addsetting
	{
	function view($db_object,$common)
		{
			
			$setting = $common->prefix_table("performance_setting");
			$config = $common->prefix_table("config");
	
			$qry = "select ps_id,category,metrics,plan_no from $setting";
			$res = $db_object->get_a_line($qry);
			
			$selqry="select person_affected,person_help_needed,
				i_boss,b_boss,no_of_qualification from $config where id=1";
			$rslt=$db_object->get_a_line($selqry);

			$peraff = $rslt['person_affected'];
			$perhelp  = $rslt['person_help_needed'];
			$vals["category"] = $res['category'];
			$vals["metrics"] = $res['metrics'];
			$vals["peraff"] = $peraff;
			$vals["perhelp"] = $perhelp;
			$vals["qualification"] = $rslt['no_of_qualification'];			
			$vals["plan"]=$res['plan_no'];

			if($rslt['i_boss']=='Y')
			{
				$vals["ichecked"]="checked";
			}
			if($rslt['b_boss']=='Y')
			{
				$vals["bchecked"]="checked";
			}
			$path = $common->path;
			$path = $path."templates/performance/add_setting.html";
			$file = $common->return_file_content($db_object,$path);
			$file = $common->direct_replace($db_object,$file,$vals);
			echo $file ;
		}//end view
	function update($db_object,$common,$_POST,$user_id)
		{
			while(list($key,$val)=each($_POST))
			{
				$$key = $val;
			}
		
			$setting = $common->prefix_table("performance_setting");
			$config = $common->prefix_table("config");
			


							
//Replacing
			$qry = "replace into $setting set ps_id='1',category='$fCategory',
				metrics='$fMetrics',plan_no='$fPlan'";	
			$db_object->insert($qry);

			$insqry="update $config set person_affected ='$fPeraffected',
			person_help_needed='$fPerhelp',i_boss='$fBoss',b_boss='$fBboss',
			no_of_qualification='$fQualification' where id=1";
		   	$db_object->insert($insqry);		  	

	
									
		}

	}//end class
	$ob  = new addsetting;
	if($submit)
	{
		
		$ob->update($db_object,$common,$_POST,$user_id);
	}		
	if($user_id=='1')
	{
	$ob->view($db_object,$common);
	}


include_once("footer.php")

?>
