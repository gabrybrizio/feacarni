<?php

class shop {

    public static function consumeStock($guid, $variantGuid, $amount) {
        $article = db::table('articoli')->fetch('Article')->where("GUID", "=", $guid)->first();
        $article->selectVariant($variantGuid);

        if ($article && isset($article->variant)) {
            $data = json_decode($article->data(), true);
            $currentStock = $article->variant->stock;
            $title = $article->name();

            $newStock = $currentStock - $amount;

            if ($newStock < 0) {
                $plural = ($currentStock > 1 ? 's':'');
                $unitsLeftMessage = ($currentStock > 0 ? "<br>$currentStock unit$plural left." : "");

                throw new Exception("Impossible to continue.<br>Article <b>$title</b> is out of stock. $unitsLeftMessage");
            }

            db::table('variants')->where('guid', '=', $article->variant->guid)->update(array(
                'stock' => $newStock
            ));
        } else {
            throw new Exception("Impossible to consume stock for article $guid and variant $variantGuid.<br>Article or his variant doesn't exist.");
        }
    }

    public static function getShippingCost(float $amount, $user = null, string $paymentMethod = '', float $weight = 0): float {
        $cost = -1;

        $userData = null;
        $userType = '';
        if ($user) {
            $userData = module::dataOf($user);
            $userType = a::get($userData, 'tipologia');
        }

        if ($weight > 0) {
            // gestione spese spedizione in base al peso e alla zona di spedizione
            //$countryId = a::get($userData, 'spedizione-paese');

            //$query = 'SELECT cost FROM shipping_costs AS sc INNER JOIN countries AS c ON c.shipping_zone = sc.zone WHERE c.id = :countryId and weight >= :weight limit 1';
            //$result = db::query($query, ['countryId' => $countryId, 'weight' => $weight])->first();
            //$cost = $result->cost;
        }

        // gli utenti "rivenditori" (business) hanno delle soglie differenti
        if ($userType == user::TYPE_BUSINESS) {
            // sopra i tot euro le spese di spedizione sono gratuite
            if ($amount >= config::get('free_shipping_minimum_business')) {
                $cost = 0;
            } else {
                // sotto i tot euro le spese di spedizione ammontano a:
                $cost = config::get('basic_shipping_cost_business');
            }
        } else {
            // sopra i tot euro le spese di spedizione sono gratuite
            if ($amount >= config::get('free_shipping_minimum')) {
                $cost = 0;
            } else {
                // sotto i tot euro le spese di spedizione ammontano a:
                $cost = config::get('basic_shipping_cost');
            }
        }


        if ($cost < 0) {
            throw new Error('getShippingCost error: $cost less then 0');
        }

        return $cost;
    }

    public static function articles(?string $categoria = null, ?bool $showVariants = false) {
		$articles = db::table('articoli')->fetch('Article')->where('visibile','=','1');

		if($categoria){
            $articles = $articles->andWhere('categoria','=', $categoria);
		}

        $articles = $articles->order('nome ASC')->all();
        $retList = new Collection();

		// Se l'articolo ha più varianti le mostro nell'elenco
        $i = 0;
        foreach($articles as $article) {
            if ($article->variants && count($article->variants) > 0) {
                $variantsCount = 0;
                foreach($article->variants as $variant) {
                    // il flag showInlist deve essere a true
                    if ($variant->articleId === $article->GUID && $showVariants) {
                        $article->selectVariant($variant->guid);

                        $retList->append($i, clone $article);
                        $variantsCount++;
                        $i++;
                    }
                }

                // Se non ci sono varianti da visualizzare in elenco
                // visualizzo il prodotto generico
                if ($variantsCount === 0) {
                    $retList->append($i, clone $article);
                }
            } else {
                $retList->append($i, clone $article);
                $i++;
            }
        }

        return $retList;
    }
}