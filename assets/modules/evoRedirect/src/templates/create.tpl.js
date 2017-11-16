var form = {
	id:"form",
	view:"form", 
	container:"form",
	borderless:true,
	elements:[
		{
			rows: [
				{
					cols:[
						{ view:"text", label:"URL:",name:"url", width:460, labelPosition:"top"},
						{ view:"button", value: "", css:"update_url select", width: 40, height: 20, click: function() {setFieldAction()}}
					]
				},
				{
					cols:[
						{ view:"text", label: moduleConfig.lang.short_link,name:"short_url", width:460, labelPosition:"top", value:moduleConfig.shorturl},
						{ view:"button", value: "", css:"update_url", width: 40, height: 20, click: function() {updateShortUrl()}},
					]
				},
				{ 
					view:"select", 
					label:"Code:",
					name:"code", 
					width:500, 
					labelPosition:"top", 
					value:1,
					options:[
						{ "id":1, "value": moduleConfig.lang.redirect_type_1 },
						{ "id":2, "value": moduleConfig.lang.redirect_type_2 }
					]
				}
			]
		},
		{
			cols: [
					{  
						"view":"checkbox",
						"labelWidth":150,
						"name":"save_get",
						"value":"0",
						"margin":null,
						"css" : "line_checkbox",
						"label": moduleConfig.lang.save_get
					},
					{  
						"view":"checkbox",
						"labelWidth":150,
						"name":"active",
						"value":"0",
						"margin":null,
						"css" : "line_checkbox",
						"label": moduleConfig.lang.active
					},
					{  
						"view":"checkbox",
						"labelWidth":150,
						"name":"search_get",
						"value":"0",
						"margin":null,
						"css" : "line_checkbox",
						"label": moduleConfig.lang.search_get
					}
				]
		}
	]

};