<?php
/**
 * 이 파일은 iModule SMS모듈의 일부입니다. (https://www.imodule.kr)
 *
 * 발송기록을 가져온다.
 * 
 * @file /modules/sms/process/@getSends.php
 * @author Arzz (arzz@arzz.com)
 * @license MIT License
 * @version 3.0.0.160910
 */
if (defined('__IM__') == false) exit;

$start = Request('start');
$limit = Request('limit');
$lists = $this->db()->select($this->table->send);
$total = $lists->copy()->count();
$sort = Request('sort');
$dir = Request('dir');

$keyword = Request('keyword');
if ($keyword) $lists->where('message','%'.$keyword.'%','LIKE');
if ($limit > 0) $lists->limit($start,$limit);
$lists = $lists->orderBy($sort,$dir)->get();

for ($i=0, $loop=count($lists);$i<$loop;$i++) {
	$lists[$i]->receiver_name = $lists[$i]->tomidx == 0 ? '비회원' : $this->IM->getModule('member')->getMember($lists[$i]->tomidx)->name;
	$lists[$i]->receiver = GetPhoneNumber($lists[$i]->receiver);
	$lists[$i]->sender_name = $lists[$i]->frommidx == 0 ? '비회원' : $this->IM->getModule('member')->getMember($lists[$i]->frommidx)->name;
	$lists[$i]->sender = GetPhoneNumber($lists[$i]->sender);
}

$results->success = true;
$results->lists = $lists;
$results->total = $total;