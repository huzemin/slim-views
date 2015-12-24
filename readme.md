slim-views
----------

用于Slim Framework 3.x 的Smarty View组件

## 安装

可直接从github上直接下载，放置在项目中， 然后配置composer自动导入。

## 使用

```php
$smarty_config = array(
    'templateDir' => 'templateDir',
    'compileDir' => 'compileDir'',
    'cachedDir' => 'cachedDir',
    'configDir' => 'configDir',
    'pluginsDir' => array(
        ...
    )
);
use \Slim\Views\SmartyView as View;

// Setup Slim Framework
$app = new App();
$container = $app->getContainer();
$container['view'] = function($c) {
    $smarty_config = load_config('smarty');
    $view = new View($smarty_config);
    $view->addExtionsions($c,$smarty_config['pluginsDir']);
    return $view;
};

```

## 说明

程序借鉴了[slimphp/Twig-View](https://github.com/slimphp/Twig-View)的实现方式。
程序可以自由修改！！