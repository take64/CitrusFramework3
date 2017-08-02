<?php
/**
 * Pagecode.class.php.
 *
 *
 * PHP version 7
 *
 * @copyright   Copyright 2017, Citrus/besidesplus All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @package     Citrus
 * @subpackage  Pagecode
 * @license     http://www.besidesplus.net/
 */

namespace Citrus\Document;


use Citrus\CitrusConfigure;
use Citrus\CitrusObject;

class CitrusDocumentPagecode extends CitrusObject
{
    /** @var string html title */
    public $page_title = '';

    /** @var string html sub title */
    public $page_subtitle = '';

//    /** @var string internal page id */
//    public $page_id = '';

    /** @var string site title */
    public $site_title = '';

    /** @var string internal site id */
    public $site_id = '';

    /** @var string[] meta keywords */
    public $meta_keywords = [];

    /** @var string[] meta descriptions */
    public $meta_descriptions = [];

    /** @var string[] breadcrumbs */
    public $breadcrumbs = [];

    /** @var string[] javascripts */
    public $javascripts = [];

    /** @var string[] stylesheets */
    public $stylesheets = [];

    /** @var string stylesheet plaintext */
    public $stylesheet_plaintext = '';

    /** @var string copyright */
    public $copyright;

    /** @var string template id */
    public $template_id;



    /**
     * constructor
     */
    public function __construct()
    {
    }



    /**
     * add javascript
     *
     * @param string|string[] $javascript
     */
    public function addJavascript($javascript)
    {
        if (is_array($javascript) === true)
        {
            foreach ($javascript as $one)
            {
                $this->addJavascript($one);
            }
        }
        else if (file_exists($javascript) === true)
        {
            $network_path = '';
            $server_path = CitrusConfigure::$CONFIGURE_ITEM->application->path;
            if (strpos($javascript, $server_path) !== false)
            {
                $network_path = str_replace($server_path, '', $javascript);
            }
            if (empty($network_path) === false)
            {
                $this->add('javascript', '/'.$network_path);
            }
        }
    }

    /**
     * add stylesheet
     *
     * @param   string|string[]    $stylesheet
     */
    public function addStylesheet($stylesheet)
    {
        if (is_array($stylesheet) === true)
        {
            foreach ($stylesheet as $one)
            {
                $this->addStylesheet($one);
            }
        }
        else if (file_exists($stylesheet) === true)
        {
            $network_path = '';
            $server_path = CitrusConfigure::$CONFIGURE_ITEM->application->path;
            if (strpos($stylesheet, $server_path) !== false)
            {
                $network_path = str_replace($server_path, '', $stylesheet);
            }
            if (empty($network_path) === false)
            {
                $this->add('stylesheet', '/'.$network_path);
            }
        }
    }

    /**
     * add stylesheet plaintext
     *
     * @param string $stylesheet
     */
    public function addStylesheetPlaintext($stylesheet)
    {
        if (file_exists($stylesheet) === true)
        {
            $content = str_replace("\r\n", "\n", file_get_contents($stylesheet));
            $this->stylesheet_plaintext .= preg_replace('#/\*/?(\n|[^/]|[^*]/)*\*/#', '', $content);
        }
    }

    /**
     * add breadcrumbs
     *
     * @param string $name
     * @param string $url
     */
    public function addCrumbs($name, $url = '')
    {
        $this->add('crumbs', [$name => $url]);
    }

    /**
     * minimize javascript
     * 同じファイルパスにミニマイズ版があれば置き換える
     */
    public function minimizeJavascript()
    {
        // server path
        $server_path_base = CitrusConfigure::$CONFIGURE_ITEM->application->path;

        // file list
        $files = $this->javascript;
        foreach ($files as $ky => $vl)
        {
            // ファイル名内に.min.jsが含まれない
            if (strpos($vl, '.min.js') === false && strpos($vl, '.js') > 0)
            {
                $file_min_path = str_replace('.js', '.min.js', $vl);
                if (file_exists($server_path_base . $file_min_path) === true)
                {
                    $files[$ky] = $file_min_path;
                }
            }
        }

        $this->javascript = $files;
    }

    /**
     * minimize stylesheet
     * 同じファイルパスにミニマイズ版があれば置き換える
     */
    public function minimizeStylesheet()
    {
        // server path
        $server_path_base = CitrusConfigure::$CONFIGURE_ITEM->application->path;

        // file list
        $files = $this->stylesheet;

        foreach ($files as $ky => $vl)
        {
            // ファイル名内に.min.cssが含まれない
            if (strpos($vl, '.min.css') === false && strpos($vl, '.css') > 0)
            {
                $file_min_path = str_replace('.css', '.min.css', $vl);
                if (file_exists($server_path_base . $file_min_path) === true)
                {
                    $files[$ky] = $file_min_path;
                }
            }
        }

        $this->stylesheet = $files;
    }
}