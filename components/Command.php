<?php

namespace panix\mod\telegram\components;

use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\KeyboardButton;

abstract class Command extends \Longman\TelegramBot\Commands\Command
{
    public function isSystemCommand()
    {
        return ($this instanceof SystemCommand);
    }

    /**
     * If this is an AdminCommand
     *
     * @return bool
     */
    public function isAdminCommand()
    {
        return ($this instanceof AdminCommand);
    }

    /**
     * If this is a UserCommand
     *
     * @return bool
     */
    public function isUserCommand()
    {
        return ($this instanceof UserCommand);
    }
}
