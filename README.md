# Mapasculturais plugin FB
Plugin para importar eventos do Facebook para o Mapas Culturais

## Tabela Page
Escolha um nome que achar mais apropriado

```SQL
 CREATE TABLE IF NOT EXISTS Page (facebookPageId INTEGER PRIMARY KEY, facebookToken TEXT, facebookPageName TEXT)
```

## Tabela Event
Adicione 3 colunas necessárias para update das informações

- facebookEventId
- facebookPageId
- facebookEventUpdateTime


## Botão para importar
Chame ImportEvent, e o configure com as informações do seu APP do facebook, e adicione o nome correto de cada coluna do seu banco

```php
use arroios\plugins\ImportEvent;

$ImportEvent = new ImportEvent([
    'facebook_id' => FACEBOOK_APP_ID,
    'facebook_secret' => FACEBOOK_APP_SECRET,
    'Event' => // Colunas da tabela de eventos, caso não tenha alguma, adicione
    [
        'tableName' => 'Event',
        'columnFacebookEventId' => 'facebookEventId', // Nova coluna
        'columnFacebookPageId' => 'facebookPageId', // Nova coluna
        'columnFacebookEventUpdateTime' => 'facebookEventUpdateTime', // Nova coluna
        'columnStartTime' => 'startTime',
        'columnEndTime' => 'endTime',
        'columnName' => 'name',
        'columnDescription' => 'description',
        'columnCover' => 'cover',
        'columnPlace' => 'place',
        'columnState' => 'state',
        'columnCity' => 'city',
        'columnStreet' => 'street',
        'columnZip' => 'zip',
        'columnLatitude' => 'latitude',
        'columnLongitude' => 'longitude',
    ],
    'Page' => // Colunas da tabela de páginas do facebook, caso não tenha alguma, adicione ou crie a tabela
    [
        'tableName' => 'Page',
        'columnFacebookPageId' => 'facebookPageId',
        'columnFacebookToken' => 'facebookToken',
        'columnFacebookPageName' => 'facebookPageName',
    ]
]);

// Imprime o botão para interação
$ImportEvent->getHtml();
```

## Cron Job
Caso queira que os eventos se mantenham atualizados, crie um arquivo chamado cronJob.php na raiz e chame-o com a configurações desejada no cron job. 

Chame o mesmo script que anteiormente, com exceção do botão para imprimir, no lugar, chame o cron

```php

 /*
 ....
 Mesma configuração padrão acima
 ....
 */
  $ImportEvent->cronJob();
```
