<?php
header('Content-Type: text/html;charset=UTF-8');
include("db.php");
$conn=mysql_connect($host, $username, $password); //�إ߳s�u
mysql_query("SET NAMES 'utf8'"); //�]�w�d�ߩҥΤ��r������ utf-8
mysql_select_db($database, $conn); //�}�Ҹ�Ʈw
$page=isset($_POST['page']) ? intval($_POST['page']) : 1;
$rows=isset($_POST['rows']) ? intval($_POST['rows']) : 10;
$sort=isset($_POST['sort']) ? $_POST['sort'] : 'id';
$order=isset($_POST['order']) ? $_POST['order'] : 'asc';
if (isset($_POST['search_field'])) { //�� search
  $where=" WHERE ".$_POST['search_field']." LIKE '%".
         $_POST['search_what']."%'";
  }
else {$where="";} //�L search
$start=($page-1) * $rows;  //�����Ĥ@�ӦC���� (0 �_�l)
$SQL="SELECT COUNT(*) FROM `stocks`".$where;
$RS=mysql_query($SQL, $conn);
list($total)=mysql_fetch_row($RS); //�����`����
$SQL="SELECT * FROM `stocks` ".$where." ORDER BY ".
     $sort." ".$order." LIMIT ".$start.",".$rows; 
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
$arr=array("total" => $total, "rows" => $stock);
echo json_encode($arr);  //�N�}�C�ন JSON ��Ʈ榡�Ǧ^
?>