<!DOCTYPE html>
<html lang="en">
    <head>
        <title><?= $title ?></title>
        <meta charset="utf-8">
        <meta name="author" content="<?= $company_name ?>">
        <meta name="description" content="<?= $company_name ?>"/>
        <!-- CSS -->
        <link rel="stylesheet" type="text/css" href="<?= base_url() ?>assets/css/coming-soon/main.css">
    </head>
    <body class="no-margin">
        <div class="bgimg">
            <div class="topleft">
                <img src="<?= logo_pdf_url() ?>" class="logo" />
            </div>
            <div class="middle">
                <h1><?= $company_name ?></h1>
                <h2><?= $message ?></h2>
                <?= render_input_hidden('date_time', 'date_time', date('d/m/Y H:i')) ?>
                <?= render_input_hidden('date_start', 'date_start', $date_start) ?>
                <?= render_input_hidden('date_end', 'date_end', $date_end) ?>
                <h4><?= _l('start') ?> : <?= $date_start ?></h4>
                <h4><?= _l('end') ?> : <?= $date_end ?></h4>
                <h4><?= _l('duration') ?> : <span id="duration"></span></h4>
                <h4><?= _l('rest') ?> : <span id="rest"></span></h4>
            </div>
            <div class="bottomleft">
                <p><?= _l('thank_you_for_your_patience') ?></p>
            </div>
        </div>  
    </body>
    <script src="<?= site_url(); ?>bower_components/jquery/dist/jquery.min.js"></script>
    <script>
        $(document).ready(function () {
            calcule_duration($('input[id="date_start"]').val(), $('input[id="date_end"]').val(), $('#duration'));
            calcule_duration($('input[id="date_time"]').val(), $('input[id="date_end"]').val(), $('#rest'));
        });
        // Différence entre date
        function calcule_duration(dateStart, dateEnd, element) {
            var result = difference_entre_date(dateStart, dateEnd);
            var duration = '';
            var check = false;
            if (result.day > 0) {
                duration += result.days + ' jour ';
                check = true;
            }
            if (result.hour > 0) {
                if (check) {
                    duration += 'et ';
                }
                duration += result.hour + ' heure';
                if (result.hour > 1) {
                    duration += 's ';
                } else {
                    duration += ' ';
                }
            }
            if (result.min > 0) {
                if (check) {
                    duration += 'et ';
                }
                duration += result.min + ' minute';
                if (result.min > 1) {
                    duration += 's ';
                } else {
                    duration += ' ';
                }
            }
            element.html(duration);
        }
        // Différence entre date
        function difference_entre_date(dateDebut, dateFin) {
            // Initialisation du retour
            var diff = {};
            // Formatted date
            var arrayDateDebut = dateDebut.split("/");
            var dateDebutFormatted = arrayDateDebut[1] + '/' + arrayDateDebut[0] + '/' + arrayDateDebut[2];
            var arraydateFin = dateFin.split("/");
            var dateFinFormatted = arraydateFin[1] + '/' + arraydateFin[0] + '/' + arraydateFin[2];

            var dateDebutFormatted = new Date(dateDebutFormatted);
            var dateFinFormatted = new Date(dateFinFormatted);

            var tmp = dateFinFormatted - dateDebutFormatted;

            // Nombre de secondes entre les 2 dates
            tmp = Math.floor(tmp / 1000);
            // Extraction du nombre de secondes
            diff.sec = tmp % 60;

            // Nombre de minutes (partie entière)
            tmp = Math.floor((tmp - diff.sec) / 60);
            // Extraction du nombre de minutes
            diff.min = tmp % 60;

            // Nombre d'heures (entières)
            tmp = Math.floor((tmp - diff.min) / 60);
            // Extraction du nombre d'heures
            diff.hour = tmp % 24;

            // Nombre de jours restants
            tmp = Math.floor((tmp - diff.hour) / 24);
            diff.day = tmp;

            return diff;
        }
    </script>  
</html>