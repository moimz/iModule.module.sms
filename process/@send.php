<?php
/**
 * 이 파일은 iModule SMS모듈의 일부입니다. (https://www.imodule.kr)
 *
 * 받는사람 목록을 가져온다.
 * 
 * @file /modules/sms/process/@send.php
 * @author Arzz (arzz@arzz.com)
 * @license MIT License
 * @version 3.0.0.160910
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