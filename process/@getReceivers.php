<?php
/**
 * 이 파일은 iModule SMS모듈의 일부입니다. (https://www.imodule.kr)
 *
 * 받는사람 목록을 가져온다.
 * 
 * @file /modules/sms/process/@getReceivers.php
 * @author Arzz (arzz@arzz.com)
 * @license MIT License
 * @version 3.0.0.160910
 */
if (defined('__IM__') == false) exit;

$receivers = json_decode(Request('receivers'));

$lists = array();
for ($i=0, $loop=count($receivers);$i<$loop;$i++) {
	$member = $this->IM->getModule('member')->getMember($receivers[$i]->midx);
	
	$cellphone = $receivers[$i]->cellphone && CheckPhoneNumber($receivers[$i]->cellphone) == true ? GetPhoneNumber($receivers[$i]->cellphone) : GetPhoneNumber($member->cellphone);
	
	if ($cellphone) $lists[] = array('midx'=>$member->idx,'name'=>$member->name,'cellphone'=>$cellphone);
}

$results->success = true;
$results->lists = $lists;
$results->total = count($lists)
?>