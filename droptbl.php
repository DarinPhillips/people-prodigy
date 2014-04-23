<?php
include("includes/database.class");
$db_object=new database;

$sht="show tables";
$dtables=$db_object->get_rsltset($sht);

for($i=0;$i<count($dtables);$i++)
{
$tbl=$dtables[$i][0];
$qry="drop table $tbl";
$db_object->insert($qry);
//echo $qry;
}
echo "tables droped";
?>