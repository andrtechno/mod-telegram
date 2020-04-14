<?php

namespace panix\mod\telegram\commands\UserCommands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Request;
use Yii;
/**
 * User "/cell" command
 */
class CallCommand extends UserCommand
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
            $text = Yii::t('telegram/command', 'USAGE', $this->getUsage());
        }

        $data = [
            'chat_id' => $chat_id,
            'text' => $text . 'zzz',
        ];

        return Request::sendMessage($data);
    }
}
