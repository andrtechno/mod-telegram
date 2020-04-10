<?php
/**
 * This file is part of the TelegramBot package.
 *
 * (c) Avtandil Kikabidze aka LONGMAN <akalongman@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Request;
use panix\mod\telegram\components\Command;
use Yii;

/**
 * User "/cart" command
 *
 * Display an inline keyboard with a few buttons.
 */
class CartCommand extends Command
{
    /**
     * @var string
     */
    protected $name = 'cart';

    /**
     * @var string
     */
    protected $description = 'get cart';

    /**
     * @var string
     */
    protected $usage = '/cart';

    /**
     * @var string
     */
    protected $version = '1.0';

    // public $enabled = false;

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

        $sticker = [
            'chat_id' => $chat_id,
            'sticker' => 'BQADBAADsgUAApv7sgABW0IQT2B3WekC'
        ];
        Request::sendSticker($sticker);

        $data['chat_id'] = $chat_id;
        $data['text'] = 'в разработке';
        $data['reply_markup'] = $this->homeKeyboards();

        return Request::sendMessage($data);

    }

}
