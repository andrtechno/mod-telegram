<?php

namespace panix\mod\telegram\components;

use Longman\TelegramBot\Entities\InlineKeyboardButton;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\KeyboardButton;
use Yii;

abstract class UserCommand extends Command
{

    public function startKeyboards()
    {
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

    public function productAdminKeywords($chat_id, $product_id)
    {
        $keyboards = [];
        if ($this->telegram->isAdmin($chat_id)) {
            $keyboards = [
                new InlineKeyboardButton([
                    'text' => '✏',
                    'callback_data' => 'query=productUpdate&id=' . $product_id
                ]),
                new InlineKeyboardButton([
                    'text' => '👁',
                    'callback_data' => 'query=productSwitch&id=' . $product_id
                ]),
                new InlineKeyboardButton([
                    'text' => '❌',
                    'callback_data' => 'query=productDelete&id=' . $product_id
                ]),
            ];
        }
        return $keyboards;
    }

    public function homeKeyboards()
    {
        $keyboards[] = [
            new KeyboardButton(['text' => '🏠 Начало']),
            new KeyboardButton(['text' => '📂 Каталог']),
            new KeyboardButton(['text' => '🔎 Поиск']),
        ];

        $data = (new Keyboard([
            'keyboard' => $keyboards
        ]))->setResizeKeyboard(true)->setOneTimeKeyboard(true)->setSelective(true);

        return $data;
    }
}
