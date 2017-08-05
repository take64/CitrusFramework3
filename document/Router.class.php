<?php
/**
 * Router.class.php.
 *
 *
 * PHP version 7
 *
 * @copyright   Copyright 2017, Citrus/besidesplus All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @package     Citrus
 * @subpackage  Document
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Document;

use Citrus\CitrusNVL;
use Citrus\CitrusObject;
use Citrus\CitrusUseragent;
use Citrus\Useragent\CitrusUseragentDevice;

class CitrusDocumentRouter extends CitrusObject
{
    /** @var string */
    public $device;

    /** @var string */
    public $document;

    /** @var string */
    public $action;

    /** @var array */
    public $parameters;

    /** @var array */
    public static $DEVICE_ROUTING = [];

    /** @var bool */
    public static $IS_INITIALIZED = false;



    /**
     * initialize router
     *
     * @param array $default_configure
     * @param array $configure
     */
    public static function initialize(array $default_configure = [], array $configure = [])
    {
        // is initialized
        if (self::$IS_INITIALIZED === true)
        {
            return ;
        }

//        if (is_null($path_configure) === true)
//        {
//            $path_configure = CitrusConfigure::$PATH_CONFIGURE;
//        }
//
//        $xml = CitrusConfigure::callConfigureXML($path_configure);
//        $xpath = new DOMXPath($xml);

        // デバイス一覧
        $device_list = CitrusUseragentDevice::callDeviceList();

        // デバイス設定
        $devices = [];
        $devices = array_merge($devices, CitrusNVL::ArrayVL($default_configure, 'device', []));
        $devices = array_merge($devices, CitrusNVL::ArrayVL($configure, 'device', []));

//        var_dump($devices);
        // デバイスルーティング設定
        foreach ($device_list as $one)
        {
            if (isset($devices[$one]) === true)
            {
                self::$DEVICE_ROUTING[$one] = $devices[$one];
            }
        }

        // initialized
        self::$IS_INITIALIZED = true;
    }



    /**
     * call default URL
     *
     * @return  string
     */
    public static function callDefaultURL()
    {
//        $xml = CitrusConfigure::callConfigureXML();
//        $xpath = new DOMXPath($xml);
//        $defaultUrl = ($xpath->query('/citrus2/routing/property[@name="default"]/@value')->length > 0 ?
//            $xpath->query('/citrus2/routing/property[@name="default"]/@value')->item(0)->value : '');
//        return $defaultUrl;
        // TODO:
        return 'home/index';
    }



    /**
     * call login URL
     *
     * @return  string
     */
    public static function callLoginURL()
    {
//        $xml = CitrusConfigure::callConfigureXML();
//        $xpath = new DOMXPath($xml);
//        $defaultUrl = ($xpath->query('/citrus2/routing/property[@name="login"]/@value')->length > 0 ?
//            $xpath->query('/citrus2/routing/property[@name="login"]/@value')->item(0)->value : '');
//        return $defaultUrl;
        // TODO:
        return 'home/login';
    }



    /**
     * url parse
     *
     * @param string|null $url
     * @return CitrusDocumentRouter
     */
    public static function parseURL(string $url = null)
    {
        // ルータ
        $router = new CitrusDocumentRouter();

//        // デフォルト取得
//        if ($url === false)
//        {
//            $url = self::callDefaultURL();
//        }

        // 分割
        $routers = explode('/', $url);
//var_dump($url);
        // /で終わっている場合を考慮
        if (count($routers) > 0)
        {
            $last_index = count($routers) - 1;
            if (empty($routers[$last_index]) === true)
            {
                unset($routers[$last_index]);
            }
        }

        // /で始まっている場合を考慮
        if (count($routers) > 0)
        {
            if (empty($routers[0]) === true)
            {
                unset($routers[0]);
            }
        }

        // 添え字振り直し
        $routers = array_merge($routers);

//        // オフセット
//        $offset = 0;

        // prefix が device 設定にある場合
        if (in_array($routers[0], self::$DEVICE_ROUTING) === true)
        {
            $router->device = array_shift($routers);
        }
        // useragent から device 設定を取得
        else
        {
            $useragent = CitrusUseragent::vague();
//            var_dump([self::$DEVICE_ROUTING, $useragent->device]);
            $router->device = self::$DEVICE_ROUTING[$useragent->device];
        }

        // ルーティング要素が１つしか無い場合はデフォルトでindexをつける
        if (count($routers) === 1)
        {
            $routers[] = 'index';
        }

        // 最終要素がactionになる
        $router->action = array_pop($routers);

        // 残った要素がdocumentになる
        $router->document = implode('-', $routers);
//
//
//
//
//        switch (count($routers))
//        {
//            case ($offset + 1):
//                $router->document = $routers[$offset];
//                $router->action = 'index';
//                break;
//            case ($offset + 2):
//                $router->document = $routers[$offset];
//                $router->action = $routers[($offset + 1)];
//                break;
//            case ($offset + 3):
//                $router->document = $routers[$offset];
//                $router->action = $routers[($offset + 1)];
//                $params = explode('&', $routers[($offset + 2)]);
//                foreach ($params as $param)
//                {
//                    if (strpos($param, '=') !== false)
//                    {
//                        list($ky, $vl) = explode('=', $param);
//                        $router->add('parameters', array($ky => $vl));
//                    }
//                    else
//                    {
//                        $router->add('parameters', array('args' => $param));
//                    }
//                }
//                break;
//            case ($offset + 4):
//                $router->document = $routers[$offset];
//                $router->action = $routers[($offset + 1)];
//                $params = explode('&', $routers[($offset + 2)].'/'.$routers[($offset + 3)]);
//                foreach ($params as $param)
//                {
//                    if (strpos($param, '=') !== false)
//                    {
//                        list($ky, $vl) = explode('=', $param);
//                        $router->add('parameters', array($ky => $vl));
//                    }
//                    else
//                    {
//                        $router->add('parameters', array('args' => $param));
//                    }
//                }
//                break;
//            default:
//                break;
//        }

//var_dump($router->properties());
        return $router;
    }



    /**
     * factory
     *
     * @param array|null $request
     * @return CitrusDocumentRouter
     */
    public static function factory(array $request = null)
    {
        if (isset($request['url']) === false)
        {
            $request = ['url' => self::callDefaultURL()];
        }
        $router = self::parseURL($request['url']);
        foreach ($request as $ky => $vl)
        {
            if ($ky == 'url')
            {
                continue;
            }
            $router->add('parameters', [$ky => $vl]);
        };

//        // ロール
//        $roles = CitrusStatus::callRoles();

//        // ドキュメント呼び出し
//        $condition = new CitrusDocumentCondition();
//        $condition->system_cd = CitrusConfigure::$APPLICATION_CD;
//        $condition->in_role_cd = array(CitrusDocument::ANONYMOUS_ROLE_CD);
//        $roles = CitrusStatus::callRoles();
//        foreach ($roles as $ky => $vl)
//        {
//            $condition->in_role_cd[] = $vl->role_cd;
//        }

//        // キャッシュ取得
//        $enableDocuments = CitrusSession::$session->call('enableDocuments');

//        // ドキュメントコード生成
//        $document_code = sprintf('%s_%s_%s',
//            $router->device,
//            str_replace('-', '_', $router->document),
//            $router->action
//        );
//
//        // 呼び出せるまで；
//        for($i = 0; $i < 10; $i++)
//        {
//            $document_code
//
//            $condition->document_cd = $router->term .'_'. str_replace('-', '_', $router->document) .'_'. $router->action;
//
//            $document = CitrusDocument::callClient()->callDocument($condition);
////            CitrusLogger::debug(array(__METHOD__,$document));
//            if (empty($document) === false)
//            {
//                break;
//            }
//            else
//            {
//                if ($i == 0)
//                {
//                    $router = self::parseURL(self::callDefaultURL());
//                }
//                else
//                {
//                    $router = self::parseURL(self::callLoginURL());
//                }
//                CitrusMessage::addError('アクセスに失敗しました。');
//            }
//        }
//
//        CitrusLogger::debug(array('factory-router' => $router, 'document' => $document));
//        if (empty($document) === true)
//        {
//            header('HTTP/1.0 404 Not Found');
//        }
//        CitrusSession::$session->regist('document', $document);
//
//        // キャッシュ
//        if (is_null($document) === false)
//        {
//            $enableDocuments[$document->document_cd] = $document;
//            CitrusSession::$session->regist('enableDocuments', $enableDocuments);
//        }

        return $router;
    }


    /**
     * redirect URL
     *
     * @access  public
     * @since   0.0.3.4 2012.03.14
     * @version 0.0.3.4 2012.03.14
     * @return  string
     */
    public static function redirectURL($url = null)
    {
        if ($url != null)
        {
            header('Location: '. $url);
            exit;
        }
    }
}