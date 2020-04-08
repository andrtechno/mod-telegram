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
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\KeyboardButton;
use Longman\TelegramBot\Request;
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
        $chat_id = $this->getMessage()->getChat()->getId();

        $switch_element = mt_rand(0, 9) < 5 ? 'true' : 'false';

        $inline_keyboard = new InlineKeyboard([
            ['text' => 'inline', 'switch_inline_query' => $switch_element],
            ['text' => 'inline current chat', 'switch_inline_query_current_chat' => $switch_element],
        ], [
            ['text' => 'callback', 'callback_data' => 'callbackqueryproduct'],
            ['text' => 'callback thumb up ', 'callback_data' => 'thumb up'],
        ]);


        $data = [
            'chat_id'      => $chat_id,
            'text'         => 'inline keyboard',
           // 'reply_markup' => $inline_keyboard,
        ];
       /* $data['reply_markup'] = (new Keyboard(['купить', (new KeyboardButton(['text'=>'Share Contact']))->setText('asddsa')]))
            ->setResizeKeyboard(true)
            ->setOneTimeKeyboard(true)
            ->setSelective(true);*/


        /*$data['reply_markup'] = (new Keyboard(['купить222222', (new KeyboardButton(['text'=>'Share Contact', 'callback_data' => 'callbackqueryproduct']))->setText('asddsa')]))
            ->setResizeKeyboard(false)
            ->setOneTimeKeyboard(false)
            ->setSelective(false);*/

        $telegram = new \Longman\TelegramBot\Telegram('835652742:AAEBdMpPg9TgakFa2o8eduRSkynAZxipg-c', 'pixelion');
        $data['reply_markup'] = (new Keyboard(['Мужской', 'Женский']))
            ->setResizeKeyboard(true)
            ->setOneTimeKeyboard(true)
            ->setSelective(true);




        return Request::sendMessage($data);

       // return Yii::$app->telegram->sendMessage($data);
    }
}
