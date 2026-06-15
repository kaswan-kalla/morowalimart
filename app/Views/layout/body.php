<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <title><?= $judul ?? ''; ?></title>

    <!-- Favicon -->
    <link rel="icon" href="<?= base_url('asset/pavicon.ico') ?>" type="image/x-icon">

    <!-- CSS -->
    <link href="<?= asset_url('asset/css/bootstrap.css'); ?>" rel="stylesheet">
    <link href="<?= asset_url('asset/css/bootstrap-datepicker3.min.css'); ?>" rel="stylesheet">
    <link href="<?= asset_url('asset/css/slidebars.css'); ?>" rel="stylesheet">
    <link href="<?= asset_url('asset/css/sweetalert2.min.css'); ?>" rel="stylesheet">
    <link href="<?= asset_url('asset/jtable/themes/lightcolor/gray/jtable.min.css'); ?>" rel="stylesheet">
    <link href="<?= asset_url('asset/css/jquery-ui.min.css'); ?>" rel="stylesheet">
    <link href="<?= asset_url('asset/css/bootstrapSelect.css'); ?>" rel="stylesheet">
    <link href="<?= asset_url('asset/css/my_style.css'); ?>" rel="stylesheet">
    <link href="<?= asset_url('asset/css/bootstrap-editable.css'); ?>" rel="stylesheet">
    <link href="<?= asset_url('asset/jquery-flexdatalist-2.3.0/jquery.flexdatalist.min.css'); ?>" rel="stylesheet">
    <link href="<?= asset_url('asset/css/select2.min.css'); ?>" rel="stylesheet">
    <link href="<?= asset_url('asset/css/photoviewer.min.css'); ?>" rel="stylesheet">
    <script>
        function base_url(str = '') {
            return "<?= base_url() ?>/" + str;
        }
    </script>
    <script src="<?= asset_url('asset/js/my_function.js'); ?>"></script>
    <script src="https://cdn.jsdelivr.net/npm/mermaid/dist/mermaid.min.js"></script>
    <script>
        mermaid.initialize({
            startOnLoad: true
        });
    </script>
</head>

<body>

    <?php
    $content = $content ?? false;
    if ($content === 'Login') {
        echo $this->include('Login/index');
    } else {
        echo $this->include('layout/sidebar');
    }

    echo $this->renderSection('content');
    ?>

    <!-- JS -->
    <script src="<?= asset_url('asset/js/jquery-3.3.1.js'); ?>"></script>
    <script src="<?= asset_url('asset/js/jquery-ui.min.js'); ?>"></script>
    <script src="<?= asset_url('asset/js/jquery-form.js'); ?>"></script>
    <script src="<?= asset_url('asset/js/solid.js'); ?>"></script>
    <script src="<?= asset_url('asset/js/sweetalert2.min.js'); ?>"></script>
    <script src="<?= asset_url('asset/js/date-time-picker.min.js'); ?>"></script>
    <script src="<?= asset_url('asset/js/fontawesome.min.js'); ?>"></script>
    <script src="<?= asset_url('asset/jtable/jquery.jtable.js'); ?>"></script>
    <script src="<?= asset_url('asset/js/popper.js'); ?>"></script>
    <script src="<?= asset_url('asset/js/bootstrap.js'); ?>"></script>
    <script src="<?= asset_url('asset/js/bootstrap-datepicker.min.js'); ?>"></script>
    <script src="<?= asset_url('asset/js/velocity.min.js'); ?>"></script>
    <script src="<?= asset_url('asset/js/velocity.ui.js'); ?>"></script>
    <script src="<?= asset_url('asset/js/jquery.floatThead.js'); ?>"></script>
    <script src="<?= asset_url('asset/js/printThis.js'); ?>"></script>
    <script src="<?= asset_url('asset/js/bootstrapSelect.js'); ?>"></script>
    <script src="<?= asset_url('asset/js/bootstrap-editable.js'); ?>"></script>
    <script src="<?= asset_url('asset/js/easy.qrcode.min.js'); ?>"></script>
    <script src="<?= asset_url('asset/js/bower_components/mark.js/dists/jquery.mark.es6.js'); ?>"></script>
    <script src="<?= asset_url('asset/jquery-flexdatalist-2.3.0/jquery.flexdatalist.min.js'); ?>"></script>
    <script src="<?= asset_url('asset/js/moment-with-locales.min.js'); ?>"></script>
    <script src="<?= asset_url('asset/js/easy-number-separator.js'); ?>"></script>
    <script src="<?= asset_url('asset/js/jquery.caret.js'); ?>"></script>
    <script src="<?= asset_url('asset/js/clipboard.min.js'); ?>"></script>
    <script src="<?= asset_url('asset/jqBarGraph/jqBarGraph.1.1.js'); ?>"></script>
    <script src="<?= asset_url('asset/js/html2canvas.js'); ?>"></script>
    <script src="<?= asset_url('asset/js/select2.min.js'); ?>"></script>
    <script src="<?= asset_url('asset/js/photoviewer.min.js'); ?>"></script>
    <script src="<?= asset_url('asset/js/terbilang.js'); ?>"></script>
    <!-- <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script> -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
    <script src="<?= asset_url('asset/js/ChartHelper.js'); ?>"></script>
    <?= $this->include('js_helper') ?>

    <?php
    // JS Dinamis berdasarkan $content dan $script
    if (isset($script)) {
        foreach ($script as $s) {
            echo '<script src="' . asset_url("asset/js/view/{$content}/{$s}.js") . '"></script>';
        }
    }

    // Main view script
    echo '<script src="' . asset_url("asset/js/view/{$content}.js") . '"></script>';


    echo '<script src="' . asset_url('asset/js/my_script.js') . '"></script>';
    echo '<script src="' . asset_url('asset/js/my_test_input.js') . '"></script>';

    // Modal scripts
    if (isset($modal)) {
        foreach ($modal as $m) {
            echo '<script src="' . asset_url("asset/js/modal/{$m}.js") . '"></script>';
        }
    }
    ?>

</body>

</html>