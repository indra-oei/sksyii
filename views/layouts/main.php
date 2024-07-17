<?php
    use app\assets\AppAsset;
    use yii\helpers\Html;

    AppAsset::register($this);
?>

<?php $this->beginPage() ?>

<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= Html::encode($this->title) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.30.1/moment.min.js"></script>
    <?= Html::csrfMetaTags() ?>
    <link rel="shortcut icon" href="" type="image/x-icon">
    <?php $this->head() ?>
</head>
<body>
    <?php $this->beginBody() ?>
    <header class="header">
        <div class="container-fluid">
            <div class="headerWrap">
                <div class="headerLogo">
                    <p class="m-0">SKSYII</p>
                    <small><?= Yii::$app->session->get('contactName') ?></small>
                </div>
                <nav class="navLinks">
                    <ul>
                        <li><a href="<?= Yii::$app->getUrlManager()->createUrl('user') ?>">User</a></li>
                        <li><a href="<?= Yii::$app->getUrlManager()->createUrl('category') ?>">Category</a></li>
                        <li><a href="<?= Yii::$app->getUrlManager()->createUrl('sub-category') ?>">Subcategory</a></li>
                        <li><a href="<?= Yii::$app->getUrlManager()->createUrl('item') ?>">Item</a></li>
                        <li><a href="<?= Yii::$app->getUrlManager()->createUrl('order') ?>">Order</a></li>
                        <li><a href="<?= Yii::$app->getUrlManager()->createUrl('report') ?>">Report</a></li>
                    </ul>
                </nav>
                <div>
                    <a id="btnLogout" class="text-danger">Log Out</a>
                </div>
            </div>
        </div>
    </header>
    <main>
        <?= $content ?>
    </main>
    <footer class="footer">
        <div>
            © Indra Oei 2024 | All rights reserved
        </div>
    </footer>
    <?php $this->endBody() ?>
</body>
<script>
    $('#btnLogout').on('click', function()
    {
        $.ajax({
            type    : 'POST',
            dataType: 'json',
            url     : '<?= Yii::$app->getUrlManager()->createUrl('site/logout') ?>',
            success : function(response) 
            {
                if (response)
                {
                    window.location.href = "<?= Yii::$app->getUrlManager()->createUrl('login') ?>";
                }
            }
        })
    })
</script>
</html>
<?php $this->endPage() ?>