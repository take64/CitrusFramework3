<?php
/**
 * Paths.class.php.
 *
 *
 * PHP version 7
 *
 * @copyright   Copyright 2017, Citrus/besidesplus All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @package     Citrus
 * @subpackage  Configure
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Configure;


use Citrus\CitrusObject;

class CitrusConfigurePaths extends CitrusObject
{
    /** @var string */
    public $domain = '';

    /** @var string */
    public $cache = '';

    /** @var string */
    public $compile = '';

    /** @var string */
    public $template = '';

    /** @var string */
    public $javascript = '';

    /** @var string */
    public $stylesheet = '';

    /** @var string */
    public $smartyplugin = '';



    /**
     * call cache
     *
     * @param string|null $append_path
     * @return string
     */
    public function callCache(string $append_path = null)
    {
        return $this->replace($this->cache, $append_path);
    }



    /**
     * call compile
     *
     * @param string|null $append_path
     * @return string
     */
    public function callCompile(string $append_path = null)
    {
        return $this->replace($this->compile, $append_path);
    }



    /**
     * call template
     *
     * @param string|null $append_path
     * @return string
     */
    public function callTemplate(string $append_path = null)
    {
        return $this->replace($this->template, $append_path);
    }



    /**
     * call javascript
     *
     * @param string|null $append_path
     * @return string
     */
    public function callJavascript(string $append_path = null)
    {
        return $this->replace($this->javascript, $append_path);
    }



    /**
     * call stylesheet
     *
     * @param string|null $append_path
     * @return string
     */
    public function callStylesheet(string $append_path = null)
    {
        return $this->replace($this->stylesheet, $append_path);
    }



    /**
     * call smarty plugin
     *
     * @param string|null $append_path
     * @return string
     */
    public function callSmartyplugin(string $append_path = null)
    {
        return $this->replace($this->smartyplugin, $append_path);
    }



    /**
     * domain など置換用
     *
     * @param string      $search
     * @param string|null $append_path
     * @return string
     */
    private function replace(string $search, string $append_path = null)
    {
        $result = $search;
        $result = str_replace('{#domain#}', $this->domain, $result);

        $result .= $append_path;

        return $result;
    }
}