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

use panix\mod\telegram\models\Actions;
use panix\mod\telegram\models\AuthorizedChat;
use panix\mod\telegram\models\AuthorizedManagerChat;
use panix\mod\telegram\models\AuthorizedUsers;
use panix\mod\telegram\models\Message;
use panix\mod\telegram\models\Usernames;
use panix\mod\telegram\TelegramVars;
use Longman\TelegramBot\Conversation;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Commands\SystemCommand;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Generic message command
 */
class GenericmessageCommand extends SystemCommand
{
    /**#@+
     * {@inheritdoc}
     */
    protected $name = 'Genericmessage';
    protected $description = 'Handle generic message';
    protected $version = '1.0.2';
    protected $need_mysql = false;
    /**#@-*/

    /**
     * Execution if MySQL is required but not available
     *
     * @return boolean
     */
    public function executeNoDb()
    {
        //Do nothing
        return Request::emptyResponse();
    }


    /**
     * Execute command
     *
     * @return boolean
     */
    public function execute()
    {
        //Do nothing, just for rewriting default Longman command
        return Request::emptyResponse();
    }
}
