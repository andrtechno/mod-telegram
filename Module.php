<?php

namespace panix\mod\telegram;

use yii\base\UserException;
use yii\helpers\Url;

/**
 * telegram module definition class
 */
class Module extends \yii\base\Module implements \yii\base\BootstrapInterface
{
    public $API_KEY = null;
    public $BOT_NAME = null;
    public $hook_url = null;
    public $PASSPHRASE = null;
    public $userCommandsPath = null;
    public $timeBeforeResetChatHandler = 0;
    public $db = 'db';
    public $options = [];

    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'panix\mod\telegram\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        if (empty($this->API_KEY) || empty($this->BOT_NAME) || empty($this->hook_url))
            throw new UserException('You must set API_KEY, BOT_NAME, hook_url');
        if (empty($this->PASSPHRASE))
            throw new UserException('You must set PASSPHRASE');



        parent::init();

        $this->options = [
            'initChat' => Url::to(['/telegram/default/init-chat']),
            'destroyChat' => Url::to(['/telegram/default/destroy-chat']),
            'getAllMessages' => Url::to(['/telegram/chat/get-all-messages']),
            'getLastMessages' => Url::to(['/telegram/chat/get-last-messages']),
            'initialMessage' => \Yii::t('telegram/default', 'Write your question...'),
        ];

    }

    public function bootstrap($app)
    {
        if ($app instanceof \yii\console\Application) {
            $this->controllerNamespace = 'panix\mod\telegram\commands';
        }
    }
}
