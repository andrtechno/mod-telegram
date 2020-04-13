<?php


namespace panix\mod\telegram\controllers;

use Longman\TelegramBot\Request;
use panix\engine\CMS;
use panix\mod\telegram\components\Api;
use Yii;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Telegram;
use yii\base\UserException;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\Response;





/**
 * Default controller for the `telegram` module
 */
class DefaultController extends Controller
{

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'destroy-chat' => ['post'],
                    'init-chat' => ['post'],
                  //  'hook' => ['post'],
                ],
            ],
        ];
    }

    public function beforeAction($action)
    {
        if ($action->id == 'hook') {
            $this->enableCsrfValidation = false;
        }
        return parent::beforeAction($action);
    }

    public function actionDestroyChat()
    {
        return $this->renderPartial('button');
    }
    public function actionInitChat()
    {
        $session = \Yii::$app->session;
        if(!$session->has('tlgrm_chat_id')) {
            if (isset($_COOKIE['tlgrm_chat_id'])) {
                $tlgrmChatId = $_COOKIE['tlgrm_chat_id'];
                $session->set('tlgrm_chat_id', $tlgrmChatId);
            } else {
                $tlgrmChatId = uniqid();
                $session->set('tlgrm_chat_id', $tlgrmChatId);
                setcookie("tlgrm_chat_id", $tlgrmChatId, time() + 1800);
            }
        }
        return $this->renderPartial('chat');
    }

    public function actionHook(){
        Yii::info('test hook','application');

        $mysql_credentials = [
            'host' => 'corner2.mysql.tools',
            'user' => 'corner2_bot',
            'password' => 'oHj0!5b4#E',
            'database' => 'corner2_bot',
        ];

        try {

            // Create Telegram API object
          //  $telegram = new Telegram(Yii::$app->getModule('telegram')->api_token, Yii::$app->getModule('telegram')->bot_name);
            $telegram = new Api();
            $basePath = \Yii::$app->getModule('telegram')->basePath;
            // $commandsPath = realpath($basePath . '/commands/SystemCommands');
          //  $commandsPath = realpath($basePath . '/commands/UserCommands');
           // $telegram->setCommandConfig('/sendtochannel',['command'=>'sendtochannel','description'=>'test']);
           // CMS::dump($commandsPath);
           // $telegram->addCommandsPath($commandsPath);
            $commands_paths = [
                realpath($basePath . '/commands') . '/SystemCommands',
                realpath($basePath . '/commands') . '/AdminCommands',
                realpath($basePath . '/commands') . '/UserCommands',
            ];

            $telegram->enableMySql($mysql_credentials);
            $telegram->enableAdmins();
            $telegram->setDownloadPath(Yii::getAlias('@app/web/downloads/telegram'));
            $telegram->setUploadPath(Yii::getAlias('@app/web/uploads/telegram'));

            $telegram->addCommandsPaths($commands_paths);

            // Handle telegram webhook request
       // $telegram->setCustomInput(file_get_contents('php://input'));
            $telegram->handle();

           // $u=  file_get_contents('php://input');
         //   return $this->asJson($u,true);
        } catch (TelegramException $e) {
            // Silence is golden!
            // log telegram errors
            return $e->getMessage();
        }
        return null;
    }
}
