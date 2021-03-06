/**
 * 이 파일은 iModule SMS모듈의 일부입니다. (https://www.imodules.io)
 *
 * 사이트내에서 SMS 발송과 관련된 이벤트를 처리한다.
 * 
 * @file /modules/sms/scripts/script.js
 * @author Arzz (arzz@arzz.com)
 * @license MIT License
 * @version 3.1.0
 * @modified 2019. 11. 7.
 */
var Sms = {
	sendPopup:function(midx) {
		if (typeof midx == "object") {
			var popup = iModule.openPopup("",500,450,1);
			
			if (popup) {
				var document = popup.document;
				document.write('<form method="post" action="' + location.href.replace(location.pathname+location.search,"") + ENV.getModuleUrl("sms","@send",false) + '">');
				
				for (var i=0, loop=midx.length;i<loop;i++) {
					document.write('<input type="hidden" name="midxes[]" value="' + midx[i] + '">');
				}
				
				var form = popup.document.getElementsByTagName("form");
				form[0].submit();
			}
		} else {
			iModule.openPopup(ENV.getModuleUrl("sms","@send",midx ? midx : ""),500,450,1,"send");
		}
	},
	send:{
		init:function(id) {
			var $form = $("#"+id);
			
			if (id == "ModuleSmsSendForm") {
				$("button[data-action]",$form).on("click",function() {
					var action = $(this).attr("data-action");
					if (action == "close") {
						self.close();
					}
				});
				
				$form.inits(Sms.send.submit);
			}
		},
		submit:function($form) {
			$form.send(ENV.getProcessUrl("sms","send"),function(result) {
				if (result.success == true) {
					iModule.modal.alert("안내","성공적으로 발송하였습니다.",function() {
						iModule.modal.close();
						
						if (ENV.IS_CONTAINER_POPUP == true) {
							self.close();
						} else {
							location.replace(location.href);
						}
					},false);
				}
			});
		}
	}
};
