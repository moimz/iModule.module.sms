<?php
/**
 * 이 파일은 iModule SMS모듈의 일부입니다. (https://www.imodules.io)
 *
 * 발송기록을 삭제한다.
 * 
 * @file /modules/sms/process/@deleteSend.php
 * @author Arzz (arzz@arzz.com)
 * @license MIT License
 * @version 3.0.0.160910
 */
if (defined('__IM__') == false) exit;

$idx = Request('idx') ? explode(',',Request('idx')) : array();
if (count($idx) > 0) $this->db()->delete($this->table->send)->where('idx',$idx,'IN')->execute();

$results->success = true;
?>