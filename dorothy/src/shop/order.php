<?php

class order {

    /**
     * Legge un ordine a db.
     *
     * @param  mixed $orderId
     * @return void {obj, guid, rows: {obj, quantity, price, totalPrice}, subtotal, shippingCost, paymentMethod, vat, total};
     */
    public static function getOrder($orderId):?stdClass {
        if ($orderId === null) {
            throw new InvalidArgumentException('È necessario specificare $orderId.');
        }

        $ordine = module::listOf('ordini')->where('GUID', '=', $orderId)->first();

        if (!$ordine) {
            return null;
        }

        $ordine_data = module::dataOf($ordine);
        $carrello = a::get($ordine_data,'carrello');

        // leggo gli articoli
        foreach($carrello as $cartArticle) {
            $article = new stdClass();

            // se esiste pesco l'articolo a db altrimenti uso i valori salvati a carrello
            $obj = module::listOf('articoli')->fetch('Article')->where('GUID','=', a::get($cartArticle, 'articleId'))->first();
            if ($obj) {
                // imposto la variante corretta sul prodotto
                $variantId = a::get($cartArticle, 'variantId');
                if ($variantId) {
                    $obj->selectVariant($variantId);
                }
            }

            // se ho trovato l'articolo e la variante a db leggo i relativi dati
            if ($obj && isset($obj->variant)) {
                $article->obj = $obj;
                $article->guid = $obj->GUID;
                $article->img = $obj->image();
                $article->url = $obj->url();
            } else {
                // se non trovo più l'articolo o la variante a db uso i campi di scorta del carrello
                $article->obj = null;
                $article->guid = null;
                $article->img = null;
                $article->url = null;
            }

            $article->name = a::get($cartArticle, 'name');
            $article->description = a::get($cartArticle, 'description');
            $article->quantity = a::get($cartArticle, 'quantity');
            $article->price = a::get($cartArticle, 'price');
            $article->totalPrice = $article->quantity * $article->price;
            $article->variantId = a::get($cartArticle, 'variantId');
            $article->variantName = a::get($cartArticle, 'variantName');
            $article->variantOptions = a::get($cartArticle, 'variantOptions');
            $article->digitalDownloadPublicUrl = $article->obj->digitalDownloadPublicUrl($orderId);

            $rows[] = $article;
        };

        $ret = new stdClass();
        $ret->obj = $ordine;
        $ret->guid = $orderId;
        $ret->rows = $rows;
        $ret->subtotal = a::get($ordine_data, 'subtotal');
        $ret->shippingCost = a::get($ordine_data, 'shippingCost');
        $ret->paymentMethod = a::get($ordine_data, 'paymentMethod');
        $ret->vat = a::get($ordine_data, 'vat');
        $ret->total = a::get($ordine_data, 'total');


        // se è un ordine digitale espongo i dati dell'utente
        $ret->isDigital = (bool)a::get($ordine_data, 'isDigital', false);
        if ($ret->isDigital) {
            $userDigitalOrderData = a::get($ordine_data, 'utenteData');
            if ($userDigitalOrderData) {
                $ret->userData = new Obj();
                $ret->userData->name = a::get($userDigitalOrderData, 'name');
                $ret->userData->surname = a::get($userDigitalOrderData, 'surname');
                $ret->userData->email = a::get($userDigitalOrderData, 'email');
                $ret->userData->address = a::get($userDigitalOrderData, 'address');
                $ret->userData->cap = a::get($userDigitalOrderData, 'cap');
                $ret->userData->city = a::get($userDigitalOrderData, 'city');
                $ret->userData->provincia = a::get($userDigitalOrderData, 'provincia');
                $ret->userData->tel = a::get($userDigitalOrderData, 'tel');
                $ret->userData->marketing = a::get($userDigitalOrderData, 'marketing');
                $ret->userData->messaggio = a::get($userDigitalOrderData, 'messaggio');
            }
        }

        return $ret;
    }

    /**
     * Serve per inviare un ordine con status "Da pagare" tipo Bonifico o Contrassegno
     *
     * @param  mixed $orderId
     * @param  mixed $paymentMethod
     * @return void
     */
    public static function send(string $orderId, string $paymentMethod) {
        $availablePaymentMethod = [PaymentMethod::CashOnDelivery];

        if (!in_array($paymentMethod, $availablePaymentMethod)) {
            throw new InvalidArgumentException('$paymentMethod is not a valid payment method.');
        }

        // se nel mentre qualche prodotto non è più disponibile interrompo l'ordine
        // segnalando che il carrello è stato aggiornato
        $cart = cart::getCart($orderId);
        if ($cart && $cart->isChanged) {
            throw new CustomException(LANG::get('cart-changed'));
        }

        static::confirmOrder($orderId, $paymentMethod);
    }

    /**
     * Funzione richiamata sempre lato server per confermare l'ordine
     *
     * @param  mixed $orderId
     * @param  mixed $paymentMethod
     * @param  mixed $extraParams
     * @param  mixed $response
     * @return void
     */
    public static function confirmOrder(string $orderId, string $paymentMethod, array $extraParams = [], string $response=''):void {
        if (!util::isGUID($orderId)) {
            throw new InvalidArgumentException('$orderId is not a GUID.');
        }
        if (!in_array($paymentMethod, PaymentMethod::get())) {
            throw new InvalidArgumentException('$paymentMethod is not a valid payment method.');
        }

		$ordine = cart::getCart($orderId, false);

        if(!$ordine) {
            throw new CustomException(lang::get('shop-order-not-found'));
        }

        // Gli utenti NON admin possono modificare solo i propri ordini
        // Gli admin possono modificare tutto
        if(!(user::isAdmin() || $ordine->obj->utente === user::guid())) {
            throw new CustomException(lang::get('shop-order-not-found'));
        }

        $ordine_data = module::dataOf($ordine->obj);

        switch ($paymentMethod) {
            case PaymentMethod::BankTransfer:
                $ordine_data["stato"] = OrderStatus::DaPagare;
              break;
            case PaymentMethod::CashOnDelivery:
                $ordine_data["stato"] = OrderStatus::DaPagare;
              break;
            default:
            $ordine_data["stato"] = OrderStatus::Pagato;
          }

        //$ordine_data["stato"] = ($paymentMethod === PaymentMethod::BankTransfer || $paymentMethod === PaymentMethod::CashOnDelivery || $paymentMethod === PaymentMethod::OnlyEmail ? OrderStatus::DaPagare : OrderStatus::Pagato);

        // gestisco la transazione in modo che se fallisce la conferma dell'ordine lo stock viene ripristinato
        try {
            db::beginTransaction();

            foreach ($ordine->rows as $key => $row) {
                $product = $row->obj;

                // Aggiorno lo stock dell'articolo
                if (config::stock() && is_numeric($product->variant->stock)) {
                    shop::consumeStock($row->guid, $product->variant->guid, $row->quantity);
                }

                // Aggiungo al carrello i prezzi degli articoli dell'ordine come storico
                $ordine_data["carrello"][$key]["price"] = $row->price;

                // Aggiungo al carrello il nome dell'articolo completo come storico
                $ordine_data["carrello"][$key]["name"] = $row->obj->name();

                // Aggiungo al carrello un'eventuale descrizione come storico
                $ordine_data["carrello"][$key]["description"] = '';

                // Aggiungo al carrello il nome della variante come storico
                $ordine_data["carrello"][$key]["variantName"] = ($product->variant ? $product->variant->name : '');

                // Aggiungo al carrello le opzioni della variante come storico
                $ordine_data["carrello"][$key]["variantOptions"] = ($product->variant ? $product->variantOptionsValue() : '');
            }

            $ordine_data["subtotal"] = round($ordine->subtotal, 2);
            $ordine_data["shippingCost"] = round($ordine->shippingCost, 2);
            $ordine_data["vat"] = round($ordine->vat, 2);
            $ordine_data["total"] = round($ordine->total, 2);

            $ordine_data["dataOrdine"] = date("d/m/Y", time());
            $ordine_data["paymentMethod"] = $paymentMethod;
            if ($extraParams) {
                $ordine_data = a::merge($ordine_data, $extraParams);
            }

            $update = db::table('ordini')->where("GUID", "=", $orderId)->update(array(
                'data_ordine' => time(),
                'stato' => $ordine_data["stato"],
                'totale' => $ordine->total,
                'data' => a::json($ordine_data)
            ));

            db::commit();
        } catch (Throwable $th) {
            db::rollback();

            throw $th;
        }

        if($update) {
            try {
                if (user::isLogged()) {
                    $email = user::get('username');
                } else {
                    // se l'utente non è loggato guardo se ci sono i dati dell'ordine digitale
                    if (isset($ordine->userData) && $ordine->userData->email) {
                        $email = $ordine->userData->email;
                    }
                }

                // email all'utente e in bcc all'admin
                order::sendOrderConfirmationEmail($orderId, [$email], [site::get('email')]);

                if(config::shop_digital_products()){
                    s::destroy();
                }

            } catch(Throwable $ex) {
                trigger_error($ex->getMessage());
            }
        } else {
            throw new Exception(lang::get('shop-order-problems'));
        }
    }

    public static function sendOrderConfirmationEmail(string $orderId, array $recipients = [], array $bcc = [], string $emailSubject = '', string $emailFrom = ''):void {
		$order = order::getOrder($orderId);
        $orderData = module::dataOf($order->obj);

        $orderPaymentMethod = a::get($orderData, 'paymentMethod');
        $orderCode = str::upper(str::short($order->guid, 8, ""));

        $user = module::listOf('utenti')->where("GUID", "=", $order->obj->utente())->first();
        if ($user) {
            $name = $user->nome();
            $surname = $user->cognome();

            $indirizzoSpedizione = user::getShippingAddress($user);
            $indirizzoFatturazione = user::getBillingAddress($user);
        } else {
            // se non trovo l'utente provo a cercare i dati dell'utente
            // nell'ordine (per i prodotti digitali non è necessaria login)
            if (isset($order->userData)) {

                $name = $order->userData->name;
                $surname = $order->userData->surname;

                $indirizzoSpedizione = "";
                $indirizzoSpedizione = $order->userData->address;
                $indirizzoSpedizione .= " - " . $order->userData->cap;
                $indirizzoSpedizione .= " " . $order->userData->city . " (" . $order->userData->provincia . ")";
                $indirizzoSpedizione .= "<br>";
                $indirizzoSpedizione .= "Email: " . $order->userData->email;
                $indirizzoSpedizione .= "<br>Tel.: " . $order->userData->tel;

                $indirizzoFatturazione = $indirizzoSpedizione;

                if($order->userData->marketing == 1){
                    $indirizzoSpedizione .= "<br><br>Acconsento all'invio di materiale promozionale e news da parte di " . site::get('ragione-sociale') . ".";
                }

                if(isset($order->userData->messaggio)){
                    $indirizzoSpedizione .= "<br><br>" . $order->userData->messaggio;
                }

                $indirizzoSpedizione .= "<br><br><b>Consegna " . site::get('prossima-consegna');
                
            }
        }
        $userTitle = $name . ' ' . $surname;


        $email_text = lang::get('shop-order-thanks') . url::base();
        $email_text .= "<br><br>" . lang::get('shop-order-check');
        $email_text .= '<br><br><b>' . lang::get('shop-order') .": $orderCode</b>";

        // Se ho scelto pagamento IBAN
        if ($orderPaymentMethod === PaymentMethod::BankTransfer) {
            $iban = site::get('iban');
            $intestatarioIban = site::get('intestatario-iban');

            $email_text .= "<br><br>" . lang::get('shop-bank-transfer-fulfillment');
            $email_text .= "<br><br><b>" . lang::get('account-holder') . ": $intestatarioIban</b>";
            $email_text .= "<br><b>IBAN: $iban</b>";
        }

        $htmlProdotti = '';
        $htmlTotaliOrdine = '';
        $vat = $order->vat;
        $subtotale = $order->subtotal;
        $totale = $order->total;
        $costoSpedizione = $order->shippingCost;

        foreach($order->rows as $row) {
            $articolo_qt = $row->quantity;
            $articolo_prezzo = $row->price;
            $articolo_prezzo_totale = $row->totalPrice;

            $digitalDownloadHtml = '';
            if ($row->digitalDownloadPublicUrl) {
                $digitalDownloadHtml = '<br><a href="' . $row->digitalDownloadPublicUrl . '">DOWNLOAD</a>';
            }

            $variantOptionsHtml = '';
            if ($row->variantOptions) {
                //$variantOptionsHtml = '<br><small>' . $row->variantOptions . '</small>';
            }

            // elenco prodotti
            $htmlProdotti .= '<tr>';
            $htmlProdotti .= '<td style="padding: 10px 15px 0 0;">' . $row->name . '</td>';
            $htmlProdotti .= '<td style="padding: 10px 15px;" valign="top" align="right">' . util::euro($articolo_prezzo) . ' x ' . $articolo_qt . '</td>';
            $htmlProdotti .= '<td style="padding: 10px 0 0 15px;" valign="top" align="right">' . util::euro($articolo_prezzo_totale) . '</td>';
            $htmlProdotti .= '</tr>';
        };

        $htmlTotaliOrdine .= '<tr style="border-bottom:1px solid #ecedee">';
        $htmlTotaliOrdine .= '<td style="padding: 10px 15px 0 0;" valign="top">' . lang::get('shop-subtotal') . ':</td>';
        $htmlTotaliOrdine .= '<td style="padding: 10px 0 10px 0;" valign="top" align="right"><b>' . util::euro($subtotale) . '</b></td>';
        $htmlTotaliOrdine .= '</tr>';

        $htmlTotaliOrdine .= '<tr style="border-bottom:1px solid #ecedee">';
        $htmlTotaliOrdine .= '<td style="padding: 10px 15px 0 0;" valign="top">' . lang::get('shop-shipping-cost') . ':</td>';
        $htmlTotaliOrdine .= '<td style="padding: 10px 0;" valign="top" align="right"><b>' . util::euro($costoSpedizione) . '</b></td>';
        $htmlTotaliOrdine .= '</tr>';

        $htmlTotaliOrdine .= '<tr style="border-bottom:1px solid #ecedee">';
        $htmlTotaliOrdine .= '<td style="padding: 10px 15px 0 0;" valign="top"><b>' . lang::get('shop-total-order') . ':</b></td>';
        $htmlTotaliOrdine .= '<td style="padding: 10px 0;" valign="top" align="right"><b>' . util::euro($totale) . '</b></td>';
        $htmlTotaliOrdine .= '</tr>';

        //$indirizzoSpedizione = "";
        //$indirizzoFatturazione = "";
        //$htmlProdotti = "";
        //$htmlTotaliOrdine = "";

        $arr = [
            'email_nominativo' => $userTitle,
            'indirizzo_spedizione' => $indirizzoSpedizione,
            'indirizzo_fatturazione' => $indirizzoFatturazione,
            'html_ordine_prodotti' => $htmlProdotti,
            'html_ordine_totali' => $htmlTotaliOrdine,
        ];
        $extraHtml = template::load('email-order.php', $arr);
        $email_title = lang::get('dear') . ' ' . $userTitle;

        // Invio email
        $mail = new Mail();

        $emailFrom = 'noreply@moloc.net';
        $mail->setFrom($emailFrom, site::get('ragione-sociale'));

        foreach ($recipients as $email) {
            $mail->addAddress($email);
        }

        foreach ($bcc as $email) {
            $mail->addBCC($email);
        }

        $emailSubject = ($emailSubject !== '' ? $emailSubject : site::get('ragione-sociale') . ' - ' . lang::get('shop-order-confirmation') . ' ' . $orderCode);

        $mail->Subject = $emailSubject;
        $mail->setBody($email_title, $email_text, url::base(), lang::get('go-to-website'), '#000000', $extraHtml);

        if(!$mail->send()) {
            throw new Exception('Errore durante l\'invio dell\'email dell\'ordine');
        }
    }

    /**
     * Controlla se un'articolo/variante esiste in un ordine
     *
     * @param  mixed $orderId
     * @param  mixed $articleId
     * @param  mixed $variantId
     * @return void
     */
    public static function orderContains($orderId, $articleId, $variantId):bool {
        $ret = false;
        $order = static::getOrder($orderId);

        if ($order) {
            foreach($order->rows as $row) {
                if ($row->guid === $articleId && $row->variantId === $variantId) {
                    $ret = true;
                    break;
                }
            }
        }

        return $ret;
    }
}