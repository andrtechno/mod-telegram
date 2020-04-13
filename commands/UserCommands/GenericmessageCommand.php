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

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Conversation;
use Longman\TelegramBot\Request;

/**
 * Generic message command
 *
 * Gets executed when any type of message is sent.
 */
class GenericmessageCommand extends SystemCommand
{
    /**
     * @var string
     */
    protected $name = 'genericmessage';

    /**
     * @var string
     */
    protected $description = 'Handle generic message';

    /**
     * @var string
     */
    protected $version = '1.1.0';

    /**
     * @var bool
     */
    protected $need_mysql = true;

    /**
     * Command execute method if MySQL is required but not available
     *
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function executeNoDb()
    {
        // Do nothing
        return Request::emptyResponse();
    }

    /**
     * Command execute method
     *
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute()
    {
        //If a conversation is busy, execute the conversation command after handling the message
        $conversation = new Conversation(
            $this->getMessage()->getFrom()->getId(),
            $this->getMessage()->getChat()->getId()
        );

        $text = trim($this->getMessage()->getText());

        if (preg_match('/^(\x{1F6CD})/iu', $text, $match)) { //cart emoji
            return $this->telegram->executeCommand('cart');
        } elseif (preg_match('/^(\x{1F4C2})/iu', $text, $match)) { //folder emoji
           return $this->telegram->executeCommand('catalog');
        } elseif (preg_match('/^(\x{1F3E0})/iu', $text, $match)) { //home emoji
            $this->telegram->executeCommand('start');
            return $this->telegram->executeCommand('cancel');

        } elseif ($text == 'Отмена') {
            return $this->telegram->executeCommand('cancel');
        } elseif (preg_match('/^(\x{2753})/iu', $text, $match)) { //help emoji
            return $this->telegram->executeCommand('help');
        } elseif (preg_match('/^(\x{1F4E2})/iu', $text, $match)) { //news emoji
            return $this->telegram->executeCommand('news');
        } elseif (preg_match('/^(\x{1F4E6})/iu', $text, $match)) { //my carts emoji
            //  $telegram->executeCommand('help');
        } elseif (preg_match('/^(\x{260E}|\x{1F4DE})/iu', $text, $match)) { //phone emoji
            return $this->telegram->executeCommand('call');
        } elseif (preg_match('/^(\x{2709})/iu', $text, $match)) { //feedback emoji
            return $this->telegram->executeCommand('feedback');
        } elseif (preg_match('/^(\x{1F4E6})/iu', $text, $match)) { //package emoji
            return $this->telegram->executeCommand('history');
        } elseif (preg_match('/^(\x{2699})/iu', $text, $match)) { //gear emoji
            return $this->telegram->executeCommand('settings');
        } elseif (preg_match('/^(\x{1F50E})/iu', $text, $match)) { //search emoji
            return $this->telegram->executeCommand('search');
        }


        //Fetch conversation command if it exists and execute it
        if ($conversation->exists() && ($command = $conversation->getCommand())) {
            return $this->telegram->executeCommand($command);
        }

        return Request::emptyResponse();
    }
}
