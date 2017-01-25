<?php
/**
 * Created by PhpStorm.
 * User: jhans
 * Date: 24/01/2017
 * Time: 13:59
 */

namespace arroios\plugins;


Class ImportEvent extends Facebook
{
    public $user = false;
    public $data = false;
    public $html = 'button';
    public $templateTag = '<a class="arroios-bt" href="#link#">Importar eventos do facebook</a>';

    function __construct($conf)
    {

        parent::__construct($conf);

        if(isset($_GET['action']) && $_GET['action'] == 'login')
        {
            $this->data = $this->login();

            if($this->data['error'] == false && $this->data['token'] != false)
            {
                $this->html = 'page_list';
            }
        }

       /* if($verifyLogin['error'] == false && $verifyLogin['$accessTokenShortLived'] != false)
        {

        }
       */

        //$login = new \Auth($conf);

       // return $login;
    }

    public function getHtml()
    {
        if($this->html == 'button')
        {
            echo str_replace('#link#', htmlspecialchars($this->getLinkToLogin()), $this->templateTag);
        }
        else if ($this->html == 'page_list')
        {
            foreach ($this->data['pages'] as $value)
            {
                print '<pre>';
                print_r($value);
                print '</pre>';
            }
        }
    }

}