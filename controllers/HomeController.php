<?php

namespace controllers;

use AmoCRM\Client\AmoCRMApiClient;
use models\User;

class HomeController extends \vendor\classes\Controller
{
    public $defaultAction = 'index';

    public function access()
    {
        return [
            'only' => ['index', 'article'],
            'rules' => [
                [
                'allow' => true,
                'actions' => ['login', 'article'],
                //guest, authenticated
                'roles' => ['guest'],

                ],
                [
                'allow' => true,
                'actions' => ['article1'],
                //guest, authenticated
                'roles' => ['authenticated'],
                ]
            ],

        ];
    }


    public function actionIndex()
    {
//$clientId, $clientSecret, $redirectUri
//        $apiClient = new AmoCRMApiClient($clientId, $clientSecret, $redirectUri);

        echo $this->render('index', ['abc' => 1234, 'id' => $id], true);
//         render('index', ['abc' => 1234]);
    }

    public function actionArticle()
    {

//        echo '<br>actionIndex $id = <br>';
//        var_dump($id);

        echo $this->render('article', ['abc' => 1234]);
//         render('index', ['abc' => 1234]);
    }

    public function actionLogin()
    {
        echo $this->render('index', ['abc' => 1234]);
    }
}