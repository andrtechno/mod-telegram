<?php

namespace panix\mod\telegram\commands;

use Longman\TelegramBot\Request;
use yii\console\Controller;
use Yii;
class MessagesController extends Controller
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

//812367093

//343987970
    public function actionGetUpdates()
    {
        $bot_api_key = '835652742:AAEBdMpPg9TgakFa2o8eduRSkynAZxipg-c';
        $bot_username = 'pixelionbot';

// Define all IDs of admin users in this array (leave as empty array if not used)
        $admin_users = [
            812367093, //panix
           // 343987970 // Сметанин
        ];

// Define all paths for your custom commands in this array (leave as empty array if not used)
        $commands_paths = [
            __DIR__ . '/UserCommands',
        ];

// Enter your MySQL database credentials
        $mysql_credentials = [
            'host'     => 'localhost',
            'user'     => 'root',
            'password' => '47228960panix',
            'database' => 'telegram',
        ];
//print_r($commands_paths);die;
        try {
            while (true) {

                sleep(2);
                // Create Telegram API object
                $telegram = new \Longman\TelegramBot\Telegram($bot_api_key, $bot_username);

                // Add commands paths containing your custom commands
                $telegram->addCommandsPaths($commands_paths);

                // Enable admin users
                $telegram->enableAdmins($admin_users);

                // Enable MySQL
                $telegram->enableMySql($mysql_credentials);

                // Logging (Error, Debug and Raw Updates)
                // https://github.com/php-telegram-bot/core/blob/master/doc/01-utils.md#logging
                //
                // Set custom Upload and Download paths
                $telegram->setDownloadPath(Yii::getAlias('@app/web/downloads/telegram'));
                $telegram->setUploadPath(Yii::getAlias('@app/web/uploads/telegram'));

                // Here you can set some command specific parameters
                // e.g. Google geocode/timezone api key for /date command
                //$telegram->setCommandConfig('date', ['google_api_key' => 'your_google_api_key_here']);

                // Requests Limiter (tries to prevent reaching Telegram API limits)
                $telegram->enableLimiter();

                // Handle telegram getUpdates request
                $server_response = $telegram->handleGetUpdates();


                //$telegram->executeCommand('/echo ff');


                if ($server_response->isOk()) {
                    $update_count = count($server_response->getResult());
                    echo date('Y-m-d H:i:s', time()) . ' - Processed ' . $update_count . ' updates' . PHP_EOL;
                } else {
                    echo date('Y-m-d H:i:s', time()) . ' - Failed to fetch updates' . PHP_EOL;
                    echo $server_response->printError();
                }
            }
        } catch (\Longman\TelegramBot\Exception\TelegramException $e) {
            echo $e->getMessage();
            // Log telegram errors
            \Longman\TelegramBot\TelegramLog::error($e);
        } catch (\Longman\TelegramBot\Exception\TelegramLogException $e) {
            // Catch log initialisation errors
            echo $e->getMessage();
        }

    }
}
