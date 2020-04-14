<?php
/**
 * This file is part of the TelegramBot package.
 *
 * (c) Avtandil Kikabidze aka LONGMAN <akalongman@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace panix\mod\telegram\commands\UserCommands;


use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Entities\InlineKeyboardButton;
use Longman\TelegramBot\Request;
use panix\mod\telegram\commands\pager\InlineKeyboardPagination;
use panix\mod\telegram\components\InlineKeyboardPager;
use panix\mod\telegram\components\KeyboardPager;
use panix\mod\telegram\components\UserCommand;
use panix\mod\telegram\models\OrderProduct;
use panix\mod\telegram\components\KeyboardCart;
use panix\mod\telegram\components\KeyboardPagination;
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
    private $page = 0;

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
            print_r($update->getCallbackQuery());
            $callbackQuery = $update->getCallbackQuery();
            $message = $callbackQuery->getMessage();
            //  $chat = $callbackQuery->getMessage()->getChat();
            //  $user = $message->getFrom();
            $chat = $message->getChat();
            $user = $callbackQuery->getFrom();
            $chat_id = $chat->getId();
            $user_id = $user->getId();
        } else {
            $callbackQuery = null;
            $message = $this->getMessage();
            $chat = $message->getChat();
            $user = $message->getFrom();

            $chat_id = $chat->getId();
            $user_id = $user->getId();
        }
        $text = trim($message->getText(true));


        $data['chat_id'] = $chat_id;


        // $response = $data;


        $order = Order::find()->where(['client_id' => $user_id, 'checkout' => 0])->one();

        if ($order) {


            if ($this->getConfig('page')) {
                $this->page = $this->getConfig('page');
            }


            $query = OrderProduct::find()->where(['order_id' => $order->id]);
            $pages = new KeyboardPagination([
                'totalCount' => $query->count(),
                'defaultPageSize' => 1,
                //'pageSize'=>3
            ]);
            $pages->setPage($this->page);
            $products = $query->offset($pages->offset)
                ->limit($pages->limit)
                ->all();


            $pager = new InlineKeyboardPager([
                'pagination' => $pages,
                'lastPageLabel' => false,
                'firstPageLabel' => false,
                'maxButtonCount' => 1,
                'command' => 'getCart'
            ]);


            $keyboards = [];

            if ($query->count()) {
                foreach ($products as $product) {

                    if ($pager->buttons)
                        $keyboards[] = $pager->buttons;

                    $keyboards[] = [
                        new InlineKeyboardButton(['text' => '—', 'callback_data' => "spinner/{$order->id}/{$product->product_id}/down/cart"]),
                        new InlineKeyboardButton(['text' => $product->quantity . ' шт.', 'callback_data' => time()]),
                        new InlineKeyboardButton(['text' => '+', 'callback_data' => "spinner/{$order->id}/{$product->product_id}/up/cart"])
                    ];
                    $keyboards[] = [
                        new InlineKeyboardButton(['text' => Yii::t('telegram/command', 'BUTTON_CHECKOUT', $order->total_price), 'callback_data' => 'checkOut']),
                    ];
                    $keyboards[] = [
                        new InlineKeyboardButton(['text' => '❌', 'callback_data' => "cartDelete/{$order->id}/{$product->product_id}"]),
                    ];


                    $text = '*Ваша корзина*' . PHP_EOL;
                    //$text .= '[Мой товар](https://images.ua.prom.st/1866772551_w640_h640_1866772551.jpg)' . PHP_EOL;
                    //$text .= '[' . $product->name . '](https://images.ua.prom.st/1866772551_w640_h640_1866772551.jpg)' . PHP_EOL;
                    $text .= '[' . $product->name . '](https://yii2.pixelion.com.ua' . $product->image . ')' . PHP_EOL;
                    $text .= '_описание товара_' . PHP_EOL;
                    $text .= '`' . $product->price . ' грн / ' . $product->quantity . ' шт = ' . ($product->price * $product->quantity) . ' грн`' . PHP_EOL;

                    //  $data['chat_id'] = $chat_id;
                    $data['text'] = $text;
                    $data['parse_mode'] = 'Markdown';


                    $data['reply_markup'] = new InlineKeyboard([
                        'inline_keyboard' => $keyboards
                    ]);
                    if ($callbackQuery) {

                        $data['message_id'] = $message->getMessageId();
                        $response = Request::editMessageText($data);

                        $dataReplyMarkup['reply_markup'] = new InlineKeyboard([
                            'inline_keyboard' => $keyboards
                        ]);

                        return Request::editMessageReplyMarkup(array_merge($data, $dataReplyMarkup));
                    }
                    $response = $data;

                }
            } else {
                $data['text'] = Yii::$app->settings->get('telegram', 'empty_cart_text');
                $data['reply_markup'] = $this->startKeyboards();
                $response = $data;
            }
        } else {
            $data['text'] = Yii::$app->settings->get('telegram', 'empty_cart_text');
            $data['reply_markup'] = $this->startKeyboards();
            $response = $data;

        }

        return Request::sendMessage($response);
    }

    public function keywords()
    {
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
