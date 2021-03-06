<?php
/*-----------------------------------------------------------------------------
Title        : PHP Telnet API
Translator   : Tony
Version      : v1.1.1
Prototype    : 2013/03/28
Last Updated : 2013/03/28
Usage        : Manipulate Telnet Connection
Note         : This software is adapted from Antone Roundy's work on PHPTelnet 
               v1.1.1. It is only for private use.
               Original : http://www.geckotribe.com/php-telnet/
-----------------------------------------------------------------------------*/
/*-----------------------------------------------------------------------------
PHPTelnet Class
功能 : 
  此類別用來建立 Telnet 連線.			
參數 :                                   
  $host     : Telnet 主機位址
  $username : 使用者名稱
  $password : 使用者密碼
傳回值 :
  連線結果字串
範例 : 
  require_once "PHPTelnet.php";
  $telnet=new PHPTelnet();
  //若第一個參數空白 "", 則 PHPTelnet 將連線 localhost (127.0.0.1)
  $result=$telnet->Connect('www.somewhere.com','login name','password');
  if ($result==0) {
      $telnet->DoCommand('enter command here', $result);      
      echo $result; //傳回值包含跳行字元
      $telnet->DoCommand('another command', $result);
      echo $result;
      $telnet->Disconnect(); //Disconnect(0)=不登出直接斷線
      }
-----------------------------------------------------------------------------*/
class PHPTelnet {
  var $port=23;
  var $show_connect_error=1;
  var $use_usleep=0; //預設=0, 改為 1 可加快執行速度 (使用自訂之 Sleep() 函式)
  //don't change to 1 on Windows servers unless you have PHP 5
  var $sleeptime=125000;  //預設=125000 us
  var $loginsleeptime=1000000;  //預設=1000000 us
  var $fp=NULL;
  var $loginprompt;
  var $conn1;
  var $conn2;  
  /*
  0 = success
  1 = couldn't open network connection
  2 = unknown host
  3 = login failed
  4 = PHP version too low
  */
  function Connect($server,$user,$pass) {
    //檢查 PHP 版本
    $rv=0;
    $vers=explode('.',PHP_VERSION);
    $needvers=array(4,3,0);
    $j=count($vers);
    $k=count($needvers);
    if ($k < $j) {$j=$k;}
        for ($i=0; $i<$j; $i++) { //檢查 PHP 版本
             if (($vers[$i]+0)>$needvers[$i]) {break;} //版本 OK
             if (($vers[$i]+0)<$needvers[$i]) {
                 //PHP 版本太低不足以執行 Telnet
                 $this->ConnectError(4); 
                 return 4;
                 } //end of if
             } //end of for
        //先關閉連線 : 連線前先確定目前無連線
        $this->Disconnect();
        echo "PHP version checked OK\n";
        //檢查 server host 
        if (strlen($server)) { //$server 有設
            if (preg_match('/[^0-9.]/',$server)) {  //伺服器值不是 IP
                $ip=gethostbyname($server);  //轉成 IP
                if ($ip==$server) {
                    $ip='';
                    $rv=2; //Unknown host
                    } //end of if
                } //end of if
        else {$ip=$server;} //伺服器值為 IP
        } //end of if
    else {$ip='127.0.0.1';} //預設 ip
    echo "Connect to IP : $ip\n";
    if (strlen($ip)) {  //ip 有設
        if ($this->fp=fsockopen($ip,$this->port)) {  //開啟 Telnet 連線
            fputs($this->fp,$this->conn1);
            echo "conn1 connected\n";
            $this->Sleep();
            //登入帳號
            fputs($this->fp,$this->conn2);
            echo "conn2 connected\n";  
            /*
            $this->Sleep();
            $start_time=time();
            $this->GetResponse($r);   //取得主機回應 : 不需要, 取消         
            $end_time=time();          
            $delay=$end_time-$start_time;
            echo "delay : {$delay}\n";
            echo "Response Received\n";
            $r=explode("\n",$r);
            $this->loginprompt=$r[count($r)-1];          
            */
            fputs($this->fp,"$user\r");
            $this->Sleep();
            echo "Login : $user\r";
            //登入密碼
            fputs($this->fp,"$pass\r");
            if ($this->use_usleep) {usleep($this->loginsleeptime);}
            else {$this->Sleep(0.5);}
            echo "Password : $pass\r";
            $this->GetResponse($r);
            $r=explode("\n",$r);
            //檢查登入是否成功
            if (($r[count($r)-1]=='')||($this->loginprompt==$r[count($r)-1])) { 
                $rv=3; //Login failed 登入失敗
                $this->Disconnect();
               } //end of if
            echo "Login : Success!\r";
            } //end of if
        else {$rv=1;} //無法開啟 Telnet 連線
        } //end of if		
    if ($rv) {$this->ConnectError($rv);}  //0 : 表示連線成功, 1/2/3 為失敗
    return $rv;
    } //end of function
  
  function Disconnect($exit=1) { //Telnet 中斷連線
    if ($this->fp) {
        if ($exit) {$this->DoCommand('exit',$junk);}
        fclose($this->fp);
        $this->fp=NULL;
        } //end of if
    } //end of function

  function DoCommand($c,&$r,$wait_seconds=5) { //執行 Telnet 指令
    if ($this->fp) {
        fputs($this->fp,"$c\r");
        $this->Sleep($wait_seconds);
        $this->GetResponse($r);
        $r=preg_replace("/^.*?\n(.*)\n[^\n]*$/","$1",$r);
        } //end of if
    return $this->fp?1:0; //成功傳回 1 失敗傳回 0
    } //end of function
  
  function GetResponse(&$r) { //取得主機回應
    $r='';
    do { 
        $r.=fread($this->fp,1000);
        $s=socket_get_status($this->fp);
       } 
    while ($s['unread_bytes']);
    } //end of function
  
  function Sleep($seconds=1) { //暫停指令執行
    //使用微秒計時, 依指定微秒數暫停
    if ($this->use_usleep) {usleep($this->sleeptime);}  
    else {sleep($seconds);}  //否則預設暫停 1 秒
    } //end of function
  
  function PHPTelnet() { //建構子
    $this->conn1=chr(0xFF).chr(0xFB).chr(0x1F).chr(0xFF).chr(0xFB).
                 chr(0x20).chr(0xFF).chr(0xFB).chr(0x18).chr(0xFF).chr(0xFB).
                 chr(0x27).chr(0xFF).chr(0xFD).chr(0x01).chr(0xFF).chr(0xFB).
                 chr(0x03).chr(0xFF).chr(0xFD).chr(0x03).chr(0xFF).chr(0xFC).
                 chr(0x23).chr(0xFF).chr(0xFC).chr(0x24).chr(0xFF).chr(0xFA).
                 chr(0x1F).chr(0x00).chr(0x50).chr(0x00).chr(0x18).chr(0xFF).
                 chr(0xF0).chr(0xFF).chr(0xFA).chr(0x20).chr(0x00).chr(0x33).
                 chr(0x38).chr(0x34).chr(0x30).chr(0x30).chr(0x2C).chr(0x33).
                 chr(0x38).chr(0x34).chr(0x30).chr(0x30).chr(0xFF).chr(0xF0).
                 chr(0xFF).chr(0xFA).chr(0x27).chr(0x00).chr(0xFF).chr(0xF0).
                 chr(0xFF).chr(0xFA).chr(0x18).chr(0x00).chr(0x58).chr(0x54).
                 chr(0x45).chr(0x52).chr(0x4D).chr(0xFF).chr(0xF0);
    $this->conn2=chr(0xFF).chr(0xFC).chr(0x01).chr(0xFF).chr(0xFC).
                 chr(0x22).chr(0xFF).chr(0xFE).chr(0x05).chr(0xFF).chr(0xFC).
                 chr(0x21);
    } //end of function
  
  function ConnectError($num) { //連線錯誤
    if ($this->show_connect_error) {
        switch ($num) {
          case 1 : 
               echo '<br />[PHP Telnet] <a href="http://'.
                    'www.geckotribe.com/php-telnet/errors/fsockopen.php'.
	            '">Connect failed: Unable to open network connection'.
                    '</a><br />'; 
               break;
          case 2 : 
               echo '<br />[PHP Telnet] <a href="http://'.
                    'www.geckotribe.com/php-telnet/errors/unknown-host.php'.
	            '">Connect failed: Unknown host</a><br />'; 
               break;
          case 3 : 
               echo '<br />[PHP Telnet] <a href="http://'.
                    'www.geckotribe.com/php-telnet/errors/login.php'.
	            '">Connect failed: Login failed</a><br />'; 
               break;
          case 4 : 
               echo '<br />[PHP Telnet] <a href="http://'.
                    'www.geckotribe.com/php-telnet/errors/php-version.php'.
                    '">Connect failed: Your server\'s PHP version is too '.
                    'low for PHP Telnet</a><br />'; 
               break;
          } //end of switch
        }  //end of if
    } //end of function
  } //end of class
return; //?
?>