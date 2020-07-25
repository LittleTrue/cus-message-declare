<?php
/**
 *  @department : Commercial development.
 *  @description : This file is part of [DZ Purchase].
 *  DZ all rights reserved.
 */

namespace customs;

//统一海关贸易报文服务调用工厂
class CustomsTradePostFactory
{
    /**
     * 获取申报服务工厂类, 使用反射机制的优雅实现.
     *
     * @throws \Exception
     */
    public function getInstance($className, $args)
    {
        if (class_exists('\\customs\\CustomsDeclareService\\' . $className)) {
            return (new \ReflectionClass('\\customs\\CustomsDeclareService\\' . $className))->newInstance($args);
        }
        throw new \Exception('class not found!');
    }
}
