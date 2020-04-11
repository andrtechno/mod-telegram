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
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Entities\InlineKeyboardButton;
use Longman\TelegramBot\Request;
use panix\mod\telegram\models\Order;
use Yii;

/**
 * User "/cart" command
 *
 * Display an inline keyboard with a few buttons.
 */
class CartCommand extends UserCommand
{
    /**
     * @var string
     */
    protected $name = 'cart';

    /**
     * @var string
     */
    protected $description = 'Корзина заказа';

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
        $update = $this->getUpdate();

        if ($update->getCallbackQuery()) {
            $message = $update->getCallbackQuery()->getMessage();
        } else {
            $message = $this->getMessage();
        }


        $chat = $message->getChat();
        $user = $message->getFrom();
        $text = trim($message->getText(true));
        $chat_id = $chat->getId();
        $user_id = $user->getId();

        $order = Order::find()->where(['client_id'=>$user_id])->one();
        if($order){
            $keyboards= [];
            foreach ($order->products as $product){
                $keyboards[] = [
                    new InlineKeyboardButton(['text' => '—', 'callback_data' => "addCart/{$product->product_id}/down"]),
                    new InlineKeyboardButton(['text' => $product->quantity.' шт.', 'callback_data' => 'get']),
                    new InlineKeyboardButton(['text' => '+', 'callback_data' => "addCart/{$product->product_id}/up"])
                ];
                $keyboards[] = [
                    new InlineKeyboardButton(['text' => '⬅', 'callback_data' => 'get']),
                    new InlineKeyboardButton(['text' => '2 / 6', 'callback_data' => 'get']),
                    new InlineKeyboardButton(['text' => '➡', 'callback_data' => 'get'])
                ];
                $keyboards[] = [
                    new InlineKeyboardButton(['text' => '✅ Заказ на 130 грн. Офрормить', 'callback_data' => 'get']),
                ];
                $keyboards[] = [
                    new InlineKeyboardButton(['text' => '❌', 'callback_data' => "removeProductCart/{$product->product_id}"]),
                ];


                $text = '*'.$product->id.'Ваша корзина*' . PHP_EOL;
                //$text .= '[Мой товар](https://images.ua.prom.st/1866772551_w640_h640_1866772551.jpg)' . PHP_EOL;
                $text .= '[Мой товар](https://yii2.pixelion.com.ua/images/get-file/2157ff033e-2.jpg)' . PHP_EOL;
                $text .= '_описание товара_' . PHP_EOL;
                $text .= '`90 грн / 4 шт = 350 грн`' . PHP_EOL;

                $data['chat_id'] = $chat_id;
                $data['text'] = $text;
                $data['parse_mode'] = 'Markdown';
                $data['reply_markup'] = new InlineKeyboard([
                    'inline_keyboard' => $keyboards
                ]);
                $response = Request::sendMessage($data);

            }

            //$response = true;
        }else{
            echo 'empy cart';
        }

       // print_r($response);
        return $response;
    }

    public function keywords(){
        $keyboards[] = [
            new InlineKeyboardButton(['text' => '—', 'callback_data' => 'get']),
            new InlineKeyboardButton(['text' => '2 шт.', 'callback_data' => 'get']),
            new InlineKeyboardButton(['text' => '+', 'callback_data' => 'get'])
        ];
        $keyboards[] = [
            new InlineKeyboardButton(['text' => '⬅', 'callback_data' => 'get']),
            new InlineKeyboardButton(['text' => '2 / 6', 'callback_data' => 'get']),
            new InlineKeyboardButton(['text' => '➡', 'callback_data' => 'get'])
        ];
        $keyboards[] = [
            new InlineKeyboardButton(['text' => '✅ Заказ на 130 грн. Офрормить', 'callback_data' => 'get']),
        ];
        $keyboards[] = [
            new InlineKeyboardButton(['text' => '❌', 'callback_data' => 'get']),
        ];
        return $keyboards;
    }

}
