<?php

namespace panix\mod\telegram\models;

use panix\engine\SettingsModel;

class SettingsForm extends SettingsModel
{

    public static $category = 'telegram';
    protected $module = 'telegram';

    public $api_token;
    public $bot_name;
    public $password;

    public function rules()
    {
        return [
            [['api_token', 'bot_name', 'password'], "required"],
            //  [['product_related_bilateral', 'seo_categories','group_attribute'], 'boolean'],
            //  [['seo_categories_title'], 'string', 'max' => 255],
            [['api_token', 'bot_name', 'password'], 'string'],
        ];
    }


}
