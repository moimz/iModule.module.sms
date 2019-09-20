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
 * @modified 2019. 8. 21.
 */
if (defined('__IM__') == false) exit;

$errors = array();
$sender = Request('sender') ? Request('sender') : $errors['sender'] = $this->getErrorText('REQUIRED');
$midx = Request('midx');
$message = Request('message') ? Request('message') : $errors['message'] = $this->getErrorText('REQUIRED');

if ($midx == null) {
	$receiver = Request('receiver') ? Request('receiver') : $errors['receiver'] = $this->getErrorText('REQUIRED');
} else {
	$receiver = Request('receiver');
}

if (count($errors) == 0) {
	$result = $this->setSender($this->IM->getModule('member')->getLogged(),$sender)->setReceiver($midx,$receiver)->setMessage($message)->send();
	
	$results->success = $result !== true ? false : true;
	$results->error = $result !== true ? $result : null;
} else {
	$results->success = false;
	$results->errors = $errors;
}
?>