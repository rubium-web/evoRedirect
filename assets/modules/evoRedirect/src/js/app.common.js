webix.ready(function(){

	var deleteItem = function(rowID, popup) {
		var row = TableData.getItem(rowID);
		webix.confirm({
			title: moduleConfig.lang.delete_title,
			ok: moduleConfig.lang.delete_btn, 
			cancel: moduleConfig.lang.cansel_btn,
			text: moduleConfig.lang.delete_answer+" №"+rowID,
			callback:function(result){ 
				if(result == true){
					webix.ajax().post(moduleConfig.moduleurl+'action=deleteItem',{ element_id:rowID }).then(function(response) {
						var response = response.json();
						if(!response.error){
							TableData.remove(rowID);

							try{
								if(typeof(popup))
									popup.hide();
							}catch(e){}

							webix.alert({
								title: moduleConfig.lang.delete_success_title,
								text: moduleConfig.lang.success_remove_description.replace("##",rowID)
							});

						}else{
							webix.alert({
								title: moduleConfig.lang.delete_error_title,
								text: response.text
							});
						}
					}, function() {
						webix.alert({
							title: moduleConfig.lang.delete_error_title,
							text: moduleConfig.lang.delete_server_error
						});
					});


				}

			}
		})
	};

	var updateItem = function(result) {
		output = result.json();

		if (output.error) {
			$$("add_props_popup").hide();
			webix.message({type:"error",text:output.error});
		}
		
		var fieldsCheckbox = ['save_get', 'search_get','active'];

		for(var f in fieldsCheckbox){

			if(typeof(output[fieldsCheckbox[f]]) != "string" && typeof(output[fieldsCheckbox[f]]) != "nubmer")
				continue;

			var item = TableData.getItemNode({row: output['id'], column: fieldsCheckbox[f]});
			if(output[fieldsCheckbox[f]] != 0){
				jQuery(item).find('.checker').addClass('active');
			}else{
				jQuery(item).find('.checker').removeClass('active');
			}

			var newValue = jQuery(item).find('.checker').get(0).outerHTML;
			var item = TableData.getItem(output['id']);
			item[fieldsCheckbox[f]] = newValue;
			TableData.updateItem(output['id'], item);
		}


		var fieldsText = ['old_url', 'new_url', 'code'];

		for(var f in fieldsText){

			if(typeof(output[fieldsText[f]]) != "string" && typeof(output[fieldsText[f]]) != "nubmer")
				continue;

			var newValue = output[fieldsText[f]];

			if(fieldsText[f] == "new_url"){
				var newValue = "<a href='/"+output[fieldsText[f]]+"' target='_blank'>"+output[fieldsText[f]]+"</a><a class='copy_uri' data-link='/"+output[fieldsText[f]]+"'>";
			}
			
			if(fieldsText[f] == "old_url"){
				var oldUrl = output[fieldsText[f]];
				if(output["old_url_link"])
					oldUrl = output["old_url_link"];

				var newValue = "<a href='"+oldUrl+"' target='_blank'>"+output[fieldsText[f]]+"</a>";
			}
			
			var item = TableData.getItem(output['id']);
			item[fieldsText[f]] = newValue;
			TableData.updateItem(output['id'], item);
		}
	}

	var import_popup = webix.ui({
		view:"popup",
		id:"importModal",
		position:"center",
		body: importModal
	});

	$$("uploader_1").attachEvent("onUploadComplete", function(response){
		if(response.error){
			webix.message({type: "error", text: response.text});
		}else{
			$$("importModal").hide();
			webix.message({text: response.text});
			if(response.data.length)
				for (var line of response.data) {
					$$("table").add(line);
				}
		}
		$("#mainloader").removeClass('show');
	});

	$$("uploader_1").attachEvent("onBeforeFileAdd", function(item){
    	$("#mainloader").addClass('show');
	});

	document.querySelectorAll('#import_btn')[0].addEventListener("click", function(e){
		e.preventDefault();
		$$("importModal").show();
	});

	var popup_props = webix.ui({
		view:"popup",
		id:"add_props_popup",
		position:"center",
		body: popupEdit
	});

	popup_props.getNode().querySelectorAll('.delete_item')[0].addEventListener("click", function(){
		deleteItem(selected_id, popup_props);
	});

	

	popup_props.getNode().querySelectorAll('.save_item')[0].addEventListener("click", function(){
		var props_values = $$("props_form").getValues();
		webix.ajax().post(moduleConfig.moduleurl+"action=setProps", props_values).then(function (result) {
			updateItem(result);
			popup_props.hide();
		}).fail(function (xhr) {
			var response = JSON.parse(xhr.response);webix.message({type: "error", text: response.error.message});
		});
	});


	var showEdit = function(selected_id) {
		selected_id = selected_id[0];
		webix.ajax().post(moduleConfig.moduleurl+'action=getProps&selected_id='+selected_id).then(function (result) {
			var props_form = result.json();

			$$("props_form").setValues(props_form);

			var head = popup_props.getNode().querySelectorAll(".webix_win_head")[0];
			head.innerHTML = moduleConfig.lang.edit_title;
			head.style ="";

			popup_props.show();

		}).fail(function (xhr) {
			console.log('fail');
		});
	}


	function show_form() {
		popup.show();
		var head = popup.getNode().querySelectorAll(".webix_win_head")[0];
		head.innerHTML =	moduleConfig.lang.add_title;
		head.style ="";
	}

	window.updateShortUrl = function() {
		webix.ajax().post(moduleConfig.moduleurl+"action=updateRandom").then(function (result) {
			output = result.json();
			var values = $$('form').getValues();
			values.short_url = output.short_url;
			$$('form').setValues(values);

		})
	}

	var popupAdd = {};

	var formButtons = {
						cols:[
							{ view:"button", value: moduleConfig.lang.create_link_btn, click:function(){
								var values = $$('form').getValues();

								for(var val in values){
									if((values[val] == "") && (val == "url" || val == "short_url" || val == "code")){
										webix.message({type:"error", text: "Поле "+val+" не задано"});
										return false;
									}
								}

								webix.ajax().post(moduleConfig.moduleurl+"action=makeRedirect", values).then(function (result) {
									output = result.json();

									if (output.error) {
										
										webix.message({type:"error", text:output.error});
										return false;
									}
									webix.message({type:"success", text: moduleConfig.lang.generate_success+output.new_url});

									TableData.add(output);

									$$('add_form_popup').hide();
									$$("form").setValues({active:0, code:1, save_get:0, search_get:0, short_url:"", url: ""});
									updateShortUrl();


								}).fail(function (xhr) {
									var response = JSON.parse(xhr.response);
									webix.message({type: 'error', text: response.error.message});
								});
							}},
							{ view:"button", value: moduleConfig.lang.cansel_btn, click:"$$('add_form_popup').hide();", css:"close_btn"}
						]
					};
	form.elements.push(formButtons);

	var popup = webix.ui({
		view:"popup",
		id:"add_form_popup",
		position:"center",
		body:webix.copy(form)
	});


	var selected_id = 0;

	webix.ui({ 
		view:"toolbar", 
		container: 'toolbar',
		elements:[
		{ view:"button", value: moduleConfig.lang.add_btn, width: 150, click:show_form, align:"left"}
		],
		height: 35
	});

	/*
	Вывод таблицы ссылок
	 */
	window.TableData = webix.ui({
		view:"datatable", 
		url:moduleConfig.moduleurl+"action=getData",
		id: "table",
		select:"row",
		container: "table_data",
		multiselect:true,
		pager:{
			size:15,
			group:20,
			container :"pager_table"
		},	  	
		scroll: false,
		autoheight: true,
		columns:[
			{ id:"id", 			header: moduleConfig.lang.id,									width:50},
			{ id:"old_url", 	header: moduleConfig.lang.old_url,								fillspace:3},
			{ id:"new_url",		header: moduleConfig.lang.new_url,								width:100},
			{ id:"code",		header:{text: moduleConfig.lang.http_status ,css:"center"},		width:120, css:"center"},
			{ id:"save_get",	header:{text: moduleConfig.lang.save_get ,css:"center"},		width:120, css:"center"},
			{ id:"search_get",	header:{text: moduleConfig.lang.search_get ,css:"center"},		width:150, css:"center"},
			{ id:"active",		header:{text: moduleConfig.lang.active ,css:"center"}, 			width:110,css:"center"},
			{ id:"delete",		header: moduleConfig.lang.delete_btn ,							width:75}
		],
		onClick:{
			"delete-item":function(ev, id){
				var rowID = id.row;
				deleteItem(rowID);	          	
			},
			"checker":function(e, id, trg) {
				var request = {
					id: id.row
				};

				request[id.column] = ($(trg).find(".checker").hasClass('active')) ? 0 : 1;

				webix.ajax().post(moduleConfig.moduleurl+"action=setProps", request).then(function (result) {
					updateItem(result);
				}).fail(function (xhr) {
					var response = JSON.parse(xhr.response);webix.message({type: "error", text: response.error.message});
				});

			}
		}
	});
	TableData.attachEvent("onAfterLoad", function(){
	   $("#mainloader").removeClass('show');
	})

	/*
	Контекстное меню при правом клике по таблице
	 */
	webix.ui({
		view: "contextmenu",
		css:"contextmenu_style",
		id: "contextmenu",
		data: [{
			value: "Свойства"
		}],
		on:{
			onItemClick:function(){
				showEdit(selected_id);
			}
		}
	});

	$$('contextmenu').attachTo($$('table'));

	$$('table').attachEvent('onBeforeContextMenu', function(id, e, node){
		this.select(id);
		selected_id = this.getSelectedId(true, true)
	});

	$$("table").attachEvent("onItemDblClick", function(id, e, node) {
		this.select(id);
		selected_id = this.getSelectedId(true, true);
		showEdit(selected_id);
	});

	var clipboard = new Clipboard('.copy_uri', {
   		text: function(e) {
   			webix.alert({
				title: moduleConfig.lang.copy_title,
				text: window.origin+$(e).data("link")
			});
			return window.origin+$(e).data("link");
	    }
	});

});

var isSelect = false;
function setFieldAction(){
	 console.log(isSelect);
    if(!isSelect){
        isSelect = true;
        parent.tree.ca = "move";
        jQuery(".update_url.select").addClass('selecter');
    }else{
        parent.tree.ca = "open";
        isSelect = false;
        jQuery(".update_url.select").removeClass('selecter');
    }
}

function setValue(pId) {
	try{
		var vals = $$("form").getValues();
		vals.url = pId;
		$$("form").setValues(vals);
	}catch(e){
		console.log(e)
	}

}

function setMoveValue(pId, pName){
   setValue(pId)
   setTimeout(function() {
     setFieldAction();
   }, 300);
}


