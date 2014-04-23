<?

include_once("../session.php");

class lables
	{
		function view($db_object,$common,$error_msg,$default)
		{
			while(list($key,$value)=each($error_msg))
				{
					$$key = $value;
				}
			$field = $common->prefix_table("name_fields");
			$qry = "desc $field";
			$res = $db_object->get_single_column($qry);
			$des = implode(",",$res);
		
			$qry = "select $des from $field";
			$res = $db_object->get_rsltset($qry);

			$file = file("../templates/core/add_lables.html");
			$out = join("",$file);

			$pattern="/<{record_loopstart(.*?)<{record_loopend}>/s";
			preg_match($pattern,$out,$arr);
			$match=$arr[0];
			$str="";
			for($i=0;$i<count($res);$i++)
			{
				$yeschecked ="";
				$nochecked="";
				$nid = $res[$i]['name_id'];
				$name1 = $res[$i]['name_1'];
				$status = $res[$i]['status'];
				
				if($status=='YES')
				{
					$yeschecked="checked";
				
				}
				else
				{
					$nochecked = "checked";
					
				}
				$str.=preg_replace("/\<\{(.*?)\}\>/e","$$1",$match);
					
			}
			
			$out=preg_replace($pattern,$str,$out);
			$vals=array();
			$out=$common->direct_replace($db_object,$out,$vals);
			echo $out;
		}
		function save($db_object,$common,$_POST,$default)
		{
			$yes= array();
			$no = array();
			while(list($key,$value)=each($_POST))
			{
			 $$key = $value;
				if($value=='YES')
				{
					if(ereg("^check_",$key))
					{
						$yes[]=substr($key,6);
		
					}
				}
				if($value=='NO')
				{
					if(ereg("^check_",$key))
					{
						$no[]=substr($key,6);
					}
				}
			}
			$yes_id = implode("','",$yes);
			$no_id = implode("','",$no);
			$fields = $common->prefix_table(name_fields);
			
			$qry1 = "update $fields set status='YES' where name_id in  ('$yes_id')";
			//echo "$qry1<br>";
			$res = $db_object->insert($qry1);
			$qry2 = "update $fields set status='NO' where name_id in  ('$no_id')";
			$res = $db_object->insert($qry2);
			//echo "$qry2<br>";
			
		}
	}//end class
	$ob = new lables;

if($fAddnew)
{
	header('Location:add_new_labels.php');
	exit;
}
	
include_once("header.php");
	if($submit)
	{
		$ob->save($db_object,$common,$_POST,$default);
	}
	$ob->view($db_object,$common,$error_msg,$default);
	
include_once("footer.php");

?>
