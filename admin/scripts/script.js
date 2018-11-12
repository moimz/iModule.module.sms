/**
 * 이 파일은 iModule SMS모듈의 일부입니다. (https://www.imodules.io)
 *
 * SMS 관리자의 UI/UX 기능을 제어한다.
 * 
 * @file /modules/sms/admin/scripts/script.js
 * @author Arzz (arzz@arzz.com)
 * @license GPLv3
 * @version 3.0.0
 * @modified 2018. 11. 13.
 */
var Sms = {
	add:function() {
		if (!Ext.getCmp("ModuleSmsWriteWindow")) return;
		
		new Ext.Window({
			id:"ModuleSmsAddWindow",
			title:"대상추가",
			width:400,
			modal:true,
			resizable:false,
			autoScroll:true,
			border:false,
			layout:"fit",
			items:[
				new Ext.form.FormPanel({
					id:"ModuleSmsAddForm",
					bodyPadding:"10 10 5 10",
					fieldDefaults:{labelAlign:"right",labelWidth:80,anchor:"100%",allowBlank:false},
					items:[
						new Ext.form.TextArea({
							fieldLabel:"수신번호",
							name:"cellphone",
							emptyText:"한줄에 수신번호를 한개씩 입력하세요.",
							enableKeyEvents:true,
							listeners:{
								blur:function(form,value) {
									var lines = form.getValue().split("\n");
									for (var i=0, loop=lines.length;i<loop;i++) {
										var line = lines[i].replace(/-/g,'');
										
										if (line.indexOf("02") === 0) {
											if (line.length == 9) {
												var number = line.substr(0,2) + "-" + line.substr(2,3) + "-" + line.substr(5,4);
											} else if (line.length == 10) {
												var number = line.substr(0,2) + "-" + line.substr(2,4) + "-" + line.substr(6,4);
											} else {
												var number = lines[i];
											}
										} else if (line.indexOf("0") === 0) {
											if (line.length == 10) {
												var number = line.substr(0,3) + "-" + line.substr(3,3) + "-" + line.substr(6,4);
											} else if (line.length == 11) {
												var number = line.substr(0,3) + "-" + line.substr(3,4) + "-" + line.substr(7,4);
											} else {
												var number = lines[i];
											}
										} else {
											var number = lines[i];
										}
										
										lines[i] = number;
									}
									form.setValue(lines.join("\n"));
								}
							},
							validator:function(value) {
								var lines = value.split("\n");
								for (var i=0, loop=lines.length;i<loop;i++) {
									if (lines[i].length > 0 && lines[i].search(/^[0-9]{2,3}-[0-9]{3,4}-[0-9]{4}$/) == -1) return "휴대전화번호 형식이 올바르지 않습니다.("+lines[i]+")";
									return true;
								}
							}
						})
					]
				})
			],
			buttons:[
				new Ext.Button({
					text:"추가",
					handler:function() {
						if (Ext.getCmp("ModuleSmsAddForm").getForm().isValid() == true) {
							var lines = Ext.getCmp("ModuleSmsAddForm").getForm().findField("cellphone").getValue().split("\n");
							for (var i=0, loop=lines.length;i<loop;i++) {
								Ext.getCmp("ModuleSmsWriteReceiverList").getStore().add({midx:0,name:"직접입력",cellphone:lines[i]});
							}
							Ext.getCmp("ModuleSmsAddWindow").close();
						}
					}
				}),
				new Ext.Button({
					text:"취소",
					handler:function() {
						Ext.getCmp("ModuleSmsAddWindow").close();
					}
				})
			]
		}).show();
	},
	search:function() {
		
	},
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
									text:"대상추가",
									iconCls:"mi mi-plus",
									menu:[{
										iconCls:"mi mi-group",
										text:"회원검색",
										handler:function() {
											Sms.search();
										}
									},{
										iconCls:"xi xi-form",
										text:"직접추가",
										handler:function() {
											Sms.add();
										}
									}]
								}),
								new Ext.Button({
									text:"선택대상 제외",
									iconCls:"mi mi-trash",
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
									value:sender ? sender : null,
									enableKeyEvents:true,
									listeners:{
										blur:function(form,value) {
											var lines = form.getValue().split("\n");
											for (var i=0, loop=lines.length;i<loop;i++) {
												var line = lines[i].replace(/-/g,'');
												
												if (line.indexOf("02") === 0) {
													if (line.length == 9) {
														var number = line.substr(0,2) + "-" + line.substr(2,3) + "-" + line.substr(5,4);
													} else if (line.length == 10) {
														var number = line.substr(0,2) + "-" + line.substr(2,4) + "-" + line.substr(6,4);
													} else {
														var number = lines[i];
													}
												} else if (line.indexOf("0") === 0) {
													if (line.length == 10) {
														var number = line.substr(0,3) + "-" + line.substr(3,3) + "-" + line.substr(6,4);
													} else if (line.length == 11) {
														var number = line.substr(0,3) + "-" + line.substr(3,4) + "-" + line.substr(7,4);
													} else {
														var number = lines[i];
													}
												} else {
													var number = lines[i];
												}
												
												lines[i] = number;
											}
											
											form.setValue(lines.join("\n"));
										}
									},
									validator:function(value) {
										var lines = value.split("\n");
										for (var i=0, loop=lines.length;i<loop;i++) {
											if (lines[i].length > 0 && lines[i].search(/^[0-9]{2,3}-[0-9]{3,4}-[0-9]{4}$/) == -1) return "휴대전화번호 형식이 올바르지 않습니다.("+lines[i]+")";
											return true;
										}
									}
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
											form.getLength(form);
										},
										change:function(form) {
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
									if (Ext.getCmp("ModuleSmsSendList")) Ext.getCmp("ModuleSmsSendList").getStore().loadPage(1);
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
	},
	list:{
		view:function(data) {
			new Ext.Window({
				id:"ModuleSmsViewWindow",
				title:"전송기록보기",
				width:600,
				modal:true,
				resizable:false,
				autoScroll:true,
				border:false,
				layout:"fit",
				items:[
					new Ext.form.FormPanel({
						id:"ModuleSmsViewForm",
						bodyPadding:"10 10 5 10",
						fieldDefaults:{labelAlign:"right",labelWidth:100,anchor:"100%",allowBlank:false},
						items:[
							new Ext.form.Hidden({
								name:"tomidx",
								value:data.tomidx
							}),
							new Ext.form.Hidden({
								name:"receivers"
							}),
							new Ext.form.FieldContainer({
								layout:"hbox",
								items:[
									new Ext.form.TextField({
										fieldLabel:"보내는사람",
										value:data.sender_name,
										flex:1,
										readOnly:true
									}),
									new Ext.form.TextField({
										fieldLabel:"보내는번호",
										name:"sender",
										value:data.sender,
										flex:1
									})
								]
							}),
							new Ext.form.FieldContainer({
								layout:"hbox",
								items:[
									new Ext.form.TextField({
										fieldLabel:"받는사람",
										name:"name",
										value:data.receiver_name,
										flex:1,
										readOnly:true
									}),
									new Ext.form.TextField({
										fieldLabel:"받는번호",
										name:"cellphone",
										emptyText:"000-0000-0000",
										value:data.receiver,
										flex:1,
										validator:function(value) {
											if (value.length > 0 && value.search(/^[0-9]{3}-[0-9]{3,4}-[0-9]{4}$/) == -1) return "휴대전화번호 형식이 올바르지 않습니다.";
											return true;
										}
									})
								]
							}),
							new Ext.form.TextArea({
								fieldLabel:"내용",
								name:"message",
								value:data.message,
								margin:"0 0 0 0",
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
										form.getLength(form);
									},
									change:function(form) {
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
				],
				buttons:[
					new Ext.Button({
						text:"재전송",
						handler:function() {
							var form = Ext.getCmp("ModuleSmsViewForm").getForm();
							var receivers = [];
							receivers.push({midx:form.findField("tomidx").getValue(),name:form.findField("name").getValue(),cellphone:form.findField("cellphone").getValue()});
							
							form.findField("receivers").setValue(JSON.stringify(receivers));
						
							Ext.getCmp("ModuleSmsViewForm").getForm().submit({
								url:ENV.getProcessUrl("sms","@send"),
								submitEmptyText:false,
								waitTitle:Admin.getText("action/wait"),
								waitMsg:Admin.getText("action/saving"),
								success:function(form,action) {
									Ext.Msg.show({title:Admin.getText("alert/info"),msg:"성공적으로 전송하였습니다.",buttons:Ext.Msg.OK,icon:Ext.Msg.INFO,fn:function() {
										if (Ext.getCmp("ModuleSmsSendList")) Ext.getCmp("ModuleSmsSendList").getStore().loadPage(1);
										Ext.getCmp("ModuleSmsViewWindow").close();
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
						text:"닫기",
						handler:function() {
							Ext.getCmp("ModuleSmsViewWindow").close();
						}
					})
				],
				listeners:{
					show:function() {
						Ext.getCmp("ModuleSmsViewForm").getForm().findField("message").getLength(Ext.getCmp("ModuleSmsViewForm").getForm().findField("message"));
					}
				}
			}).show();
		},
		delete:function() {
			var selected = Ext.getCmp("ModuleSmsSendList").getSelectionModel().getSelection();
			if (selected.length == 0) {
				Ext.Msg.show({title:Admin.getText("alert/error"),msg:"삭제할 전송기록을 선택하여 주십시오.",buttons:Ext.Msg.OK,icon:Ext.Msg.ERROR});
				return;
			}
			
			var idxes = [];
			for (var i=0, loop=selected.length;i<loop;i++) {
				idxes.push(selected[i].get("idx"));
			}
			
			Ext.Msg.show({title:Admin.getText("alert/info"),msg:"선택하신 전송기록을 정말 삭제하시겠습니까?",buttons:Ext.Msg.OKCANCEL,icon:Ext.Msg.QUESTION,fn:function(button) {
				if (button == "ok") {
					Ext.Msg.wait(Admin.getText("action/working"),Admin.getText("action/wait"));
					$.send(ENV.getProcessUrl("sms","@deleteSend"),{idx:idxes.join(",")},function(result) {
						if (result.success == true) {
							Ext.Msg.show({title:Admin.getText("alert/info"),msg:Admin.getText("action/worked"),buttons:Ext.Msg.OK,icon:Ext.Msg.INFO,fn:function() {
								Ext.getCmp("ModuleSmsSendList").getStore().reload();
							}});
						}
					});
				}
			}});
		}
	}
};