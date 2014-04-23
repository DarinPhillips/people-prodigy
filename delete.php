<?
include_once("includes/database.class");

include_once("includes/common.class");

include_once("includes/globals.php");

$common=new common();

$db_object=new database();


class delete
{
	function delete_employee($db_object,$user_id,$gbl_delete_table)
	{
		//print_r($gbl_delete_tables);
		
		$c=count($gbl_delete_table);
		
		$values=@array_values($gbl_delete_table);
		
		$keys=@array_keys($gbl_delete_table);
		
		for($a=0;$a<count($keys);$a++)
		{
			$delqry="delete from $keys[$a] where $values[$a]='$user_id'";
			
			echo "DEL=$delqry<br>";
			
			$db_object->insert($delqry);
		}
	}
}

$obj=new delete();

$user_id=237;

$obj->delete_employee($db_object,$user_id,$gbl_delete_table);
?>
