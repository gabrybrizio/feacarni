<?php

class Variant extends Obj {
	public function __construct() {
    }

	public function price($user = null) {
        $ret = null;

        $userData = null;
        $userType = null;
        if ($user) {
            $userData = json_decode($user->data, true);
            $userType = a::get($userData, 'tipologia');
        }

		if($userType === user::TYPE_BUSINESS) {
			$price = $this->priceBusiness;
			$ret = (float)str::replace($price,',','.');
		} else {
			$price = $this->pricePrivate;
			$ret = (float)str::replace($price,',','.');
		}

        return $ret;
	}

	/**
	 * Restituisce l'elenco dei valori delle opzioni della variante.
	 * Es. "Rossa / Cotone / S"
	 *
	 * @param  string $optionNames separati da virgola
	 * @param  mixed $separator
	 * @return string
	 */
	public function getOptionsValue(string $optionNames, ?string $separator = ' / '):string {
		$variantOptions = explode(',', $optionNames);
		$variantValues = [];

		foreach ($variantOptions as $option) {
			if (isset($this->{$option})) {
				$variantValues[] = $this->{$option};
			}
		}

		return implode($separator, $variantValues);
	}
}