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

        echo $this->render('index', ['abc' => 'abc1234', 'id' => $id]);
//         render('index', ['abc' => 1234]);
    }

    public function actionAmocrm()
    {
        echo $this->render('amocrm');
    }

    public function actionArticle()
    {
        $clientId = '237f9f6e-8cbd-4094-a8b5-0c797e1362b9';
        $clientSecret = 'hne1VrSBEW813h14bkcnKuPDFQG50hge2ICsNFU9oeZ59I8iFLCEXXIIAMrH9Niu';
        $redirectUri = 'http://767f4f4f0110.ngrok.io/amocrm';

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

        //$apiClient->setAccessToken($this->getToken());
        //$ownerDetails = $apiClient->getOAuthClient()->getResourceOwner($this->getToken());

        //printf('Hello, %s!', $ownerDetails->getName());

        /*echo '<pre>';
        var_dump($apiClient->getOAuthClient());
        echo '</pre>';*/

        echo $this->render('article', ['apiClient' => $apiClient]);
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