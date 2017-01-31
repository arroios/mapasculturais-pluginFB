# Mapasculturais plugin FB
Plugin para importar eventos do Facebook para o Mapas Culturais

Antes de tudo, configure o seu Plugin no facebook, e adicione os hosts do seu dominio
O dominio local para testes tem que ser o "mesmo" que utilizado online ex.:
www.meusite.com.br você vai precisar utilizar meusite.local ou www.meusite.local, somente assim o facebook vai
liberar para ser testado a api.

após fazer isso, rede, você vai precisar configurar o host da sua máquina, e depois rodar o seu servidor neste host


## DB Update
vá até o arquivo db-update.php do seu tema e adicione:

```SQL
 'arroios_plugin_inmport_facebook' => function() use($conn)
     {
         // Cria Tabela para dar suporte a paginas
         $conn->executeQuery('CREATE TABLE IF NOT EXISTS facebook_page ( facebook_page_id   TEXT, facebook_token  TEXT, facebook_page_name TEXT)');
 
         // da suporte para mais campos na tabela event
         $conn->executeQuery('
       
        ALTER TABLE public.event 
        ADD COLUMN facebook_event_id TEXT, 
        ADD COLUMN facebook_page_id TEXT, 
        ADD COLUMN facebook_event_update_time TEXT; 
         
        ');
 
         // da suporte para mais campos na tabela space
         $conn->executeQuery('
       
        ALTER TABLE public.space
           ADD COLUMN facebook_place_id TEXT;  
         
        ');
     },
```

Execute o seguinte arquivo, para entrar as configurações do banco
```bash
/mapasculturais/scripts/db-update.sh
```

## Config
No seu arquivo de configurações, você precisa adicionar um namespace e as configurações do plugin

```php
...
 
'namespaces' => array_merge( $config['namespaces'], [
    'arroios\plugins' => '/vagrant/plugin/mapasculturais-pluginFB'
]),
 
...
```

```php
...
 
'arroios.plugin' => [
    'import.facebook' => [
        'facebook_id' => SEU_FACEBOOK_APP_ID, //add o seu facebook id
        'facebook_secret' => SEU_FACEBOOK_APP_SECRET, // add o seu app secret do facebook
        'Event' =>
            [
                'tableName' => 'event',
                'columnFacebookEventId' => 'facebook_event_id',
                'columnFacebookPageId' => 'facebook_page_id',
                'columnFacebookEventUpdateTime' => 'facebook_event_update_time',
            ],
        'Page' =>
            [
                'tableName' => 'facebook_page',
                'columnFacebookPageId' => 'facebook_page_id',
                'columnFacebookToken' => 'facebook_token',
                'columnFacebookPageName' => 'facebook_page_name',
            ]
    ]
],
 
... 
```

## Botão para importar
Este é o botão para importar os eventos do facebook, adicione ele onde achar mais apropriado
```php
<?php (new \arroios\plugins\ImportEvent($app->config['arroios.plugin']['import.facebook'], $app->user->id))->getHtml() ?>
```

## Cron Job
Caso queira que os eventos se mantenham atualizados, crie um arquivo chamado e adicione essas configurações e depois é só chamar pelo cron

```php
<?php (new \arroios\plugins\ImportEvent($app->config['arroios.plugin']['import.facebook'], $app->user->id))->cronJob() ?>
```

Ou somente execute este o arquivo cronjob.php que esta localizado na raiz do plugin

```php
php  /vagrant/plugins/mapasculturais-pluginFB/cronjob.php
```