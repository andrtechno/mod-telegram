<?php

namespace panix\mod\telegram\components;

use Yii;

//defined('TB_BASE_PATH') || define('TB_BASE_PATH', __DIR__);
define('TB_BASE_COMMANDS_PATH', __DIR__ . '/commands');

class Api extends \Longman\TelegramBot\Telegram
{
    protected $version = '1.0.0';
    private $config = [];

    public function __construct()
    {
        $this->config = Yii::$app->settings->get('telegram');
        //  echo TB_BASE_PATH.PHP_EOL;
        // echo TB_BASE_COMMANDS_PATH.PHP_EOL;
        $api_key = $this->config->api_token;
        $bot_username = $this->config->bot_name;
        parent::__construct($api_key, $bot_username);
        $this->enableAdmins();
        $this->setDownloadPath(Yii::getAlias('@app/web/downloads/telegram'));
        $this->setUploadPath(Yii::getAlias('@app/web/uploads/telegram'));

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
    public function enableAdmins($admin_ids = [])
    {
        $list = [];
        if (isset($this->config->bot_admins) && $this->config->bot_admins)
            $list = explode(',', $this->config->bot_admins);

        $admin_ids = array_merge($admin_ids, $list);

        foreach ($admin_ids as $admin_id) {
            $this->enableAdmin((int)$admin_id);
        }

        return $this;
    }



}