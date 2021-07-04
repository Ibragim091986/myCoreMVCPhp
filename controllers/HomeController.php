<?php

namespace controllers;

use AmoCRM\Client\AmoCRMApiClient;
use League\OAuth2\Client\Token\AccessTokenInterface;
use models\User;
use vendor\classes\Core;

class HomeController extends \vendor\classes\Controller
{
    public $defaultAction = 'index';

    public function access()
    {
        return [
            'only' => ['article', 'login', 'createleads', 'amocrm'],
            'rules' => [
                [
                'allow' => true,
                'actions' => ['login'],
                //guest, authenticated
                'roles' => ['guest'],

                ],
                [
                'allow' => true,
                'actions' => ['article', 'createleads', 'amocrm'],
                //guest, authenticated
                'roles' => ['authenticated'],
                ]
            ],

        ];
    }


    public function actionIndex($id = 0)
    {

        echo $this->render('index', ['abc' => 'abc1234', 'id' => $id]);
//         render('index', ['abc' => 1234]);
    }

    public function actionAmocrm()
    {
        echo $this->render('amocrm');
    }

    public function actionArticle()
    {
        $apiClient = $this->connectAmocrm();

        echo $this->render('article', ['apiClient' => $apiClient]);
//         render('index', ['abc' => 1234]);
    }

    public function actionCreateLeads()
    {
        $apiClient = $this->connectAmocrm();

        echo $this->render('createleads', ['apiClient' => $apiClient], null , 'botstrmain');
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



    public function connectAmocrm()
    {
        $clientId = Core::$config['amocrm']['clientId'];
        $clientSecret = Core::$config['amocrm']['clientSecret'];
        $redirectUri = Core::$config['amocrm']['redirectUri'];

        $apiClient = new AmoCRMApiClient($clientId, $clientSecret, $redirectUri);

        $accessToken = $this->getToken();
        $apiClient->setAccessToken($accessToken)
            ->setAccountBaseDomain($accessToken->getValues()['baseDomain'])
            ->onAccessTokenRefresh(
                function (AccessTokenInterface $accessToken, string $baseDomain) {
                    $this->saveToken(
                        [
                            'accessToken' => $accessToken->getToken(),
                            'refreshToken' => $accessToken->getRefreshToken(),
                            'expires' => $accessToken->getExpires(),
                            'baseDomain' => $baseDomain,
                        ]
                    );
                }
            );

        return $apiClient;

    }



    public function saveToken($accessToken)
    {
        if (
            isset($accessToken)
            && isset($accessToken['accessToken'])
            && isset($accessToken['refreshToken'])
            && isset($accessToken['expires'])
            && isset($accessToken['baseDomain'])
        ) {
            $data = [
                'accessToken' => $accessToken['accessToken'],
                'expires' => $accessToken['expires'],
                'refreshToken' => $accessToken['refreshToken'],
                'baseDomain' => $accessToken['baseDomain'],
            ];

            file_put_contents(TOKEN_FILE, json_encode($data));
        } else {
            exit('Invalid access token ' . var_export($accessToken, true));
        }
    }

    /**
     * @return \League\OAuth2\Client\Token\AccessToken
     */
    private function getToken()
    {
        $tokenDir = \vendor\classes\Core::$homeDir . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR . 'token_info.json';
        $accessToken = json_decode(file_get_contents($tokenDir), true);

        if (
            isset($accessToken)
            && isset($accessToken['accessToken'])
            && isset($accessToken['refreshToken'])
            && isset($accessToken['expires'])
            && isset($accessToken['baseDomain'])
        ) {
            return new \League\OAuth2\Client\Token\AccessToken([
                'access_token' => $accessToken['accessToken'],
                'refresh_token' => $accessToken['refreshToken'],
                'expires' => $accessToken['expires'],
                'baseDomain' => $accessToken['baseDomain'],
            ]);
        } else {
            exit('Invalid access token ' . var_export($accessToken, true));
        }
    }
}