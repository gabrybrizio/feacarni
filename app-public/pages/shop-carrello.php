<?php
    require __DIR__ . '/../../app/init.php';

    page::title(lang::get('cart'));

    require __DIR__ . '/../inc/header.php';

    $cart = cart::getCartByUser(user::guid());
?>

<div class="carrello">
    <div class="container">
        <?php
            if(!$cart || ($cart && count($cart->rows) === 0)):
        ?>
            <h1 class="t-center"><?=lang::get('cart-empty') ?></h1>
        <?php
            else:

            // Se il carrello è stato ripulito (prodotto o variante non più esistente, quantità richiesta non più disponibile)
            // segnalo l'utente
            if($cart->isChanged): ?>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    MODAL.open(LANG.get('cart-changed'));
                });
            </script>
            <?php endif; ?>

            <h1><?=lang::get('cart') ?></h1>
            <div class="ordine-carrello">
                <table>
                    <thead>
                        <tr>
                            <th class="t-center"><?=lang::get('article') ?></th>
                            <th>&#160;</th>
                            <th class="t-right"><?=lang::get('shop-unit-price') ?></th>
                            <th class="t-center"><?=lang::get('shop-quantity') ?></th>
                            <th class="t-right"><?=lang::get('shop-total-price') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                <?php

                        foreach($cart->rows as $row):
                            $articolo = $row->obj;
                            $articolo_data = module::dataOf($articolo);
                    ?>
                                    <tr class="art">
                                        <td class="art-image">
                                            <a href="<?=$articolo->url() ?>" style="background-image:url('<?= thumb::src($articolo->image('carne'), 500) ?>')"></a>
                                        </td>
                                        <td class="art-nome">
                                            <a href="<?=$articolo->url()  ?>"><?=$articolo->name() ?></a><br>
                                            <a onclick="SHOP.remove('<?=$articolo->GUID ?>', '<?=(isset($articolo->variant) ? $articolo->variant->guid : '') ?>')"><small><?=lang::get('remove') ?></small></a>
                                        </td>
                                        <td class="art-prezzo">
                                            <?=util::euro($row->price) ?>
                                        </td>
                                        <td class="art-qt">
                                            <a onclick="SHOP.update('<?=$articolo->GUID ?>','-','1',true, '<?=(isset($articolo->variant) ? $articolo->variant->guid : '') ?>');"><?=icon::get('minus-circle') ?></a>
                                            <input type="text" value="<?= $row->quantity; ?>" onblur="SHOP.update('<?=$articolo->GUID ?>','=',this.value,true, '<?=(isset($articolo->variant) ? $articolo->variant->guid : '') ?>');">
                                            <a onclick="SHOP.update('<?=$articolo->GUID ?>','+','1',true, '<?=(isset($articolo->variant) ? $articolo->variant->guid : '') ?>');"><?=icon::get('plus-circle') ?></a>
                                        </td>
                                        <td class="art-subtotale">
                                            <?=util::euro($row->totalPrice); ?>
                                        </td>
                                    </tr>
                <?php endforeach; ?>
                        </tobdy>
                    </table>
            </div>

            <div class="ordine-totale">

                <div class="totale-label">
                    <?=lang::get('shop-total-order') ?>:
                </div>

                <div class="totale-prezzo">
                    <?php
                        echo util::euro((user::isLogged() ? $cart->total : $cart->subtotal));
                    ?>
                </div>
            </div>

            <div class="ordine-info">
                <p>
                    Non pagherai nulla ora, inserisci i tuoi dati nel modulo sottostante e verrai ricontattato quanto prima.<br>
                    <b>IL PROSSIMO GIORNO DI CONSEGNA È IL <?= site::get('prossima-consegna');?></b>
                </p>
            </div>        

            <?php if(config::shop_digital_products() && !user::isLogged()): ?>
            <div id="ordine_form">            
                <form id="digitalOrderForm"  action="javascript:void(0);">
                    <div class="form-field is-half">
                        <label>Nome*:</label>
                        <input type="text" name="name" value=""/>
                    </div>
                    <div class="form-field is-half">
                        <label>Cognome*:</label>
                        <input type="text" name="surname" value=""/>
                    </div>                    
                    <div class="form-field is-half">
                        <label>Indirizzo*:</label>
                        <input type="text" name="address" value=""/>
                    </div> 
                    <div class="form-field is-half">
                        <label>CAP*:</label>
                        <input type="text" name="cap" value=""/>
                    </div>  
                    <div class="form-field is-half">
                        <label>Città*:</label>
                        <input type="text" name="city" value=""/>
                    </div>
                    <div class="form-field is-half">
                        <label>Provincia*:</label>
                        <input type="text" name="provincia" value=""/>
                    </div>                                                                            				
                    <div class="form-field is-half">
                        <label>Email*:</label>
                        <input type="text" name="email" value=""/>
                    </div>
                    <div class="form-field is-half">
                        <label>Telefono*:</label>
                        <input type="text" name="tel" value=""/>
                    </div>                    	
                    <div class="form-field">
                        <label>Messaggio:</label>
                        <textarea name="messaggio"></textarea>
                    </div>

                    <input type="text" name="contact" style="display:none !important" tabindex="-1" autocomplete="off">

                    <div class="form-field is-checkbox">
                        <input type="checkbox" id="privacy" name="privacy" value="true">
                        <label for="privacy"><?=lang::get('privacy-policy-agree') ?> <a href="" target="_blank"><?=lang::get('privacy-policy') ?></a> e nelle <a href="/condizioni-di-vendita/">Condizioni di vendita</a></label>
                    </div>

                    <div class="form-field is-checkbox">
                        <input type="checkbox" id="marketing" name="marketing" value="1">
                        <label for="marketing"><?=lang::get('privacy-advertising-agree') ?> <?=site::get('ragione-sociale'); ?></label>
                    </div>                    
                </form>
            </div> 
            <?php endif; ?>

            <?php
            if(user::isLogged() || config::shop_digital_products()):
            ?>
                <!-- PAYPAL -->
                <?php if(config::paypal()) : ?>
                    <div id="paypal_button" class="ordine-actions"></div>
                    <script src="https://www.paypal.com/sdk/js?client-id=<?=config::paypal_client_id()?>&currency=EUR&disable-funding=mybank,sofort"></script>
                <?php endif; ?>

                <?php if(!config::shop_digital_products()): ?>
                    <?php if(config::bank_transfer()) : ?>
                        <div class="t-center"><a onclick="SHOP.payWithBankTransferModal('<?=$cart->guid ?>');" class="btn"><?=lang::get('payment-method-bank-transfer') ?></a></div>
                    <?php endif; ?>

                    <?php if(config::cash_on_delivery()) : ?>
                        <div class="t-center"><a onclick="SHOP.payWithCashOnDeliveryModal('<?=$cart->guid ?>');" class="btn"><?=lang::get('payment-method-cash-on-delivery') ?></a></div>
                    <?php endif; ?>
                <?php endif; ?>

                <div class="t-center" style="margin-top:30px">
                    <a onclick="SHOP.onlyEmail('<?=$cart->guid ?>');" class="btn">Invia ordine</a>
                </div>

                <?php if(config::satispay()) : ?>
                    <?php
                        $paymentId = satispay::createPaymentId($cart->total * 100);
                    ?>
                    <div class="ordine-actions">
                        <img id="pay-with-satispay" src="https://online.satispay.com/images/en-pay-red.svg" alt="Pay with Satispay" style="height: 50px; cursor: pointer;" />
                        <script src="https://<?=(config::satispay_sandbox() ? "staging." : "") ?>online.satispay.com/web-button.js"></script>
                        <script>
                            let satispay = SatispayWebButton.configure({
                                paymentId: '<?=$paymentId ?>',
                                completed: function(data) {
                                    SHOP.payWithSatispay('<?=$paymentId ?>', '<?=$cart->guid ?>');
                                }
                            });

                            document.getElementById('pay-with-satispay').addEventListener('click', function(e) {
                                <?php if(config::shop_digital_products()): ?>
                                    LOADING.start();
                                    SHOP.saveDigitalOrderData('digitalOrderForm')
                                    .then(function() {
                                        MODAL.close();
                                        e.preventDefault();
                                        satispay.open();
                                    }).catch(function(error) {
                                        MODAL.open(error.message);
                                    });
                                <?php else: ?>
                                    e.preventDefault();
                                    satispay.open();
                                <?php endif; ?>
                            })
                        </script>
                    </div>
                <?php endif; ?>

            <?php else: ?>
                <!-- LOGGATI -->
                <div class="ordine-actions">
                    <a href="/accedi/" class="btn"><?=lang::get('shop-login-to-purchase') ?></a>
                </div>
            <?php endif; ?>
    <?php endif; ?>
    </div>
</div>

<?php
    if($cart && (user::isLogged() || config::shop_digital_products())):
?>
    <?php if(config::paypal()): ?>
        <script>
            paypal.Buttons({
                createOrder: function(data, actions) {
                    return API('paypalCreateOrder').then(function(data) {
                        return data.id;
                    }).catch(error =>
                        MODAL.open(LANG.get('warning-try-later'))
                    );
                },
                onClick: function(data, actions) {
                    <?php if(config::shop_digital_products()): ?>
                        LOADING.start();
                        return SHOP.saveDigitalOrderData('digitalOrderForm').then(function() {
                            paypal_button.classList.add("is-hidden");
                            return actions.resolve();
                        }).catch(function(error) {
                            MODAL.open(error.message);
                            return actions.reject();
                        });
                    <?php else: ?>
                        paypal_button.classList.add("is-hidden");
                        LOADING.start();
                    <?php endif; ?>
                },
                onCancel: function (data) {
                    MODAL.open(LANG.get('interruption'));
                    paypal_button.classList.remove("is-hidden");
                },
                onApprove: function(data, actions) {
                    MODAL.open(LANG.get('loading'), "", true);

                    return API('paypalExecutePayment', {
                        'paypalOrderId': data.orderID
                    }).then(function(data) {
                        if(data === true) {
                            // Pagamento riuscito
                            MODAL.open(LANG.get('shop-order-ok'), "ordine-ok");
                        } else if (data.trim() == 'OKBUTNOEMAIL') {
                            MODAL.open(LANG.get('shop-order-ko-email'), "ordine-ok");
                        } else {
                            throw new Error();
                        }
                    }).catch(error =>
                        MODAL.open(LANG.get('warning-try-later'))
                    );
                }
            }).render('#paypal_button');

        </script>
    <?php endif; ?>
<?php endif; ?>

<?php
require __DIR__ . '/../inc/footer.php';
?>