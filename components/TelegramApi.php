<?php

namespace panix\mod\telegram\components;

use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Telegram;
use Yii;

defined('TB_BASE_PATH') || define('TB_BASE_PATH', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'commands');
defined('TB_BASE_COMMANDS_PATH') || define('TB_BASE_COMMANDS_PATH', TB_BASE_PATH);

class TelegramApi extends Telegram
{
    protected $version = '1.0 (beta)';

    /**
     * Telegram constructor.
     *
     * @param string $api_key
     * @param string $bot_username
     *
     * @throws TelegramException
     */
    public function __construct($api_key = '', $bot_username = '')
    {

        if (empty($api_key)) {
            $api_key = Yii::$app->settings->get('telegram', 'api_token');
        }
        if (empty($bot_username)) {
            $bot_username = Yii::$app->settings->get('telegram', 'bot_name');
        }
        parent::__construct($api_key, $bot_username);

    }


    public function getCommandObject($command)
    {
        $which = ['System'];
        $this->isAdmin() && $which[] = 'Admin';
        $which[] = 'User';
        foreach ($which as $auth) {
            $command_namespace = 'panix\\mod\\telegram\\commands\\' . $auth . 'Commands\\' . $this->ucfirstUnicode($command) . 'Command';
            if (class_exists($command_namespace)) {
                return new $command_namespace($this, $this->update);
            }
        }
        return null;
    }
}