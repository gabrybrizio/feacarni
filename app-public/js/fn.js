let PAYMENT_METHOD;
let ORDER_STATUS;

var UTIL = (function () {

    var publicAPIs = {};

    publicAPIs.pad = function(num, len){
        // es. 00003 pad(3,5)
        return Array(len + 1 - num.toString().length).join('0') + num;
    }

    publicAPIs.approx = function(num, decimal){
        exp = Math.pow(10, decimal);
        n = Math.round((num + Number.EPSILON) * exp) / exp;
        return n;
    }

    publicAPIs.rnd = function(min, max){
        min = Math.ceil(min);
        max = Math.floor(max);
        return Math.floor(Math.random() * (max - min + 1)) + min;
    }

    publicAPIs.parameter = function(field, url){
        var href = url ? url : window.location.href;
        var reg = new RegExp('[?&]' + field + '=([^&#]*)', 'i');
        var string = reg.exec(href);
        return string ? string[1] : '';
    }

    publicAPIs.uFirstLetter = function(string){
        return string.charAt(0).toUpperCase() + string.slice(1);
    }

    publicAPIs.isGUID = function(g){
        var pattern = new RegExp('^[0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$', 'i');
        if (pattern.test(g) === true) {
            return true;
        } else {
            return false;
        }
    }

    publicAPIs.scrollToEl = function(id,offset){
        offset = offset || 0;
        element = document.getElementById(id);
        window.scrollTo(0,(element.offsetTop - offset));
    }

    publicAPIs.isInViewport = function(el){
        var distance = el.getBoundingClientRect();
        return (
            distance.top >= 0 &&
            distance.left >= 0 &&
            distance.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
            distance.right <= (window.innerWidth || document.documentElement.clientWidth)
        );
    }

	return publicAPIs;

})();

window.addEventListener('scroll', function (event) {
    /*
    if(window.scrollY > 50){
        header.classList.add("is-stick");
    }else{
        header.classList.remove("is-stick");
    }
    */
}, false);

var ARRAY = (function () {

    var publicAPIs = {};

    publicAPIs.unique = function(Arr){
        Arr=Arr.sort();
        i=0;
        while(i<Arr.length){
            if(Arr[i]==Arr[i+1]){
                Arr.splice(i,1);
                i--
            }
            i++
        }
        return Arr;
    }

    publicAPIs.shuffle = function(Arr){
        for(var j, x, i = Arr.length; i; j = parseInt(Math.random() * i), x = Arr[--i], Arr[i] = Arr[j], Arr[j] = x);
        return Arr;
    }

    publicAPIs.remove = function(Arr){
        var what, a = arguments, L = a.length, ax;
        while (L > 1 && Arr.length) {
            what = a[--L];
            while ((ax= Arr.indexOf(what)) !== -1) {
                Arr.splice(ax, 1);
            }
        }
        return Arr;
    }

	return publicAPIs;

})();

var REGISTRATION = (function () {

    var publicAPIs = {};

    publicAPIs.choice = function(_t,_tipo){

        // Visualizzo o nascondo i campi relativi all'azienda
        switch (_tipo) {
            case "Azienda":
                document.querySelectorAll(".field-azienda").forEach(function (field) {
                    field.classList.remove("is-hidden");
                });
              break;
            default:
                document.querySelectorAll(".field-azienda").forEach(function (field) {
                    field.classList.add("is-hidden");
                });
        }

        // focus sul pulsante scelto
        document.querySelectorAll(".form-registrazione-tipo a").forEach(function (a) {
            a.classList.remove("is-focus");
        });

        _t.classList.add("is-focus");

        // Valorizzo il campo hidden
        tipologia.value = _tipo;
    }

    publicAPIs.copy = function(){
        spedizione_paese.value = fatturazione_paese.value;
        spedizione_indirizzo.value = fatturazione_indirizzo.value;
        spedizione_citta.value = fatturazione_citta.value;
        spedizione_provincia.value = fatturazione_provincia.value;
        spedizione_cap.value = fatturazione_cap.value;
    }

    publicAPIs.send = function(){

        btn_send.classList.add("is-hidden");

        form_feedback.classList.remove("red","green");
        form_feedback.classList.add("yellow","is-visible")
        form_feedback.innerHTML = LANG.get('registration-in-progress');

        var form = document.querySelector('#registration_form');
        var formData = new FormData(form);

        API('userSignup', formData)
        .then(function (response) {
            form_feedback.classList.remove("red","yellow","green");
            if (response == "OK"){

                form_feedback.classList.remove("is-visible");
                MODAL.open(LANG.get('registration-success'),"registrazione-ok");

            }else if(response == "AZIENDA"){

                form_feedback.classList.remove("is-visible");
                MODAL.open(LANG.get('registration-business'), "registrazione-ok");

            }else{
                form_feedback.classList.add("red");
                btn_send.classList.remove("is-hidden");

                if(response == "KO"){
                    form_feedback.innerHTML = LANG.get('warning-try-later');
                }else{
                    form_feedback.innerHTML = response;
                }
            }
        })
        .catch(function (response) {
            form_feedback.classList.remove("green","yellow");
            form_feedback.classList.add("red");
            if (response.message) {
                form_feedback.innerHTML = response.message;
            }else{
                form_feedback.innerHTML = LANG.get('warning-try-later');
            }

            btn_send.classList.remove("is-hidden");
        });
    }

    return publicAPIs;

})();

var PROFILE = (function () {

    var publicAPIs = {};

    publicAPIs.update = function(){

        btn_send.classList.add("is-hidden");

        form_feedback.classList.remove("red","green");
        form_feedback.classList.add("yellow","is-visible")
        form_feedback.innerHTML = LANG.get('loading');

        var form = document.querySelector('#profile_form');
        var formData = new FormData(form);

        API('userUpdate', formData)
        .then(function (response) {
            form_feedback.classList.remove("red","yellow","green");
            form_feedback.classList.remove("is-visible");
            MODAL.open(LANG.get('update-ok'),"profilo-ok");
        })
        .catch(function (err) {
            form_feedback.classList.remove("green","yellow");
            form_feedback.classList.add("red");
            if (err.message) {
                form_feedback.innerHTML = err.message;
            }else{
                form_feedback.innerHTML = LANG.get('warning-try-later');
            }

            btn_send.classList.remove("is-hidden");
        });
    }

    return publicAPIs;

})();

var MODAL = (function () {

    var publicAPIs = {};

    publicAPIs.open = function(content,actions,freeze, fullscreen){

        modal_content.innerHTML = content;

        actions = actions || "";
        freeze = freeze || false;

        if(actions == "ordine-ok"){
            actions = '<a class="btn" href="/home/">' + LANG.get('close') + '</a>';
            freeze = true;
        }

        if(actions == "registrazione-ok" || actions == "profilo-ok"){
            actions = '<a class="btn" href="/">' + LANG.get('close') + '</a>';
            freeze = true;
        }

        if(!freeze){
            actions += '<a class="btn" onclick="MODAL.close()">' + LANG.get('close') + '</a>';
        }

        if(fullscreen) {
            modal_window.classList.add('modal-fullscreen');
        } else {
            modal_window.classList.remove('modal-fullscreen');
        }

        modal_actions.innerHTML = actions
        modal_container.classList.add('is-visible');
        document.body.style.overFlow = "hidden";
    }

    publicAPIs.close = function(){
        modal_container.className = '';
        modal_window.className = '';
        document.body.style.overFlow = "auto";
        modal_content.innerHTML = "";
        modal_actions.innerHTML = "";
    }

	return publicAPIs;

})();

var LOADING = (function () {
    let loading = {};

    loading.start = function() {
        MODAL.open(LANG.get('loading'), '', true);
    }

    loading.end = function() {
        MODAL.close();
    }

    return loading;
})();

var FORM = (function () {

    var publicAPIs = {};

    publicAPIs.serialize = function(form){
        var serialized = [];

        for (var i = 0; i < form.elements.length; i++) {

            var field = form.elements[i];

            if (!field.name || field.disabled || field.type === 'file' || field.type === 'reset' || field.type === 'submit' || field.type === 'button') continue;

            if (field.type === 'select-multiple' || field.type === 'select-one') {
                for (var n = 0; n < field.options.length; n++) {
                    if (!field.options[n].selected) continue;
                    serialized.push(encodeURIComponent(field.name) + "=" + encodeURIComponent(field.options[n].value));
                }
            }else if ((field.type !== 'checkbox' && field.type !== 'radio') || field.checked) {
                serialized.push(encodeURIComponent(field.name) + "=" + encodeURIComponent(field.value));
            }
        }

        return serialized.join('&');
    }

    publicAPIs.send = function(form){

        btn_send.classList.add("is-hidden");

        form_feedback.classList.remove("red","green");
        form_feedback.classList.add("yellow","is-visible")
        form_feedback.innerHTML = LANG.get('loading');

        let $form = document.querySelector('#contact_form');
        let formData = new FormData($form);

        API('contactForm', formData)
        .then(function (data) {
            form_feedback.classList.remove("red","yellow","green");

            form_feedback.classList.add("green");
            form_feedback.innerHTML = LANG.get('email-sent-ok');
        })
        .catch(function (error) {
            form_feedback.classList.remove("green","yellow");
            form_feedback.classList.add("red");
            if (error.message) {
                form_feedback.innerHTML = error.message;
            }else{
                form_feedback.innerHTML = LANG.get('warning-try-later');
            }

            btn_send.classList.remove("is-hidden");
        });
    }

    return publicAPIs;

})();

var SHOP = (function () {
    var shop = {};

    shop.update = function(product_id, product_sign, product_qt, urlTo, variantId){
        if (urlTo) {
            urlTo = (urlTo === true ? window.location.href : urlTo);
        }

        API('cartUpdate', {
            "productId": product_id,
            "sign": product_sign,
            "quantity": product_qt,
            "variantId": variantId
        })
        .then(function (data) {
            let goToCartBtn = '<a class="btn btn-important" href="/carrello/">' + LANG.get('cart-go-to') + '</a>';
            let continueBtn = '<a class="btn" onclick="MODAL.close()">' + LANG.get('shop-continue-shopping') + '</a>';

            switch (data.trim()) {
                case "ADD":
                    if(urlTo){
                        window.location = urlTo;
                    }else{
                        MODAL.open(LANG.get('cart-added-item'), goToCartBtn + continueBtn, true);
                        SHOP.count();
                    }
                  break;
                case "UPDATED":
                    if(urlTo){
                        window.location = urlTo;
                    }else{
                        MODAL.open(LANG.get('cart-added-item'), goToCartBtn + continueBtn, true);
                        SHOP.count();
                    }
                    break;
                case "OUT-OF-STOCK":
                    let closeBtn = '<a class="btn" onclick="MODAL.close();' + (urlTo ? 'window.location = ' + urlTo + ';' : '') + '">' + LANG.get('close') + '</a>';
                    MODAL.open(LANG.get('cart-out-of-stock'), closeBtn, true);
                    break;
            }
        })
        .catch(function (err) {
            MODAL.open(LANG.get('warning-try-later'));
        });
    }

    shop.count = function(){
        let cart_icon_count = document.getElementById('cart_icon_count');

        if (cart_icon_count) {
            API('cartCount').then(function(data) {
                cart_icon_count.innerHTML = (isNaN(data)? '': data);
            }).catch();
        }
    }

    shop.remove = function(productId, variantId){
        API('cartRemoveProduct',{
            "productId": productId,
            "variantId": variantId
        })
        .then(function (data) {
            if(data === true) {
                window.location = window.location.href;
            } else {
                MODAL.open(LANG.get('warning-try-later'));
            }
        });
    }

    shop.payWithBankTransferModal = function(orderId) {
        let content = LANG.get('shop-bank-transfer-payment');
        let btn = '<a class="btn btn-important" onclick="SHOP.payWithBankTransfer(\'' + orderId + '\')">' + LANG.get('proceed') + '</a>'
        MODAL.open(content, btn);
    }

    shop.payWithBankTransfer = function(orderId) {
        LOADING.start();

        API('sendOrder', {
            "orderId": orderId,
            "paymentMethod": PAYMENT_METHOD.BankTransfer
        })
        .then(function () {
            MODAL.open(LANG.get('shop-order-ok'), "ordine-ok");
        })
        .catch(function (error) {
            MODAL.open(LANG.get('warning-try-later'));
        });
    }

    shop.onlyEmail = function(orderId) {
        LOADING.start();
        
        return SHOP.saveDigitalOrderData('digitalOrderForm').then(function(){
            API('sendOrder', {
                "orderId": orderId,
                "paymentMethod": PAYMENT_METHOD.CashOnDelivery
            })
            .then(function () {
                MODAL.open(LANG.get('shop-order-ok'), "ordine-ok");
            })
            .catch(function (error) {
                MODAL.open(LANG.get('warning-try-later'));
            });
        }).catch(function(error) {
            MODAL.open(error.message);
        });
    }    

    shop.payWithSatispay = function(satispayOrderId, orderId) {
        LOADING.start();

        API('satispayConfirmOrder', {
            "satispayOrderId": satispayOrderId,
            "orderId": orderId
        })
        .then(function () {
            MODAL.open(LANG.get('shop-order-ok'), 'ordine-ok');
        })
        .catch(function (error) {
            MODAL.open(LANG.get('warning-try-later'));
        });
    }

    shop.payWithCashOnDeliveryModal = function(orderId) {
        let content = LANG.get('shop-cash-on-delivery-payment');
        let btn = '<a class="btn btn-important" onclick="SHOP.payWithCashOnDelivery(\'' + orderId + '\')">' + LANG.get('proceed') + '</a>'
        MODAL.open(content, btn);
    }

    shop.payWithCashOnDelivery = function(orderId) {
        LOADING.start();

        API('sendOrder', {
            "orderId": orderId,
            "paymentMethod": PAYMENT_METHOD.CashOnDelivery
        })
        .then(function () {
            MODAL.open(LANG.get('shop-order-ok'), 'ordine-ok');
        })
        .catch(function (error) {
            MODAL.open(LANG.get('warning-try-later'));
        });
    }

    shop.changeVariant = function($el) {
        location.href = $el.value;
    }

    shop.saveDigitalOrderData = function(formId) {
        let $form = document.getElementById(formId);
        let formData = null;

        if ($form) {
            formData = new FormData($form);
        }

        return API('saveDigitalOrderData', formData);
    }

	return shop;

})();

let LANG = (function () {
    'use strict'
    let lang = {};
    let settings;

    lang.init = function(options) {
        let defaults = {
        };
        settings = Object.assign({}, defaults, options);
    }

    lang.dictionary = function() {
        return LANG_CONFIG;
    }

    lang.get = function(key, parameters = null) {
        let ret = '';

        if(!LANG.dictionary) {
            console.log('LANG.dictionary not found.');
        }

        if (LANG.dictionary().hasOwnProperty(key)) {
            ret = LANG.dictionary()[key]
        } else {
            console.log('Language key \'' + key + '\' not found in LANG.dictionary()');
        }

        if (parameters) {
            for (const key in parameters) {
                ret = ret.replaceAll(':' + key, parameters[key]);
            }
        }

        return ret;
    }

	return lang;
})();

var ICON = (function() {
	let jsonIcons = {};

	let icon = function(iconName) {
		let template = ICONS_CONFIG['_template'];
		let icon = template.replace('{{icon}}', ICONS_CONFIG[iconName]);
		return icon;
	}

	icon.init = function(json) {
		jsonIcons = json;
	};

	return icon;
})();

var APP = (function () {
    'use strict'
    let app = {};
    let settings;

    app.init = function(options) {
        let defaults = {
            orderStatus: '',
            paymentMethod: '',
            csrf: ''
        };
        settings = Object.assign({}, defaults, options);

        // leggo i dati passati lato server
        PAYMENT_METHOD = settings.paymentMethod;
        ORDER_STATUS = settings.orderStatus;

        SHOP.count();
    }

    app.csrf = function() {
        return settings.csrf;
    }

	return app;
})();

var API = (function () {
    'use strict'
    let api = {};
    let settings;

    api = function(endpoint, parameters) {
        let body;
        let isJson = false;
        if (parameters instanceof FormData) {
            body = parameters;
            body.append('endpoint', endpoint);
            body.append('csrf', APP.csrf());
        } else {
            isJson = true;
            let defaultParameters = {
                endpoint: endpoint,
                csrf: APP.csrf(),
            };
            parameters = Object.assign({}, defaultParameters, parameters);

            body = JSON.stringify(parameters);
        }

        function parseJSON(response) {
            return new Promise((resolve) => response.json()
              .then((json) => resolve({
                status: response.status,
                ok: response.ok,
                json,
              })));
        }

        let fetchOptions = {
            method: 'post',
            body: body,
        }

        if (isJson) {
            fetchOptions.headers = {
                'content-type': 'application/json'
            }
        }

        return new Promise((resolve, reject) => {
            fetch('/ajax/api.php', fetchOptions)
            .then(parseJSON)
            .then((response) => {
                if (response.ok) {
                    return resolve(response.json.content);
                }
                // extract the error from the server's json
                return reject(response.json);
                })
            .catch((error) => reject({
                networkError: error.message,
            }));
        });
    }

	return api;
})();

var RICETTA = (function () {

    var ricetta = {};
    
    ricetta.set = function(ricetta){
        document.querySelectorAll(".ricette-tabs a").forEach(function(ricetta_tab,i){
            ricetta_tab.classList.remove("focus");
        }); 

        document.querySelectorAll(".ricette-list .ricetta").forEach(function(ricetta_content,i){
            ricetta_content.classList.remove("focus");
        });

        document.querySelector('.tab-' + ricetta).classList.add("focus");
        document.querySelector('.ricetta-' + ricetta).classList.add("focus");

        $background_focus = document.getElementById("ricetta-" + ricetta + "-immagine").getAttribute("src");
        document.querySelector('.articolo-ricette-immagine').style.backgroundImage = "url('" + $background_focus + "')";
    }    

	return ricetta;

})();

// REGISTER SERVICE WORKER
/*
if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/sw.js', {
        scope: '/'
    }).then(function (reg) {
        if (reg.installing) {
            console.log('Service worker installing');
        } else if (reg.waiting) {
            console.log('Service worker installed');
        } else if (reg.active) {
            console.log('Service worker active');
        }
    }).catch(function (error) {
        // registration failed
        console.log('Registration failed with ' + error);
    });
}
*/
