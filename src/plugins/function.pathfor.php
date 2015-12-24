<?php
/**
 * Smarty Plugin
 * -----------------------------------------------
 * 通过路由名称获取相对链接
 *------------------------------------------------
 * @author huzemin <huzemin8@126.com>
 */

function smarty_function_pathFor($params, $smarty)
{
    $name = isset($params['name']) ? $params['name'] : '';
    $data = $queryParams = array();
    if (isset($params['options'])) {
        switch (gettype($params['options'])) {
            case 'array':
                $data = $params['options'];
                break;
            case 'string':
                $options = explode('|', $params['options']);
                foreach ($options as $option) {
                    list($key, $value) = explode('.', $option);
                    $data[$key] = $value;
                }
                break;
            default:
                throw new \Exception('Options parameter is of unknown type, provide either string or array');
        }
    }
    if (isset($params['queries'])) {
        switch (gettype($params['queries'])) {
            case 'array':
                $queryParams = $params['queries'];
                break;
            case 'string':
                $queries = explode('|', $params['queries']);
                foreach ($queries as $query) {
                    list($key, $value) = explode('.', $query);
                    $queryParams[$key] = $value;
                }
                break;
            default:
                throw new \Exception('Queries parameter is of unknown type, provide either string or array');
        }
    }
    $container = $smarty->getRegisteredObject('Container');
    if(isset($container) && $container instanceof \Slim\Container) {
        return $container->router->pathFor($name, $data, $queryParams);
    }
    return '';
}