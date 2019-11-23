<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2017, CitrusFramework. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Controller;

use Citrus\CitrusException;
use Citrus\Configure;
use Citrus\Document\Pagecode;
use Citrus\Document\Router;
use Citrus\Formmap;
use Citrus\Http\Header;
use Citrus\Library\Smarty3;
use Citrus\Message;
use Citrus\Session;
use Citrus\Struct;
use Exception;
use Smarty_Internal_Template;

/**
 * ページ処理
 */
class Page extends Struct
{
    /** @var Pagecode */
    protected $pagecode;

    /** @var Smarty3 */
    protected $smarty = null;

    /** @var Formmap */
    protected $formmap = null;



    /**
     * controller run
     *
     * @throws CitrusException
     */
    public function run()
    {
        try
        {
            // ルーター
            $router = clone Session::$router;
            // 実行アクション
            $actionName = $router->action;
            if (method_exists($this, $actionName) === false)
            {
                $actionName = 'none';
                $router->action = $actionName;
                if (method_exists($this, $actionName) === false)
                {
                    $router404 = Router::parseURL(Configure::$CONFIGURE_ITEM->routing->error404);
                    $actionName = $router404->action;
                    $router->document = $router404->document;
                    $router->action = $actionName;
                }
            }

            // 初期化実行
            $templateRouterInitialize = $this->initialize();
            $router = $templateRouterInitialize ?: $router;

            // アクション実行
            $templateRouterAction = $this->$actionName();
            $router = $templateRouterAction ?: $router;

            // 後片付け
            $templateRouterReleace = $this->release();
            $router = $templateRouterReleace ?: $router;

            // form値のbind
            $this->callFormmap()->bind();

            // テンプレート当て込み
            $this->callSmarty()->assign('router', Session::$router);
            $this->callSmarty()->assign('pagecode', $this->callPagecode());
            $this->callSmarty()->assign('formmap', $this->callFormmap());
            $this->callSmarty()->assign('errors', Message::popErrors());
            $this->callSmarty()->assign('messages', Message::popMessages());
            $this->callSmarty()->assign('successes', Message::popSuccesses());

            // セッションのコミット
            Session::commit();

            // リソース読み込み
            $this->loadResource($router);

            // テンプレート読み込み
            $this->loadTemplate($router);
        }
        catch (CitrusException $e)
        {
            Header::status404();
            throw $e;
        }
        catch (Exception $e)
        {
            Header::status404();
            throw CitrusException::convert($e);
        }
    }



    /**
     * テンプレート読み込んで表示
     *
     * @param Smarty_Internal_Template|Smarty3|null $template
     * @param Router|null     $router
     * @throws CitrusException|\SmartyException
     */
    public static function displayTemplate($template, Router $router = null)
    {
        $router = $router ?: Session::$router;

        $templateDocumentArray = explode('_', str_replace('-', '_', $router->document));
        $templateArray = [$router->device];
        foreach ($templateDocumentArray as $templateDocument)
        {
            $templateArray[] = $templateDocument;
        }
        $templateArray[] = $router->action;

        foreach ($templateArray as $ky => $vl)
        {
            $templateArray[$ky] = ucfirst($vl);
        }

        // テンプレート読み込み
        $template_path  = Configure::$CONFIGURE_ITEM->paths->callTemplate('/Page') . '/' . implode('/', $templateArray).'.tpl';
        if (file_exists($template_path) === false)
        {
            throw new CitrusException(sprintf('[%s]のテンプレートが存在しません。', $template_path));
        }
        $template->display($template_path);
    }



    /**
     * initialize method
     *
     * @return Router|null
     */
    protected function initialize()
    {
        return null;
    }



    /**
     * release method
     *
     * @return Router|null
     */
    protected function release()
    {
        return null;
    }


    /**
     * 404 method
     *
     * @return Router|null
     */
    protected function error404()
    {
        return null;
    }



    /**
     * call pagecode
     *
     * @return Pagecode
     */
    protected function callPagecode() : Pagecode
    {
        if (is_null($this->pagecode) === true)
        {
            $application = Configure::$CONFIGURE_ITEM->application;
            $_pagecode = new Pagecode();
            $_pagecode->site_id     = $application->id;
            $_pagecode->site_title  = $application->name;
            $_pagecode->copyright   = $application->copyright;

            $this->pagecode = $_pagecode;
        }
        return $this->pagecode;
    }



    /**
     * call formmap element
     *
     * @return Formmap
     */
    protected function callFormmap() : Formmap
    {
        if (is_null($this->formmap) === true)
        {
            Formmap::initialize(Configure::$CONFIGURE_PLAIN_DEFAULT, Configure::$CONFIGURE_PLAIN_DOMAIN);
            $this->formmap = new Formmap();
        }
        return $this->formmap;
    }



    /**
     * call smarty element
     *
     * @return Smarty3
     */
    protected function callSmarty() : Smarty3
    {
        if (is_null($this->smarty) === true)
        {
            $this->smarty = new Smarty3();
        }
        return $this->smarty;
    }



    /**
     * リソース読み込み
     *
     * @param Router|null $router
     */
    private function loadResource(Router $router = null)
    {
        $router = $router ?: Session::$router;

        // リソース配列用パス
        $resourceDocumentList = explode('_', str_replace('-', '_', $router->document));
        $resourceList = [$router->device];
        foreach ($resourceDocumentList as $ky => $vl)
        {
            $resourceList[] = $vl;
        }
        $resourceList[] = $router->action;
        foreach ($resourceList as $ky => $vl)
        {
            $resourceList[$ky] = ucfirst(strtolower($vl));
        }

        // stylesheet, javascript
        $resourceAppendedList = [];
        foreach ($resourceList as $ky => $vl)
        {
            $resourceAppendedList[] = $vl;
            $path = '/' . implode('/', $resourceAppendedList);
            $this->callPagecode()->addStylesheet($path . '.css');
            $this->callPagecode()->addJavascript($path . '.js');
        }

        // プラグイン
        $this->callSmarty()->addPluginsDir([Configure::$CONFIGURE_ITEM->paths->callTemplate('/Plug')]);
    }



    /**
     * テンプレート読み込み
     *
     * @param Router|null $router
     * @throws CitrusException|\SmartyException
     */
    private function loadTemplate(Router $router = null)
    {
        $router = $router ?: Session::$router;

        self::displayTemplate($this->callSmarty(), $router);
    }
}
