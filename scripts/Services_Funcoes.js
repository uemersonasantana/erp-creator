/*
 * 
 */

function div_conteudo(url, caixa) {
	var i = 'Ops, houve um problema!';
	$.ajax({
        type: "POST",
        url: url,
        cache: true,
        beforeSend: function () {
        	$('#carregando').show();
        },
        success: function (a) {
        	
        	$('#carregando').hide();
        	
        	if ( BrowserDetect.browser == 'Explorer' && BrowserDetect.version == 7 ) {
        		document.getElementById('e_valor').innerHTML = a;
        	} else {
        		$('#'+caixa).append(a);
        	}
        },
        error: function (a) {
            alert('Erro ao carregar página.');
        }
   });
}
/*function pg_modal(pg, tour) {
	$.ajax({
		type: "POST",
		url: pg,
		cache: true,
		beforeSend: function () {
        	$('#e_carregamento_conteudo').html('Carregando...').show();
        },
        success: function (a) {
        	$('#e_carregamento_conteudo').html('').hide();
        	$('#dialog').empty();
            $('#dialog').append(a);
            var id = '#dialog';

			var maskHeight = $(document).height();
			var maskWidth = $(window).width();
		
			$('#mask').css({'width':maskWidth,'height':maskHeight});
		
			$('#mask').fadeIn(1000);	
			$('#mask').fadeTo("slow",0.8);	
		
			//Get the window height and width
			var winH = $(window).height();
			var winW = $(window).width();
		          
			$(id).css('top',  winH/2-$(id).height()/2);
			$(id).css('left', winW/2-$(id).width()/2);
		
			$(id).fadeIn(2000);
			//-- Caso haja tour, então irá se fecha o balão sobre a foto assim que carregar --
			if ( tour == 'sim' ) {
				$('#e_seta_tour').hide();
				$('#e_balao_tour').hide();
			}
        }
	});	
}*/
function pg_modal(pg, largura) {
	$.ajax({
		type: "POST",
		url: pg,
		cache: true,
		beforeSend: function () {
			$('#carregamento').show();
        	//$('#carregamento').modal({
			  //backdrop: 'static',
			  //keyboard: false,
			//});
        },
        success: function (a) {
        	$('#carregamento').hide();
        	//$('#carregamento').modal('hide');
        	
        	if ( largura > 0 ) {
        		$('#myModal').css({'height':'100%', 'width':largura+'px', 'margin-top':'-70px', 'margin-left':'-'+(largura/2)+'px'});
        	}
			$('#myModal').html(a);
    		$('#myModal').modal({
			  backdrop: 'static',
			  keyboard: false,
			});
        }
	});
}
//Envio de formulário
function formularios_dinamicos(a, b, c, d, desabilitar, texto_modal) {
	/* 
		* a = Formulário
		* b = url a ser enviado os valores do formulário
		* c = Objeto de carregamento a ser exibido
		* d = Objeto para receber o resultado do formulario
	*/
	var h = $(a).serialize();

	// Caso haja algum erro que impessa de enviar os dados do formulário, é exibido uma mensagem de erro
	var i = 'Ops... houve um erro!';
	$.ajax({
	    type: "POST",
	    url: b,
	    data: h,
	   	mimeType: "multipart/form-data",
            
	    beforeSend: function () {
	    	$(d).html('<strong>' + a + '</strong>').hide();
	    	if ( c != '' ) {
	    		$(c).show();
	    	} else {
	    		//$('#myModal .modal-header #myModalLabel').html(texto_modal);
	    		//$('#myModal').modal({
				//  backdrop: 'static',
				//  keyboard: false
				//});
	    	}
	    	if ( desabilitar = 's' ) {
				$("input").attr("disabled", "disabled");
				$("button").attr("disabled", "disabled");
			}
	    },
	    success: function (a) {
	    	if ( c != '' ) {
    			$(c).hide();
			} else {
				//$('#myModal .modal-header #myModalLabel').html('');
	    		//$('#myModal').modal('hide');
			}
            if ( d != '' ) {
            	$(d).html('<strong>' + a + '</strong>').show();
            }
            if ( desabilitar = 's' ) {
				$("input").removeAttr("disabled", "disabled");
				$("button").removeAttr("disabled", "disabled");
			}
	    },
	    error: function (a) {
	    	//$('#e_carregamento_conteudo').html(i).show();
	    }
	})
}
function pg_dinamica_cancelar() {
	$(this).hide();
	$('#mask, .window').hide();
}
/*
 * Função para upload de imagem
 */
function $m(theVar){
	return document.getElementById(theVar)
}
function remove(theVar){
	var theParent = theVar.parentNode;
	theParent.removeChild(theVar);
}
function addEvent(obj, evType, fn){
	if(obj.addEventListener)
	    obj.addEventListener(evType, fn, true)
	if(obj.attachEvent)
	    obj.attachEvent("on"+evType, fn)
}
function removeEvent(obj, type, fn){
	if(obj.detachEvent){
		obj.detachEvent('on'+type, fn);
	}else{
		obj.removeEventListener(type, fn, false);
	}
}
function isWebKit(){
	return RegExp(" AppleWebKit/").test(navigator.userAgent);
}
function ajaxUpload(form,url_action,id_element,html_show_loading,html_error_http){
	var detectWebKit = isWebKit();
	form = typeof(form)=="string"?$m(form):form;
	var erro="";
	if(form==null || typeof(form)=="undefined"){
		erro += "A primeira forma de parâmetro não existe.\n";
	}else if(form.nodeName.toLowerCase()!="form"){
		erro += "A forma de seu primeiro parâmetro não formam um.\n";
	}
	if($m(id_element)==null){
		erro += "O elemento do parâmetro 3 não existe.\n";
	}
	if(erro.length>0){
		alert("Erro na chamada ajaxUpload:\n" + erro);
		return;
	}
	var iframe = document.createElement("iframe");
	iframe.setAttribute("id","ajax-temp");
	iframe.setAttribute("name","ajax-temp");
	iframe.setAttribute("width","0");
	iframe.setAttribute("height","0");
	iframe.setAttribute("border","0");
	iframe.setAttribute("style","width: 0; height: 0; border: none;");
	form.parentNode.appendChild(iframe);
	window.frames['ajax-temp'].name="ajax-temp";
	var doUpload = function(){
		removeEvent($m('ajax-temp'),"load", doUpload);
		var cross = "javascript: ";
		cross += "window.parent.$m('"+id_element+"').innerHTML = document.body.innerHTML; void(0);";
		$m(id_element).innerHTML = html_error_http;
		$m('ajax-temp').src = cross;
		if(detectWebKit){
			$('#d_pu_pf_btn').show();
			$('#d_pu_pf_caixa p').show();
			$('#d_pu_pf_caixa span').hide();
        	remove($m('ajax-temp'));
        }else{
        	$('#d_pu_pf_btn').show();
        	$('#d_pu_pf_caixa p').show();
			$('#d_pu_pf_caixa span').hide();
        	setTimeout(function(){ remove($m('ajax-temp'))}, 250);
        }
    }
	addEvent($m('ajax-temp'),"load", doUpload);
	form.setAttribute("target","ajax-temp");
	form.setAttribute("action",url_action);
	form.setAttribute("method","post");
	form.setAttribute("enctype","multipart/form-data");
	form.setAttribute("encoding","multipart/form-data");
	if(html_show_loading.length > 0){
		$('#d_pu_pf_btn').hide();
		$('#d_pu_pf_caixa p').hide();
		$('#d_pu_pf_caixa span').show();
	}
	form.submit();
}
/* -- Fim -- */

//-- Abrir páginas --
function link_pagina(tipo, url) {
	if ( tipo == 'mesma_pagina' ) {
		window.open(url,'_self');
	} else if ( tipo == 'nova_pagina' ) {
		window.open(url,'_blank');
	}
}

//-- Executar scripts dinâmicamente --
function executar_script(url, funcao_link_pagina, lp_url) {
	$.ajax({type: "GET",url: url, 
		success: function (a) {
			if ( funcao_link_pagina == 's' ) {
				return link_pagina('mesma_pagina', lp_url);
			}
		}	
	});	
}