<?php
/**
 * 이 파일은 iModule SMS모듈의 일부입니다. (https://www.imodules.io)
 *
 * 발송기록을 가져온다.
 * 
 * @file /modules/sms/process/@getSends.php
 * @author Arzz (arzz@arzz.com)
 * @license MIT License
 * @version 3.1.0
 * @modified 2018. 11. 13.
 */
if (defined('__IM__') == false) exit;

$start = Request('start');
$limit = Request('limit');
$sort = Request('sort');
$dir = Request('dir');
$start_date = Request('start_date') ? strtotime(Request('start_date')) : 0;
$end_date = Request('end_date') ? strtotime(Request('end_date')) : time();

$mMember = $this->IM->getModule('member');
$keycode = Request('keycode');
$keyword = Request('keyword');

$lists = $this->db()->select($this->table->send.' s','s.*')->join($mMember->getTable('member').' sm','sm.idx=s.frommidx','LEFT')->join($mMember->getTable('member').' rm','rm.idx=s.tomidx','LEFT')->where('s.reg_date',$start_date,'>=')->where('s.reg_date',$end_date,'<');

if ($keyword) {
	if ($keycode == 'sender_number') {
		$keyword = str_replace('-','',$keyword);
		$lists->where('sender','%'.$keyword.'%','LIKE');
	} elseif ($keycode == 'receiver_number') {
		$keyword = str_replace('-','',$keyword);
		$lists->where('sender','%'.$keyword.'%','LIKE');
	} elseif ($keycode == 'message') {
		$lists->where('message','%'.$keyword.'%','LIKE');
	} else {
		$mMember = $this->IM->getModule('member');
		$members = $mMember->db()->select($mMember->getTable('member'),'idx')->where('name','%'.$keyword.'%','LIKE')->get('idx');
		if (count($members) == 0) {
			$lists->where(0);
		} else {
			$lists->where($keycode == 'sender' ? 'frommidx' : 'tomidx',$members,'IN');
		}
	}
}

$total = $lists->copy()->count();

if ($limit > 0) $lists->limit($start,$limit);
if ($sort == 'sender_name') {
	$lists->orderBy('sm.name',$dir);
} elseif ($sort == 'receiver_name') {
	$lists->orderBy('rm.name',$dir);
} else {
	$lists->orderBy('s.'.$sort,$dir);
}
$lists = $lists->get();

for ($i=0, $loop=count($lists);$i<$loop;$i++) {
	$lists[$i]->receiver_name = $lists[$i]->tomidx == 0 ? '비회원' : $this->IM->getModule('member')->getMember($lists[$i]->tomidx)->name;
	$lists[$i]->receiver = GetPhoneNumber($lists[$i]->receiver);
	$lists[$i]->sender_name = $lists[$i]->frommidx == 0 ? '비회원' : $this->IM->getModule('member')->getMember($lists[$i]->frommidx)->name;
	$lists[$i]->sender = GetPhoneNumber($lists[$i]->sender);
}

$results->success = true;
$results->lists = $lists;
$results->total = $total;