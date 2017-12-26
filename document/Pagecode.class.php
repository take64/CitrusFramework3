<?php
/**
 * @copyright   Copyright 2017, Citrus/besidesplus All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Document;


use Citrus\Citrus;
use Citrus\CitrusConfigure;
use Citrus\CitrusObject;

class CitrusDocumentPagecode extends CitrusObject
{
    /** @var string html title */
    public $page_title = '';

    /** @var string html sub title */
    public $page_subtitle = '';

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

    /** @var string stylesheet, javascript suffix */
    public $suffix = '';



    /**
     * constructor
     */
    public function __construct()
    {
        $this->suffix = Citrus::$TIMESTAMP_INT;
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
            foreach ($javascript as $path)
            {
                $this->addJavascript($path);
            }
        }
        else
        {
            // パスが絶対パスの場合
            if (file_exists($javascript) === true)
            {
                $path = str_replace(CitrusConfigure::$CONFIGURE_ITEM->application->path, '', $javascript);
                $this->add('javascripts', $path);
                return ;
            }
            // パスがベースディレクトリ以下指定の場合
            $path = CitrusConfigure::$CONFIGURE_ITEM->application->path . $javascript;
            if (file_exists($path) === true) {
                $this->add('javascripts', $javascript);
                return;
            }
            // パスがライブラリの可能性
            $path = CitrusConfigure::$CONFIGURE_ITEM->paths->callJavascriptLibrary($javascript);
            if (file_exists($path) === true)
            {
                $this->addJavascript($path);
                return;
            }
            // パスが独自追加の場合
            $path = CitrusConfigure::$CONFIGURE_ITEM->paths->callJavascript($javascript);
            if (file_exists($path) === true)
            {
                $this->addJavascript($path);
                return;
            }
            // ページ用リソースの場合
            $path = CitrusConfigure::$CONFIGURE_ITEM->paths->callJavascript('/Page/' . $javascript);
            if (file_exists($path) === true)
            {
                $this->addJavascript($path);
                return;
            }
        }
    }



    /**
     * add stylesheet
     *
     * @param string|string[] $stylesheet
     */
    public function addStylesheet($stylesheet)
    {
        if (is_array($stylesheet) === true) {
            foreach ($stylesheet as $one) {
                $this->addStylesheet($one);
            }
        } else {
            // パスが絶対パスの場合
            if (file_exists($stylesheet) === true) {
                $path = str_replace(CitrusConfigure::$CONFIGURE_ITEM->application->path, '', $stylesheet);
                $this->add('stylesheets', $path);
                return;
            }
            // パスがベースディレクトリ以下指定の場合
            $path = CitrusConfigure::$CONFIGURE_ITEM->application->path . $stylesheet;
            if (file_exists($path) === true) {
                $this->add('stylesheets', $stylesheet);
                return;
            }
            // パスがライブラリの可能性
            $path = CitrusConfigure::$CONFIGURE_ITEM->paths->callStylesheetLibrary($stylesheet);
            if (file_exists($path) === true) {
                $this->addStylesheet($path);
                return;
            }
            // パスが独自追加の場合
            $path = CitrusConfigure::$CONFIGURE_ITEM->paths->callStylesheet($stylesheet);
            if (file_exists($path) === true) {
                $this->addStylesheet($path);
                return;
            }
            // ページ用リソースの場合
            $path = CitrusConfigure::$CONFIGURE_ITEM->paths->callStylesheet('/Page/' . $stylesheet);
            if (file_exists($path) === true) {
                $this->addStylesheet($path);
                return;
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
        $files = $this->javascripts;
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
        $files = $this->stylesheets;

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