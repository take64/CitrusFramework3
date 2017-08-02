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
 * @license     http://www.besidesplus.net/
 */

namespace Citrus\Controller;


use Citrus\CitrusConfigure;
use Citrus\CitrusFormmap;
use Citrus\CitrusMessage;
use Citrus\CitrusSession;
use Citrus\Document\CitrusDocumentPagecode;
use Citrus\Library\CitrusLibrarySmarty3;

class CitrusControllerPage
{
    /** @var CitrusDocumentPagecode */
    public $pagecode;

    /** @var CitrusLibrarySmarty3 */
    public $smarty = null;

    /** @var CitrusFormmap */
    public $formmap = null;

    /**
     * controller run
     */
    public function run()
    {
        try
        {
            // besides default setting
            $pagecode = new CitrusDocumentPagecode();
            $application = CitrusConfigure::$CONFIGURE_ITEM->application;
            $pagecode->site_id      = $application->id;
            $pagecode->site_title   = $application->name;
            $pagecode->copyright    = $application->copyright;

            // ルーター
            $router = CitrusSession::$router;
            $actionName = $router->action;

            // CitrusLogger::debug($router);

//            // ページ名
//            $document = CitrusSession::$session->call('document');
//            $pagecode->page_id      = $document->document_cd;
//            $pagecode->page_title   = $document->name;

            $this->pagecode = $pagecode;

            // サイト用名称
            $site_title = $application->name;
            if (empty($site_title) === false)
            {
                $pagecode->site_title    = $site_title;
            }

            // form値のbind
            $this->callFormmap()->bind();


            $templateRouter = $this->initialize();

            // リソース配列用パス
            $resourceDocumentList = explode('_', str_replace('-', '_', CitrusSession::$router->document));
            $resourceList[] = CitrusSession::$router->device;
            foreach ($resourceDocumentList as $ky => $vl)
            {
                $resourceList[] = $vl;
            }
            $resourceList[] = CitrusSession::$router->action;
            foreach ($resourceList as $ky => $vl)
            {
                $resourceList[$ky] = ucfirst(strtolower($vl));
            }

            // TODO:
//            // CSS,JS追加
//            $resourceAppendedList = [];
//            foreach ($resourceList as $ky => $vl)
//            {
//                $resourceAppendedList[] = $vl;
//                $this->pagecode->addStylesheet(CitrusConfigure::$DIR_STYLESHEET_PAGE.implode('/', $resourceAppendedList).'.css');
//                $this->pagecode->addJavascript(CitrusConfigure::$DIR_JAVASCRIPT_PAGE.implode('/', $resourceAppendedList).'.js');
//            }

            // CitrusLogger::debug($this);

            if (method_exists($this, $actionName) === true)
            {
                $templateRouterAction = $this->$actionName();
            }
            else
            {
                // TODO:
//                $this->none();
                $templateRouterAction = null;
            }
            $templateRouterReleace = $this->release();

            if ($templateRouterAction !== null)
            {
                $templateRouter = $templateRouterAction;
            }
            if ($templateRouterReleace !== null)
            {
                $templateRouter = $templateRouterReleace;
            }
            if ($templateRouter === null)
            {
                $templateRouter = CitrusSession::$router;
            }


            $this->callSmarty()->assign('router', CitrusSession::$router);
            $this->callSmarty()->assign('pagecode', $this->pagecode);
            $this->callSmarty()->assign('formmap',  $this->callFormmap());
            $this->callSmarty()->assign('errors',   CitrusMessage::popErrors());
            $this->callSmarty()->assign('message',  CitrusMessage::popMessages());

            $templateDocumentArray = explode('_', str_replace('-', '_', $templateRouter->document));
            $templateArray[] = $templateRouter->device;
            foreach ($templateDocumentArray as $templateDocument)
            {
                $templateArray[] = $templateDocument;
            }
            $templateArray[] = $templateRouter->action;

            foreach ($templateArray as $ky => $vl)
            {
                $templateArray[$ky] = ucfirst($vl);
            }

            $template_path  = CitrusConfigure::$CONFIGURE_ITEM->paths->callTemplate('/Page') . '/' . implode('/', $templateArray).'.tpl';

            $this->callSmarty()->addPluginsDir(CitrusConfigure::$CONFIGURE_ITEM->paths->callTemplate('/Plug'));
//            $this->callSmarty()->addPluginsDir(CitrusConfigure::$DIR_TEMPLATE_PLUG);
//            $plugin_path    = CitrusConfigure::$DIR_TEMPLATE_PLUG.$templateArray[0].'/';
//            $this->callSmarty()->addPluginsDir($plugin_path);

            //CitrusLogger::debug($this->callSmarty()->plugins_dir);

            $this->callSmarty()->display($template_path);
        }
        catch(Exception $e)
        {
            CitrusLogger::debug($e);
            header("HTTP/1.0 404 Not Found");
        }
    }

    /**
     * initialize method
     *
     * @access  protected
     * @since   0.0.3.5 2012.03.17
     * @version 0.0.3.5 2012.03.17
     * @return  string
     */
    protected function initialize()
    {
        return null;
    }

    /**
     * release method
     *
     * @access  protected
     * @since   0.0.3.5 2012.03.17
     * @version 0.0.3.5 2012.03.17
     * @return  string
     */
    protected function release()
    {
        return null;
    }

    /**
     * call formmap element
     *
     * @return  CitrusFormmap
     */
    protected function callFormmap()
    {
        if ($this->formmap === null)
        {
            CitrusFormmap::initialize();
            $this->formmap = new CitrusFormmap();
        }
        return $this->formmap;
    }

    /**
     * call smarty element
     *
     * @return  CitrusLibrarySmarty3
     */
    protected function callSmarty()
    {
        if ($this->smarty === null)
        {
            $this->smarty = new CitrusLibrarySmarty3();
        }
        return $this->smarty;
    }
}