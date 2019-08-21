<?php
/**
 * 이 파일은 iModule SMS모듈의 일부입니다. (https://www.imodules.io)
 *
 * SMS모듈 기본템플릿 - SMS발송
 * 
 * @file /modules/sms/templets/default/send.php
 * @author Arzz (arzz@arzz.com)
 * @license MIT License
 * @version 3.1.0
 * @modified 2019. 8. 21.
 */
if (defined('__IM__') == false) exit;
?>
<ul data-role="form" class="black inner">
	<li>
		<label>발송번호</label>
		<div>
			<div data-role="input">
				<input type="text" name="sender" value="<?php echo $member->cellphone; ?>">
			</div>
		</div>
	</li>
	<li>
		<label>수신번호</label>
		<div>
			<?php if ($midx == null) { ?>
			<div data-role="input">
				<input type="text" name="receiver" placeholder="000-0000-0000">
			</div>
			<?php } else { ?>
			<div data-role="text">
				<b><?php echo $receiver->nickname; ?></b> (<?php echo $receiver->cellphone; ?>)
			</div>
			<?php } ?>
		</div>
	</li>
	<li>
		<label>내용</label>
		<div>
			<div data-role="input">
				<textarea name="message"></textarea>
			</div>
		</div>
	</li>
</ul>

<div data-role="button">
	<button type="submit">전송하기</button>
	<?php if (defined('__IM_CONTAINER_POPUP__') == true) { ?>
	<button type="button" data-action="close">닫기</button>
	<?php } ?>
</div>