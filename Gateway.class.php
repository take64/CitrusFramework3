<?php
/**
 * Gateway.class.php.
 *
 *
 * PHP version 7
 *
 * @copyright   Copyright 2017, Citrus/besidesplus All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @package     Citrus
 * @subpackage  .
 * @license     http://www.citrus.tk/
 */

namespace Citrus;


use Citrus\Autoloader\CitrusAutoloaderException;
use Citrus\Document\CitrusDocumentRouter;
use Citrus\Sqlmap\CitrusSqlmapException;
use Exception;

class CitrusGateway
{
    /** @var string controller */
    const TYPE_CONTROLLER = 'controller';

    /** @var string command */
    const TYPE_COMMAND = 'command';




    /**
     * gateway main logic
     *
     * @param string|null $type リクエストタイプ
     */
    public static function main(string $type = null)
    {
        // security nullbyte replace
        $search = "\0";
        $replace = '';
        foreach ($_GET as &$one)     { $one = str_replace($search, $replace, $one); }
        foreach ($_POST as &$one)    { $one = str_replace($search, $replace, $one); }
        foreach ($_REQUEST as &$one) { $one = str_replace($search, $replace, $one); }

        // logic selecter
        switch ($type)
        {
            // web pages
            case self::TYPE_CONTROLLER :
                // セッション処理開始
//                CitrusSession::factory(CitrusConfigure::$SECURITY_TICKET_ENABLE);
                // TODO:
                CitrusSession::factory(true);
                self::controller();
                break;

            // cli command
            case self::TYPE_COMMAND :
                // セッション処理開始
                CitrusSession::part();
                self::command();
                break;

            // service client
            case self::TYPE_CLIENT :
                // セッション処理開始
                CitrusSession::part();
                self::client();
                break;

            // file request
            case self::TYPE_REQUEST :
                CitrusSession::part();
                self::request();
                break;

            // binary output
            case self::TYPE_OUTPUT :
                CitrusSession::part();
                self::output();
                break;

            default :
                CitrusGateway::main(TYPE_CONTROLLER);
                break;
        }
    }



    /**
     * controller main logic
     */
    protected static function controller()
    {
        try
        {
            // ルートパース
            $device_code    = CitrusSession::$router->get('device');
            $document_code  = CitrusSession::$router->get('document');
            $action_code    = CitrusSession::$router->get('action');

            // ドキュメントコード
            $ucfirst_document_code = '';
            $ucfirst_document_codes = [];
            foreach (explode('-', $document_code) as $one)
            {
                $ucfirst_code = ucfirst($one);
                $ucfirst_document_code .= $ucfirst_code;
                $ucfirst_document_codes[] = $ucfirst_code;
            }

            // デバイス取得
            if (is_null($device_code) === true)
            {
                $useragent = CitrusStatus::callUseragent();
                $device_code = $useragent->device;
            }

            // 頭文字だけ大文字で後は小文字のterm
            $ucfirst_device_code  = ucfirst(strtolower($device_code));

            // 頭文字だけ大文字で後は小文字のAPPLICATION_CD
            $ucfirst_application_id = ucfirst(strtolower(CitrusConfigure::$CONFIGURE_ITEM->application->id));

            $controller_file_prefix = array_pop($ucfirst_document_codes);
            $controller_namespace = '\\' . $ucfirst_application_id . '\\Controller\\' . $ucfirst_device_code;
            foreach ($ucfirst_document_codes as $one)
            {
                $controller_namespace .= ('\\' . $one);
            }

            $controller_class_name = $ucfirst_application_id . $ucfirst_device_code . $ucfirst_document_code . 'Controller';

            // I have control
            $controller_namespace_class_name = $controller_namespace . '\\' . $controller_class_name;
            spl_autoload_call($controller_namespace_class_name);
            $controller = new $controller_namespace_class_name();
            $controller->run();

            // save controller
            CitrusSession::commit();
        }
        catch (CitrusAutoloaderException $e)
        {
            header("HTTP/1.0 404 Not Found");
            // 404でリダイレクトの様に振る舞う
            CitrusSession::$router = CitrusDocumentRouter::parseURL(
                CitrusConfigure::$CONFIGURE_ITEM->routing->error404
            );
            self::controller();
        }
        catch (CitrusException $e)
        {
            // 404でリダイレクトの様に振る舞う
            CitrusSession::$router = CitrusDocumentRouter::parseURL(
                CitrusConfigure::$CONFIGURE_ITEM->routing->error404
            );
            self::controller();
        }
        catch (CitrusErrorException $ee)
        {
            CitrusLogger::debug($ee);
            $ee->_commit();
        }
        catch (Exception $e)
        {
            CitrusLogger::debug($e);
        }
    }


    /**
     * cli command main logic
     */
    protected static function command()
    {
        try
        {
            $command = CitrusCommand::callCommand();
            $command->execute();
        }
        catch (CitrusSqlmapException $e)
        {
            CitrusLogger::debug($e);
        }
        catch (Exception $e)
        {
            CitrusLogger::debug($e);
        }
//        catch (CitrusServiceException $se)
//        {
//            CitrusLogger::debug($se);
//            $se->_commit();
//        }
//        catch (CitrusErrorException $ee)
//        {
//            CitrusLogger::debug($ee);
//            $ee->_commit();
//        }
//        catch (Exception $e)
//        {
//            CitrusLogger::debug($e);
//        }
    }
}