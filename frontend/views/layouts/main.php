<?php

use yii\helpers\Html;
use frontend\assets\AppAsset;

AppAsset::register($this);
?>

<?php $this->beginPage(); ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="<?= Yii::$app->charset; ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?php echo Html::csrfMetaTags() ?>
        <title><?= Html::encode($this->title); ?></title>
        <?php $this->head(); ?>
    </head>
    <body>
        <?php $this->beginBody(); ?>
        
        <div class="basis">

            <!-- main -->
            <div class="main container">
                <div class="header-index col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <?= $this->render('parts/our_header')?>
                    <?= $this->render('parts/header_login')?>
                </div>

                <div class="content">
                    <?= $content ?>
                </div>

                <div class="clearfooter">

                </div>

                <?= $this->render('parts/footer')?>
            </div>
        </div>
        <!-- /main -->

    <?php $this->endBody(); ?>
</body>
</html>
<?php $this->endPage(); ?>