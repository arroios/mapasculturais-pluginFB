<?php

namespace arroios\plugins;


use arroios\plugins\models\Event;
use arroios\plugins\models\Page;

Class ImportEvent extends Facebook
{
    public $data = false;
    public $html = 'button';
    public $modelEvent;
    public $modelPage;

    function __construct($conf)
    {
        parent::__construct($conf);

        if(isset($_GET['plugin-facebook-action']) && $_GET['plugin-facebook-action'] == 'login')
        {
            $this->data = $this->login($conf);

            if($this->data['error'] == false && $this->data['token'] != false)
            {
                $this->html = 'page_list';
            }
        }
        else if(isset($_GET['plugin-facebook-action']) && $_GET['plugin-facebook-action'] == 'save' && isset($_POST['pages']) && count($_POST['pages']) > 0)
        {
            $this->data = [];

            foreach ($_POST['pages'] as $page)
            {
                $arr = json_decode($page, true);

                $this->data[] = $this->getEvents($conf, $arr);
            }

            $this->html = 'success';
        }

        $this->modelEvent = new Event($conf['Event']);
        $this->modelPage = new Page($conf['Page']);
    }

    public function getHtml()
    {
        if($this->html == 'button')
        {
            print '<a id="plugin-facebook-bt" href="'.htmlspecialchars($this->getLinkToLogin()).'">Importar eventos do facebook</a>';
        }
        else if ($this->html == 'page_list')
        {
            $link = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'].'?plugin-facebook-action=save';
            $linkOut = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'];
            print '<section id="plugin-facebook-section">';
            print '<form method="POST" id="plugin-facebook-form" name="plugin-facebook-form" action="'.$link.'">';
            print '<div id="plugin-facebook-form-header">';
            print '<h1>Escolha as páginas que deseja sincronizar os eventos</h1>';
            print '</div>';
            print '<div id="plugin-facebook-form-body">';
            print '<div id="plugin-facebook-form-body-container">';
            foreach ($this->data['pages'] as $value)
            {

                print '<div><label>';
                print $value->name;
                print '<input type="checkbox" value=\''.(json_encode($value)).'\' name="pages[]" />';
                print '</label></div>';

            }
            print '</div>';
            print '</div>';
            print '<div id="plugin-facebook-form-footer">';
            print '<input type="submit" value="Salvar" name="save" />';
            print '<a href="'.$linkOut.'">Sair</a>';
            print '<div>';
            print '</form>';
            print '</section>';
        }
        else if ($this->html == 'success')
        {
            print '<h4>Eventos sincronizados com sucesso</h4>';
        }
    }
}