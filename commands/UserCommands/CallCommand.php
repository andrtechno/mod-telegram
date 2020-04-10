<?php

namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Request;
use panix\mod\telegram\components\Command;

/**
 * User "/cell" command
 */
class CallCommand extends Command
{
    /**#@+
     * {@inheritdoc}
     */
    protected $name = 'call';
    protected $description = 'call number';
    protected $usage = '/call <number>';
    protected $version = '1.0.1';
    public $enabled = true;
    public $private_only = true;
    /**#@-*/

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $message = $this->getMessage();
        $chat_id = $message->getChat()->getId();
        $text = trim($message->getText(true));

        if ($text === '') {
            $text = 'Command usage: ' . $this->getUsage();
        }

        $data = [
            'chat_id' => $chat_id,
            'text' => $text,
        ];

        return Request::sendMessage($data);
    }
}
