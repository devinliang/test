<?php
header('Content-Type: text/html;charset=UTF-8');
include("db.php");
$conn=mysql_connect($host, $username, $password); //�إ߳s�u
mysql_query("SET NAMES 'utf8'"); //�]�w�d�ߩҥΤ��r������ utf-8
mysql_select_db($database, $conn); //�}�Ҹ�Ʈw
$term=$_GET['term'];  //�^�� jQueryUI �ǥX�Ѽ� 'term'
$SQL="SELECT * FROM `stocks_list` WHERE `stock_id` LIKE '".$term. 
     "%' OR `stock_name` LIKE '".$term."%'"; 
$result=mysql_query($SQL, $conn); //���� SQL ���O
$arr=Array(); //�Ψ��x�s stock_id ���Ȥ��}�C
for ($i=0; $i<mysql_numrows($result); $i++) { //���X������ (�C)
     $row=mysql_fetch_array($result); //���o�C�}�C
     $label=$row["stock_name"];
     $value=$row["stock_id"];
     $arr[]='{"label":"'.$label.'","value":"'.$value.'"}'; //��J�}�C
     } //end of for
echo "[".join(",", $arr)."]";
?>