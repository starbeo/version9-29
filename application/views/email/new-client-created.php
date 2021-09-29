<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head><title>Bienvenue chez <?= get_option('companyname') ?></title></head>
    <body>
        <div style="max-width: 800px; margin: 0; padding: 30px 0;">
            <table width="80%" border="0" cellpadding="0" cellspacing="0">
                <tr>
                    <td width="5%"></td>
                    <td align="left" width="95%" style="font: 13px/18px Arial, Helvetica, sans-serif;">
                        <h2 style="font: normal 20px/23px Arial, Helvetica, sans-serif; margin: 0; padding: 0 0 18px; color: black;">Bienvenue chez <?= get_option('companyname') ?></h2>
                        <h4 style="font: normal 14px/16px Arial, Helvetica, sans-serif; margin: 0; padding: 0 0 18px; color: black;">Bonjour <b><?= $name ?></b></h4>
                        <p>Nous voulions juste vous souhaiter la <b>bienvenue</b>, votre compte est actif pour l'instant.</p>
                        <p>Vos identifiants :</p>
                        <p><b>Email :</b> <?= $email ?></p>
                        <p><b>Mot de passe :</b> <?= $password ?></p>
                        <p>Cliquez sur le lien ci-après pour être rediriger vers l'<b>Espace Client</b> : <a href="<?= base_url('authentication/client') ?>">Cliquer ici</a></p> 
                        <p>Pour toute information complémentaire, une équipe dédiée est à votre disposition au <?= get_option('company_phonenumber') ?> du Lundi au Samedi, de 9h à 18h.</p>
                        <p>Nous espérons que notre service MyColis / MyColis Mobile vous donnera entière satisfaction.</p>
                        <p>Veuillez agréer, Madame, Monsieur, l'expression de nos salutations distinguées.</p>
                        <p><span style="font-weight: bold; color: #F00;">N.B:</span> Par mesure de sécurité, lors de vos accès à notre site, vous êtes priés de vous assurer que vous êtes bien sur le bon site en vérifiant que l'URL commence bien par <a href="<?= base_url('authentication/client') ?>"><?= base_url('authentication/client') ?></a> au niveau de la barre d'adresse de votre navigateur.
                             En cas de doute, vous êtes priés de contacter notre Centre de Relation Client au numéro: <?= get_option('company_phonenumber') ?>.</p>
                        <p>En vous remerciant par avance.</p>
                        <br />
                        <p style="margin: 3px;">Cordialement,</p>
                        <p style="margin: 3px;">L’équipe du service client de <?= get_option('companyname') ?></p>
                        <p style="margin: 3px;">Email 1 : support@powercoursier.ma</p>
                        <p style="margin: 3px;">Email 2 : <?= get_option('smtp_email') ?></p>
                        <p style="margin: 3px;">Tél : <?= get_option('company_phonenumber') ?></p>
                        <br />
                        <br />
                        <center><img src="<?= logo_pdf_url() ?>" style="width: 50%; height: 60px;" /></center>
                        <br />
                        <br />
                        <center>Il s'agit d'un e-mail automatisé de <b><?= get_option('companyname') ?></b>, veuillez donc ne pas répondre à cet e-mail.</center>
                    </td>
                </tr>
            </table>
        </div>
    </body>
</html>
