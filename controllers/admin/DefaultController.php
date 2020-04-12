<?php

namespace panix\mod\telegram\controllers\admin;

use Yii;
use panix\engine\controllers\AdminController;
use panix\mod\telegram\models\SettingsForm;

class DefaultController extends AdminController
{

    public $icon = 'settings';

    public function actionIndex()
    {
        $this->pageName = Yii::t('app/default', 'SETTINGS');
        $this->breadcrumbs = [
            [
                'label' => $this->module->info['label'],
                'url' => $this->module->info['url'],
            ],
            $this->pageName
        ];
        $this->buttons=[
            [
                'label'=>'Emoji',
                'url'=>'https://emojipedia.org/apple/',
                'options'=>['target'=>'_blank']
            ]
        ];
        $model = new SettingsForm();
        if ($model->load(Yii::$app->request->post())) {
            $model->save();
            return Yii::$app->getResponse()->redirect(['/admin/telegram']);
        }
        return $this->render('index', [
            'model' => $model
        ]);
    }

}
