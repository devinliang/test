<?php
header('Content-Type: text/html;charset=UTF-8');
include("db.php");
$conn=mysql_connect($host, $username, $password); //�إ߳s�u
mysql_query("SET NAMES 'utf8'"); //�]�w�d�ߩҥΤ��r������ utf-8
mysql_select_db($database, $conn); //�}�Ҹ�Ʈw
$start=$_GET['start'];  //�^�� ExtJS �ǥX�Ѽ� 'start'
$limit=$_GET['limit'];  //�^�� ExtJS �ǥX�Ѽ� 'limit'
$sort=$_GET['sort'];    //�^�� ExtJS �ǥX�Ѽ� 'sort'
$dir=$_GET['dir'];      //�^�� ExtJS �ǥX�Ѽ� 'dir'
if (is_null($dir)) {$dir="ASC";}
$SQL="SELECT * FROM `stocks_list`"; 
$result=mysql_query($SQL, $conn); //���� SQL ���O
$stock=array(); 
for ($i=0; $i<mysql_numrows($result); $i++) { //���X������ (�C)
     $row=mysql_fetch_array($result); //���o�C�}�C
     $stock_name=$row["stock_name"];
     $stock_id="<a href='http://tw.stock.yahoo.com/q/q?s=".
               $row["stock_id"]."' target='_blank'>".
               $row["stock_id"]."</a>";
     //$stock_id=$row["stock_id"];
     $edit="<a href='edit_stocks_list.php?stock_id=".
           $row["stock_id"]."&stock_name=".
           $row["stock_name"]."' target='_blank'><img src='edit.gif' ".
           "style='border-width:0px;width:15px;height:15px;'></a>";
     $stock[$i]=array(
                      "stock_name" => $stock_name, 
                      "stock_id" => $stock_id);
     } //end of for
$arr=array("totalProperty" => 100, "root" => $stock);
echo json_encode($arr);  //�N�}�C�ন JSON ��Ʈ榡�Ǧ^
?>