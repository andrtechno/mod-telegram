<?php

namespace panix\mod\telegram\components;

use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\KeyboardButton;
use Longman\TelegramBot\Entities\Update;
use Yii;
abstract class Command extends \Longman\TelegramBot\Commands\Command
{


    public function __construct(TelegramApi $telegram, Update $update = null)
    {
        $this->description = Yii::t('telegram/command','COMMAND_'.strtoupper($this->name));
        parent::__construct($telegram,$update);
    }

    public function startKeyboards(){
        $keyboards[] = [
            new KeyboardButton(['text' => '📂 Каталог']),
            new KeyboardButton(['text' => '🔎 Поиск']),
            new KeyboardButton(['text' => '🛍 Корзина'])
        ];
        $keyboards[] = [
            new KeyboardButton(['text' => '📢 Новости']),
            new KeyboardButton(['text' => '📦 Мои заказы'])
        ];
        $keyboards[] = [
            new KeyboardButton(['text' => '⚙ Настройки']),
            new KeyboardButton(['text' => '❓ Помощь'])
        ];

        $data = (new Keyboard([
            'keyboard' => $keyboards
        ]))->setResizeKeyboard(true)->setOneTimeKeyboard(true)->setSelective(true);

        return $data;
    }


    public function homeKeyboards(){
        $keyboards[] = [
            new KeyboardButton(['text' => '🏠 Начало']),
        ];

        $data = (new Keyboard([
            'keyboard' => $keyboards
        ]))->setResizeKeyboard(true)->setOneTimeKeyboard(true)->setSelective(true);

        return $data;
    }
}
