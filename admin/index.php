<?php
/**
 * 이 파일은 iModule SMS모듈의 일부입니다. (https://www.imodules.io)
 *
 * SMS모듈 관리자패널을 구성한다.
 * 
 * @file /modules/sms/admin/index.php
 * @author Arzz (arzz@arzz.com)
 * @license GPLv3
 * @version 3.0.0
 * @modified 2019. 2. 6.
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
				title:Sms.getText("admin/list/title"),
				border:false,
				tbar:[
					new Ext.Button({
						text:"전체기간",
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
						visibility:"calendar,application",
						store:new Ext.data.ArrayStore({
							fields:["display","value"],
							data:[["발송자","sender"],["발송번호","sender_number"],["수신자","receiver"],["수신번호","receiver_number"],["내용","message"]]
						}),
						width:100,
						editable:false,
						displayField:"display",
						valueField:"value",
						value:"message"
					}),
					new Ext.form.TextField({
						id:"ModuleSmsSendListKeyword",
						emptyText:"검색어",
						width:140
					}),
					new Ext.Button({
						iconCls:"mi mi-search",
						handler:function() {
							Ext.getCmp("ModuleSmsSendList").getStore().getProxy().setExtraParam("keycode",Ext.getCmp("ModuleSmsSendListKeycode").getValue());
							Ext.getCmp("ModuleSmsSendList").getStore().getProxy().setExtraParam("keyword",Ext.getCmp("ModuleSmsSendListKeyword").getValue());
							Ext.getCmp("ModuleSmsSendList").getStore().loadPage(1);
						}
					}),
					"-",
					new Ext.Button({
						text:Sms.getText("admin/list/addBoard"),
						iconCls:"fa fa-plus",
						handler:function() {
							Sms.write();
						}
					}),
					new Ext.Button({
						text:"선택기록삭제",
						iconCls:"mi mi-trash",
						handler:function() {
							Sms.list.delete();
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
					fields:["bid","title","nickname","exp","point","reg_date","last_login","display_url","count","image"],
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
						
						menu.add('<div class="x-menu-title">'+record.data.receiver_name+'('+record.data.receiver+')</div>');
						
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
			})
		]
	})
); });
</script>