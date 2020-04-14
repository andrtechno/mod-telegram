<?php
/**
 * This file is part of the TelegramBot package.
 *
 * (c) Avtandil Kikabidze aka LONGMAN <akalongman@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace panix\mod\telegram\commands\SystemCommands;

use Longman\TelegramBot\Conversation;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\ReplyKeyboardHide;
use Longman\TelegramBot\Request;
use panix\mod\telegram\components\SystemCommand;

/**
 *
 * This command cancels the currently active conversation and
 * returns a message to let the user know which conversation it was.
 * If no conversation is active, the returned message says so.
 */
class PaymentCommand extends SystemCommand
{
    /**#@+
     * {@inheritdoc}
     */
    protected $name = 'payment';
    protected $description = 'payment order';

    protected $version = '1.0.0';
    protected $need_mysql = true;
    public $enabled = true;
    public $private_only=true;
    public $order_id;
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $update = $this->getUpdate();

        $callback_query = $update->getCallbackQuery();
        $message = $callback_query->getMessage();
        $chat_id = $message->getChat()->getId();


        // $chat = $message->getChat();
        $user = $message->getFrom();
        $user_id = $user->getId();


        $callback_query_id = $callback_query->getId();
        $callback_data = $callback_query->getData();


        $data = [
            'chat_id' => $chat_id,
            'text' => 'Система оплаты в разработке!!! ID:'.$this->getConfig('order_id'),
        ];

        return Request::sendMessage($data);
    }


}
