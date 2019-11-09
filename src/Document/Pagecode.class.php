<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2017, CitrusFramework. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Document;

use Citrus\Citrus;
use Citrus\Configure;
use Citrus\Struct;

/**
 * ページコード処理
 */
class Pagecode extends Struct
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
     * @return void
     */
    public function addJavascript($javascript): void
    {
        // 配列の場合は再起する
        if (true === is_array($javascript))
        {
            foreach ($javascript as $path)
            {
                $this->addJavascript($path);
            }
            return;
        }

        // パスが絶対パスの場合
        if (true === file_exists($javascript))
        {
            $path = str_replace(Configure::$CONFIGURE_ITEM->application->path, '', $javascript);
            $this->add('javascripts', $path);
            return ;
        }
        // パスがベースディレクトリ以下指定の場合
        $path = Configure::$CONFIGURE_ITEM->application->path . $javascript;
        if (true === file_exists($path))
        {
            $this->add('javascripts', $javascript);
            return;
        }
        // パスがライブラリの可能性
        $path = Configure::$CONFIGURE_ITEM->paths->callJavascriptLibrary($javascript);
        if (true === file_exists($path))
        {
            $this->addJavascript($path);
            return;
        }
        // パスが独自追加の場合
        $path = Configure::$CONFIGURE_ITEM->paths->callJavascript($javascript);
        if (true === file_exists($path))
        {
            $this->addJavascript($path);
            return;
        }
        // ページ用リソースの場合
        $path = Configure::$CONFIGURE_ITEM->paths->callJavascript('/Page' . $javascript);
        if (true === file_exists($path))
        {
            $this->addJavascript($path);
            return;
        }
    }



    /**
     * add stylesheet
     *
     * @param string|string[] $stylesheet
     * @return void
     */
    public function addStylesheet($stylesheet): void
    {
        // 配列の場合は再起する
        if (true === is_array($stylesheet))
        {
            foreach ($stylesheet as $path)
            {
                $this->addStylesheet($path);
            }
            return;
        }

        // パスが絶対パスの場合
        if (true === file_exists($stylesheet))
        {
            $path = str_replace(Configure::$CONFIGURE_ITEM->application->path, '', $stylesheet);
            $this->add('stylesheets', $path);
            return;
        }
        // パスがベースディレクトリ以下指定の場合
        $path = Configure::$CONFIGURE_ITEM->application->path . $stylesheet;
        if (true === file_exists($path))
        {
            $this->add('stylesheets', $stylesheet);
            return;
        }
        // パスがライブラリの可能性
        $path = Configure::$CONFIGURE_ITEM->paths->callStylesheetLibrary($stylesheet);
        if (true === file_exists($path))
        {
            $this->addStylesheet($path);
            return;
        }
        // パスが独自追加の場合
        $path = Configure::$CONFIGURE_ITEM->paths->callStylesheet($stylesheet);
        if (true === file_exists($path))
        {
            $this->addStylesheet($path);
            return;
        }
        // ページ用リソースの場合
        $path = Configure::$CONFIGURE_ITEM->paths->callStylesheet('/Page' . $stylesheet);
        if (true === file_exists($path))
        {
            $this->addStylesheet($path);
            return;
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
        $server_path_base = Configure::$CONFIGURE_ITEM->application->path;

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
        $server_path_base = Configure::$CONFIGURE_ITEM->application->path;

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