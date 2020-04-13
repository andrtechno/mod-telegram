<?php

namespace panix\mod\telegram\components;

use Yii;

//defined('TB_BASE_PATH') || define('TB_BASE_PATH', __DIR__);
define('TB_BASE_COMMANDS_PATH', __DIR__ . '/commands');

class Api extends \Longman\TelegramBot\Telegram
{
    protected $version = '1.0.0';
    private $config=[];
    public function __construct()
    {
        $this->config = Yii::$app->settings->get('telegram');
      //  echo TB_BASE_PATH.PHP_EOL;
       // echo TB_BASE_COMMANDS_PATH.PHP_EOL;
        $api_key = $this->config->api_token;
        $bot_username = $this->config->bot_name;
        parent::__construct($api_key, $bot_username);
    }

    public function enableAdmins($admin_ids=[])
    {

        $list = explode(',', $this->config->bot_admins);

        $admin_ids = array_merge($admin_ids, $list);

        foreach ($admin_ids as $admin_id) {
            $this->enableAdmin((int) $admin_id);
        }

        return $this;
    }
}