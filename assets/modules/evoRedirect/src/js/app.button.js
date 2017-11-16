(function() {
	if(!document.querySelectorAll('[name="alias"]').length) {
		console.error("evoRedirect can't found name=alias");
		return 0 ;
	}
	
	var config = window.configMR;
	var aliasTitleElement = document.querySelectorAll('[name="alias"]')[0].parentElement.previousElementSibling;
	jQuery(aliasTitleElement).addClass('aliaswrapper');

	new Clipboard(".submit_"+config.moduleName+"_code", {
	    text: function(e) {
	    	var url = document.querySelectorAll(".input_"+config.moduleName+"_code")[0].value;
	        return window.origin+"/"+url;
	    }
	});

	function BoxButton() {

		var modalIsShow = false;

		var openBtn = document.createElement('a');
		openBtn.className = "open_"+config.moduleName+"_window";
		openBtn.setAttribute("title", "Создать короткую ссылку");

		openBtn.addEventListener("click", function(e){

			e.preventDefault();
			
			if(modalIsShow) return false;

			modalIsShow = true;
			var modal = new ModalWindow();
			e.target.parentElement.prepend(modal);

			

		})

		jQuery(function($){
			$(document).mouseup(function (e){
				var div = $(".modal_evoRedirect_wrapper"); 
				if (!div.is(e.target)
				    && div.has(e.target).length === 0) { 
					modalIsShow = false;
					div.remove(); 
				}
			});
		});

		function ModalWindow() {

			var modalWrapper = document.createElement('div');
			modalWrapper.className = "modal_"+config.moduleName+"_wrapper";

			var modalContent = document.createElement('div');
			modalContent.className = "modal_"+config.moduleName+"_content";

			var inputCode = document.createElement('input');
			inputCode.setAttribute("type", "text");
			inputCode.setAttribute("name", "codeLink_"+config.moduleName);
			inputCode.className = "input_"+config.moduleName+"_code";

			var submitButton = document.createElement('button');
			submitButton.className = "submit_"+config.moduleName+"_code";

			submitButton.addEventListener("click", function(e){
				e.preventDefault();

				if(inputCode.value.length <= 0){
					alert("Короткая ссылка не может быть пустой!");
					return false;	
				}

				createShort(modalWrapper, inputCode);
			})

			modalContent.append(inputCode);
			modalContent.append(submitButton);

			updateRandom(inputCode);

			modalWrapper.append(modalContent);	

			return modalWrapper;
		}

		function createShort(modal, input) {
		
			jQuery.ajax({
			  url: config.moduleurl+"action=makeRedirect",
			  context: document.body,
			  method: "POST",
			  data: { short_url:input.value, url: config.document.id, active: 1}
			}).done(function(req) {
			  modal.remove();
			  modalIsShow = false;
			  alert("Коротка ссылка "+input.value+" успешно создана и скопирована в буфер обмена");

			});
		}

		function updateRandom(element) {

			jQuery.ajax({
			  url: config.moduleurl+"action=updateRandom",
			  context: document.body,
			  method: "POST",
			  data: {}
			}).done(function(req) {
			  element.value = req.short_url;
			});

		}

		return openBtn;
	}



	function init() {
		var btn = new BoxButton;
		aliasTitleElement.prepend(btn);
	}

	init();

})()



