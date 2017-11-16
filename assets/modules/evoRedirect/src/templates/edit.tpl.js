var popupEdit = {
	"view":"form",
	"id":"props_form",
	"width":500,
	"elements":[  
		{  
			"view":"text",
			"labelWidth":150,
			"name":"id",
			"value":"",
			"margin":null,
			"readonly":true,
			"hidden":true,
			"label":"id"
		},
		{  
			"view":"text",
			"labelWidth":150,
			"name":"old_url",
			"value":"",
			"margin":null,
			"label": moduleConfig.lang.old_url
		},
		{  
			"view":"text",
			"labelWidth":150,
			"name":"new_url",
			"value":"",
			"margin":null,
			"label":"URI"
		},
		{  
			"view":"select",
			"labelWidth":150,
			"name":"code",
			"value":"",
			"options":[  
				{
					"id":301,
					"value": moduleConfig.lang.redirect_type_1
				},
				{  
					"id":302,
					"value": moduleConfig.lang.redirect_type_2
				}
			],
			"margin":null,
			"label":"HTTP"
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
		},
		{  
			cols:[  
				{  
					"view":"button",
					"value": moduleConfig.lang.save_btn,
					"type":"form",
					"css":"save_item"
				},
				{  
					"view":"button",
					"value": moduleConfig.lang.delete_btn,
					"type":"form",
					"css":"delete_item"
				}
			],
			"margin":5
		}
	]
};