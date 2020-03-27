<?php
/**
 * 이 파일은 iModule SMS모듈의 일부입니다. (https://www.imodules.io)
 *
 * 모듈 환경설정 패널을 가져온다.
 * 
 * @file /modules/sms/admin/configs.php
 * @author Arzz (arzz@arzz.com)
 * @license GPLv3
 * @version 3.1.0
 * @modified 2020. 3. 27.
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
			title:Sms.getText("admin/configs/form/default_setting"),
			items:[
				Admin.templetField(Sms.getText("admin/configs/form/templet"),"templet","module","sms",false),
				new Ext.form.TextField({
					fieldLabel:Sms.getText("admin/configs/form/default_sender"),
					name:"sender",
					emptyValue:"000-000-0000"
				}),
				new Ext.form.Checkbox({
					fieldLabel:Sms.getText("admin/configs/form/use_lms"),
					name:"use_lms",
					boxLabel:Sms.getText("admin/configs/form/use_lms_help"),
					uncheckedValue:"",
					checked:true,
					afterBodyEl:'<div class="x-form-help">'+Sms.getText("admin/configs/form/use_lms_help_default")+'</div>'
				}),
				new Ext.form.TextField({
					fieldLabel:Sms.getText("admin/configs/form/prefix"),
					name:"prefix",
					allowBlank:true,
					afterBodyEl:'<div class="x-form-help">'+Sms.getText("admin/configs/form/prefix_help")+'</div>'
				}),
				new Ext.form.TextField({
					fieldLabel:Sms.getText("admin/configs/form/postfix"),
					name:"postfix",
					allowBlank:true,
					afterBodyEl:'<div class="x-form-help">'+Sms.getText("admin/configs/form/postfix_help")+'</div>'
				})
			]
		})
	]
});
</script>