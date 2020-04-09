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
 * User "/product" command
 *
 * Display an inline keyboard with a few buttons.
 */
class ProductCommand extends UserCommand
{
    /**
     * @var string
     */
    protected $name = 'product';

    /**
     * @var string
     */
    protected $description = 'get product';

    /**
     * @var string
     */
    protected $usage = '/product <id>';

    /**
     * @var string
     */
    protected $version = '1.0';

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

        //Conversation start
        $this->conversation = new Conversation($user_id, $chat_id, $this->getName());


        $telegram = new \Longman\TelegramBot\Telegram('835652742:AAEBdMpPg9TgakFa2o8eduRSkynAZxipg-c', 'pixelion');

        $preg = preg_match('/^\/product\s+([0-9]+)/iu', trim($message->getText()), $match);
        if ($preg) {
            if (isset($match[1])) {


                $product = Product::find()->published()->where(['id' => $match[1]])->one();
                if($product){
                    $inline_keyboard = new InlineKeyboard([
                        ['text' => '👉 '.$product->price . ' UAH. Купить 👈', 'callback_data' => 'callbackqueryproduct']], [
                        ['text' => '🏆 ☎️  🛒 🎁 Характеристики', 'callback_data' => 'product_attributes'],
                        ['text' => 'LALAL 💵 💴 💶 💷 💰 💳  ✉️📦 📁 📄 📞 ✔ 🔴 🇺🇦 🤝  🚚 callback thumb up ', 'callback_data' => 'thumb up'],
                    ]);

                    $sendPhoto = Yii::$app->telegram->sendPhoto([
                        'photo' => $product->getImage()->getPathToOrigin(),
                        'chat_id' => $chat_id,
                        'parse_mode' => 'HTML',
                        'caption' => '<strong>'.$product->name.'</strong>',
                        //'reply_markup' => $inline_keyboard,
                    ]);
                    $data = [
                        'chat_id' => $chat_id,
                        'parse_mode' => 'HTML',
                        //   'callback_query_id' => $callback_query_id,
                        'text' => Yii::$app->view->render('@telegram/views/default/test', ['product' => $product]),
                        'reply_markup' => $inline_keyboard,
                    ];

                }else{
                    $data = [
                        'chat_id' => $chat_id,
                        'parse_mode' => 'HTML',
                        'text' => Yii::t('shop/default','NOT_FOUND_PRODUCT'),
                       // 'reply_markup' => $inline_keyboard,
                    ];
                }


                return Request::sendMessage($data);
            }
        }
        $inline_keyboard = new InlineKeyboard([
            ['text' => 'inline' . $message, 'switch_inline_query' => 123],
            ['text' => 'inline current chat', 'switch_inline_query_current_chat' => 321],
        ], [
            ['text' => 'getProduct', 'callback_data' => '/getProduct '.$match[1]],
            ['text' => 'callback thumb up ', 'callback_data' => 'thumb up'],
        ]);


        $data = [
            'chat_id' => $chat_id,
            'text' => 'inline keyboard',
            'reply_markup' => $inline_keyboard,
        ];

        /* $data['reply_markup'] = (new Keyboard(['купить', (new KeyboardButton(['text'=>'Share Contact']))->setText('asddsa')]))
             ->setResizeKeyboard(true)
             ->setOneTimeKeyboard(true)
             ->setSelective(true);*/


        /*$data['reply_markup'] = (new Keyboard(['купить222222', (new KeyboardButton(['text'=>'Share Contact', 'callback_data' => 'callbackqueryproduct']))->setText('asddsa')]))
            ->setResizeKeyboard(false)
            ->setOneTimeKeyboard(false)
            ->setSelective(false);*/


        /*$data['reply_markup'] = (new Keyboard(['Мужской', 'Женский']))
            ->setResizeKeyboard(true)
            ->setOneTimeKeyboard(true)
            ->setSelective(true);*/


        return Request::sendMessage($data);

        // return Yii::$app->telegram->sendMessage($data);
    }
}
