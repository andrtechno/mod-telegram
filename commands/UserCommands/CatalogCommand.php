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

use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Entities\InlineKeyboardButton;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\KeyboardButton;
use Longman\TelegramBot\Request;
use panix\mod\shop\models\Category;
use panix\mod\telegram\components\Command;
use Yii;

/**
 * User "/catalog" command
 *
 * Display an inline keyboard with a few buttons.
 */
class CatalogCommand extends Command
{
    /**
     * @var string
     */
    protected $name = 'catalog';

    /**
     * @var string
     */
    protected $description = 'get catalog';

    /**
     * @var string
     */
    protected $usage = '/catalog <id>';

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


        $preg = preg_match('/^(\/catalog)\s([0-9]+)/', trim($message->getText()), $match);
        //if ($message->getText() == '/catalog' || $preg) {
            $id = (isset($match[1])) ? $match[1] : 1;
            $root = Category::findOne($id);
            $categories = $root->children()->all();


            $inlineKeyboards = [];
            if ($categories) {

                foreach ($categories as $category) {
                    $child = $category->children()->all();
                    $count = $category->countItems;
                    if ($count) {
                        if ($child) {
                            $inlineKeyboards[] = [new InlineKeyboardButton(['text' => '📂 ' . $category->name . ' (' . $count . ')', 'callback_data' => 'getCatalog ' . $category->id])];
                        } else {
                            $inlineKeyboards[] = [new InlineKeyboardButton(['text' => '📄 ' . $category->name . ' (' . $count . ')', 'callback_data' => 'getCatalogList ' . $category->id])];
                        }
                    }

                }
            }

            /*$sticker=[
                'chat_id' => $chat_id,
                'sticker'=>'BQADBAADsgUAApv7sgABW0IQT2B3WekC'
            ];
            Request::sendSticker($sticker);*/


            $data = [
                'chat_id' => $chat_id,
                'text' => 'Выберите раздел:',
                'reply_markup' => new InlineKeyboard([
                    'inline_keyboard' => $inlineKeyboards
                ]),
            ];


            $dataCatalog = [
                'chat_id' => $chat_id,
                'text' => '⬇ Каталог продукции',

            ];
            $keyboards[] = [
                new KeyboardButton(['text' => '🏠 Начало', 'callback_data' => 'goHome']),
            ];

            if ($this->telegram->isAdmin($chat_id)) {
                //  $keyboards[] = [new InlineKeyboardButton(['text' => '✏ 📝  ⚙ Редактировать', 'callback_data' => 'get']), new InlineKeyboardButton(['text' => '❌ Удалить', 'callback_data' => 'get'])];
                //  $keyboards[] = [new InlineKeyboardButton(['text' => '❓ 👤  👥 🛍 ✅ 🟢 🔴Удалить', 'callback_data' => 'get'])];
            }


            $dataCatalog['reply_markup'] = (new Keyboard([
                'keyboard' => $keyboards
            ]))->setResizeKeyboard(true)->setOneTimeKeyboard(true)->setSelective(true);
            Request::sendMessage($dataCatalog);


            $result =  $data;
       // }
         return Request::sendMessage($result);
        // return Yii::$app->telegram->sendMessage($result);
    }

}
