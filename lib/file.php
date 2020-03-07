<?php
/*-----------------------------------------------------------------------------
Title        : PHP File API
Author       : Tony
Version      : v1.0.0
Prototype    : 2013/03/28
Last Updated : 2013/03/30
Usage        : Manipulate Files
-----------------------------------------------------------------------------*/
/*-----------------------------------------------------------------------------
read_file($filename)
�\�� : 
  ����ƥΨ�Ū����r��, ���G�H�r��Ǧ^.				
�Ѽ� :                                   
  $filename : ��r���ɦW (�۹���|, �Ҧp log/visitors.txt)   
�Ǧ^�� :
  ���\�Ǧ^�ɮפ��e, ���ѶǦ^ FALSE.
�d�� : 
  $result=read_file("./download/test.txt");    
-----------------------------------------------------------------------------*/
function read_file($filename) {
  $result=file_get_contents(realpath($filename));
  return $result;
  }
/*-----------------------------------------------------------------------------
read_lines($filename)
�\�� : 
  ����ƥH�欰���Ū���~����r��, �h������r����s�J�}�C���Ǧ^.				
�Ѽ� :                                   
  $filename : ��r���ɦW (�۹���|, �Ҧp log/visitors.txt))   
�Ǧ^�� :
  �}�C
�d�� : 
  $result=read_lines("test.log");
  for ($i=0; $i<count($result); $i++) {
       echo "line ".$i.":".$result[$i];
       }
  foreach ($result as $k => $v) {
       echo "line ".$k[$i].":".$v;
       }
-----------------------------------------------------------------------------*/
function read_lines($filename) {
  $result=file(realpath($filename));
  return $result;  
  }
/*-----------------------------------------------------------------------------
read_binary($filename)
�\�� : 
  ����ƥΨ�Ū���G�i���� (�Ҧp����), ���G�H�r��Ǧ^.				
�Ѽ� :                                   
  $filename : �G�i�����ɦW (�۹���|, �Ҧp log/visitors.txt))  
�Ǧ^�� :
  ���\�Ǧ^�G�i���ɤ��e, ���ѶǦ^ FALSE.
�d�� : 
  $result=read_binary("test.gif"); 
  echo $result; //��ܹ���
-----------------------------------------------------------------------------*/
function read_binary($filename) {
  $pointer=fopen($filename, "rb");  //Windows �D���@�w�n�[ b �~�i�H
  $result=fread($pointer, filesize($filename)); 
  fclose($pointer); //����
  return $result;
  }
/*-----------------------------------------------------------------------------
write_file($filename,$text)
�\�� : 
  ����ƥH��w���N�Ҧ��N��r�g�J�~����r�� (���N�줺�e).
�Ѽ� :                                   
  $filename : ��r���ɦW (�۹���|, �Ҧp log/visitors.txt))
  $text     : �ɮפ��e   
�Ǧ^�� :
  ���\�Ǧ^�g�J�� bytes ��, ���ѶǦ^ FALSE.
  �`�N, �Ǧ^ 0 byte ��Q�{�� FALSE, �G�P�_�ɭn�� if ($result===FALSE).
�d�� : 
  $result=write_file("./log/daily.log", "This is a book");    
-----------------------------------------------------------------------------*/  
function write_file($filename,$text) {
  $result=file_put_contents(realpath($filename), $text, LOCK_EX); //��w&���N
  return $result; 
  }
/*-----------------------------------------------------------------------------
append_file($filename,$text)
�\�� : 
  ����ƥH��w�K�[�Ҧ��N��r�g�J�~����r�� (��b�줺�e����).
�Ѽ� :                                   
  $filename : ��r���ɦW (�۹���|, �Ҧp log/visitors.txt))
  $text     : �ɮפ��e   
�Ǧ^�� :
  ���\�Ǧ^�g�J�� bytes ��, ���ѶǦ^ FALSE.
  �`�N, �Ǧ^ 0 byte ��Q�{�� FALSE, �G�P�_�ɭn�� if ($result===FALSE).
�d�� : 
  $result=write_file("./log/daily.log", "This is a book");    
-----------------------------------------------------------------------------*/  
function append_file($filename,$text) {
  $result=file_put_contents(realpath($filename), $text, FILE_APPEND|LOCK_EX); 
  return $result; 
  }
/*-----------------------------------------------------------------------------
upload_file($uploader,$path="./")
�\�� : 
  ����ƱN�W�Ǥ���@�ɮ�, �ѼȦs�ϲ��ܫ��w���|�U.
�Ѽ� :                                   
  $uploader : �W�Ǥ���W��, �Y <input type="file" name="uploader"> ���� name ��
  $path     : �ɮ��x�s���|, �Ҧp "./images"   
�Ǧ^�� :
  ���\�Ǧ^�}�C, ���ѶǦ^ FALSE. �}�C���T�Ӥ��� :
  $result["name"]=�ɮצW��
  $result["type"]=�ɮ�����
  $result["size"]=�ɮפj�p
�d�� : 
  <input type="file" name="uploader">
  $result=upload_file("uploader", "./images"); 
  echo "�ɮ� ".$result["name"]." �W�Ǧ��\ (".$result["size"]." bytes)";
-----------------------------------------------------------------------------*/  
function upload_file($uploader,$path="./") {
  if ($_FILES[$uploader]["error"]==0) { //�W�Ǧ��\
      $tmp_name=$_FILES[$uploader]["tmp_name"];
      $new_name=$path.$_FILES[$uploader]["name"];
      if (move_upload_file($tmp_name, $new_name)) { //���ʦ��\
          $result["name"]=$_FILES[$uploader]["name"];
          $result["type"]=$_FILES[$uploader]["type"];
          $result["size"]=$_FILES[$uploader]["size"];
          } //end of if
      else {$result=FALSE;} //���ʥ���
      } //end of if
  else {$result=FALSE;} //�W�ǥ���
  return $result; 
  }
/*-----------------------------------------------------------------------------
upload_files($uploader,$path="./")
�\�� : 
  ����ƱN�W�Ǥ��h���ɮ�, �ѼȦs�ϲ��ܫ��w���|�U.
�Ѽ� :                                   
  $uploader : �W�Ǥ���W��, �D�}�C�Φ�, �Ҧp�U�C input ���� name ��
              �ɮ�1 : <input type="file" name="uploader[]"> 
              �ɮ�2 : <input type="file" name="uploader[]"> 
              �W�Ǫ�椧 enctype ���]�� multipart/form-data :
              <form action="upload.php" method="post" 
              enctype="multipart/form-data">
  $path     : �ɮ��x�s���|, �Ҧp "./images" (�w�]�Ȭ��ثe�{���Ҧb�ؿ� ./)
�Ǧ^�� :
  ���\�Ǧ^�@�ӤG���}�C, ���ѶǦ^ FALSE. �}�C�Ĥ@�����Ʀr����, �ĤG�������p��,
  ���T�Ӥ��� : �Ҧp�Ĥ@�ӤW���ɮ� :
  $result[0]["name"]=�ɮצW��
  $result[0]["type"]=�ɮ�����
  $result[0]["size"]=�ɮפj�p
�d�� : 
  <input type="file" name="uploader[]">
  $result=upload_files("uploader", "./images"); 
  for ($i=0; $i<count($result); $i++) {
       echo $result[$i]["name"]."�W�Ǧ��\(".$result[$i]["size"]." bytes)<br>";
       }
-----------------------------------------------------------------------------*/  
function upload_files($uploader,$path="./") {
  $counts=count($_FILES[$uploader]["name"]); //�W���ɮ׼�
  for ($i=0; $i<$counts; $i++) { 
       if ($_FILES[$uploader]["error"][$i]==0) { //�W�Ǧ��\
           $tmp_name=$_FILES[$uploader]["tmp_name"][$i];
           $new_name=$path.$_FILES[$uploader]["name"][$i];
           if (move_upload_file($tmp_name, $new_name)) { //���ʦ��\
               $result[$i]["name"]=$_FILES[$uploader]["name"][$i];
               $result[$i]["type"]=$_FILES[$uploader]["type"][$i];
               $result[$i]["size"]=$_FILES[$uploader]["size"][$i];
               } //end of if
           else {$result=FALSE;} //���ʥ���
           } //end of if
       else {$result=FALSE;} //�W�ǥ���
       } //end of for
  return $result; 
  }
/*-----------------------------------------------------------------------------
get_filelist($path)
�\�� : 
  ����ƫ��w���|�U���ɮ׻P�ؿ��C��.
�Ѽ� :  
  $path : �ɮ��x�s���|, �Ҧp "./images", "sys/log"   
�Ǧ^�� :
  ���\�Ǧ^�}�C, ���ѶǦ^ FALSE. �}�C����ӭӤ��� :
  $result["file"]=�ɮצC��
  $result["dir"]=�ؿ��C��
�d�� :
  $result=get_filelist("./images"); 
  echo "�ɮצC��".$result["file"]." �W�Ǧ��\ (".$result["size"]." bytes)";
-----------------------------------------------------------------------------*/  
function get_filelist($path) {
  $realpath=realpath($path);
  $resource=scandir($realpath);
  foreach ($resource as $filename) {
    if (is_dir($realpath."\\".$filename)) {$dir[]=$filename;}
    if (is_file($realpath."\\".$filename)) {$file[]=$filename;}
    }
  $result["dir"]=join(",",$dir);
  $result["file"]=join(",",$file);
  return $result; 
  }
?>