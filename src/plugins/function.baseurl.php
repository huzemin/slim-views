<?php
/*
 * Smarty Plugin
 * -----------------------------------------------
 * 通过网站的基本路径
 *------------------------------------------------
 * @author huzemin<huzemin8@126.com>
 */

function smarty_function_baseUrl($params, $smarty) {
    $container = $smarty->getRegisteredObject('Container');
    if(isset($container) && $container instanceof \Slim\Container) {
        return $container->request->getUri();
    }
}