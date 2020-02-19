<?php
/**
 * 이 파일은 iModule SMS모듈의 일부입니다. (https://www.imodules.io)
 *
 * SMS모듈 관리자패널을 구성한다.
 * 
 * @file /modules/sms/admin/index.php
 * @author Arzz (arzz@arzz.com)
 * @license GPLv3
 * @version 3.1.0
 * @modified 2020. 2. 19.
 */
if (defined('__IM__') == false) exit;
?>
<script>
Ext.onReady(function () { Ext.getCmp("iModuleAdminPanel").add(
	new Ext.TabPanel({
		id:"ModuleSms",
		border:false,
		tabPosition:"bottom",
		items:[
			new Ext.grid.Panel({
				id:"ModuleSmsSendList",
				iconCls:"fa fa-bars",
				title:Sms.getText("admin/list/title"),
				border:false,
				tbar:[
					new Ext.Button({
						text:Sms.getText("admin/list/all_period"),
						iconCls:"fa fa fa-check-square-o",
						pressed:true,
						enableToggle:true,
						handler:function(button) {
							if (button.pressed === true) {
								button.setIconCls("fa fa fa-check-square-o");
								Ext.getCmp("ModuleSmsSendListStartDate").disable();
								Ext.getCmp("ModuleSmsSendListEndDate").disable();
								Ext.getCmp("ModuleSmsSendList").getStore().getProxy().setExtraParam("start_date","");
								Ext.getCmp("ModuleSmsSendList").getStore().getProxy().setExtraParam("end_date","");
								Ext.getCmp("ModuleSmsSendList").getStore().loadPage(1);
							} else {
								button.setIconCls("fa fa fa-square-o");
								Ext.getCmp("ModuleSmsSendListStartDate").enable();
								Ext.getCmp("ModuleSmsSendListEndDate").enable();
								Ext.getCmp("ModuleSmsSendList").getStore().getProxy().setExtraParam("start_date",moment(Ext.getCmp("ModuleSmsSendListStartDate").getValue()).format("YYYY-MM-DD"));
								Ext.getCmp("ModuleSmsSendList").getStore().getProxy().setExtraParam("end_date",moment(Ext.getCmp("ModuleSmsSendListEndDate").getValue()).format("YYYY-MM-DD"));
								Ext.getCmp("ModuleSmsSendList").getStore().loadPage(1);
							}
						}
					}),
					new Ext.form.DateField({
						id:"ModuleSmsSendListStartDate",
						width:120,
						value:moment().format("YYYY-MM-01"),
						format:"Y-m-d",
						disabled:true,
						listeners:{
							change:function(form,value) {
								Ext.getCmp("ModuleSmsSendList").getStore().getProxy().setExtraParam("start_date",moment(value).format("YYYY-MM-DD"));
								Ext.getCmp("ModuleSmsSendList").getStore().loadPage(1);
							}
						}
					}),
					new Ext.form.DisplayField({
						value:"~"
					}),
					new Ext.form.DateField({
						id:"ModuleSmsSendListEndDate",
						width:120,
						value:moment().add(1,"month").date(0).format("YYYY-MM-DD"),
						format:"Y-m-d",
						disabled:true,
						listeners:{
							change:function(form,value) {
								Ext.getCmp("ModuleSmsSendList").getStore().getProxy().setExtraParam("end_date",moment(value).format("YYYY-MM-DD"));
								Ext.getCmp("ModuleSmsSendList").getStore().loadPage(1);
							}
						}
					}),
					"-",
					new Ext.form.ComboBox({
						id:"ModuleSmsSendListKeycode",
						store:new Ext.data.ArrayStore({
							fields:["display","value"],
							data:(function() {
								var datas = [];
								for (var field in Sms.getText("admin/list/keycodes")) {
									datas.push([Sms.getText("admin/list/keycodes/"+field),field]);
								}
								return datas;
							})()
						}),
						width:100,
						editable:false,
						displayField:"display",
						valueField:"value",
						value:"message"
					}),
					Admin.searchField("ModuleSmsSendListKeyword",180,Sms.getText("admin/list/keyword"),function(keyword) {
						Ext.getCmp("ModuleSmsSendList").getStore().getProxy().setExtraParam("keycode",Ext.getCmp("ModuleSmsSendListKeycode").getValue());
						Ext.getCmp("ModuleSmsSendList").getStore().getProxy().setExtraParam("keyword",Ext.getCmp("ModuleSmsSendListKeyword").getValue());
						Ext.getCmp("ModuleSmsSendList").getStore().loadPage(1);
					}),
					"-",
					new Ext.Button({
						text:Sms.getText("admin/list/write"),
						iconCls:"mi mi-plus",
						handler:function() {
							Sms.write();
						}
					}),
					new Ext.Button({
						text:Sms.getText("admin/list/delete"),
						iconCls:"mi mi-trash",
						handler:function() {
							Sms.list.delete();
						}
					}),
					"->",
					new Ext.button.Segmented({
						allowMultiple:false,
						items:[
							new Ext.Button({
								text:"전체",
								is_push:"",
								pressed:true,
								iconCls:"fa fa-check-square-o"
							}),
							new Ext.Button({
								text:"알림발송",
								is_push:"TRUE",
								iconCls:"fa fa-square-o"
							}),
							new Ext.Button({
								text:"직접발송",
								is_push:"FALSE",
								iconCls:"fa fa-square-o"
							})
						],
						listeners:{
							toggle:function(segmented,button,pressed) {
								for (var i=0, loop=segmented.items.items.length;i<loop;i++) {
									segmented.items.items[i].setIconCls("fa fa-square-o");
								}
								
								Ext.getCmp("ModuleSmsSendList").getStore().getProxy().setExtraParam("is_push",button.is_push);
								Ext.getCmp("ModuleSmsSendList").getStore().loadPage(1);
								button.setIconCls("fa fa-check-square-o");
							}
						}
					})
				],
				store:new Ext.data.JsonStore({
					proxy:{
						type:"ajax",
						simpleSortMode:true,
						url:ENV.getProcessUrl("sms","@getSends"),
						reader:{type:"json"}
					},
					remoteSort:true,
					sorters:[{property:"reg_date",direction:"DESC"}],
					autoLoad:true,
					pageSize:50,
					fields:["receiver","receiver_name","sender","sender_name","message","reg_date","status"],
					listeners:{
						load:function(store,records,success,e) {
							if (success == false) {
								if (e.getError()) {
									Ext.Msg.show({title:Admin.getText("alert/error"),msg:e.getError(),buttons:Ext.Msg.OK,icon:Ext.Msg.ERROR});
								} else {
									Ext.Msg.show({title:Admin.getText("alert/error"),msg:Admin.getText("error/load"),buttons:Ext.Msg.OK,icon:Ext.Msg.ERROR});
								}
							}
						}
					}
				}),
				columns:[{
					text:Sms.getText("admin/list/columns/receiver_name"),
					width:120,
					sortable:true,
					dataIndex:"receiver_name"
				},{
					text:Sms.getText("admin/list/columns/receiver"),
					width:120,
					sortable:true,
					dataIndex:"receiver"
				},{
					text:Sms.getText("admin/list/columns/sender_name"),
					width:120,
					sortable:true,
					dataIndex:"sender_name"
				},{
					text:Sms.getText("admin/list/columns/sender"),
					width:120,
					sortable:true,
					dataIndex:"sender"
				},{
					text:Sms.getText("admin/list/columns/message"),
					minWidth:200,
					flex:1,
					sortable:true,
					dataIndex:"message"
				},{
					text:Sms.getText("admin/list/columns/reg_date"),
					width:130,
					align:"center",
					dataIndex:"reg_date",
					sortable:true,
					renderer:function(value) {
						return moment(value * 1000).format("YYYY-MM-DD HH:mm");
					}
				},{
					text:Sms.getText("admin/list/columns/status"),
					width:80,
					align:"center",
					dataIndex:"status",
					sortable:true,
					renderer:function(value,p) {
						if (value == "FAIL") p.style = "color:red;";
						else p.style = "color:blue;";
						
						return Sms.getText("status/"+value);
					}
				}],
				selModel:new Ext.selection.CheckboxModel(),
				bbar:new Ext.PagingToolbar({
					store:null,
					displayInfo:false,
					items:[
						"->",
						{xtype:"tbtext",text:Admin.getText("text/grid_help")}
					],
					listeners:{
						beforerender:function(tool) {
							tool.bindStore(Ext.getCmp("ModuleSmsSendList").getStore());
						}
					}
				}),
				listeners:{
					itemdblclick:function(grid,record) {
						Sms.list.view(record.data);
					},
					itemcontextmenu:function(grid,record,item,index,e) {
						var menu = new Ext.menu.Menu();
						
						menu.addTitle(record.data.receiver_name+"("+record.data.receiver+")");
						
						menu.add({
							iconCls:"xi xi-form",
							text:"전송기록보기",
							handler:function() {
								Sms.list.view(record.data);
							}
						});
						
						menu.add({
							iconCls:"mi mi-trash",
							text:"전송기록삭제",
							handler:function() {
								Sms.list.delete();
							}
						});
						
						e.stopEvent();
						menu.showAt(e.getXY());
					}
				}
			}),
			<?php if ($this->IM->getModule('member')->isAdmin() == true) { ?>
			new Ext.grid.Panel({
				id:"ModuleSmsAdminList",
				iconCls:"xi xi-crown",
				title:"관리자 관리",
				border:false,
				tbar:[
					new Ext.Button({
						id:"ModuleSmsAdminListAddButton",
						text:"관리자 추가",
						iconCls:"mi mi-plus",
						handler:function() {
							Member.search(function(member) {
								Ext.Msg.show({title:Admin.getText("alert/info"),msg:member.name+"님을 관리자로 추가하시겠습니까?",buttons:Ext.Msg.OKCANCEL,icon:Ext.Msg.QUESTION,fn:function(button) {
									if (button == "ok") {
										Ext.Msg.wait(Admin.getText("action/working"),Admin.getText("action/wait"));
										$.send(ENV.getProcessUrl("sms","@saveAdmin"),{midx:member.idx},function(result) {
											if (result.success == true) {
												Ext.Msg.show({title:Admin.getText("alert/info"),msg:Admin.getText("action/worked"),buttons:Ext.Msg.OK,icon:Ext.Msg.INFO,fn:function() {
													Ext.getCmp("ModuleSmsAdminList").getStore().reload();
												}});
											} else {
												Ext.Msg.show({title:Admin.getText("alert/error"),msg:result.message,buttons:Ext.Msg.OK,icon:Ext.Msg.ERROR});
											}
											return false;
										});
									}
								}});
							});
						}
					})
				],
				store:new Ext.data.JsonStore({
					proxy:{
						type:"ajax",
						simpleSortMode:true,
						url:ENV.getProcessUrl("sms","@getAdmins"),
						extraParams:{},
						reader:{type:"json"}
					},
					remoteSort:false,
					sorters:[{property:"name",direction:"ASC"}],
					autoLoad:true,
					pageSize:0,
					fields:[],
					listeners:{
						load:function(store,records,success,e) {
							if (success == false) {
								if (e.getError()) {
									Ext.Msg.show({title:Admin.getText("alert/error"),msg:e.getError(),buttons:Ext.Msg.OK,icon:Ext.Msg.ERROR});
								} else {
									Ext.Msg.show({title:Admin.getText("alert/error"),msg:Admin.getErrorText("LOAD_DATA_FAILED"),buttons:Ext.Msg.OK,icon:Ext.Msg.ERROR});
								}
							}
						}
					}
				}),
				columns:[{
					text:"이름",
					dataIndex:"name",
					sortable:true,
					width:100
				},{
					text:"이메일",
					dataIndex:"email",
					sortable:true,
					width:140,
					flex:1
				}],
				selModel:new Ext.selection.CheckboxModel(),
				bbar:[
					new Ext.Button({
						iconCls:"x-tbar-loading",
						handler:function() {
							Ext.getCmp("ModuleSmsAdminList").getStore().reload();
						}
					}),
					"->",
					{xtype:"tbtext",text:Admin.getText("text/grid_help")}
				],
				listeners:{
					itemdblclick:function(grid,record) {
						Sms.admin.add(record.data.midx);
					},
					itemcontextmenu:function(grid,record,item,index,e) {
						var menu = new Ext.menu.Menu();
						
						menu.addTitle(record.data.name);
						
						menu.add({
							iconCls:"xi xi-trash",
							text:"관리자 삭제",
							handler:function() {
								Ext.Msg.show({title:Admin.getText("alert/info"),msg:"관리자를 삭제하시겠습니까?<br>해당 관리자는 더이상 관리할 수 없습니다.",buttons:Ext.Msg.OKCANCEL,icon:Ext.Msg.QUESTION,fn:function(button) {
									if (button == "ok") {
										Ext.Msg.wait(Admin.getText("action/working"),Admin.getText("action/wait"));
										$.send(ENV.getProcessUrl("sms","@deleteAdmin"),{midx:record.data.midx},function(result) {
											if (result.success == true) {
												Ext.Msg.show({title:Admin.getText("alert/info"),msg:Admin.getText("action/worked"),buttons:Ext.Msg.OK,icon:Ext.Msg.INFO,fn:function() {
													Ext.getCmp("ModuleSmsAdminList").getStore().reload();
												}});
											} else {
												Ext.Msg.show({title:Admin.getText("alert/error"),msg:result.message,buttons:Ext.Msg.OK,icon:Ext.Msg.ERROR});
											}
											return false;
										});
									}
								}});
							}
						});
						
						e.stopEvent();
						menu.showAt(e.getXY());
					}
				}
			}),
			<?php } ?>
			null
		]
	})
); });
</script>