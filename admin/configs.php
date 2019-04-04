<?php
/**
 * 이 파일은 iModule SMS모듈의 일부입니다. (https://www.imodules.io)
 *
 * 모듈 환경설정 패널을 가져온다.
 * 
 * @file /modules/sms/admin/configs.php
 * @author Arzz (arzz@arzz.com)
 * @license GPLv3
 * @version 3.0.0
 * @modified 2019. 4. 4.
 */
if (defined('__IM__') == false) exit;
?>
<script>
new Ext.form.Panel({
	id:"ModuleConfigForm",
	border:false,
	bodyPadding:"10 10 5 10",
	width:500,
	fieldDefaults:{labelAlign:"right",labelWidth:100,anchor:"100%",allowBlank:false},
	items:[
		new Ext.form.FieldSet({
			title:"기본설정",
			items:[
				new Ext.form.TextField({
					fieldLabel:"기본발송번호",
					name:"sender",
					emptyValue:"000-000-0000"
				}),
				new Ext.form.Checkbox({
					fieldLabel:"LMS발송여부",
					name:"lms",
					boxLabel:"문자길이가 긴 경우 LMS으로 발송합니다.",
					uncheckedValue:"",
					afterBodyEl:'<div class="x-form-help">LMS으로 발송을 하지 않을 경우 80자로 끊어서 발송합니다.</div>'
				})
			]
		})
	]
});
</script>