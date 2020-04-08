<?php

use panix\engine\Html;
use panix\engine\bootstrap\ActiveForm;


//print_r(Yii::$app->telegram->getMe());

Yii::$app->telegram->sendMessage([
            'chat_id' => 1200120610,
            'text' => 'upload_photo',
       ]);

//\panix\engine\CMS::dump(Yii::$app->telegram->getUpdates());
?>

<?php if (!Yii::$app->request->getIsSecureConnection()) { ?>
    <div class="alert alert-warning">Webhook требует SSL!</div>
<?php } ?>
<?php
$form = ActiveForm::begin();
?>
    <div class="card">
        <div class="card-header">
            <h5><?= $this->context->pageName ?></h5>
        </div>
        <div class="card-body">
            <?= $form->field($model, 'api_token') ?>
            <?= $form->field($model, 'bot_name') ?>
            <?= $form->field($model, 'password') ?>
        </div>
        <div class="card-footer text-center">
            <?= $model->submitButton(); ?>
        </div>
    </div>
<?php ActiveForm::end(); ?>

<?php
echo \panix\mod\telegram\TelegramWidget::widget();
