<?php
/**
 * 이 파일은 iModule 기반으로 하는 SMS 모듈 입니다. (https://www.imodule.kr)
 *
 * 관리자를 삭제한다.
 *
 * @file /modules/sms/process/@savePollForm.php
 * @author Eunseop Lim (eslim@naddle.net)
 * @license MIT License
 * @version 3.0.0
 * @modified 2019. 7. 18.
 */
if (defined('__IM__') == false) exit;

$midx = Request('midx');

$this->db()->delete($this->table->admin)->where('midx',$midx)->execute();
$results->success = true;
?>