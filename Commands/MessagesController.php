<?php

namespace panix\mod\telegram\commands;

use yii\console\Controller;

class MessagesController extends Controller
{
    public function actionClean($keep = 7)
    {
        $db = \Yii::$app->db;
        $db->createCommand()->delete('tlgrm_messages', 'time < \'' . date("Y-m-d H:i:s", time() - (3600 * 24 * $keep)) . '\'')->execute();
    }
}
