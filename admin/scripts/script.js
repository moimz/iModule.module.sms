/**
 * 이 파일은 iModule SMS모듈의 일부입니다. (https://www.imodule.kr)
 *
 * SMS 관리자의 UI/UX 기능을 제어한다.
 * 
 * @file /modules/sms/admin/scripts/script.js
 * @author Arzz (arzz@arzz.com)
 * @license MIT License
 * @version 3.0.0.160910
 */
var Sms = {
	write:function(sender,receivers) {
		new Ext.Window({
			id:"ModuleSmsWriteWindow",
			title:"SMS발송",
			width:500,
			height:420,
			modal:true,
			resizable:false,
			autoScroll:true,
			border:false,
			layout:"fit",
			items:[
				new Ext.Panel({
					layout:{type:"hbox",align:"stretch"},
					border:false,
					items:[
						new Ext.grid.Panel({
							id:"ModuleSmsWriteReceiverList",
							border:true,
							layout:"fit",
							width:300,
							margin:"-1 0 -1 -1",
							tbar:[
								new Ext.Button({
									text:"추가",
									iconCls:"mi mi-plus",
									handler:function() {
										
									}
								}),
								new Ext.Button({
									text:"선택대상 제외",
									iconCls:"mi mi-plus",
									handler:function() {
										var selected = Ext.getCmp("ModuleSmsWriteReceiverList").getSelectionModel().getSelection();
										if (selected.length == 0) {
											Ext.Msg.show({title:Admin.getText("alert/error"),msg:"삭제할 대상을 선택하여 주십시오.",buttons:Ext.Msg.OK,icon:Ext.Msg.ERROR});
											return;
										}
										Ext.getCmp("ModuleSmsWriteReceiverList").getStore().remove(selected);
									}
								})
							],
							store:new Ext.data.ArrayStore({
								fields:["midx","name","cellphone"],
								data:[],
								sorters:[{property:"name",direction:"ASC"}]
							}),
							columns:[{
								text:"이름",
								minWidth:150,
								flex:1,
								dataIndex:"name"
							},{
								text:"휴대전화번호",
								width:150,
								dataIndex:"cellphone"
							}],
							selModel:new Ext.selection.CheckboxModel()
						}),
						new Ext.form.Panel({
							id:"ModuleSmsWriteForm",
							border:false,
							flex:1,
							bodyPadding:"10 10 10 10",
							fieldDefaults:{labelAlign:"top",labelWidth:100,anchor:"100%",allowBlank:false},
							items:[
								new Ext.form.Hidden({
									name:"receivers"
								}),
								new Ext.form.TextField({
									fieldLabel:"보내는 번호",
									name:"sender",
									emptyText:"000-0000-0000",
									value:sender ? sender : null
								}),
								new Ext.form.TextArea({
									fieldLabel:"내용",
									name:"message",
									height:200,
									getLength:function(form) {
										var str = form.getValue();
										
										var bytes = 0;
										for (var i=0, loop=str.length; i<loop; i++) {
											if (escape(str.charAt(i)).length > 4) {
												bytes+= 2;
											} else {
												bytes++;
											}
										}
										
										form.getForm().findField("length").setValue(bytes + " byte" + (bytes > 0 ? "s" : ""));
									},
									listeners:{
										blur:function(form) {
											console.log("blur");
											form.getLength(form);
										},
										change:function(form) {
											console.log("change");
											form.getLength(form);
										}
									}
								}),
								new Ext.form.DisplayField({
									name:"length",
									value:"0 byte",
									margin:"0 0 0 0",
									fieldStyle:"color:#666; text-align:right;"
								})
							]
						})
					]
				})
			],
			buttons:[
				new Ext.Button({
					text:"발송",
					handler:function() {
						var form = Ext.getCmp("ModuleSmsWriteForm").getForm();
						
						var receivers = JSON.stringify(Admin.grid(Ext.getCmp("ModuleSmsWriteReceiverList"),["midx","name","cellphone"]));
						form.findField("receivers").setValue(receivers);
						
						Ext.getCmp("ModuleSmsWriteForm").getForm().submit({
							url:ENV.getProcessUrl("sms","@send"),
							submitEmptyText:false,
							waitTitle:Admin.getText("action/wait"),
							waitMsg:Admin.getText("action/saving"),
							success:function(form,action) {
								Ext.Msg.show({title:Admin.getText("alert/info"),msg:"성공적으로 전송하였습니다.",buttons:Ext.Msg.OK,icon:Ext.Msg.INFO,fn:function() {
									Ext.getCmp("ModuleSmsWriteWindow").close();
								}});
							},
							failure:function(form,action) {
								if (action.result) {
									if (action.result.message) {
										Ext.Msg.show({title:Admin.getText("alert/error"),msg:action.result.message,buttons:Ext.Msg.OK,icon:Ext.Msg.ERROR});
									} else {
										Ext.Msg.show({title:Admin.getText("alert/error"),msg:Admin.getErrorText("DATA_SAVE_FAILED"),buttons:Ext.Msg.OK,icon:Ext.Msg.ERROR});
									}
								} else {
									Ext.Msg.show({title:Admin.getText("alert/error"),msg:Admin.getErrorText("INVALID_FORM_DATA"),buttons:Ext.Msg.OK,icon:Ext.Msg.ERROR});
								}
							}
						});
					}
				}),
				new Ext.Button({
					text:"취소",
					handler:function() {
						Ext.getCmp("ModuleSmsWriteWindow").close();
					}
				})
			],
			listeners:{
				show:function() {
					if (receivers !== undefined && typeof receivers == "object" && receivers.length > 0) {
						$.send(ENV.getProcessUrl("sms","@getReceivers"),{receivers:JSON.stringify(receivers)},function(result) {
							if (result.success == true) {
								Ext.getCmp("ModuleSmsWriteReceiverList").getStore().add(result.lists);
							}
						});
					}
				}
			}
		}).show();
	}
};