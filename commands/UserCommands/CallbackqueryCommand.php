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
use Longman\TelegramBot\Entities\KeyboardButton;
use panix\mod\shop\models\Attribute;
use panix\mod\shop\models\Category;
use panix\mod\shop\models\Product;
use panix\mod\telegram\components\KeyboardMore;
use panix\mod\telegram\components\KeyboardPagination;
use panix\mod\telegram\models\AuthorizedManagerChat;
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
if($callback_data == '/cart'){
    $this->telegram->executeCommand('cart');
    return Request::emptyResponse();
}
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


        } elseif (preg_match('/^getCatalog\s+([0-9]+)/iu', trim($callback_data), $match)) {


//print_r($message);die;

            $id = (isset($match[1])) ? $match[1] : 1;
            $root = Category::findOne($id);

            $categories = $root->children()->all();


            $keyboards = [];
            if ($categories) {

                foreach ($categories as $category) {
                    $child = $category->children()->all();
                    $count = $category->countItems;
                    if ($count) {
                        if ($child) {
                            $keyboards[] = [new InlineKeyboardButton(['text' => '📂 ' . $category->name, 'callback_data' => 'getCatalog ' . $category->id])];
                        } else {
                            $keyboards[] = [new InlineKeyboardButton(['text' => ' ' . $category->name . ' (' . $count . ')', 'callback_data' => 'getCatalogList/' . $category->id])];
                        }
                    }

                }
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

        } elseif (preg_match('/^addCart\/([0-9]+)/iu', trim($callback_data), $match)) {

           /* $product = Product::find()->published()->where(['id'=>$match[1]])->one();
            $dataCatalog = [
                'chat_id' => $chat_id,
                'text' => 'addCart',

            ];
*/

            $keyboards[] = [
                new InlineKeyboardButton([
                    'text' => 'товарв в корзине',
                    'callback_data' => "openCart"
                ]),
                new InlineKeyboardButton([
                    'text' => '🛍 Корзина',
                    'callback_data' => "/cart"
                ])
            ];
            if ($this->telegram->isAdmin($chat_id)) {
                $keyboards[] = [
                    new InlineKeyboardButton(['text' => '✏', 'callback_data' => 'get']),
                    new InlineKeyboardButton(['text' => '❌', 'callback_data' => 'get']),
                    new InlineKeyboardButton(['text' => '👁', 'callback_data' => 'get'])
                ];
            }


            $dataEdit['chat_id'] = $chat_id;
            $dataEdit['message_id'] = $update->getCallbackQuery()->getMessage()->getMessageId();
            $dataEdit['reply_markup'] = new InlineKeyboard([
                'inline_keyboard' => $keyboards
            ]);
            return Request::editMessageReplyMarkup($dataEdit);

        } elseif (preg_match('/^getCatalogList\/([0-9]+)/iu', trim($callback_data), $match)) {


            if (isset($match[1])) {


                $query = Product::find()->published()->sort()->applyCategories($match[1]);
                $pages = new KeyboardPagination(['totalCount' => $query->count()]);
                $products = $query->offset($pages->offset)
                    ->limit($pages->limit)
                    ->all();


                $test = new KeyboardMore(['pagination' => $pages,
                ]);


                $keyboards2[] = [
                    new KeyboardButton(['text' => '📂 Каталог', 'callback_data' => 'getCatalog']),
                    new KeyboardButton(['text' => '🛍 Корзина'])
                ];

                $keyboards2 = array_merge($test->buttons, $keyboards2);
              /*  $dataCatalog = [
                    'chat_id' => $chat_id,
                    'text' => 'test',

                ];
                $dataCatalog['reply_markup'] = (new Keyboard([
                    'keyboard' => $keyboards2
                ]))->setResizeKeyboard(true)->setOneTimeKeyboard(true)->setSelective(true);
                Request::sendMessage($dataCatalog);*/


                $products = $query->all();
                if ($products) {
                    foreach ($products as $index => $product) {
                        $keyboards = [];
                        $caption = '<strong>' . $product->name . '</strong>' . PHP_EOL;
                        $caption .= $product->price . ' грн' . PHP_EOL . PHP_EOL;
                        $caption .= '<strong>Характеристики:</strong>' . PHP_EOL;
                        foreach ($this->attributes($product) as $name=>$value){
                            $caption .= '<strong>'.$name . '</strong>: '.$value . PHP_EOL;
                        }
                      //  print_r($this->attributes($product));die;


                        $keyboards[] = [
                            new InlineKeyboardButton([
                                'text' => '👉 ' . $product->price . ' UAH. — Купить 👈',
                                'callback_data' => "addCart/{$product->id}"
                            ])
                        ];
                        if ($this->telegram->isAdmin($chat_id)) {
                            $keyboards[] = [
                                new InlineKeyboardButton(['text' => '✏', 'callback_data' => 'get']),
                                new InlineKeyboardButton(['text' => '❌', 'callback_data' => 'get']),
                                new InlineKeyboardButton(['text' => '👁', 'callback_data' => 'get'])
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
                        $photoRequest = Request::sendPhoto($dataPhoto);
                    }
                }

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

        $callbackDataArr = explode(' ', $callback_data);

        if ($callbackDataArr[0] == 'client_chat_id') {

            $data['show_alert'] = true;
            //Закрепляем чат за авторизованным менеджером
            $authChat = AuthorizedManagerChat::findOne(intval($chatId));
            $authChat->client_chat_id = $callbackDataArr[1];
            if ($authChat->validate() && $authChat->save()) {
                $data['text'] = Yii::t('telegram/default', 'Start conversation with chat ') . $callbackDataArr[1];
                Request::answerCallbackQuery($data);
                unset($data['show_alert'], $data['callback_query_id']);
                $data['chat_id'] = $chatId;
                return Yii::$app->telegram->sendMessage($data);
                // return Request::sendMessage($data);
            } else {
                try {
                    $authChat = AuthorizedManagerChat::find()->where(['client_chat_id' => $callbackDataArr[1]])->one();
                    $manager = Usernames::find()->where(['chat_id' => $authChat->chat_id])->one();
                    $data['text'] = Yii::t('telegram/default', 'Conversation already in progress in this chat. Responsible: ') . ($manager->username ? $manager->username : "not_found");
                } catch (\Exception $e) {
                    $data['text'] = Yii::t('telegram/default', 'Seems conversation already in progress in this chat.');
                }
                unset($data['show_alert'], $data['callback_query_id']);
                $data['chat_id'] = $chatId;


                return Yii::$app->telegram->sendMessage($data);

                //return Request::sendMessage($data);
            }
        } else {
            $data['text'] = Yii::t('telegram/default', 'Unknown command.');
            $data['show_alert'] = false;

            // return Yii::$app->telegram->sendMessage($data);
            return Request::answerCallbackQuery($data);
        }

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
}
