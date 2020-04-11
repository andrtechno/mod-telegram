<?php
/**
 * This file is part of the TelegramBot package.
 *
 * (c) Avtandil Kikabidze aka LONGMAN <akalongman@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace panix\mod\telegram\commands\SystemCommands;
use panix\mod\shop\models\Product;
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
        //Do nothing, just for rewriting default Longman command

        $callback_query    = $this->getCallbackQuery();


        $callback_query_id = $callback_query->getId();
        $callback_data     = $callback_query->getData();

        if($callback_data == 'callbackqueryproduct'){

            $product = Product::findOne(2665);
            $text= ' get product'.$product->name;
        }else{
            $text= ' Hello World!';
        }
        $data = [
            'callback_query_id' => $callback_query_id,
            'text'              => $text,
            'show_alert'        => $callback_data === 'thumb up',
            'cache_time'        => 5,
        ];
return Yii::$app->telegram->sendMessage($data);
       // return Request::answerCallbackQuery($data);

       // return Request::emptyResponse();
    }
}
