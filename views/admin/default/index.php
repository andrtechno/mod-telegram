<?php

use panix\engine\Html;
use panix\engine\bootstrap\ActiveForm;
use Longman\TelegramBot\Entities\KeyboardButton;
$bot_api_key = '835652742:AAEBdMpPg9TgakFa2o8eduRSkynAZxipg-c';
$bot_username = 'pixelionbot';
//print_r(Yii::$app->telegram->getMe());
/*
Yii::$app->telegram->sendMessage([
            'chat_id' => 835652742,
            'text' => 'upload_photo',
       ]);

*/

Yii::$app->telegram->sendChatAction([
    'chat_id' => 835652742,
    'action' => 'upload_photo',
]);

$admins = Yii::$app->telegram->getChatAdministrators([
    'chat_id' => '835652742',
]);

$telegram = new \Longman\TelegramBot\Telegram($bot_api_key, $bot_username);
$results = \Longman\TelegramBot\Request::sendToActiveChats(
    'sendMessage', // Callback function to execute (see Request.php methods)
    ['text' => 'Hey! Check out the new features!!'], // Param to evaluate the request
    [
        'groups'      => true,
        'supergroups' => true,
        'channels'    => false,
        'users'       => true,
    ]
);

$pref = preg_match('/^(\x{1F4E6})/iu', '📦 Мои заказы', $match);
var_dump($pref);
print_r($match);


$str = "🐘";
echo  htmlspecialchars($str);


echo $this->context->module->hook_url;

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
            <?= $form->field($model, 'empty_cart_text')->textarea() ?>
            <?php  echo $form->field($model, 'empty_history_text')->textarea() ?>
        </div>
        <?=
        $form->field($model, 'bot_admins')
            ->widget(\panix\ext\taginput\TagInput::class, ['placeholder' => 'ID'])
            ->hint('Введите ID и нажмите Enter');
        ?>


        <div class="card-footer text-center">
            <?= $model->submitButton(); ?>
        </div>
    </div>
<?php ActiveForm::end(); ?>

<?php
echo \panix\mod\telegram\TelegramWidget::widget();
