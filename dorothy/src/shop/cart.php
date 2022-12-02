<?php

class cart {

    public static function getCartByUser($userId = null):?stdClass {
        if ($userId === null) {
            throw new InvalidArgumentException('È necessario specificare $userId.');
        }

        $ordine = module::listOf('ordini')->where('utente', '=', $userId)->andWhere('stato', '=', OrderStatus::Carrello)->first();

        if (!$ordine) {
            return null;
        } else {
            return static::getCart($ordine->GUID);
        }
    }

    /**
     * Restituisce la rappresentazione di un carrello.
     * Effettua anche il controllo degli articoli a carrello. Se non esistono o non sono visibili li cancella.
     * Calcola al volo le spese di spedizione in base al tipo di utente
     * Calcola al volo i prezzi dei prodotti in base al tipo di utente
     *
     * @param  mixed $orderId
     * @return void {obj, guid, rows: {obj, quantity, price, totalPrice}, subtotal, shippingCost, vat, total};
     */
    public static function getCart($orderId, $cleanCart = true):?stdClass {
        if ($orderId === null) {
            throw new InvalidArgumentException('È necessario specificare $orderId.');
        }

        $ordine = module::listOf('ordini')->where('GUID', '=', $orderId)->first();

        if (!$ordine) {
            return null;
        }

        $user = module::listOf("utenti")->where('GUID','=', $ordine->utente)->first();
        $userData = null;
        if ($user) {
            $userData = module::dataOf($user);
        }

        $subtotale = 0;

        $ordine_data = module::dataOf($ordine);
        $carrello = a::get($ordine_data, 'carrello');

        // Pulisce il carrello da articoli non visibili o non più presenti a db
        $isCartChanged = false;
        if ($cleanCart) {
            $isCartChanged = cart::clean($ordine);

            $ordine_data = module::dataOf($ordine);
            $carrello = a::get($ordine_data, 'carrello');

            // se ho effettivamente ripulito il carrello e non ho più righe esco
            if ($isCartChanged && $carrello && count($carrello) === 0) {
                return null;
            }
        }

        $rows = [];
        // leggo gli articoli
        foreach($carrello as $cartArticle) {
            $articolo = module::listOf('articoli')->fetch('Article')->where('GUID','=', a::get($cartArticle, 'articleId'))->first();

            $variantId = a::get($cartArticle, 'variantId');
            if ($variantId) {
                $articolo->selectVariant($variantId);
            }

            $articolo_GUID = $articolo->GUID();
            $articolo_data = module::dataOf($articolo);

            // Recupero i prezzo dai singoli articoli
            // Se l'utente non è loggato prendo il prezzo da privato
            $articolo_prezzo = $articolo->price(($user ? $user : null));

            // Recupero la quantità salvata a db
            $articolo_qt =  a::get($cartArticle, 'quantity');

            $articolo_prezzo_totale = $articolo_prezzo * $articolo_qt;
            $subtotale += $articolo_prezzo_totale;

            $article = new stdClass();
            $article->obj = $articolo;
            $article->guid = $articolo->GUID();
            $article->quantity = $articolo_qt;
            $article->price = $articolo_prezzo;
            $article->totalPrice = round($articolo_prezzo_totale, 2);

            $rows[] = $article;
        };

        // Se l'ordine non è legato ad un utente (non è loggato) oppure è uno shop di prodotti digitali
        // le spese di spedizione sono a 0
        $shippingCost = ($user && !config::shop_digital_products() ? shop::getShippingCost($subtotale, $user) : 0);

        $ret = new stdClass();
        $ret->obj = $ordine;
        $ret->guid = $orderId;
        $ret->rows = $rows;
        $ret->subtotal = round($subtotale, 2);
        $ret->shippingCost = round($shippingCost, 2);
        $ret->paymentMethod = '';
        $ret->isChanged = $isCartChanged;
        $ret->vat = 0;

        $ret->total = $ret->subtotal + $ret->shippingCost + $ret->vat;

        // se è un ordine digitale espongo i dati dell'utente
        $isDigitalOrder = (bool)a::get($ordine_data, 'isDigital', false);
        if ($isDigitalOrder) {
            $userDigitalOrderData = a::get($ordine_data, 'utenteData');
            if ($userDigitalOrderData) {
                $ret->userData = new Obj();
                $ret->userData->email = a::get($userDigitalOrderData, 'email');
                $ret->userData->name = a::get($userDigitalOrderData, 'name');
                $ret->userData->surname = a::get($userDigitalOrderData, 'surname');
                $ret->userData->tel = a::get($userDigitalOrderData, 'tel');
            }
        }

        return $ret;
    }

    public static function count():int {
        $ret = 0;

        try {
            // Se è presente un ordine a carrello
            $ordine = module::listOf('ordini')->where('utente', '=', user::guid())->andWhere('stato', '=', OrderStatus::Carrello)->first();

            if($ordine) {
                $ordine_data = module::dataOf($ordine);

                foreach(a::get($ordine_data, 'carrello') as $articolo){
                    $ret += a::get($articolo, 'quantity');
                }
            }
        } catch (Throwable $ex) {
            $ret = 0;
        }

        return $ret;
    }

    public static function removeProduct(string $productId, string $variantId) {
        $ret = false;

        if (!util::isGUID($productId)) {
            throw new Exception('$productId is not a valid GUID.');
        }

        if (!util::isGUID($variantId)) {
            throw new Exception("variantId '$variantId' is not a valid GUID.");
        }

        // Controllo se esiste già un ordine a db per l'utente
        $order = module::listOf('ordini')->where('utente', '=', user::guid())->andWhere('stato', '=', OrderStatus::Carrello)->first();

        if (!$order) {
            throw new Exception("$order not found.");
        }

        $order_data = module::dataOf($order);
        $cart = $order_data['carrello'];

        // scorro tutto il carrello escludendo l'articolo con la stessa variante
        $newCart = $cart;
        foreach($cart as $key => $articolo) {
            // se articolo e variante coincidono
            if(a::get($articolo, "articleId") === $productId && a::get($articolo, "variantId") === $variantId) {
                unset($newCart[$key]);
            }
        }
        $order_data['carrello'] = array_values($newCart);

        // Se ci sono ancora articoli aggiorno il carrello
        if(count($order_data['carrello']) > 0) {
            $update = db::table('ordini')->where('GUID', '=', $order->GUID())->update(array(
                'data' => a::json($order_data)
            ));

            if ($update) {
                $ret = true;
            }
        } else {
            // Il carrello è vuoto, cancello l'ordine
            $delete = db::table('ordini')->where('GUID', '=', $order->GUID())->delete();

            if ($delete) {
                $ret = true;
            }
        }

        return $ret;
    }

    public static function update(string $productId, int $quantity, string $sign, string $variantId) {
        $ret = '';

        if (!util::isGUID($productId)) {
            throw new Exception("productId '$productId' is not a valid GUID.");
        }

        if (!util::isGUID($variantId)) {
            throw new Exception("variantId '$variantId' is not a valid GUID.");
        }

        if (!v::integer($quantity)) {
            throw new Exception("$quantity '$quantity' is not a valid integer.");
        }

        if (!str::contains('+-=', $sign)) {
            throw new Exception('$sign is not valid.');
        }

        // Controllo esistenza prodotto
        $prodotto = module::listOf('articoli')->fetch('Article')->where('GUID','=',$productId)->first();
        $prodotto->selectVariant($variantId);

        if(!$prodotto) {
            throw new Exception('$prodotto not found.');
        }

        $prodotto_data = module::dataOf($prodotto);

        // Controllo se esiste già un ordine a db per l'utente
        $ordine = module::listOf('ordini')->where("utente", "=", s::get('public_id'))->andWhere("stato", "=", OrderStatus::Carrello)->first();

        if(!$ordine) {
            // controllo lo stock

            if (config::stock() && !$prodotto->isInStock($quantity)) {
                $ret = 'OUT-OF-STOCK';
            } else {
                // Se l'ordine NON esiste ne creo uno
                $data = [
                    'utente' => s::get('public_id'),
                    'dataOrdine' => date("d/m/Y", time()),
                    'stato' => OrderStatus::Carrello,
                    'carrello' => [
                        [
                            'articleId' => $productId,
                            'quantity' => $quantity,
                            'variantId' => $variantId
                        ]
                    ]
                ];

                $dati = array(
                    'GUID' => util::GUID(),
                    'utente' => s::get('public_id'),
                    'data_ordine' => time(),
                    'stato' => OrderStatus::Carrello,
                    'data' => a::json($data)
                );

                $insert = db::table('ordini')->insert($dati);

                if(v::num($insert)){
                    $ret = "ADD";
                }
            }
        }else{

            // Controllo se l'articolo è già a carrello
            $ordine_data = module::dataOf($ordine);
            $carrello = a::get($ordine_data,"carrello");
            $articolo_presente = false;
            $currentArticle = null;

            foreach($carrello as $key=>$articolo){
                if(a::get($articolo, "articleId") == $productId){
                    // controllo che la variante a carrello sia identica
                    $cartVariant = a::get($articolo, "variantId");

                    if ($variantId === $cartVariant) {
                        $articolo_presente = true;

                        // mi salvo per referenza l'articolo corrente
                        $currentArticle = &$carrello[$key];
                    }
                }
            }

            if($articolo_presente){
                // Articolo già presente modifico la quantità

                // Recupero quantità attuale
                $articolo_qt_attuale = $currentArticle['quantity'];

                switch ($sign) {
                    case "+":
                        $articolo_qt_new = $articolo_qt_attuale + $quantity;
                    break;
                    case "-":
                        $articolo_qt_new = $articolo_qt_attuale - $quantity;
                    break;
                    case "=":
                        $articolo_qt_new = $quantity;
                    break;
                }

                // La quantità non può essere 0
                if($articolo_qt_new <= 0) $articolo_qt_new = $articolo_qt_attuale;

                // Se sto gestendo prodotti digitali non posso avere quantità > 1
                //if (config::shop_digital_products() && $articolo_qt_new > 1) {
                //    $articolo_qt_new = 1;
                //}

                if (config::stock() && !$prodotto->isInStock($articolo_qt_new)) {
                    $ret = 'OUT-OF-STOCK';
                } else {
                    $currentArticle['quantity'] = $articolo_qt_new;

                    // Aggiorno la data ordine
                    $ordine_data['dataOrdine'] = date("d/m/Y", time());
                    $ordine_data['carrello'] = $carrello;

                    $update = db::table('ordini')->where("GUID", "=", $ordine->GUID())->update(array(
                        'data_ordine' => time(),
                        'data' => a::json($ordine_data)
                    ));

                    if($update){
                        $ret = "UPDATED";
                    }
                }

            }else{
                // controllo lo stock
                if (config::stock() && !$prodotto->isInStock($quantity)) {
                    $ret = 'OUT-OF-STOCK';
                } else {
                    // Non è presente lo aggiungo
                    $ordine_data["carrello"][] = [
                        'articleId' => $productId,
                        'quantity' => $quantity,
                        'variantId' => $variantId
                    ];

                    $update = db::table('ordini')->where("GUID", "=", $ordine->GUID())->update(array(
                        'data' => a::json($ordine_data)
                    ));

                    if($update){
                        $ret = "ADD";
                    }
                }
            }
        }

        if ($ret === '') {
            throw new Exception();
        }

        return $ret;
    }

    /**
     * Pulisce il carrello da articoli non visibili o non più presenti a db
     *
     * @param  mixed $cart
     * @return bool
     */
    public static function clean(&$order):bool {
        $isChanged = false;

        $dataOrder = module::dataOf($order);
        $cart = a::get($dataOrder, 'carrello');

        // Estraggo gli id degli articoli a carrello
        $articoli_id_a_carrello = a::extract($cart, 'articleId');

        // Recupero gli articoli presenti in carrello a db con una sola query
        $articoli_a_carrello = db::table('articoli')->fetch('Article');

        foreach($articoli_id_a_carrello as $articolo_id) {
            $articoli_a_carrello = $articoli_a_carrello->orWhere('GUID', '=', $articolo_id);
        }

        $articoli_a_carrello = $articoli_a_carrello->all();

        // Articoli effettivamente visualizzati
        $articoli_id_visualizzati = [];

        if ($articoli_a_carrello->count() === 0) {
            return null;
        }

        $newCart = [];

        // Ciclo gli articoli a carrello
        foreach($cart as $cartArticle) {
            $articleId = a::get($cartArticle, 'articleId');
            $variantId = a::get($cartArticle, 'variantId');
            $quantity = a::get($cartArticle, 'quantity');

            $articleObj = $articoli_a_carrello->filter(function($article) use ($articleId) {
                return ($article->GUID === $articleId);
            });

            if ($articleObj && $articleObj->count() > 0) {
                $articleObj = $articleObj->first();
                $articleObj->selectVariant($variantId);

                $articleData = module::dataOf($articleObj);

                // Visualizzo solamente articoli visibili
                if(a::get($articleData, 'visibile') == '1') {

                    // La variante deve ancora esistere a db
                    if (isset($articleObj->variant) && util::isGUID($articleObj->variant->guid)) {

                        // il prodotto dev'essere in stock per quella quantità
                        if ($articleObj->isInStock($quantity)) {
                            $newCart[] = $cartArticle;
                        } else if (is_numeric($articleObj->variant->stock) && $articleObj->variant->stock > 0) {
                            // se non è in stock per quella quantità
                            // modifico il carrello con la quantità rimasta
                            $cartArticle['quantity'] = $articleObj->variant->stock;
                            $newCart[] = $cartArticle;
                        }
                    }
                }
            }
        }

        // scorro il vecchio carrello e lo confronto con il nuovo
        $isDifferent = false;
        for ($i=0; $i < count($cart); $i++) {
            $a = (isset($cart[$i]) ? $cart[$i] : null);
            $b = (isset($newCart[$i]) ? $newCart[$i] : null);

            if (count(a::diff($a, $b)) > 0) {
                $isDifferent = true;
            }
        }

        if ($isDifferent) {
            $cart = $newCart;
            $isChanged = true;

            // se ho effettivamente ripulito il carrello aggiorno l'ordine
            $dataOrder['carrello'] = $newCart;
            $order->data = a::json($dataOrder);

            if(count($newCart) > 0) {
                // Se dopo la pulizia ho ancora articoli a carrello lo aggiorno
                db::table('ordini')->where('GUID', '=', $order->GUID())->update(['data' => $order->data]);
            } else {
                // Se dopo la pulizia il carrello è vuoto, cancello l'ordine
                db::table('ordini')->where('GUID', '=', $order->GUID())->delete();
            }
        }

        return $isChanged;
    }

    public static function saveDigitalOrderData(?array $data) {
        $order = module::listOf('ordini')->where('utente', '=', user::guid())->andWhere('stato', '=', OrderStatus::Carrello)->first();

        if (!$order) {
            throw new Exception("Cart not found.");
        }

        $orderData = module::dataOf($order);

        // se l'utente non è loggato controllo i dati
        if (!user::isLogged()) {
            $name = a::get($data, 'name');
            $surname = a::get($data, 'surname');
            $address = a::get($data, 'address');
            $cap = a::get($data, 'cap');
            $city = a::get($data, 'city');
            $provincia = a::get($data, 'provincia');
            $email = a::get($data, 'email');
            $tel = a::get($data, 'tel');
            $privacy = a::get($data, 'privacy');
            $contact = a::get($data, 'contact');

            if(!$name || !$surname || !$address || !$cap || !$city || !$provincia || !$email || !$tel){
                throw new CustomException(lang::get('validation-fields-required'));
            }

            if(!v::email($email)) {
                throw new CustomException(lang::get('validation-email'));
            }

            if($privacy != 'true'){
                throw new CustomException(lang::get('validation-privacy'));
            }

            // honeypot
            if($contact != ''){
                throw new Exception();
            }

            unset($data['privacy']);
            unset($data['contact']);
            unset($data['csrf']);
            unset($data['endpoint']);

            $orderData['utenteData'] = $data;
        }

        $orderData['isDigital'] = true;
        $update = db::table('ordini')->where('GUID', '=', $order->GUID())->update(['data' => a::json($orderData)]);

        return $update;
    }
}