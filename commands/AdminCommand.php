<?php

/**
 * This file is part of the TelegramBot package.
 *
 * (c) Avtandil Kikabidze aka LONGMAN <akalongman@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace panix\mod\telegram\commands;

use panix\mod\telegram\components\Command;

abstract class AdminCommand extends Command
{
    /**
     * @var bool
     */
    protected $private_only = true;
}
