<?php

namespace panix\mod\telegram\components;

use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\KeyboardButton;

abstract class SystemCommand extends \Longman\TelegramBot\Commands\SystemCommand
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
}
