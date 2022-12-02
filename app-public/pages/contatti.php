<?php     require __DIR__ . '/../../app/init.php';    page::title(lang::get('contact'));    require __DIR__ . '/../inc/header.php';?><div class="contacts">    <div class="contact-content">        <div class="contact-content-content" data-aos="zoom-in-down">            <h2 class="section-title ff-secondary text-start text-primary fw-normal">Sede Legale</h2>            Fea Carni è un marchio<br>            <b>di Antica Salumeria snc.</b><br>            <?= site::get('indirizzo'); ?> |            <?= site::get('citta'); ?> (<?= site::get('provincia'); ?>) Italia            <br><br>            <b>P.IVA</b> <?= site::get('partita-iva'); ?><br>            <b>Mobile:</b> <a href="tel:<?=util::tel(site::get('telefono')); ?>"><?= site::get('telefono'); ?></a>            <br><br>            <b><a href="mailto:<?= site::get('email'); ?>"><?= site::get('email'); ?></a></b><br><br>        </div>    </div>    <div class="contact-form-wrapper">        <h1 class="section-title ff-secondary text-start text-primary fw-normal" data-aos="zoom-in-down">Scrivici</h1>        <p data-aos="zoom-in-down">            Compila il seguente modulo, verrai ricontattato al più presto!<br>            <b>Tutti i campi sono obbligatori.</b>        </p>        <br><br>        <form id="contact_form" action="javascript:void(0);" method="post">            <div class="form-field" data-aos="zoom-in-down">                <label>Nome:</label>                <input type="text" name="nome" value=""/>            </div>				            <div class="form-field" data-aos="zoom-in-down">                <label>Email:</label>                <input type="text" name="email" value=""/>            </div>	            <div class="form-field" data-aos="zoom-in-down">                <label>Messaggio:</label>                <textarea name="messaggio"></textarea>            </div>            <br>            <input type="hidden" name="csrf" value="<?= csrf() ?>">             <input type="text" name="contact_me" style="display:none !important" tabindex="-1" autocomplete="off">             <div class="form-field is-checkbox" data-aos="fade-left">                <input type="checkbox" id="privacy" name="privacy" value="accetto">                <label for="privacy"><?=lang::get('privacy-policy-agree') ?> <a href="" target="_blank"><?=lang::get('privacy-policy') ?></a></label>            </div>            <div class="form-field is-checkbox" data-aos="fade-left">                <input type="checkbox" id="marketing" name="marketing" value="1">                <label for="marketing"><?=lang::get('privacy-advertising-agree') ?> <?=site::get('ragione-sociale'); ?></label>            </div>            <div id="form_feedback"></div>            <br>            <a class="pul">                invia                <p></p>                <p></p>                <p></p>                <p></p>            </a>            <!--<a id="btn_send" href="javascript:void(0)" class="btn"  onclick="FORM.send()">INVIA</a>-->        </form>    </div></div>    <div class="section-title2">        <br>        <h2 class="section-title ff-secondary text-start text-primary fw-normal" data-aos="zoom-in-up">Dove ci potete trovare?</h2>        <br>        <div class="world" data-aos="zoom-in-up">            <iframe style= "width: 100%; height:500px; border-radius: 25px;" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d7151.832178709903!2d7.624797976830307!3d44.556834063485425!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x12cd5b289e12a279%3A0x679cce89691e31ed!2sVia%20Magliano%2C%203%2C%2012038%20Levaldigi%20CN!5e1!3m2!1sit!2sit!4v1669480747341!5m2!1sit!2sit" frameborder="0" allowfullscreen></iframe>        </div>        <br>        <div class="world2">            <div style="justify-content: center; color: #d7d7d7;" class="d-flex align-items-center border-start border-5  px-3" data-aos="fade-right">                <h1 class="titolo2 flex-shrink-0 display-5 text-primary mb-0" data-toggle="counter-up"><ion-icon size="large" name="location-outline"></ion-icon></h1>                <div class="ps-4">                    <h6 class="mb-0 heebo">Via Magliano 3/a, Levaldigi (CN), Italia 12038</h6>                </div>            </div>            <br>            <div style="justify-content: center; color: #d7d7d7;" class="d-flex align-items-center border-start border-5  px-3" data-aos="fade-left">                <h1 class="titolo2 flex-shrink-0 display-5 text-primary mb-0" data-toggle="counter-up"><ion-icon size="large" name="location-outline"></ion-icon></h1>                <div class="ps-4">                    <h6 class="mb-0 heebo">Viale 25 Aprile 2, Pietra Ligure (SV), Italia 17027</h6>                </div>            </div>            <br>            <div  style="justify-content: center; color: #d7d7d7;" class="d-flex align-items-center border-start border-5  px-3" data-aos="fade-right">                <h1 class="titolo2 flex-shrink-0 display-5 text-primary mb-0" data-toggle="counter-up"><ion-icon size="large" name="location-outline"></ion-icon></h1>                <div class="ps-4">                    <h6 class="mb-0 heebo">Effettuiamo consegne a domicilio, Milano (MI), Italia 20100</h6>                </div>            </div>        </div>    </div><!--<div class="contact-map">    <iframe width='100%' height='100%' src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2843.026200111492!2d7.620142715744607!3d44.55556800162876!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x12cd5b2620bda7d7%3A0x10fca4015922cd8c!2sVia%20Magliano%2C%203A%2C%2012038%20Levaldigi%20CN!5e0!3m2!1sit!2sit!4v1625662678944!5m2!1sit!2sit" style="border:0;" allowfullscreen="" loading="lazy"></iframe></div>--><script>    m_contatti.classList.add("is-active");</script><?php require __DIR__ . '/../inc/footer.php';?>