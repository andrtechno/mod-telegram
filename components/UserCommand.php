<?php

namespace panix\mod\telegram\components;

use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\KeyboardButton;
use Yii;

abstract class UserCommand extends \Longman\TelegramBot\Commands\UserCommand
{


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
