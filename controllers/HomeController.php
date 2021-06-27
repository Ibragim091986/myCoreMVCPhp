<?php

namespace controllers;

use AmoCRM\Client\AmoCRMApiClient;
use models\User;
use vendor\classes\Core;

class HomeController extends \vendor\classes\Controller
{
    public $defaultAction = 'index';

    public function access()
    {
        return [
            'only' => ['index1', 'article', 'login'],
            'rules' => [
                [
                'allow' => true,
                'actions' => ['login', 'article1'],
                //guest, authenticated
                'roles' => ['guest'],

                ],
                [
                'allow' => true,
                'actions' => ['login1','article'],
                //guest, authenticated
                'roles' => ['authenticated'],
                ]
            ],

        ];
    }


    public function actionIndex($id = 0)
    {
//$clientId, $clientSecret, $redirectUri
//        $apiClient = new AmoCRMApiClient($clientId, $clientSecret, $redirectUri);

        echo $this->render('index', ['abc' => 'abc1234', 'id' => $id]);
//         render('index', ['abc' => 1234]);
    }

    public function actionArticle()
    {

        echo $this->render('article', ['abc' => 1234]);
//         render('index', ['abc' => 1234]);
    }

    public function actionLogin()
    {
        $matchPass = true;
        if(isset($_POST['login']) && isset($_POST['password'])) {
            $user = \models\User::findIdentity(['username' => $_POST['login']]);
            if($user && Core::$user->login($user, $_POST['password'])) {
                Core::$request->goHome();
            }else{
                $matchPass = false;
            }
        }
        //$user = \models\User::findIdentity('1');
        //Core::$user->login($user, 'amocrm');
        //Core::$request->goHome();
        //Core::$user->logout();
        //echo '<pre>';
        //var_dump('$this->_user');
        //var_dump(Core::$user->getIsGuest());
        //var_dump($user);


        //23feb27c7f24fe816af296ff57e10993
        //var_dump($_SESSION);
        //var_dump(md5('amocrm'));
        //echo '</pre>';

        echo $this->render('login', ['matchPass' => $matchPass], 'views/layouts','loginmain');
    }

    public function actionError()
    {
        echo $this->render('error', ['abc' => 1234]);
    }

    public function actionLogout()
    {
        Core::$user->logout();
        Core::$request->goHome();
        echo $this->render('login', ['abc' => 1234]);
    }
}