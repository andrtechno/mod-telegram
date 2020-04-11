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

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Entities\InlineKeyboardButton;
use Longman\TelegramBot\Request;
use panix\mod\telegram\models\OrderProduct;

/**
 *
 * Display an inline keyboard with a few buttons.
 */
class CartproductremoveCommand extends SystemCommand
{
    /**
     * @var string
     */
    protected $name = 'cartproductremove';

    /**
     * @var string
     */
    protected $description = 'Remove product in cart';

    /**
     * @var string
     */
    protected $version = '1.0.0';
    public $product_id;

    /**
     * Command execute method
     *
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute()
    {

        if (($this->product_id = trim($this->getConfig('product_id'))) === '') {
            $this->product_id = NULL;
        }





        $update = $this->getUpdate();
        if ($update->getCallbackQuery()) {
            $message = $update->getCallbackQuery()->getMessage();
            $user_id = $update->getCallbackQuery()->getFrom()->getId();
        } else {
            $message = $this->getMessage();
            $user_id = $message->getFrom()->getId();
        }


        $chat_id = $message->getChat()->getId();


echo 'zz';
        $orderProduct = OrderProduct::findOne(['product_id' => $this->product_id, 'client_id' => $user_id]);
        if($orderProduct){
            $originalProduct = $orderProduct->originalProduct;
            $orderProduct->delete();


            $keyboards[] = [
                new InlineKeyboardButton([
                    'text' => '👉 ' . $originalProduct->price . ' UAH. — Купить 👈',
                    'callback_data' => "addCart/{$originalProduct->id}"
                ])
            ];
            if ($this->telegram->isAdmin($chat_id)) {
                $keyboards[] = [
                    new InlineKeyboardButton(['text' => '✏', 'callback_data' => "productUpdate/{$originalProduct->id}"]),
                    new InlineKeyboardButton(['text' => '❌', 'callback_data' => "productDelete/{$originalProduct->id}"]),
                    new InlineKeyboardButton(['text' => '👁', 'callback_data' => "productHide/{$originalProduct->id}"])
                ];
            }


            $dataEdit['chat_id'] = $chat_id;
            $dataEdit['message_id'] = $message->getMessageId();
            $dataEdit['reply_markup'] = new InlineKeyboard([
                'inline_keyboard' => $keyboards
            ]);


            return Request::editMessageReplyMarkup($dataEdit);


        }






        return Request::emptyResponse();
    }
}
