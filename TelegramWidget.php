<?php

namespace panix\mod\telegram;

use yii\helpers\Html;
use yii\web\Cookie;
use yii\widgets\ActiveForm;
use Yii;

class TelegramWidget extends \yii\base\Widget
{

    public static $tlgrmChatId = null;

    public function init()
    {
        parent::init();
    }

    public function run()
    {
        $view = $this->getView();
        TelegramAsset::register($view);
        $this->renderInitiateBtn();
    }

    private function renderInitiateBtn()
    {
        echo $this->render('default/button.php');
    }

}