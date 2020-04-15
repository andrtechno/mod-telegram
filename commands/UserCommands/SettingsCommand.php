<?php

namespace panix\mod\telegram\commands\UserCommands;


use Longman\TelegramBot\Request;
use panix\mod\telegram\commands\pager\InlineKeyboardPagination;
use panix\mod\telegram\components\UserCommand;


/**
 * User "/settings" command
 */
class SettingsCommand extends UserCommand
{
    /**#@+
     * {@inheritdoc}
     */
    protected $name = 'settings';
    protected $description = 'setting user profile';
    protected $usage = '/settings <name> <value>';
    protected $version = '1.0.1';
    public $enabled = false;
    public $private_only = true;

    public $notification = true;
    /**#@-*/

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $message = $this->getMessage();
        $chat = $message->getChat();
        $chat_id = $chat->getId();
        $text = trim($message->getText(true));

        if ($text === '') {
            $text = 'Command usage: ' . $this->getUsage();
        }
        $text2 = trim($message->getText(false));
        //echo $text . ' - ' . $text2 . PHP_EOL;

        $dataPoll = [
            'chat_id' => $chat_id,
            'question' => 'Test Poll',
            'is_anonymous' => true,
            'type' => 'quiz', //quiz, regular
            'allows_multiple_answers' => false,
            //'options'=>['test','test2']
            'options' => json_encode(['jhhh', 'jhhsh'])
        ];
        $dataDice = [
            'chat_id' => $chat_id,
            'question' => 'Test Poll',
            'is_anonymous' => true,
            'type' => 'quiz', //quiz, regular
            'allows_multiple_answers' => false,
            //'options'=>['test','test2']
            'options' => json_encode(['jhhh', 'jhhsh'])
        ];

        /*  $results = Request::sendToActiveChats(
              'sendMessage', // Callback function to execute (see Request.php methods)
              ['text' => $chat->getFirstName().' '.$chat->getLastName().' @'.$chat->getUsername().'! go go go!'], // Param to evaluate the request
              [
                  'groups'      => true,
                  'supergroups' => true,
                  'channels'    => false,
                  'users'       => true,
              ]
          );*/
       // echo $dataPoll['options'].PHP_EOL;
        $data = [
            'chat_id' => $chat_id,
            'text' => $text,
            'poll' => Request::sendPoll($dataPoll)
        ];








        $labels        = [              // optional. Change button labels (showing defaults)
            'default'  => '%d',
            //'first'    => '« %d',
            'previous' => '‹ %d',
            'current'  => '· %d ·',
            'next'     => '%d ›',
            //'last'     => '%d »',
        ];
        $items = range(1, 100);
        $selected_page = 7;
        $callback_data_format = 'command={COMMAND}&oldPage={OLD_PAGE}&newPage={NEW_PAGE}';
        $command='command_pager';
        $ikp = new InlineKeyboardPagination($items, $command);
        $ikp->setMaxButtons(5, false); // Second parameter set to always show 7 buttons if possible.
        $ikp->setLabels($labels);
        $ikp->setCallbackDataFormat($callback_data_format);

// Get pagination.
        $pagination = $ikp->getPagination($selected_page);

// or, in 2 steps.
        $ikp->setSelectedPage($selected_page);
        $pagination = $ikp->getPagination();
        if (!empty($pagination['keyboard'])) {
            //$pagination['keyboard'][0]['callback_data']; // command=testCommand&oldPage=10&newPage=1
            //$pagination['keyboard'][1]['callback_data']; // command=testCommand&oldPage=10&newPage=7


            $data['reply_markup'] = [
                'inline_keyboard' => [
                    $pagination['keyboard'],
                ],
            ];

}










       // $Request = Request::sendPoll($dataPoll);

        print_r($Request);

        return Request::sendMessage($data);
    }
}
