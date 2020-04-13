<?php

namespace panix\mod\telegram\components;


defined('TB_BASE_PATH') || define('TB_BASE_PATH', __DIR__.'/../');
defined('TB_BASE_COMMANDS_PATH') || define('TB_BASE_COMMANDS_PATH', TB_BASE_PATH . '/commands');

class Api extends \Longman\TelegramBot\Telegram
{
    protected $version = '1.0.0';

}