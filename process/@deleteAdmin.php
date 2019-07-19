<?php
/**
 * 이 파일은 iModule SMS모듈의 일부입니다. (https://www.imodules.io)
 *
 * 관리자를 삭제한다.
 *
 * @file /modules/sms/process/@deleteAdmin.php
 * @author Eunseop Lim (eslim@naddle.net)
 * @license MIT License
 * @version 3.0.0
 * @modified 2019. 7. 18.
 */
if (defined('__IM__') == false) exit;

$midx = Param('midx');

$this->db()->delete($this->table->admin)->where('midx',$midx)->execute();
$results->success = true;
?>