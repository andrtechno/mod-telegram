<?php

namespace panix\mod\telegram\commands;

use Longman\TelegramBot\Commands\UserCommands\CatalogCommand;
use Longman\TelegramBot\Conversation;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Telegram;
use yii\console\Controller;
use Yii;
use yii\console\Exception;
use panix\mod\telegram\components\Api;

class IndexController extends Controller
{
    public function beforeAction2($action)
    {
        $langManager = Yii::$app->languageManager;
        Yii::$app->language = (isset($langManager->default->code)) ? $langManager->default->code : Yii::$app->language;
        return parent::beforeAction($action); // TODO: Change the autogenerated stub
    }

    public function actionClean($keep = 7)
    {
        $db = \Yii::$app->db;
        $db->createCommand()->delete('{{%tlgrm_messages}}', 'time < \'' . date("Y-m-d H:i:s", time() - (3600 * 24 * $keep)) . '\'')->execute();
    }


    public function actionIndex()
    {

// Define all IDs of admin users in this array (leave as empty array if not used)
        $admin_users = [812367093, //panix// 343987970 // Сметанин];
        $admin_users2 = explode(',', Yii::$app->settings->get('telegram', 'bot_admins'));

        $commands_paths = [
            __DIR__ . '/UserCommands',
            __DIR__ . '/SystemCommands',
            __DIR__ . '/AdminCommands',
        ];


        $mysql_credentials = [
            'host' => 'localhost',
            'user' => 'root',
            'password' => '47228960panix',
            'database' => 'telegram',
        ];
        $api_key = Yii::$app->settings->get('telegram', 'api_token');
        $bot_username = Yii::$app->settings->get('telegram', 'bot_name');
        try {

            $telegram = new Api();

            // Add commands paths containing your custom commands
            $telegram->addCommandsPaths($commands_paths);

            // Enable admin users
            $telegram->enableAdmins();

            // Enable MySQL
            $telegram->enableMySql($mysql_credentials);

            // Logging (Error, Debug and Raw Updates)
            // https://github.com/php-telegram-bot/core/blob/master/doc/01-utils.md#logging
            //
            // Set custom Upload and Download paths
            $telegram->setDownloadPath(Yii::getAlias('@app/web/downloads/telegram'));
            $telegram->setUploadPath(Yii::getAlias('@app/web/uploads/telegram'));
            $i=1;
            while (true) {

                sleep(2);
                // Create Telegram API object


                // Here you can set some command specific parameters
                // e.g. Google geocode/timezone api key for /date command
               // $telegram->setCommandConfig('date', ['google_api_key' => 'your_google_api_key_here']);

                // Requests Limiter (tries to prevent reaching Telegram API limits)
                $telegram->enableLimiter();

                // Handle telegram getUpdates request
                $server_response = $telegram->handleGetUpdates();

                if ($server_response->isOk()) {

                    foreach ($server_response->getResult() as $result) {
                        $message = $result->getMessage();
                        if ($message) {
                            $text = $message->getText();
                            // $preg = preg_match('/^(\/catalog)\s([0-9]+)/', trim($message->getText()), $match);

                            //
                            if (preg_match('/^(\x{1F6CD})/iu', trim($text), $match)) { //cart emoji
                                $telegram->executeCommand('cart');
                            } elseif (preg_match('/^(\x{1F4C2})/iu', trim($text), $match)) { //folder emoji
                                $telegram->executeCommand('catalog');
                            } elseif (preg_match('/^(\x{1F3E0})/iu', trim($text), $match)) { //home emoji
                                $telegram->executeCommand('start');
                                $telegram->executeCommand('cancel');

                            } elseif (trim($text) == 'Отмена') {
                                $telegram->executeCommand('cancel');
                            } elseif (preg_match('/^(\x{2753})/iu', trim($text), $match)) { //help emoji
                                // $telegram->executeCommand('help');
                            } elseif (preg_match('/^(\x{1F4E2})/iu', trim($text), $match)) { //news emoji
                                  $telegram->executeCommand('news');
                            } elseif (preg_match('/^(\x{1F4E6})/iu', trim($text), $match)) { //my carts emoji
                                //  $telegram->executeCommand('help');
                            } elseif (preg_match('/^(\x{260E}|\x{1F4DE})/iu', trim($text), $match)) { //phone emoji
                                  $telegram->executeCommand('call');
                            } elseif (preg_match('/^(\x{2709})/iu', trim($text), $match)) { //feedback emoji
                                $telegram->executeCommand('feedback');
                            } elseif (preg_match('/^(\x{1F4E6})/iu', trim($text), $match)) { //package emoji
                                $telegram->executeCommand('history');
                            } elseif (preg_match('/^(\x{2699})/iu', trim($text), $match)) { //gear emoji
                                $telegram->executeCommand('settings');
                            } elseif (preg_match('/^(\x{1F50E})/iu', trim($text), $match)) { //search emoji
                                $telegram->executeCommand('search');

                              //  $telegram->setCommandConfig('catalog', ['category_id' => '20']);
                              //  $telegram->executeCommand('catalog');
                            }
                        }

                    }
                    $update_count = count($server_response->getResult());
                    echo $i.': '.date('Y-m-d H:i:s', time()) . ' - Processed ' . $update_count . ' updates' . PHP_EOL;
                } else {
                    echo $i.': '.date('Y-m-d H:i:s', time()) . ' - Failed to fetch updates' . PHP_EOL;
                    echo $server_response->printError();
                }
                $i++;
            }
        } catch (\Longman\TelegramBot\Exception\TelegramException $e) { //\Longman\TelegramBot\Exception\TelegramException
            echo 'TelegramException: '.$e->getMessage();
            // Log telegram errors
            //Yii::error($e->getMessage(),'telegram');
            \Longman\TelegramBot\TelegramLog::error($e);
        } catch
        (\Longman\TelegramBot\Exception\TelegramLogException $e) {
            // Catch log initialisation errors
            echo 'TelegramLogException: '.$e->getMessage();
        }

    }
}
