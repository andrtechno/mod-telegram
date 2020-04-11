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
use Longman\TelegramBot\Conversation;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\KeyboardButton;
use Longman\TelegramBot\Entities\PhotoSize;
use Longman\TelegramBot\Request;
use panix\mod\cart\models\Delivery;
use panix\mod\cart\models\Payment;
use Yii;

/**
 * User "/checkout" command
 *
 * Command that demonstrated the Conversation funtionality in form of a simple survey.
 */
class CheckOutCommand extends SystemCommand
{
    /**
     * @var string
     */
    protected $name = 'checkout';

    /**
     * @var string
     */
    protected $description = 'checkout';

    /**
     * @var string
     */
    protected $usage = '/checkout';

    /**
     * @var string
     */
    protected $version = '1.0.0';

    /**
     * @var bool
     */
    protected $need_mysql = true;

    /**
     * @var bool
     */
    protected $private_only = true;

    /**
     * Conversation Object
     *
     * @var \Longman\TelegramBot\Conversation
     */
    protected $conversation;

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


        //Preparing Response


        if ($chat->isGroupChat() || $chat->isSuperGroup()) {
            //reply to message id is applied by default
            //Force reply is applied by default so it can work with privacy on
            $data['reply_markup'] = Keyboard::forceReply(['selective' => true]);
        }

        //Conversation start
        $this->conversation = new Conversation($user_id, $chat_id, $this->getName());

        $notes = &$this->conversation->notes;
        !is_array($notes) && $notes = [];

        //cache data from the tracking session if any
        $state = 0;
        if (isset($notes['state'])) {
            $state = $notes['state'];
        }
        $data['chat_id'] = $chat_id;
        if ($state ==0) {

           // $data['parse_mode'] = 'HTML';
           // $data['text'] = 'Оформление заказа';
           // $data['reply_markup'] = $this->startKeyboards();
           // $hello = Request::sendMessage($data);
        }


        $result = Request::emptyResponse();

        //State machine
        //Entrypoint of the machine state if given by the track
        //Every time a step is achieved the track is updated
        switch ($state) {
            case 0:



                if ($text === '') {
                    $notes['state'] = 0;
                    $this->conversation->update();
                    if($user->getFirstName() && $user->getLastName()){
                        $data['text'] = $user->getFirstName().' '.$user->getLastName();
                      //  $text = $data['text'];


                        $data['reply_markup'] = (new Keyboard([$user->getFirstName().' '.$user->getLastName()]))
                            ->setResizeKeyboard(true)
                            ->setOneTimeKeyboard(true)
                            ->setSelective(true);

                        $result = Request::sendMessage($data);
                    }else{
                        $data['text'] = 'ФИО:';
                        $data['reply_markup'] = Keyboard::remove(['selective' => true]);
                        $result = Request::sendMessage($data);
                    }

                    break;
                }

                $notes['name'] = $text;
                $text = '';
            // no break
            case 1:

                $delivery = Delivery::find()->all();
                $deliveryList = [];
                foreach ($delivery as $item){
                    $deliveryList[]=$item->name;
                }

                if ($text === '' || !in_array($text, $deliveryList, true)) {
                    $notes['state'] = 1;
                    $this->conversation->update();

                    $data['reply_markup'] = (new Keyboard($deliveryList))
                        ->setResizeKeyboard(true)
                        ->setOneTimeKeyboard(true)
                        ->setSelective(true);

                    $data['text'] = 'Выберите вариант доставки:';
                    if ($text !== '') {
                        $data['text'] = 'Выберите вариант доставки, на клавиатуры:';
                    }

                    $result = Request::sendMessage($data);
                    break;
                }

                $notes['delivery'] = $text;
            // no break
            case 2:

                $payments = Payment::find()->all();
                $paymentList = [];
                foreach ($payments as $k=>$item){

                    $paymentList[]=$item->name;
                }

                if ($text === '' || !in_array($text, $paymentList, true)) {
                    $notes['state'] = 2;
                    $this->conversation->update();

                    $data['reply_markup'] = (new Keyboard($paymentList))
                        ->setResizeKeyboard(true)
                        ->setOneTimeKeyboard(true)
                        ->setSelective(true);

                    $data['text'] = 'Выберите вариант оплаты:';
                    if ($text !== '') {
                        $data['text'] = 'Выберите вариант оплаты, на клавиатуры:';
                    }

                    $result = Request::sendMessage($data);
                    break;
                }

                $notes['payment'] = $text;
            // no break
            case 3:
                if ($message->getContact() === null) {
                    $notes['state'] = 3;
                    $this->conversation->update();

                    $data['reply_markup'] = (new Keyboard(
                        (new KeyboardButton('Оставить конакты'))->setRequestContact(true)
                    ))
                        ->setOneTimeKeyboard(true)
                        ->setResizeKeyboard(true)
                        ->setSelective(true);

                    $data['text'] = 'Share your contact information:';

                    $result = Request::sendMessage($data);
                    break;
                }

                $notes['phone_number'] = $message->getContact()->getPhoneNumber();

            // no break
            case 4:
                $this->conversation->update();
                $out_text = '✅ Ваш заказ успешно оформлен' . PHP_EOL;
                unset($notes['state']);
                foreach ($notes as $k => $v) {
                    $out_text .= PHP_EOL . '<strong>'.ucfirst($k) . '</strong>: ' . $v;
                }

                $data['parse_mode'] = 'HTML';
                $data['reply_markup'] = Keyboard::remove(['selective' => true]);
                $data['text'] = $out_text;
                $this->conversation->stop();

                $result = Request::sendMessage($data);
                break;
        }

        return $result;
    }
}
