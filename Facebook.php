<?php
namespace arroios\plugins;

use Facebook\Facebook as CoreFacebook;
use Facebook\FacebookRequest;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\FacebookApp;
use arroios\plugins\models\Page;
use arroios\plugins\models\Event;

Class Facebook
{
    private $fb;
    private $facebook_id;
    private $facebook_secret;
    private $facebook_permissions = ['email', 'manage_pages'];
    private $token = false;


    protected $userId;
    protected $conn;

    /**
     * Facebook constructor.
     * @param $conf
     * @param $userId
     */
    function __construct($conf, $userId, $conn)
    {
        if(!session_id()) {
            session_start();
        }

        $this->facebook_id = @$conf['facebook_id'];
        $this->facebook_secret = @$conf['facebook_secret'];
        $this->userId = $userId;
        $this->conn = $conn;

        if(isset($conf['facebook_permissions']))
            $this->facebook_permissions =  $conf['facebook_permissions'];

        $this->fb = new CoreFacebook([
            'app_id' => $this->facebook_id, // Replace {app-id} with your app id
            'app_secret' => $this->facebook_secret,
            'default_graph_version' => 'v2.2',
        ]);

    }

    /**
     * @return string
     */
    protected function getLinkToLogin()
    {
        // Verifica se utiliza ? ou &
        $signal = (strpos($_SERVER['REQUEST_URI'], '?') > 0) ? '&' : '?';
        // Monta o link
        $link = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].$signal.'plugin-facebook-action=login';
        // Permissões do facebook
        $permissions = $this->facebook_permissions;

        $helper = $this->fb->getRedirectLoginHelper();
        $loginUrl = $helper->getLoginUrl($link, $permissions);

        return $loginUrl;
    }

    /**
     * @param $conf
     * @return array|bool
     */
    protected function login($conf)
    {
        $error = false;

        // Verifica se a chamada na URL esta correta
        if(isset($_GET['code']) && $_GET['state']) {

            try
            {
                $helper = $this->fb->getRedirectLoginHelper();
                $accessToken = $helper->getAccessToken();

                if (!isset($accessToken))
                {
                    $error = 'Bad request';
                }
                else
                {
                    $oAuth2Client = $this->fb->getOAuth2Client();
                    $tokenMetadata = $oAuth2Client->debugToken($accessToken);


                    $fbApp = new FacebookApp($this->facebook_id, $this->facebook_secret);
                    $requestAccount = new FacebookRequest($fbApp, $accessToken->getValue(), 'GET', '/oauth/access_token', [
                        'grant_type' => 'fb_exchange_token',
                        'fb_exchange_token' => $accessToken->getValue(),
                        'client_id' => $this->facebook_id,
                        'client_secret' => $this->facebook_secret
                    ]);

                    $this->token = $this->fb->getClient()->sendRequest($requestAccount)->getDecodedBody()['access_token'];
                    $pages = $this->getPages($conf, current($tokenMetadata)['user_id']);

                }
            }
            catch (FacebookResponseException $e)
            {
                return false;
            }
        }

        return [
            'error' => $error,
            'token' => $this->token,
            'userId' => $pages['userId'],
            'pages' => $pages['pages']
        ];
    }

    /**
     * @param $conf
     * @param $userId
     * @return array|bool
     */
    protected function getPages($conf, $userId)
    {
        try
        {
            $fbApp = new FacebookApp($this->facebook_id, $this->facebook_secret);

            $requestAccount = new FacebookRequest($fbApp, $this->token, 'GET', '/me/accounts', [
                'fields' => 'name,id,access_token',
                'limit' => '100'
            ]);

            $dataAccount = $this->fb->getClient()->sendRequest($requestAccount)->getDecodedBody()['data'];

            // Percorre pela conta do usuário
            $pages = [];
            foreach ($dataAccount as $value)
            {
                // Instacia uma nova página
                $page = new Page($conf['Page'], $this->userId);
                // Carrega os dados no model
                $page->load($value, 'facebook');
                // Puxa as informações e joga na variável
                $pages[] = $page->getInfo();
            }

            return [
                'userId' => $userId,
                'pages' => $pages
            ];

        }
        catch(FacebookResponseException $e)
        {
            return false;
        }
    }

    /**
     * @param $conf
     * @param $pageData
     * @return array|bool
     */
    protected function getEvents($conf, $pageData)
    {
        try
        {
            // Instacia uma nova página
            $page = new Page($conf['Page'], $this->userId);
            $page->conn = $this->conn;
            // Carrega os dados no model
            $page->load($pageData, 'model');
            // Salva esta página
            $page->save();


            $fbApp = new FacebookApp($this->facebook_id, $this->facebook_secret);

            $requestAccount = new FacebookRequest($fbApp, $page->facebookToken, 'GET', '/'.$page->facebookPageId.'/events', [
                'fields' => 'start_time,end_time,name,description,id,updated_time,cover,place',
                'limit' => '100',
                'since' => time()
            ]);

            $data = $this->fb->getClient()->sendRequest($requestAccount)->getDecodedBody()['data'];

            // Percorre pelos eventos desta página
            $events = [];
            foreach ($data as $value)
            {
                // Instacia um novo evento
                $__temp = new Event($conf, $page->userId);
                $__temp->conn = $this->conn;
                // Carrega os dados no model
                $__temp->load($value, $page->facebookPageId);
                // Salva este evento
                $__save = $__temp->save();

                // Puxa as informações e joga na variável
                $events[] = $__save;
            }

            return [
                'page' => $page->getInfo(),
                'events' => $events
            ];
        }
        catch(FacebookResponseException $e)
        {
            return false;
        }
    }

}