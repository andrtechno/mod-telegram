<?php

namespace Longman\TelegramBot\Commands\SystemCommands;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\KeyboardButton;
use Longman\TelegramBot\Request;
use Yii;

/**
 * Start command
 *
 * Gets executed when a user first starts using the bot.
 */
class StartCommand extends SystemCommand
{
    /**
     * @var string
     */
    protected $name = 'start';

    /**
     * @var string
     */
    protected $description = 'Start command';

    /**
     * @var string
     */
    protected $usage = '/start';

    /**
     * @var string
     */
    protected $version = '1.0.0';

    /**
     * @var bool
     */
    protected $private_only = true;
    /**
     * Conversation Object
     *
     * @var \Longman\TelegramBot\Conversation
     */
    protected $conversation;

    /**
     * Command execute method
     *
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute()
    {

        $message = $this->getMessage();

        $chat = $message->getChat();
        $user = $message->getFrom();
        $text = trim($message->getText(true));
        $chat_id = $chat->getId();
        $user_id = $user->getId();


        $text = Yii::t('telegram/command', 'START');

        $data = [
            'parse_mode' => 'HTML',
            'chat_id' => $chat_id,
            'text' => $text,
        ];


        /* $data['reply_markup'] = (new Keyboard(
              [
                  'купить',
                  (new KeyboardButton(['text' => 'Share Contact']))->setText('asddsa'),
                  ['text' => 'test', 'callback_data' => 'thumb up']
              ]
          ))
              ->setResizeKeyboard(true)
              ->setOneTimeKeyboard(true)
              ->setSelective(true);*/

        $keyboards[] = [
            new KeyboardButton(['text' => '📂 Каталог', 'callback_data' => 'callbackqueryproduct']),
            new KeyboardButton(['text' => '🛍 Корзина', 'callback_data' => 'product_attributes'])
        ];
        $keyboards[] = [
            new KeyboardButton(['text' => '📢 Новости', 'callback_data' => 'callbackqueryproduct']),
            new KeyboardButton(['text' => '📦 Заказы', 'callback_data' => 'product_attributes'])
        ];
        $keyboards[] = [
            new KeyboardButton(['text' => '⚙ Настройки', 'callback_data' => 'callbackqueryproduct']),
            new KeyboardButton(['text' => '❓ Помощь', 'callback_data' => 'product_attributes'])
        ];

        if ($this->telegram->isAdmin($chat_id)) {
            //  $keyboards[] = [new InlineKeyboardButton(['text' => '✏ 📝  ⚙ Редактировать', 'callback_data' => 'get']), new InlineKeyboardButton(['text' => '❌ Удалить', 'callback_data' => 'get'])];
            //  $keyboards[] = [new InlineKeyboardButton(['text' => '❓ 👤  👥 🛍 ✅ 🟢 🔴Удалить', 'callback_data' => 'get'])];
        }


        $data['reply_markup'] = (new Keyboard([
            'keyboard' => $keyboards
        ]))->setResizeKeyboard(true)->setOneTimeKeyboard(true)->setSelective(true);


        return Request::sendMessage($data);
    }
}
