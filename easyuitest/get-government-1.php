<?php
header('Content-Type: text/html;charset=UTF-8');
include("db.php");
$conn=mysql_connect($host, $username, $password); //�إ߳s�u
mysql_query("SET NAMES 'utf8'"); //�]�w�d�ߩҥΤ��r������ utf-8
mysql_select_db($database, $conn); //�}�Ҹ�Ʈw
$result=array();
$id=isset($_POST['id']) ? intval($_POST['id']) : 0;
$rs=mysql_query("SELECT * FROM government WHERE parentId=$id"); 
while ($row=mysql_fetch_array($rs)){ //�Y�`�I�s�b
  $node=array();
  $node["id"]=$row["id"];
  $node["text"]=$row["text"];
  $node["state"]=has_child($row["id"]) ? "closed" : "open";
  array_push($result,$node);
  }
echo json_encode($result);
function has_child($id){
  $SQL="SELECT count(*) FROM government WHERE parentId=$id";
  $rs=mysql_query($SQL);
  $row=mysql_fetch_array($rs);
  return $row[0] > 0 ? true : false;
  }
?>