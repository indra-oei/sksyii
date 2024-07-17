<?php
    use app\assets\AppAsset;
    use yii\helpers\Html;

    AppAsset::register($this);
?>

<?php $this->beginPage() ?>

<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
        <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <?= Html::csrfMetaTags() ?>

        <title><?= Html::encode($this->title) ?></title>
		<link rel="shortcut icon" href="" type="image/x-icon">

        <?php $this->head() ?>
    </head>
    <body class="my-login-page">
        <?php $this->beginBody() ?>

        <div class="wrap">
            <div class="container">
                <?= $content ?>
            </div>
        </div>

        <!-- Start Footer -->
        <?= $this->render('@app/views/layouts/footer.php') ?>
        <!-- End Footer -->

        <?php $this->endBody() ?>
    </body>
</html>
<?php $this->endPage() ?>