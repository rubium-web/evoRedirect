var importModal = {
	id:"import",
	view:"form", 
	container:"import",
	borderless:true,
	elements:[
        { 
        	view:"template", 
        	template:"<a href='/assets/modules/evoRedirect/src/more/import.csv' class='import-file-show'>"+moduleConfig.lang.exemple_file+"</a>", 
        	autoheight:true
        },
		{ 
			cols:[  
				{
		            view:"uploader",
		            id: "uploader_1",
		            value: moduleConfig.lang.select_file_btn,
		            link:"mylist",
		            name:"files",
		            upload: moduleConfig.moduleurl+"action=importFile",
		            datatype:"json",
		            multiple: false
		        },
				{  
					"view":"button",
					"value": moduleConfig.lang.cansel_btn,
					"type":"form",
					"css":"delete_item",
					"click": function() {
						$$("importModal").hide();
					}
				}
			],
			"margin":5
		}
	]

};