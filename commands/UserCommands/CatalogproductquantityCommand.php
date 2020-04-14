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

/**
 *
 * Display an inline keyboard with a few buttons.
 */
class CatalogproductquantityCommand extends SystemCommand
{
    /**
     * @var string
     */
    protected $name = 'catalogproductquantity';

    /**
     * @var string
     */
    protected $description = 'Change product quantity in catalog';

    /**
     * @var string
     */
    protected $version = '1.0.0';
    public $product_id;
    public $quantity;
    public $order_id;
    private $chat_id;

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
        if (($this->quantity = trim($this->getConfig('quantity'))) === '') {
            $this->quantity = NULL;
        }
        if (($this->order_id = trim($this->getConfig('order_id'))) === '') {
            $this->order_id = NULL;
        }
        $update = $this->getUpdate();
        if ($update->getCallbackQuery()) {
            $message = $update->getCallbackQuery()->getMessage();
        } else {
            $message = $this->getMessage();
        }


        $chat_id = $message->getChat()->getId();

        $keyboards[] = [
            new InlineKeyboardButton([
                'text' => '—',
                'callback_data' => "addCart/{$this->order_id}/{$this->product_id}/down/catalog"
            ]),
            new InlineKeyboardButton([
                'text' => '' . $this->quantity . ' шт.',
                'callback_data' => time()
            ]),
            new InlineKeyboardButton([
                'text' => '+',
                'callback_data' => "addCart/{$this->order_id}/{$this->product_id}/up/catalog"
            ]),
            new InlineKeyboardButton([
                'text' => '❌',
                'callback_data' => "removeProductCart/{$this->product_id}"
            ]),
        ];
        $keyboards[] = [
            new InlineKeyboardButton([
                'text' => '🛍 Корзина',
                'callback_data' => "getCart"
            ])
        ];
        if ($this->telegram->isAdmin($chat_id)) {
            $keyboards[] = [
                new InlineKeyboardButton(['text' => '✏', 'callback_data' => "productUpdate/{$this->product_id}"]),
                new InlineKeyboardButton(['text' => '❌', 'callback_data' => "productDelete/{$this->product_id}"]),
                new InlineKeyboardButton(['text' => '👁', 'callback_data' => "productHide/{$this->product_id}"])
            ];
        }


        $dataEdit['chat_id'] = $chat_id;
        $dataEdit['message_id'] = $message->getMessageId();
        $dataEdit['reply_markup'] = new InlineKeyboard([
            'inline_keyboard' => $keyboards
        ]);


        return Request::editMessageReplyMarkup($dataEdit);
    }

}
