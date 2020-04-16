<?php

namespace panix\mod\telegram\components;

use Longman\TelegramBot\Entities\InlineKeyboardButton;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\KeyboardButton;
use Longman\TelegramBot\Request;

abstract class Command extends \Longman\TelegramBot\Commands\Command
{
    public function isSystemCommand()
    {
        return ($this instanceof SystemCommand);
    }

    /**
     * If this is an AdminCommand
     *
     * @return bool
     */
    public function isAdminCommand()
    {
        return ($this instanceof AdminCommand);
    }

    /**
     * If this is a UserCommand
     *
     * @return bool
     */
    public function isUserCommand()
    {
        return ($this instanceof UserCommand);
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

    public function startKeyboards()
    {
        $keyboards[] = [
            new KeyboardButton(['text' => '📂 Каталог']),
            new KeyboardButton(['text' => '🔎 Поиск']),
            new KeyboardButton(['text' => '🛍 Корзина'])
        ];
        $keyboards[] = [
            //  new KeyboardButton(['text' => '📢 Новости']),
            new KeyboardButton(['text' => '📦 Мои заказы']),
            new KeyboardButton(['text' => '❓ Помощь'])
        ];
        // $keyboards[] = [
        //  new KeyboardButton(['text' => '⚙ Настройки']),
        //   new KeyboardButton(['text' => '❓ Помощь'])
        // ];

        $data = (new Keyboard([
            'keyboard' => $keyboards
        ]))->setResizeKeyboard(true)->setOneTimeKeyboard(true)->setSelective(true);

        return $data;
    }

    public function number_format($sum)
    {
        return number_format($sum, 1, '.', ' ');
    }

    public function errorMessage()
    {


        if ($this->getUpdate()->getCallbackQuery()) {
            $data['chat_id'] = $this->getUpdate()->getCallbackQuery()->getMessage()->getChat()->getId();
        } else {
            $data['chat_id'] = $this->getUpdate()->getMessage()->getChat()->getId();
        }


        $data['text'] = 'Ошибка';

        return Request::sendMessage($data);
    }

    public function catalogKeyboards()
    {
        $keyboards[] = [
            new KeyboardButton(['text' => '🏠 Начало']),
            new KeyboardButton(['text' => '📂 Каталог']),
            new KeyboardButton(['text' => '🔎 Поиск']),
        ];

        $keyboards[] = [
            new KeyboardButton(['text' => '🛍 Корзина']),
            new KeyboardButton(['text' => '📦 Мои заказы']),
            new KeyboardButton(['text' => '❓ Помощь'])
        ];

        $data = (new Keyboard([
            'keyboard' => $keyboards
        ]))->setResizeKeyboard(true)->setOneTimeKeyboard(true)->setSelective(true);

        return $data;
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
