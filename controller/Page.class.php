<?php
/**
 * Page.class.php.
 *
 *
 * PHP version 7
 *
 * @copyright   Copyright 2017, Citrus/besidesplus All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @package     Citrus
 * @subpackage  Controller
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Controller;


use Citrus\CitrusConfigure;
use Citrus\CitrusException;
use Citrus\CitrusFormmap;
use Citrus\CitrusMessage;
use Citrus\CitrusSession;
use Citrus\Document\CitrusDocumentPagecode;
use Citrus\Document\CitrusDocumentRouter;
use Citrus\Http\CitrusHttpHeader;
use Citrus\Library\CitrusLibrarySmarty3;
use Exception;
use Smarty_Internal_Template;

class CitrusControllerPage
{
    /** @var CitrusDocumentPagecode */
    private $pagecode;

    /** @var CitrusLibrarySmarty3 */
    private $smarty = null;

    /** @var CitrusFormmap */
    private $formmap = null;

    /**
     * controller run
     */
    public function run()
    {
        try
        {
            // ルーター
            $router = clone CitrusSession::$router;
            // 実行アクション
            $actionName = $router->action;
            if (method_exists($this, $actionName) === false)
            {
                $actionName = 'none';
                $router->action = $actionName;
                if (method_exists($this, $actionName) === false)
                {
                    $router404 = CitrusDocumentRouter::parseURL(CitrusConfigure::$CONFIGURE_ITEM->routing->error404);
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
            $this->callSmarty()->assign('router', CitrusSession::$router);
            $this->callSmarty()->assign('pagecode', $this->callPagecode());
            $this->callSmarty()->assign('formmap',  $this->callFormmap());
            $this->callSmarty()->assign('errors',   CitrusMessage::popErrors());
            $this->callSmarty()->assign('messages', CitrusMessage::popMessages());
            $this->callSmarty()->assign('successes',CitrusMessage::popSuccesses());

            // セッションのコミット
            CitrusSession::commit();

            // リソース読み込み
            $this->loadResource($router);

            // テンプレート読み込み
            $this->loadTemplate($router);
        }
        catch (CitrusException $e)
        {
            CitrusHttpHeader::status404();
            throw $e;
        }
        catch (Exception $e)
        {
            CitrusHttpHeader::status404();
            throw CitrusException::convert($e);
        }
    }



    /**
     * リソース読み込み
     *
     * @param CitrusDocumentRouter|null $router
     */
    private function loadResource(CitrusDocumentRouter $router = null)
    {
        $router = $router ?: CitrusSession::$router;

        // リソース配列用パス
        $resourceDocumentList = explode('_', str_replace('-', '_', $router->document));
        $resourceList[] = $router->device;
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
            $this->callPagecode()->addStylesheet(implode('/', $resourceAppendedList) . '.css');
            $this->callPagecode()->addJavascript(implode('/', $resourceAppendedList) . '.js');
        }

        // プラグイン
        $this->callSmarty()->addPluginsDir([CitrusConfigure::$CONFIGURE_ITEM->paths->callTemplate('/Plug')]);
    }



    /**
     * テンプレート読み込み
     *
     * @param CitrusDocumentRouter|null $router
     * @throws CitrusException
     */
    private function loadTemplate(CitrusDocumentRouter $router = null)
    {
        $router = $router ?: CitrusSession::$router;

        self::displayTemplate($this->callSmarty(), $router);
    }



    /**
     * テンプレート読み込んで表示
     *
     * @param Smarty_Internal_Template|CitrusLibrarySmarty3|null $template
     * @param CitrusDocumentRouter|null     $router
     * @throws CitrusException
     */
    public static function displayTemplate($template, CitrusDocumentRouter $router = null)
    {
        $router = $router ?: CitrusSession::$router;

        $templateDocumentArray = explode('_', str_replace('-', '_', $router->document));
        $templateArray[] = $router->device;
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
        $template_path  = CitrusConfigure::$CONFIGURE_ITEM->paths->callTemplate('/Page') . '/' . implode('/', $templateArray).'.tpl';
        if (file_exists($template_path) === false)
        {
            throw new CitrusException(sprintf('[%s]のテンプレートが存在しません。', $template_path));
        }
        $template->display($template_path);
    }


    /**
     * initialize method
     *
     * @return CitrusDocumentRouter|null
     */
    protected function initialize()
    {
        return null;
    }



    /**
     * release method
     *
     * @return CitrusDocumentRouter|null
     */
    protected function release()
    {
        return null;
    }


    /**
     * 404 method
     *
     * @return CitrusDocumentRouter|null
     */
    protected function error404()
    {
        return null;
    }



    /**
     * call pagecode
     *
     * @return CitrusDocumentPagecode
     */
    protected function callPagecode() : CitrusDocumentPagecode
    {
        if (is_null($this->pagecode) === true)
        {
            $application = CitrusConfigure::$CONFIGURE_ITEM->application;
            $_pagecode = new CitrusDocumentPagecode();
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
     * @return CitrusFormmap
     */
    protected function callFormmap() : CitrusFormmap
    {
        if (is_null($this->formmap) === true)
        {
            CitrusFormmap::initialize(CitrusConfigure::$CONFIGURE_PLAIN_DEFAULT, CitrusConfigure::$CONFIGURE_PLAIN_DOMAIN);
            $this->formmap = new CitrusFormmap();
        }
        return $this->formmap;
    }



    /**
     * call smarty element
     *
     * @return CitrusLibrarySmarty3
     */
    protected function callSmarty() : CitrusLibrarySmarty3
    {
        if (is_null($this->smarty) === true)
        {
            $this->smarty = new CitrusLibrarySmarty3();
        }
        return $this->smarty;
    }
}
