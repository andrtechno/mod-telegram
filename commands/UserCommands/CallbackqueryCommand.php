<?php
/**
 * This file is part of the TelegramBot package.
 *
 * (c) Avtandil Kikabidze aka LONGMAN <akalongman@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Longman\TelegramBot\Commands\SystemCommands;


use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Entities\InlineKeyboardButton;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\KeyboardButton;
use panix\mod\shop\models\Attribute;
use panix\mod\shop\models\Category;
use panix\mod\shop\models\Product;
use panix\mod\telegram\commands\pager\InlineKeyboardPagination;
use panix\mod\telegram\components\InlineKeyboardPager;
use panix\mod\telegram\components\KeyboardMore;
use panix\mod\telegram\components\KeyboardPager;
use panix\mod\telegram\components\KeyboardPagination;
use panix\mod\telegram\models\AuthorizedManagerChat;
use panix\mod\telegram\models\Order;
use panix\mod\telegram\models\OrderProduct;
use panix\mod\telegram\models\Usernames;
use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Request;
use Yii;

/**
 * Callback query command
 */
class CallbackqueryCommand extends SystemCommand
{
    /**#@+
     * {@inheritdoc}
     */
    protected $name = 'callbackquery';
    protected $description = 'Reply to callback query';
    protected $version = '1.0.0';
    /**#@-*/

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

        $data['callback_query_id'] = $callback_query_id;
        if ($callback_data == 'getProduct') {

            $product = Product::find()->where(['id' => 2665])->one();
            $inline_keyboard = new InlineKeyboard([
                ['text' => '👉 ' . $product->price . ' грн. Купить', 'callback_data' => 'callbackqueryproduct']], [
                ['text' => '🏆  🛒 🎁 Характеристики', 'callback_data' => 'product_attributes'],
                ['text' => '🤝  🚚 callback thumb up ', 'callback_data' => 'thumb up'],
            ]);


            $data = [
                'chat_id' => $chat_id,
                'parse_mode' => 'HTML',
                'callback_query_id' => $callback_query_id,
                'text' => Yii::$app->view->render('@telegram/views/default/test', ['product' => $product]),
                'reply_markup' => $inline_keyboard,
            ];
            $sendPhoto = Yii::$app->telegram->sendPhoto([
                'photo' => Yii::getAlias('@app/web/uploads') . DIRECTORY_SEPARATOR . '1.jpg',
                'chat_id' => $chat_id,
                'parse_mode' => 'HTML',
                'caption' => 'LALAL 💵 💴 💶 💷 💰 💳  ✉️📦 📁 📄 📞 ✔ 🔴 🇺🇦<strong>sadsasdadsa sad as dasdas das d asd asd asd asd asdsdadsa</strong>',
                'reply_markup' => $inline_keyboard,
            ]);
            return Yii::$app->telegram->sendMessage($data);
        } elseif ($callback_data == 'product_attributes') {
            $product = Product::find()->where(['id' => 2665])->one();
            $eav = $product->getEavAttributes();
            $data = [
                'chat_id' => $chat_id,
                // 'parse_mode' => 'HTML',
                'callback_query_id' => $callback_query_id,
                'show_alert' => true,
                'text' => 'Тут будут параметры ' . $callback_query_id,
                // 'reply_markup' => $inline_keyboard,
            ];


            return Request::answerCallbackQuery($data);

        } elseif ($callback_data == 'goHome') {
            return $this->telegram->executeCommand('start');
        } elseif (strpos(trim($callback_data),'command_pager')) {

            return $this->telegram
                ->setCommandConfig('cart', ['page' => $orderProduct->product_id])
                ->executeCommand('cart');

            $params = InlineKeyboardPagination::getParametersFromCallbackData($callback_data);
print_r($params);
//$params = [
//    'command' => 'testCommand',
//    'oldPage' => '10',
//    'newPage' => '1',
//];

// or, just use PHP directly if you like. (literally what the helper does!)
            print_r(parse_str($callback_data, $params));



            return Request::emptyResponse();


        } elseif (preg_match('/^getCatalog\s+([0-9]+)/iu', trim($callback_data), $match)) {


//print_r($message);die;

            $id = (isset($match[1])) ? $match[1] : 1;
            $root = Category::findOne($id);

            $categories = $root->children()->all();


            $keyboards = [];
            if ($categories) {

                foreach ($categories as $category) {
                    $child = $category->children()->count();
                    $count = $category->countItems;
                    if ($count) {
                        if ($child) {
                            $keyboards[] = [
                                new InlineKeyboardButton([
                                    'text' => '📂 ' . $category->name,
                                    'callback_data' => 'getCatalog ' . $category->id
                                ])];
                        } else {
                            $keyboards[] = [
                                new InlineKeyboardButton([
                                    'text' => ' ' . $category->name . ' (' . $count . ')',
                                    'callback_data' => 'getCatalogList/' . $category->id
                                ])];
                        }
                    }

                }

            }
            $back = $root->parent()->one();
            if ($back) {
                $keyboards[] = [
                    new InlineKeyboardButton([
                        'text' => '↩ ' . $back->name,
                        'callback_data' => 'getCatalog ' . $back->id
                    ])];
            }
            $data = [
                'chat_id' => $chat_id,
                'parse_mode' => 'HTML',
                'text' => '⬇ <strong>' . $root->name . '</strong>' . $root->description . '',
                'reply_markup' => new InlineKeyboard([
                    'inline_keyboard' => $keyboards
                ]),
            ];


            //  print_r($msg);
            //  echo 'tester';
            //  $preg=  preg_match('/^getCatalog\s+([0-9]+)/iu', trim($callback_data), $match);
            //if($preg){
            //  print_r($message);die;
            $dataEdit['chat_id'] = $chat_id;
            $dataEdit['message_id'] = $message->getMessageId();
            $dataEdit['reply_markup'] = new InlineKeyboard([
                'inline_keyboard' => $keyboards
            ]);
            return Request::editMessageReplyMarkup($dataEdit);
            //  return Yii::$app->telegram->sendMessage($data);
            // }
        } elseif (preg_match('/^removeProductCart\/([0-9]+)\/([0-9]+)/iu', trim($callback_data), $match)) {
            $user_id = $callback_query->getFrom()->getId();

            $this->telegram->setCommandConfig('cartproductremove', [
                'product_id' => $match[2],
                'order_id' => $match[1],
            ]);
            return $this->telegram->executeCommand('cartproductremove');

        } elseif (preg_match('/^addCart\/([0-9]+)\/([0-9]+)\/(up|down)/iu', trim($callback_data), $match)) {
            $user_id = $callback_query->getFrom()->getId();

            $orderProduct = OrderProduct::findOne([
                'order_id' => $match[1],
                'product_id' => $match[2],
              //  'client_id' => $user_id
            ]);
            if ($match[3] == 'up') {
                $orderProduct->quantity++;
            } else {
                $orderProduct->quantity--;
            }
            if ($orderProduct->quantity >= 1) {
                $orderProduct->save(false);
            } else {
                //return $this->telegram
                //    ->setCommandConfig('cartproductremove', ['product_id' => $orderProduct->product_id])
                //    ->executeCommand('cartproductremove');
            }

            return $this->telegram
                ->setCommandConfig('cartproductquantity', [
                    'product_id' => $orderProduct->product_id,
                    'quantity' => $orderProduct->quantity
                ])
                ->executeCommand('cartproductquantity');


        } elseif (preg_match('/^addCart\/([0-9]+)/iu', trim($callback_data), $match)) {

            /* $product = Product::find()->published()->where(['id'=>$match[1]])->one();
             $dataCatalog = [
                 'chat_id' => $chat_id,
                 'text' => 'addCart',

             ];
 */
            $user_id = $callback_query->getFrom()->getId();

            $product = Product::findOne($match[1]);
            $order = Order::findOne(['client_id' => $user_id, 'checkout' => 0]);
            $quantity = 1;
            if (!$order) {
                $order = new Order;
                $order->client_id = $user_id;
                $order->firstname = $callback_query->getFrom()->getFirstName();
                $order->lastname = $callback_query->getFrom()->getLastName();
                $order->save();

                $order->addProduct($product, $quantity, $product->price);
            } else {
                $op = OrderProduct::findOne(['product_id' => $product->id, 'order_id' => $order->id]);
                if ($op) {
                    $op->quantity++;
                    $quantity = $op->quantity;
                    $op->save(false);
                } else {
                    $order->addProduct($product, $quantity, $product->price);
                }
            }

            $this->telegram->setCommandConfig('cartproductquantity', [
                'product_id' => $product->id,
                'quantity' => $quantity
            ]);
            $response = $this->telegram->executeCommand('cartproductquantity');

            return $response;

        } elseif (preg_match('/getCart/', trim($callback_data), $match)) { //preg_match('/^getCart\/([0-9]+)/iu', trim($callback_data), $match)

            echo $callback_data;
            $params = InlineKeyboardPager::getParametersFromCallbackData($callback_data);
            echo 'q: '. $params['page'].PHP_EOL;
            $this->telegram->setCommandConfig('cart', [
                'page' => $params['page'],
            ]);
            $response = $this->telegram->executeCommand('cart');

            return $response;
        } elseif (preg_match('/^getCatalogList\/([0-9]+)/iu', trim($callback_data), $match)) {
            $user_id = $callback_query->getFrom()->getId();
            $order = Order::findOne(['client_id'=>$user_id,'checkout'=>0]);
            if (isset($match[1])) {



                $query = Product::find()->published()->sort()->applyCategories($match[1]);
                $pages = new KeyboardPagination([
                    'totalCount' => $query->count(),
                    'defaultPageSize' => 2,
                    //'pageSize'=>3
                ]);
                $pages->setPage(1);
                $products = $query->offset($pages->offset)
                    ->limit($pages->limit)
                    ->all();



                $pager = new KeyboardPager([
                    'pagination' => $pages,
                    'lastPageLabel' => false,
                    'firstPageLabel' => false,
                    'maxButtonCount' => 1,
                    'command' => 'getCart',
                    'nextPageLabel' => '▶ еще'
                ]);



                if ($products) {

                    foreach ($products as $index => $product) {
                        $keyboards = [];
                        $caption = '<strong>' . $product->name . '</strong>' . PHP_EOL;
                        $caption .= $product->price . ' грн' . PHP_EOL . PHP_EOL;
                        $caption .= '<strong>Характеристики:</strong>' . PHP_EOL;
                        foreach ($this->attributes($product) as $name => $value) {
                            $caption .= '<strong>' . $name . '</strong>: ' . $value . PHP_EOL;
                        }
                        //  print_r($this->attributes($product));die;


                        $orderProduct = OrderProduct::findOne(['product_id' => $product->id, 'order_id' => $order->id]);
                        if ($orderProduct) {

                            //  $this->telegram->setCommandConfig('cartproductquantity', [
                            //      'product_id' => $orderProduct->product_id,
                            //      'quantity' => $orderProduct->quantity
                            //  ]);
                            // $response = $this->telegram->executeCommand('cartproductquantity');

                            //   return new CartproductquantityCommand()->getKeyboards();
                            $keyboards[] = [
                                new InlineKeyboardButton([
                                    'text' => '—',
                                    'callback_data' => "addCart/{$order->id}/{$product->id}/down"
                                ]),
                                new InlineKeyboardButton([
                                    'text' => '' . $orderProduct->quantity . ' шт.',
                                    'callback_data' => time()
                                ]),
                                new InlineKeyboardButton([
                                    'text' => '+',
                                    'callback_data' => "addCart/{$order->id}/{$product->id}/up"
                                ]),
                                new InlineKeyboardButton([
                                    'text' => '❌',
                                    'callback_data' => "removeProductCart/{$order->id}/{$product->id}"
                                ]),
                            ];
                            $keyboards[] = [
                                new InlineKeyboardButton([
                                    'text' => '🛍 Корзина',
                                    'callback_data' => "getCart"
                                ])
                            ];


                            //   $keyboards[] = $this->telegram->executeCommand('cartproductquantity')->getKeywords();
                        } else {
                            $keyboards[] = [
                                new InlineKeyboardButton([
                                    'text' => Yii::t('telegram/command','BUTTON_BUY',$product->price),
                                    'callback_data' => "addCart/{$product->id}"
                                ])
                            ];
                        }

                        if ($this->telegram->isAdmin($chat_id)) {
                            $keyboards[] = [
                                new InlineKeyboardButton(['text' => '✏', 'callback_data' => "productUpdate/{$product->id}"]),
                                new InlineKeyboardButton(['text' => '❌', 'callback_data' => "productDelete/{$product->id}"]),
                                new InlineKeyboardButton(['text' => '👁', 'callback_data' => "productHide/{$product->id}"])
                            ];
                        }

                        $dataPhoto = [
                            'photo' => $product->getImage()->getPathToOrigin(),
                            // 'photo' => 'https://yii2.pixelion.com.ua'.$product->getImage()->getUrl(),
                            'chat_id' => $chat_id,
                            'parse_mode' => 'HTML',
                            'caption' => $caption,
                            'reply_markup' => new InlineKeyboard([
                                'inline_keyboard' => $keyboards
                            ]),
                        ];
                        $response = Request::sendPhoto($dataPhoto);
                    }
                }




                $keyboards2[] = [
                    new KeyboardButton(['text' => '📂 Каталог', 'callback_data' => 'getCatalog']),
                    new KeyboardButton(['text' => '🛍 Корзина']),
                    new KeyboardButton(['text' => 'еще']),
                   // new KeyboardMore(['pagination' => $pages])
                ];
                $data['chat_id'] = $chat_id;
                $data['text'] = $pages->page . ' / ' . $pages->totalCount;
                $data['reply_markup'] = (new Keyboard([
                    'keyboard' => $keyboards2
                ]))->setResizeKeyboard(true)->setOneTimeKeyboard(true)->setSelective(true);
                return Request::sendMessage($data);

            }

            return Request::emptyResponse();
        } else {
            $text = ' Hello World!';
        }
        /*  $data = [
              'callback_query_id' => $callback_query_id,
              'text'              => $text,
              'show_alert'        => $callback_data === 'thumb up',
              'cache_time'        => 5,
          ];*/

        return Request::answerCallbackQuery($data);

    }

    protected $_attributes;
    public $model;
    protected $_models;

    public function attributes($product)
    {

        $eav = $product;
        /** @var \panix\mod\shop\components\EavBehavior $eav */
        $this->_attributes = $eav->getEavAttributes();


        $data = [];
        $groups = [];
        foreach ($this->getModels() as $model) {
            /** @var Attribute $model */
            $abbr = ($model->abbreviation) ? ' ' . $model->abbreviation : '';

            $value = $model->renderValue($this->_attributes[$model->name]) . $abbr;

            $data[$model->title] = $value;
        }

        return $data;

    }

    public function getModels()
    {
        if (is_array($this->_models))
            return $this->_models;

        $this->_models = [];
        //$cr = new CDbCriteria;
        //$cr->addInCondition('t.name', array_keys($this->_attributes));

        // $query = Attribute::getDb()->cache(function () {
        $query = Attribute::find()
            ->where(['IN', 'name', array_keys($this->_attributes)])
            ->displayOnFront()
            ->sort()
            ->all();
        // }, 3600);


        foreach ($query as $m)
            $this->_models[$m->name] = $m;

        return $this->_models;
    }

    public function callbacktest($ccc){
        echo 'zzz';
    }

}
