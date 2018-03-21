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
use Citrus\Controller\CitrusControllerPage;
use Citrus\Document\CitrusDocumentRouter;
use Citrus\Http\CitrusHttpHeader;
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
        if ($type == self::TYPE_CONTROLLER)
        {
            // セッション処理開始
            CitrusSession::factory(true);
            self::controller();
        }
        else if ($type == self::TYPE_COMMAND)
        {
            // セッション処理開始
            CitrusSession::part();
            self::command();
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

            // ドキュメントコード
            $ucfirst_document_code = '';
            $ucfirst_document_codes = [];
            foreach (explode('-', $document_code) as $one)
            {
                $ucfirst_code = ucfirst($one);
                $ucfirst_document_code .= $ucfirst_code;
                $ucfirst_document_codes[] = $ucfirst_code;
            }

            // 頭文字だけ大文字で後は小文字のterm
            $ucfirst_device_code  = ucfirst(strtolower($device_code));

            // 頭文字だけ大文字で後は小文字のAPPLICATION_CD
            $ucfirst_application_id = ucfirst(CitrusConfigure::$CONFIGURE_ITEM->application->id);

            // 末尾を取り除く
            array_pop($ucfirst_document_codes);
            $controller_namespace = '\\' . $ucfirst_application_id . '\\Controller\\' . $ucfirst_device_code;
            foreach ($ucfirst_document_codes as $one)
            {
                $controller_namespace .= ('\\' . $one);
            }

            $controller_class_name = $ucfirst_application_id . $ucfirst_device_code . $ucfirst_document_code . 'Controller';

            // I have control
            $controller_namespace_class_name = $controller_namespace . '\\' . $controller_class_name;
            spl_autoload_call($controller_namespace_class_name);
            /** @var CitrusControllerPage $controller */
            $controller = new $controller_namespace_class_name();
            $controller->run();

            // save controller
            CitrusSession::commit();
        }
        catch (CitrusAutoloaderException $e)
        {
            // 404でリダイレクトの様に振る舞う
            CitrusHttpHeader::status404();
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
    }
}