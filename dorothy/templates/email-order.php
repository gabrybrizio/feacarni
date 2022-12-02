<?php if ($indirizzo_spedizione && $indirizzo_fatturazione): ?>
<!--[if mso | IE]><table align="center" border="0" cellpadding="0" cellspacing="0" class="" style="width:600px;" width="600" ><tr><td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;"><![endif]-->
<div style="background:#f0f2f5;background-color:#f0f2f5;margin:0px auto;max-width:600px;">
        <table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="background:#f0f2f5;background-color:#f0f2f5;width:100%;">
            <tbody>
                <tr>
                    <td style="border-top:1px solid #cccccc;direction:ltr;font-size:0px;padding:20px 0;padding-bottom:20px;padding-top:20px;text-align:center;">
                        <!--[if mso | IE]><table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td class="" style="vertical-align:middle;width:300px;" ><![endif]-->
                        <div class="mj-column-per-50 mj-outlook-group-fix" style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:middle;width:100%;">
                            <table border="0" cellpadding="0" cellspacing="0" role="presentation" style="vertical-align:middle;" width="100%">
                                <tr>
                                    <td align="left" style="font-size:0px;padding:10px 25px;padding-left:25px;word-break:break-word;">
                                        <div style="font-family:open Sans Helvetica, Arial, sans-serif;font-size:15px;line-height:19px;text-align:left;color:#333333;">
                                            <b><?=lang::get('account-shipping') ?></b><br /><?=$email_nominativo ?><br />
                                            <?=$indirizzo_spedizione ?></div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <!--[if mso | IE]></td><td class="" style="vertical-align:middle;width:300px;" ><![endif]-->
                        <div class="mj-column-per-50 mj-outlook-group-fix" style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:middle;width:100%;">
                            <table border="0" cellpadding="0" cellspacing="0" role="presentation" style="vertical-align:middle;" width="100%">
                                <tr>
                                    <td align="left" style="font-size:0px;padding:10px 25px;padding-left:25px;word-break:break-word;">
                                        <div style="font-family:open Sans Helvetica, Arial, sans-serif;font-size:15px;line-height:19px;text-align:left;color:#333333;">
                                            <b><?=lang::get('account-billing') ?></b><br /><?=$email_nominativo ?><br />
                                            <?=$indirizzo_fatturazione ?>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <!--[if mso | IE]></td></tr></table><![endif]-->
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
<!--[if mso | IE]></td></tr></table><![endif]-->
<?php endif; ?>
<!--[if mso | IE]><table align="center" border="0" cellpadding="0" cellspacing="0" class="" style="width:600px;" width="600" ><tr><td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;"><![endif]-->
    <div style="background:#cccccc;background-color:#cccccc;margin:0px auto;max-width:600px;">
        <table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="background:#cccccc;background-color:#cccccc;width:100%;">
            <tbody>
                <tr>
                    <td style="direction:ltr;font-size:0px;padding:20px 0;padding-bottom:10px;padding-top:10px;text-align:center;">
                        <!--[if mso | IE]><table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td class="" style="vertical-align:middle;width:600px;" ><![endif]-->
                        <div class="mj-column-per-100 mj-outlook-group-fix" style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:middle;width:100%;">
                            <table border="0" cellpadding="0" cellspacing="0" role="presentation" style="vertical-align:middle;" width="100%">
                                <tr>
                                    <td align="left" style="font-size:0px;padding:10px 25px;padding-left:25px;word-break:break-word;">
                                        <div style="font-family:open Sans Helvetica, Arial, sans-serif;font-size:18px;line-height:18px;text-align:left;color:#333333;">
                                            <b><?=lang::get('shop-order') ?></b></div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <!--[if mso | IE]></td></tr></table><![endif]-->
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <!--[if mso | IE]></td></tr></table><table align="center" border="0" cellpadding="0" cellspacing="0" class="" style="width:600px;" width="600" ><tr><td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;"><![endif]-->
    <div style="background:#f0f2f5;background-color:#f0f2f5;margin:0px auto;max-width:600px;">
        <table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="background:#f0f2f5;background-color:#f0f2f5;width:100%;">
            <tbody>
                <tr>
                    <td style="direction:ltr;font-size:0px;padding:20px;text-align:center;">
                        <!--[if mso | IE]><table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td class="" style="vertical-align:top;width:560px;" ><![endif]-->
                        <div class="mj-column-per-100 mj-outlook-group-fix" style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%;">
                            <table border="0" cellpadding="0" cellspacing="0" role="presentation" style="vertical-align:top;" width="100%">
                                <tr>
                                    <td align="left" style="background:#ffffff;font-size:0px;padding:15px;word-break:break-word;">
                                        <table cellpadding="0" cellspacing="0" width="100%" border="0" style="color:#000000;font-family:Ubuntu, Helvetica, Arial, sans-serif;font-size:15px;line-height:20px;table-layout:auto;width:100%;border:none;">
                                            <tr style="border-bottom:1px solid #ecedee;text-align:left;padding:15px 0;">
                                                <th style="padding: 0 15px 0 0;"><?=lang::get('products') ?></th>
                                                <th style="padding: 0 15px;" align="right"><?=lang::get('shop-unit-price') ?></th>
                                                <th style="padding: 0 0 0 15px;" align="right"></th>
                                            </tr>
                                            <?=$html_ordine_prodotti ?>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="left" style="background:#ffffff;font-size:0px;padding:15px;word-break:break-word;">
                                        <table cellpadding="0" cellspacing="0" width="100%" border="0" style="color:#000000;font-family:Ubuntu, Helvetica, Arial, sans-serif;font-size:15px;line-height:20px;table-layout:auto;width:100%;border:none;">
                                            <?=$html_ordine_totali ?>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <!--[if mso | IE]></td></tr></table><![endif]-->
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <!--[if mso | IE]></td></tr></table><![endif]-->