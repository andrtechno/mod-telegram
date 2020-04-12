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


use Longman\TelegramBot\Conversation;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\KeyboardButton;
use Longman\TelegramBot\Entities\PhotoSize;
use Longman\TelegramBot\Request;
use panix\mod\telegram\components\UserCommand;
use panix\mod\telegram\models\Order;
use Yii;

/**
 * User "/history" command
 *
 * Command that demonstrated the Conversation funtionality in form of a simple survey.
 */
class HistoryCommand extends UserCommand
{
    /**
     * @var string
     */
    protected $name = 'history';

    /**
     * @var string
     */
    protected $description = 'Order history';

    /**
     * @var string
     */
    protected $usage = '/history';

    /**
     * @var string
     */
    protected $version = '1.0.0';


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
        $data['chat_id'] = $chat_id;

        $result = Request::emptyResponse();

        $order = Order::find()->where(['client_id' => $user_id, 'checkout' => 1])->all();
        if (!$order) {
            $data['text'] = Yii::$app->settings->get('telegram', 'empty_history_text');
            $data['reply_markup'] = $this->startKeyboards();
            $result = Request::sendMessage($data);
        } else {
            $data['text'] = 'Скоро будет работать';
            $data['reply_markup'] = $this->startKeyboards();
            $result = Request::sendMessage($data);
        }
        return $result;
    }
}
