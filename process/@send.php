<?php
/**
 * 이 파일은 iModule SMS모듈의 일부입니다. (https://www.imodules.io)
 *
 * SMS 을 전송한다.
 * 
 * @file /modules/sms/process/@send.php
 * @author Arzz (arzz@arzz.com)
 * @license MIT License
 * @version 3.1.0
 * @modified 2018. 11. 13.
 */
if (defined('__IM__') == false) exit;

$sender = Request('sender');
$receivers = json_decode(Request('receivers'));
$message = Request('message');

$success = true;
for ($i=0, $loop=count($receivers);$i<$loop;$i++) {
	$result = $this->setSender($this->IM->getModule('member')->getLogged(),$sender)->setReceiver($receivers[$i]->midx,$receivers[$i]->cellphone)->setMessage($message)->send();
	$success = $result !== true ? false : $success;
}

$results->success = $success;
?>