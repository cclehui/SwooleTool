<?php

namespace SwooleGlue\Component\Config;

use SwooleGlue\AbstractInterface\Singleton;
use SwooleGlue\Component\Spl\SplArray;

class ConfigUtil {
    protected $conf;

    use Singleton;

    public function __construct() {
        $file = SWOOLESERVER_ROOT . '/SwooleGlueConfig.php';
        $data = [];
        if (file_exists($file)) {
            $data = require $file;
        } else {
            die("config file $file not exists");
        }


        $this->conf = new SplArray($data);
    }

    /**
     * 获取配置项
     * @param string $keyPath 配置项名称 支持点语法
     * @return array|mixed|null
     */
    public function getConf($keyPath = '') {
        if ($keyPath == '') {
            return $this->toArray();
        }
        return $this->conf->get($keyPath);
    }

    /**
     * 设置配置项
     * 在server启动以后，无法动态的去添加，修改配置信息（进程数据独立）
     * @param string $keyPath 配置项名称 支持点语法
     * @param mixed $data 配置项数据
     */
    public function setConf($keyPath, $data) {
        $this->conf->set($keyPath, $data);
    }

    /**
     * 获取全部配置项
     * @return array
     */
    public function toArray(): array {
        return $this->conf->getArrayCopy();
    }

    /**
     * 覆盖配置项
     * @param array $conf 配置项数组
     */
    public function load(array $conf) {
        $this->conf = new SplArray($conf);
    }

    /**
     * 载入一个文件的配置项
     * @param string $filePath 配置文件路径
     * @param bool $merge 是否将内容合并入主配置
     * @author : evalor <master@evalor.cn>
     */
    public function loadFile($filePath, $merge = false) {
        if (is_file($filePath)) {
            $confData = require_once $filePath;
            if (is_array($confData) && !empty($confData)) {
                $basename = strtolower(basename($filePath, '.php'));
                if (!$merge) {
                    $this->conf[$basename] = $confData;
                } else {
                    $this->conf = new SplArray(array_merge($this->toArray(), $confData));
                }
            }
        }
    }
}