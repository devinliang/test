<?php
header('Content-Type: text/html;charset=UTF-8');
include("db.php");
$conn=mysql_connect($host, $username, $password); //�إ߳s�u
mysql_query("SET NAMES 'utf8'"); //�]�w�d�ߩҥΤ��r������ utf-8
mysql_select_db($database, $conn); //�}�Ҹ�Ʈw
$SQL="SELECT COUNT(*) FROM `stocks`";
$RS=mysql_query($SQL, $conn);
list($total)=mysql_fetch_row($RS); //�����`����
$SQL="SELECT * FROM `stocks`"; 
$result=mysql_query($SQL, $conn); //���� SQL ���O
$stock=array(); 
for ($i=0; $i<mysql_numrows($result); $i++) { //���X������ (�C)
     $row=mysql_fetch_array($result); //���o�C�}�C
     $stock[$i]=array("name" => $row["name"],
		              "id" => $row["id"],
		              "close" => $row["close"],
		              "volumn" => $row["volumn"],
		              "meeting" => $row["meeting"],
		              "election" => $row["election"],
		              "category" => $row["category"]
		              );
     } //end of for
$arr=array("totalProperty" => $total, "root" => $stock);
echo json_encode($arr);  //�N�}�C�ন JSON ��Ʈ榡�Ǧ^
?>