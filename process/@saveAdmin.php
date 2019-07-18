<?php
/**
 * 이 파일은 iModule 기반으로 하는 SMS 모듈 입니다. (https://www.imodule.kr)
 *
 * 관리자를 저장한다.
 *
 * @file /modules/sms/process/@saveAdmin.php
 * @author Eunseop Lim (esilm@naddle.net)
 * @license MIT License
 * @version 3.0.0
 * @modified 2019. 7. 18.
 */
if (defined('__IM__') == false) exit;

$midx = Request('midx');


$this->db()->replace($this->table->admin,array('midx'=>$midx))->execute();
$results->success = true;
?>