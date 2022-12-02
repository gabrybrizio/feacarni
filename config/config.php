<?php



return [

	'version' => '0.2',

	'debug' => true,

	'debug_email' => 'info@grafichevincenti.it',

	'maintenance' => false,

	'landing' => 'articoli-list.php',



	'paypal' => false,



	// sandbox

	'paypal_sandbox' => true,

	'paypal_client_id' => 'AeuNKVghcvN7YYOo43z8PrW9NbtcLDnLiBJ5LEReZ5zFgbMAhbFFP0x8DKtCD3AN_mrzCBwv6x9N-O2t', //sandbox

	'paypal_client_secret' => 'ECIHwQrJJ7Nr-m_VK9KKFWUxDzlOwYI6MTG_LZ9JKMtpQuDpHjhmHaVPehKE6wipMae2_4UN16RFG_m2', //sandbox



	// prod

	//'paypal_sandbox' => false,

	//'paypal_client_id' => '',

	//'paypal_client_secret' => '',



	'satispay' => false,



	// sandbox

	'satispay_sandbox' => true,

	'satispay_public_key' => "-----BEGIN PUBLIC KEY-----\nMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAxFY4KwTKUNg68WskkMXp\nV3HveAkZw/QeViV1iRaXg2WPGzxsXl5Nlb0ThXoJOkGHPtkg0SW9oTBF7fFOz7XO\njyba4yhZv41a9ntgJXnedXxPe+oJ5G44IRpCu4KWIw87bM4iX1KklTmWZ1etTeQ7\nVlsJP4ln0kQ6GAKvKuZyjiCsnCQeG+YVGJm6RajL2307ZgQS4t962OpG4QgMY+N6\ndAeQdT+DCfUaLcyN4a+8uO0PKNbPB+wfcePs5myf/AS4dsv3p7Ap1P7NJncFCPyq\nXilh234wBgPug1M9aLkONexfnDBB//nDGl33qKIbv/zoKtX13rHu6Dir9LX23sy9\nowIDAQAB\n-----END PUBLIC KEY-----\n",

	'satispay_private_key' => "-----BEGIN PRIVATE KEY-----\nMIIEvwIBADANBgkqhkiG9w0BAQEFAASCBKkwggSlAgEAAoIBAQDEVjgrBMpQ2Drx\naySQxelXce94CRnD9B5WJXWJFpeDZY8bPGxeXk2VvROFegk6QYc+2SDRJb2hMEXt\n8U7Ptc6PJtrjKFm/jVr2e2Aled51fE976gnkbjghGkK7gpYjDztsziJfUqSVOZZn\nV61N5DtWWwk/iWfSRDoYAq8q5nKOIKycJB4b5hUYmbpFqMvbfTtmBBLi33rY6kbh\nCAxj43p0B5B1P4MJ9RotzI3hr7y47Q8o1s8H7B9x4+zmbJ/8BLh2y/ensCnU/s0m\ndwUI/KpeKWHbfjAGA+6DUz1ouQ417F+cMEH/+cMaXfeoohu//Ogq1fXese7oOKv0\ntfbezL2jAgMBAAECggEAFW0TVB6BtbDjPdVjeCkg/doBOChGF4xcgyozWXKNtlku\n9NnzOZkw/lZT+w0GVMUx7Tnkyu5Dc4PBHeZno2ND13t2B7Qvlyfl9WAhbfWOwWN/\niRZRXgxpM7OZ9LybhdfztDwKpDxwmZhfQYff8dAmaZYlvDlSiy/G5zfjxdrARaI5\n7TTkakP7C3t9u4TuF2OLAiPCiTvOa4W2k1JkzFud+JXPzwagNL0nzs5Mg1iRGJkg\nn5L4YSHCuv7q9fWRXFuG31YiOak1JX+oHCp/Nnag3/7hmZzxdXzSNSEItg5JGoQG\nbWBAdz/jZ3Yqd7wdln8AqMZuNtSGEl+LFUW4RXNygQKBgQD8aAci5LNzvCKVExAF\nkrrpOL0XZTApq58qpXS/wn76kfZ9XAHrK2PAUJdqzNZOIIsR5zq+xNSL8G4+1a2V\n0zg+I6gcFx4GwLDVrbCfcNXHbbukAj1MD3fzvlfAas1miAbJHezYBXmHO4GCNlNh\nCfWs0Sk5Z2YGKop10DI2TxyY5wKBgQDHIdQwgev/Tent94M9if05WNja9EtkYvrb\nMYSw5hpAKYUIneDXmxUyfets5tnYgRvJ+VHmTsGIKuwEXp2Ph/rAYP4AzEu8nG/X\njLn2dG9bixF6ibMJGh2+kMABCI0wPb/BZWXaiIUBw5n0qZ/c+uAIGdm62I8G2Pgu\nhms2tjZx5QKBgQCOMij7Dm7On8m3xDJ9gUlNhIp6uKXmg+o6g1X/hho1HbAItaHb\nE+mxeXWkVFvTTeArKOJegNLhN4VUXtrZElKYJ0xY7ukaksnMx3o+iLnXDUntvtk7\nlWdVaQFUxJaM0ctI2FQK6yKo/ewbKHHWtF60im04Crlede7lKnJvSChrNwKBgQCl\nAdE0iNQn8EYh3HGx8sAUXx0DAl/exW4daAEOHP4voLgOFdUUk/uSpWkKCpkFoKaI\nTMKi1yvjS73bORPStdkAxN51htRTbEX5FTGbmYwVDT3kjmn+5tcy6/tOX+muydw1\nw3INDgc5GP5gEJbZpEE0NUeIXp7WC8BLvTLmWULwSQKBgQDIVsOpdac1X9FlFoNE\nmYJ2tE9sx5RVPPz/d55O6X2CNJtyuymwjBO8PO6npzS54UV8edbAHWMsWgtLndK+\nbl3vdAk2mPdoOmNUwtKg1RBJWbri89Xyfr06/ul+7d1fJR6cXhNwas7+7UsXmTsL\nMsICtQz+w9Aw8z8hbXvdjFn/fQ==\n-----END PRIVATE KEY-----\n",

	'satispay_key_id' => "67chit4su358tlu41ersnv6m9d866nfmderh2kjj5i47s15vdr081m0ct48kq4lsl924jlb87t8gsnqbkp93b1a4h4n1s8lp7f3i07kcrift2vnlqe0esjpfvijp3l2qoed4i6suvlght5gp6i7ekd5ncchhbskf8073rgnpd0io7q7cfvqqjrpedj4f8896s2hakvad",



	// prod

	//'satispay_sandbox' => false,

	//'satispay_public_key' => "",

	//'satispay_private_key' => "",

	//'satispay_key_id' => "",



	'bank_transfer' => false,

	'cash_on_delivery' => false,



	'email_error_reporting' => false,



	'stock' => false, // se true utilizza il campo 'stock' salvato negli articoli



	// i campi che discriminano le varianti

	'variant_options' => '',



	'language_default' => 'it',

	'language_show_in_url' => false, // se true visualizza la lingua negli url es. sito.it/en/articoli



	'mail_host' => "smtps.aruba.it",

	'mail_username' => "info@feacarni.it",

	'mail_password' => "7Vr7XtrTp!.eszv",



	//'mail_host' => "mail.moloc.net",

	//'mail_username' => "noreply@moloc.net",

	//'mail_password' => "l5l@9*XcGv+*",		



	'db_type' => "sqlite", // sqlite|mysql

	'db_host' => "",

	'db_name' => "db.sqlite", //se sqlite indicare il nome del db nella cartella app-data

	'db_user' => "",

	'db_password' => "",



	// vedi https://github.com/eiconweb/how-to/blob/main/facebook-post.md

	'facebook_page_id' => '114108427589453', // test page: 114108427589453 - miriorama: 360630651025158

	'facebook_app_id' => '188924433170360', // Dorothy Eicon (profilo): Dorothy (developer app)

	'facebook_app_secret' => '70211ec0d47268921b32f00a1fd69098', // Dorothy Eicon (profilo): Dorothy (developer app)

	'facebook_access_token' => 'EAACr02W6f7gBAOlOrZAxgAspSwXki82VDhYcjaham6YSHoCxHc5uzptY4XAAXJt3mDoGeKsUrK53PjPatahpMNhciHQOUKZCOEo3u3fLSalp0FEyVwQ6Bb5Kk7v1jVx34GZBrRQWcmwklAZBKVZBAoV1WDdtWgsvB7r6hmLnqeZCtZBruM4McBj',



	// cifra minima per spese spedizione gratuite account privati/personal

	'free_shipping_minimum' => 19,

	// costo spese spedizione sotto la soglia free_shipping_minimum

	'basic_shipping_cost' => 4.99,



	// cifra minima per spese spedizione gratuite account rivenditori/business

	'free_shipping_minimum_business' => 199,

	// costo spese spedizione sotto la soglia free_shipping_minimum_business

	'basic_shipping_cost_business' => 5.99,



	// Se true configura i prodotti come download digitali.

	// Utilizza il campo "digitalDownload"

	'shop_digital_products' => true

];