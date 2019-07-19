<?php
/**
 * 이 파일은 iModule SMS모듈의 일부입니다. (https://www.imodules.io)
 *
 * 관리자 목록을 불러온다.
 * 
 * @file /modules/sms/process/@getAdmins.php
 * @author Eunseop Lim (eslim@naddle.net)
 * @license MIT License
 * @version 3.0.0
 * @modified 2019. 7. 18.
 */
if (defined('__IM__') == false) exit;

$lists = $this->db()->select($this->table->admin)->get();
for ($i=0, $loop=count($lists);$i<$loop;$i++) {
	$member = $this->IM->getModule('member')->getMember($lists[$i]->midx);
	
	$lists[$i]->name = $member->name;
	$lists[$i]->email = $member->email;
}

$results->success = true;
$results->lists = $lists;
$results->total = count($lists);
?>