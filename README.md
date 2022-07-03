# Bitrix Controller Component
Simple controller component for bitrix

## How to install
Put all files to 
`/local/components/%your_namespace%/controller/`
For example:
`/local/components/other/controller/`

Then create some templates directories in your theme template directory.
`/local/templates/%template_name%/components/%your_namespace%/controller/news/`
For example:
`/local/templates/main/components/other/controller/news/`

Then create **result_modifier.php** and **template.php** in the same directory.

## How to include

You can use this code wherever you want.

```php
$APPLICATION->IncludeComponent(
    'other:controller',
    'news',
    [
        'CACHE_TIME' => 3600,
        'CACHE_TIME_DB' => '3600',
        'CACHE_GROUPS' => 'N',
        'CACHE_SALT' => '#news.001', // some unique string
        'SEF_MODE' => 'Y',
        'SEF_FOLDER' => '/news/', // entity's root directory and url
        'URL_TEMPLATES' => [
            'detail=#SECTION_CODE#/#ELEMENT_CODE#/',
            'section=#SECTION_CODE#/'
            ... // any other pages rules
        ]
        ... // your parameters will be stored in $arParams variable
    ]
);
```

Then put the files with the same names as URL_TEMPLATES into template directory near the **result_modifier.php** and **template.php** files. There are **detail.php** and **section.php** in this case.

## urlrewrite.php

Then you need to modify **/urlrewrite.php** file.
For example:
```php
$arUrlRewrite = array(
    ...
    16 => array(
        'CONDITION' => '#^/news/#',
        'RULE' => '',
        'ID' => 'other:controller',
        'PATH' => '/news/index.php',
        'SORT' => 100,
    ),
    ...
)
```

### result_modifier.php example

```php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

if ($arResult['PAGE'] === 'section') {
    // get some result from DB
    // you can use $arResult['VARIABLES']['SECTION_CODE']
    $arResult['SECTION'] = $codeResult;
} elseif ($arResult['PAGE'] === 'detail') {
    // get some result from DB
    // you can use $arResult['VARIABLES']['ELEMENT_CODE']
    $arResult['ITEM'] = $codeResult;
} else { // this is list
    // get some result from DB
    $arResult['LIST'] = $codeResult;
}
```

### template.php example

```php
foreach ($arResult['LIST'] as $newsItem) {
    // some code
}
```

### section.php example

```php
echo $arResult['SECTION']['NAME'];

foreach ($arResult['SECTION']['ITEMS'] as $newsItem) {
    // some code
}
```

### detail.php example

```php
echo $arResult['ITEM']['NAME'];
```