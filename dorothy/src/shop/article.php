<?php

class Article extends Obj {
	public $variants = [];
	private static $allVariants = null;

	public function __construct() {
		// carico le varianti
		$this->loadVariants();

		// se ho una sola variante vuol dire che è quella di default e la seleziono
		if ($this->variants && count($this->variants) === 1) {
			$this->selectVariant(array_key_first($this->variants));
		} else {
			// se ho più varianti non ne seleziono nessuna
			// e prendo dei valori di default direttamente dal prodotto (es. il nome,le immagini)
			$this->selectVariant(null);
		}
    }

	/**
	 * Restituisce il nome dell'articolo aggiungendo eventualmente il nome della variante
	 *
	 * @return string
	 */
	public function name():string {
		//$ret = $this->nome . ' ' . ($this->variant ? $this->variant->name : '');
		$ret = $this->nome;

		return $ret;
	}

	/**
	 * Seleziona una variante per il prodotto da $this->variants e la carica in $this->variant.
	 *
	 * @param  string $variantGuid
	 * @return void
	 */
	public function selectVariant(string $variantGuid = null) {
		$this->variant = null;

		// se trovo la variante
		if ($variantGuid && $this->variants && count($this->variants) > 0) {
			if (isset($this->variants[$variantGuid])) {
				$this->variant = $this->variants[$variantGuid];
			}
		}
	}

	/**
	 * Restituisce l'elenco dei valori delle opzioni della variante selezionata.
	 * Es. "Rossa / Cotone / S"
	 *
	 * @param  string $optionNames separati da virgola
	 * @param  mixed $separator
	 * @return void
	 */
	public function variantOptionsValue(?string $optionNames = null, ?string $separator = ' / ') {
		// se non specifico i campi prendo quelli del config
		if (!$optionNames) {
			$optionNames = config::get('variant_options');
		}

		return $this->variant->getOptionsValue($optionNames, $separator);
	}

	/**
	 * Carica da db le varianti per questo articolo
	 *
	 * @return void
	 */
	public function loadVariants():void {
		if(is_null(static::$allVariants)) {
            static::$allVariants = db::table('variants')->fetch('variant')->andWhere('visible','=','1')->order('articleId, sortOrder ASC')->all();
        }
		if (static::$allVariants) {
			foreach(static::$allVariants as $variant) {
				if ($variant->articleId === $this->GUID) {
					$this->variants[$variant->guid] = $variant;
				}
			}
		}

		// $variants = db::table('variants')->fetch('variant')->where('articleId','=', $this->GUID)->andWhere('visible','=','1')->order('articleId, sortOrder ASC')->all();
		// foreach($variants as $variant) {
		// 	$this->variants[$variant->guid] = $variant;
		// }
	}

	public function hasMultipleVariants():bool {
		return (is_array($this->variants) && count($this->variants) > 1);
	}

	public function price($user = null) {
        $ret = null;

        $userData = null;
        $userType = null;
        if ($user) {
            $userData = module::dataOf($user);
            $userType = a::v($userData, 'tipologia');
        }

		$price = 0;
		if ($this->variant) {
			// prendo il prezzo dalla variante selezionata

			$this->variant->price($user);
			if($userType === user::TYPE_BUSINESS) {
				$price = $this->variant->priceBusiness;
			} else {
				$price = $this->variant->pricePrivate;
			}
		} else if (count($this->variants) > 0) {
			// se non ho una variante selezionata prendo il prezzo più basso

			$prices = [];
			foreach($this->variants as $variant) {
				$prices[] = $variant->price($user);
			}
			asort($prices);
			$prices = array_values($prices);
			$price = $prices[0];
		}

		$ret = (float)str::replace($price,',','.');

        return $ret;
	}

	public function url($variant = null) {
		$slug = str::slug($this->nome);

        $variantGuid = '';
        $variantName = '';

		// se ho passato una variante specifica uso quella
		if ($variant) {
			$variantGuid = $variant->guid;
			$variantName = $variant->name;
		} else {
			// altrimenti guardo se c'è una variante selezionata
			if ($this->variant) {
				$variantGuid = $this->variant->guid;
				$variantName = $this->variant->name;
			}
		}

		if ($variantName) {
			$slug .= '-' . str::slug($variantName);
		}

		$productGuid = $this->GUID;
		$ret = "/catalogo/$slug/$productGuid/" . ($variantGuid ? $variantGuid . '/' : '');

		return $ret;
	}

	/**
	 * File di un prodotto digitale.
	 *
	 * @return void
	 */
	public function digitalDownloadFile() {
		$ret = null;

		if (config::shop_digital_products() && $this->variant) {
			$fieldName = 'digitalDownload';
			$variantData = json_decode($this->variant->data, true);
			$fileName = a::get($variantData, $fieldName);
			if ($fileName) {
				$ret = module::documentsOf('articoli', $this->GUID, $fileName);
			}
		}

		return $ret;
	}

	/**
	 * Url pubblico per il download di prodotti digitali (usato per es. nell'email di evasione ordine)
	 * Controlla che l'ordine abbia effettivamente quel prodotto.
	 *
	 * @return void
	 */
	public function digitalDownloadPublicUrl($orderId) {
		$ret = null;

		if (config::shop_digital_products() && $this->variant && $this->digitalDownloadFile()) {
			$articleId = $this->GUID;
			$variantId = $this->variant->guid;
			$ret = url::to("digital-download/$orderId/$articleId/$variantId/");
		}

		return $ret;
	}

	public function image(?string $articlePropertyName = 'cover', ?string $variantPropertyName = 'immagini') {

		$data = json_decode($this->data, true);
		$ret = module::imagesOf('articoli', $this->GUID, a::get($data, $articlePropertyName));

		return $ret;
	}

	/**
	 * Indica se l'articolo è visibile
	 *
	 * @return bool
	 */
	public function isVisible():bool {
		$ret = $this->visibile == 1;

		return $ret;
	}

	public function isInStock(int $newQuantity = 0):bool {
        return !$this->isOutOfStock($newQuantity);
    }

    public function isOutOfStock(int $newQuantity = 0):bool {
        $ret = true;
        $currentStock = '';

        if (isset($this->variant) && util::isGUID($this->variant->guid)) {
            $currentStock = intval($this->variant->stock);
        }

        if (config::stock() && is_numeric($currentStock)) {
            $futureStock = $currentStock - $newQuantity;

            if ($currentStock === 0 || $futureStock < 0) {
                $ret = true;
            } else {
                $ret = false;
            }
        } else {
            $ret = false;
        }

        return $ret;
    }
}