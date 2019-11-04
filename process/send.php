<?php
/**
 * 이 파일은 iModule SMS모듈의 일부입니다. (https://www.imodules.io)
 *
 * SMS 을 전송한다.
 * 
 * @file /modules/sms/process/send.php
 * @author Arzz (arzz@arzz.com)
 * @license MIT License
 * @version 3.1.0
 * @modified 2019. 11. 4.
 */
if (defined('__IM__') == false) exit;

$errors = array();
$sender = Request('sender') ? Request('sender') : $errors['sender'] = $this->getErrorText('REQUIRED');
$midxes = Request('midxes');
$midx = Request('midx');
$message = Request('message') ? Request('message') : $errors['message'] = $this->getErrorText('REQUIRED');

if ($midxes == null && $midx == null) {
	$receiver = Request('receiver') ? Request('receiver') : $errors['receiver'] = $this->getErrorText('REQUIRED');
} elseif ($midxes == null) {
	$midxes = array($midx);
}

if (count($errors) == 0) {
	foreach ($midxes as $midx) {
		$result = $this->setSender($this->IM->getModule('member')->getLogged(),$sender)->setReceiver($midx)->setMessage($message)->send();
	}
	
	$results->success = $result !== true ? false : true;
	$results->error = $result !== true ? $result : null;
} else {
	$results->success = false;
	$results->errors = $errors;
}
?>