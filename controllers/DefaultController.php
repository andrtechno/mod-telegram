<?php


namespace panix\mod\telegram\controllers;

use panix\engine\CMS;
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

    public function actionSetWebhook(){
        Yii::$app->response->format = Response::FORMAT_HTML;
        try {
            // Create Telegram API object
            $telegram = new Telegram(Yii::$app->getModule('telegram')->api_token, Yii::$app->getModule('telegram')->bot_name);

            if (!empty(\Yii::$app->modules['telegram']->userCommandsPath)){
                if(!$commandsPath = realpath(\Yii::getAlias(\Yii::$app->modules['telegram']->userCommandsPath))){
                    $commandsPath = realpath(\Yii::getAlias('@app') . \Yii::$app->modules['telegram']->userCommandsPath);
                }

                if(!is_dir($commandsPath)) throw new UserException('dir ' . \Yii::$app->modules['telegram']->userCommandsPath . ' not found!');
            }
            
            // Set webhook
            $result = $telegram->setWebHook(Yii::$app->modules['telegram']->hook_url);
            if ($result->isOk()) {
                return $result->getDescription();
            }
        } catch (TelegramException $e) {
            return $e->getMessage();
        }
        return null;
    }

    /**
     * @return null|string
     * @throws ForbiddenHttpException
     */
    public function actionUnsetWebhook(){
        Yii::$app->response->format = Response::FORMAT_HTML;
        if (\Yii::$app->user->isGuest) throw new ForbiddenHttpException();
        try {
            // Create Telegram API object
            $telegram = new Telegram(Yii::$app->getModule('telegram')->api_token, Yii::$app->getModule('telegram')->bot_name);

            // Unset webhook
            $result = $telegram->deleteWebhook();

            if ($result->isOk()) {
                return $result->getDescription();
            }
        } catch (TelegramException $e) {
            return $e->getMessage();
        }
    }

    public function actionHook(){
        Yii::info('test hook','application');
        try {

            // Create Telegram API object
            $telegram = new Telegram(Yii::$app->getModule('telegram')->api_token, Yii::$app->getModule('telegram')->bot_name);
            $basePath = \Yii::$app->getModule('telegram')->basePath;
            // $commandsPath = realpath($basePath . '/commands/SystemCommands');
            $commandsPath = realpath($basePath . '/commands/UserCommands');
           // $telegram->setCommandConfig('/sendtochannel',['command'=>'sendtochannel','description'=>'test']);
           // CMS::dump($commandsPath);
            $telegram->addCommandsPath($commandsPath);
            if (!empty(\Yii::$app->modules['telegram']->userCommandsPath)){
                if(!$commandsPath = realpath(\Yii::getAlias(\Yii::$app->modules['telegram']->userCommandsPath))){
                    $commandsPath = realpath(\Yii::getAlias('@app') . \Yii::$app->modules['telegram']->userCommandsPath);
                }

                $telegram->addCommandsPath($commandsPath);
            }
            // Handle telegram webhook request
            $telegram->handle();
        } catch (TelegramException $e) {
            // Silence is golden!
            // log telegram errors
            return $e->getMessage();
        }
        return null;
    }
}
