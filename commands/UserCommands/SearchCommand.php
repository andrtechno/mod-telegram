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

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Conversation;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Entities\InlineKeyboardButton;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\KeyboardButton;
use Longman\TelegramBot\Request;
use panix\mod\shop\models\Product;
use Yii;

/**
 * User "/search" command
 *
 * Display an inline keyboard with a few buttons.
 */
class SearchCommand extends UserCommand
{
    /**
     * @var string
     */
    protected $name = 'search';

    /**
     * @var string
     */
    protected $description = 'Поиск товаров';

    /**
     * @var string
     */
    protected $usage = '/search <string>';

    /**
     * @var string
     */
    protected $version = '1.0';
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

        //Preparing Response
        $data = [
            'chat_id' => $chat_id,
        ];

        if ($chat->isGroupChat() || $chat->isSuperGroup()) {
            //reply to message id is applied by default
            //Force reply is applied by default so it can work with privacy on
            $data['reply_markup'] = Keyboard::forceReply(['selective' => true]);
        }

        //Conversation start
        $this->conversation = new Conversation($user_id, $chat_id, $this->getName());

        $notes = &$this->conversation->notes;
        !is_array($notes) && $notes = [];

        //cache data from the tracking session if any
        $state = 0;
        if (isset($notes['state'])) {
            $state = $notes['state'];
        }

        $result = Request::emptyResponse();


        if ($text === '') {
            $notes['state'] = 0;
            $this->conversation->update();


            $data['reply_markup'] = Keyboard::remove(['selective' => true]);

            //$result = Request::sendMessage($data);
            $data['text'] = 'Введите название товара или артикул:';
            if ($text !== '') {
                $data['text'] = 'Введите название товара или артикул:';
            }

            $result = Request::sendMessage($data);

        } else {
            $query = Product::find();
            $query->sort();
            $query->published();
            $query->groupBy(Product::tableName() . '.`id`');
            $query->applySearch($text);

            $result = $query->all();

            if ($result) {

                $inline_keyboard = new InlineKeyboard([
                    [
                        'text' => '👉 ' . Yii::t('shop/default', 'SEARCH_RESULT', [
                                'query' => $text,
                                'count' => count($result),
                            ]),
                        // 'callback_data' => 'thumb up'
                        'url' => 'https://yii2.pixelion.com.ua/search?q=' . $text
                    ],
                ]);

                $data = [
                    'chat_id' => $chat_id,
                    'parse_mode' => 'HTML',
                    'text' => Yii::t('shop/default', 'SEARCH_RESULT', [
                        'query' => $text,
                        'count' => count($result),
                    ]),
                    'reply_markup' => $inline_keyboard,
                ];

            } else {
                $data = [
                    'chat_id' => $chat_id,
                    'parse_mode' => 'HTML',
                    'text' => Yii::t('shop/default', 'SEARCH_RESULT', [
                        'query' => $text,
                        'count' => 0,
                    ]),
                ];
            }

            $result = Request::sendMessage($data);
        }


        return $result;
    }
}
