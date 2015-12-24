<?php
/**
 * Smarty 模板引擎在Slim Framework中的使用
 * @author huzemin <huzemin8@126.com>
 * 参考：https://github.com/slimphp/Twig-View
 */
namespace Slim\Views;

use \Slim\Http\Response;
use \Slim\Container;

class SmartyView implements \ArrayAccess, \Countable, \IteratorAggregate
{
    /**
     * \Smarty 函数扩展
     * @var array
     */
    private $extensions = array();
    /**
     * \Smarty 对象
     * @var \Smarty
     */
    private $smartyInstance = null;
    /**
     * 默认变量
     * @var array
     */
    private $defaultVariables = array();

    public function __construct($config = array())
    {
        if (!($this->smartyInstance instanceof \Smarty)) {
            if (!class_exists('\Smarty')) {
                if (!empty($config['smartyDir'])
                    && !is_dir($config['smartyDir'])
                ) {
                    throw new \RuntimeException('Cannot set the Smarty lib directory : '
                        . $config['smartyDir'] . '. Directory does not exist.');
                }
            }
            $this->smartyInstance = new \Smarty();
            if (!empty($config['templateDir'])) {
                $this->smartyInstance->setTemplateDir($config['templateDir']);
            }
            if (!empty($config['cacheDir'])) {
                $this->smartyInstance->setCacheDir($config['cacheDir']);
            }
            if (!empty($config['complieDir'])) {
                $this->smartyInstance->setCompileDir($config['complieDir']);
            }
            if (!empty($config['configDir'])) {
                $this->smartyInstance->setConfigDir($config['configDir']);
            }
        }
    }

    /**
     * 将\Smarty编译解析后的内容不直接显示，而是写入Responce的Body中。
     * @param $template
     * @param array $data
     * @return string
     */
    public function fetch($template, $data = array())
    {
        $data = array_merge($this->defaultVariables, $data);
        $this->smartyInstance->assign($data);
        return $this->smartyInstance->fetch($template);
    }

    /**
     * 将\Smarty编译解析后的内容直接输出显示
     * @param $template
     * @param array $data
     */
    public function display($template, $data = array())
    {
        $data = array_merge($this->defaultVariables, $data);
        $this->smartyInstance->assign($data);
        $this->smartyInstance->display($template);
    }

    public function render(Response $response, $template, $args)
    {
        $response->getBody()->write($this->fetch($template, $args));
        return $response;
    }

    public function __invoke(Response $response, $template, $args)
    {
        return $this->render($response, $template, $args);
    }

    public function getSmartyInstance()
    {
        return $this->smartyInstance;
    }

    public function addExtionsions(Container $c, $extensions = array())
    {
        $this->smartyInstance->registerObject('Container', $c);
        if (is_array($extensions)) {
            foreach ($extensions as $ext) {
                if (is_dir($ext)) {
                    $this->smartyInstance->addPluginsDir(realpath($ext));
                } else {
                    $this->registerExtension($ext);
                }
            }
        }
    }

    /**
     * 判断文件是否为PHP文件
     * @param $file
     * @return bool
     */
    public function isPHPFile($file)
    {
        return pathinfo($file, PATHINFO_EXTENSION) == 'php' ? true : false;
    }

    public function getExtensionParams($file, $split = '.')
    {
        $filename = basename($file);
        $_params = explode($split, $filename);
        $params = array();
        if (count($_params) == 3) {
            $params['type'] = strtolower($_params[0]);
            $params['name'] = $_params[1];
            $params['callback'] = 'smarty_function_' . $params['name'];
            return $params;
        }
        return null;
    }

    public function registerExtension($file)
    {
        if (is_file($file) && $this->isPHPFile($file)) {
            $params = $this->getExtensionParams($file);
            if ($params) {
                require_once $file;
                if (function_exists($params['callback'])) {
                    $this->smartyInstance->registerPlugin($params['type'], $params['name'], $params['callback']);
                }
            }
        }
    }

    public function offsetExists($key)
    {
        return array_key_exists($key, $this->defaultVariables);
    }

    public function offsetGet($key)
    {
        return $this->defaultVariables[$key];
    }

    public function offsetSet($key, $value)
    {
        $this->defaultVariables[$key] = $value;
    }

    public function offsetUnset($key)
    {
        unset($this->defaultVariables[$key]);
    }

    public function count()
    {
        return count($this->defaultVariables);
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->defaultVariables);
    }
}