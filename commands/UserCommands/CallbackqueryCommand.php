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
use panix\mod\shop\models\Product;
use panix\mod\telegram\models\AuthorizedManagerChat;
use panix\mod\telegram\models\Usernames;
use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Request;
use Yii;
use yii\helpers\Url;

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
        //print_r($callback_query);die;
        $chatId = $callback_query->getMessage()->getChat()->getId();
        $callback_query_id = $callback_query->getId();
        $callback_data = $callback_query->getData();

        $data['callback_query_id'] = $callback_query_id;

        if($callback_data == 'getProduct') {

            $product = Product::find()->where(['id' => 2665])->one();
            $inline_keyboard = new InlineKeyboard([
                ['text' => '👉 '.$product->price . ' грн. Купить', 'callback_data' => 'callbackqueryproduct']], [
                ['text' => '🏆 ☎️  🛒 🎁 Характеристики', 'callback_data' => 'product_attributes'],
                ['text' => '🤝  🚚 callback thumb up ', 'callback_data' => 'thumb up'],
            ]);

//echo Yii::getAlias('@app/web/uploads').DIRECTORY_SEPARATOR.'1.jpg';die;

            $data = [
                'chat_id' => $chatId,
                'parse_mode' => 'HTML',
                'callback_query_id' => $callback_query_id,
                'text' => Yii::$app->view->render('@telegram/views/default/test', ['product' => $product]),
                'reply_markup' => $inline_keyboard,
            ];
            $sendPhoto = Yii::$app->telegram->sendPhoto([
                'photo' => Yii::getAlias('@app/web/uploads') . DIRECTORY_SEPARATOR . '1.jpg',
                'chat_id' => $chatId,
                'parse_mode' => 'HTML',
                'caption' => 'LALAL 💵 💴 💶 💷 💰 💳  ✉️📦 📁 📄 📞 ✔ 🔴 🇺🇦<strong>sadsasdadsa sad as dasdas das d asd asd asd asd asdsdadsa</strong>',
                'reply_markup' => $inline_keyboard,
            ]);
            return Yii::$app->telegram->sendMessage($data);
        }elseif($callback_data == 'product_attributes'){
            $product = Product::find()->where(['id' => 2665])->one();
            $eav = $product->getEavAttributes();
            $data = [
                'chat_id' => $chatId,
               // 'parse_mode' => 'HTML',
                'callback_query_id' => $callback_query_id,
                'show_alert'=>true,
                'text' => 'Тут будут параметры '.$callback_query_id,
               // 'reply_markup' => $inline_keyboard,
            ];


            return Request::answerCallbackQuery($data);
        }else{
            $text= ' Hello World!';
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
            if ($authChat->validate() && $authChat->save()){
                $data['text'] = Yii::t('telegram/default', 'Start conversation with chat ') . $callbackDataArr[1];
                Request::answerCallbackQuery($data);
                unset($data['show_alert'], $data['callback_query_id']);
                $data['chat_id'] = $chatId;
                return Yii::$app->telegram->sendMessage($data);
               // return Request::sendMessage($data);
            }else{
                try {
                    $authChat = AuthorizedManagerChat::find()->where(['client_chat_id' => $callbackDataArr[1]])->one();
                    $manager = Usernames::find()->where(['chat_id' => $authChat->chat_id])->one();
                    $data['text'] = Yii::t('telegram/default', 'Conversation already in progress in this chat. Responsible: ') . ($manager->username ? $manager->username : "not_found");
                } catch (\Exception $e){
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
}
