<?php
/**
 * This file is part of the TelegramBot package.
 *
 * (c) Avtandil Kikabidze aka LONGMAN <akalongman@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace panix\mod\telegram\commands\UserCommands;

use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\KeyboardButton;
use Longman\TelegramBot\Request;
use panix\mod\telegram\components\Command;
use Yii;

/**
 * User "/help" command
 */
class HelpCommand extends Command
{
    /**#@+
     * {@inheritdoc}
     */
    protected $name = 'help';
    protected $description = '';
    protected $usage = '/help or /help <command>';
    protected $version = '1.0';

    /**#@-*/

    public function __construct($telegram, $update = NULL)
    {
        $this->description = 'Show bot commands help';
        parent::__construct($telegram, $update);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $message = $this->getMessage();
        $chat_id = $message->getChat()->getId();

        $message_id = $message->getMessageId();
        $command = trim($message->getText(true));

        //Only get enabled Admin and User commands
        $commands = array_filter($this->telegram->getCommandsList(), function ($command) {
            return (!$command->isSystemCommand() && $command->isEnabled());
        });

        //If no command parameter is passed, show the list
        if ($command === '') {
            $text = $this->telegram->getBotUsername() . ' v. ' . $this->telegram->getVersion() . "\n\n";
            $text .= Yii::t('telegram/command', 'COMMAND_LIST') . PHP_EOL;
            foreach ($commands as $command) {

               // if($command->getName() == 'debug'){
              //      print_r($command);
               // }
                $text .= '/' . $command->getName() . ' - ' . $command->getDescription() . "\n";
            }

            $text .= "\n" . Yii::t('telegram/command', 'EXACT_COMMAND') . ': /help <command>';
        } else {

            $command = str_replace('/', '', $command);
            if (isset($commands[$command])) {
                $command = $commands[$command];

                $text = 'Command: ' . $command->getName() . ' v' . $command->getVersion() . "\n";
                $text .= 'Description: ' . $command->getDescription() . "\n";
                $text .= 'Usage: ' . $command->getUsage();
            } else {
                $text = 'No help available: Command /' . $command . ' not found.';
            }
        }

        $data = [
            'chat_id' => $chat_id,
            'reply_to_message_id' => $message_id,
            'text' => $text,
        ];


        $keyboards[] = [
            new KeyboardButton(['text' => '☎ Позвонить']), //260E
            new KeyboardButton(['text' => '✉ Написать']), //2709
          //  new KeyboardButton(['text' => '📞 Написать']) //1F4DE
        ];
       // $keyboards[] = [

          //  new KeyboardButton(['text' => '📦 Мои заказы'])
       // ];
        $keyboards[] = [
            new KeyboardButton(['text' => '⚙ Настройки']),
            new KeyboardButton(['text' => '❓ Помощь'])
        ];

        $reply_markup = (new Keyboard([
            'keyboard' => $keyboards
        ]))->setResizeKeyboard(true)->setOneTimeKeyboard(true)->setSelective(true);


        $data['reply_markup'] = $reply_markup;

        return Request::sendMessage($data);
    }
}
