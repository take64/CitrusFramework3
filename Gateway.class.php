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
 * @license     http://www.besidesplus.net/
 */

namespace Citrus;


use Citrus\Sqlmap\CitrusSqlmapException;

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
var_dump([ $ucfirst_document_code, $ucfirst_document_codes ]);

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

//var_dump([$ucfirst_device_code, $ucfirst_application_id, $ucfirst_document_code, CitrusSession::$router->properties()]);

            $controller_file_prefix = array_pop($ucfirst_document_codes);
            $controller_namespace = '\\' . $ucfirst_application_id . '\\Controller\\' . $ucfirst_device_code;
            foreach ($ucfirst_document_codes as $one)
            {
                $controller_namespace .= ('\\' . $one);
            }

var_dump($controller_namespace);
            $controller_class_name = $ucfirst_application_id . $ucfirst_device_code . $ucfirst_document_code . 'Controller';
var_dump($controller_class_name);

            // I have control
            $controller_namespace_class_name = $controller_namespace . '\\' . $controller_class_name;
var_dump($controller_namespace_class_name);
            $controller = new $controller_namespace_class_name();
            $controller->run();

//            foreach ()

//            // コントローラー候補の選出
//            $controllerChoices = array();
//
//            // 大元
//            $controllerChoices[] = array(
//                'path' => CitrusConfigure::$DIR_CONTROLLER
//                    . $ucfirst_term_cd .'Controller.class.php',
//                'name' => $ucfirst_application_cd
//                    . $ucfirst_term_cd .'Controller'
//            );
//
//            // ドキュメントコードのアッパーキャメルケース化
//            $document_code_UCC = '';
//            $document_code_UCC_list = array();
//            $document_code_list = explode('_', str_replace('-', '_', $document_cd));
//            foreach ($document_code_list as $ky => $vl)
//            {
//                $choice = array();
//
//                $document_code_UCC_list[] = ucfirst(strtolower($vl));
//                $document_code_UCC = $document_code_UCC . ucfirst(strtolower($vl));
//
//                $choice = array(
//                    'path' => CitrusConfigure::$DIR_CONTROLLER
//                        . $ucfirst_term_cd .'/'
//                        . implode('/', $document_code_UCC_list) .'Controller.class.php',
//                    'name' => $ucfirst_application_cd
//                        . $ucfirst_term_cd
//                        . $document_code_UCC . 'Controller',
//                );
//                if (file_exists($choice['path']) === true)
//                {
//                    $controllerChoices[] = $choice;
//                }
//                else
//                {
//                    continue;
//                }
//            }
//
//            $lastChoice = $controllerChoices[count($controllerChoices) - 1];
//            $controller_file_path = $lastChoice['path'];
//            $controller_class_name = $lastChoice['name'];

            // CitrusLogger::debug(array(
            // '----+----+----+----',
            // $controller_file_path,
            // $controller_class_name,
            // 0
            // ));

            // go controller
//            include_once $controller_file_path;
//            $controller = new $controller_class_name();
//            $result = $controller->run();

            // CitrusLogger::debug(array(
            // '----+----+----+----',
            // $controller_file_path,
            // $controller_class_name,
            // 1
            // ));

            // save controller
            CitrusSession::commit();
        }
        catch(CitrusServiceException $se)
        {
            CitrusLogger::debug($se);
            $se->_commit();
        }
        catch(CitrusErrorException $ee)
        {
            CitrusLogger::debug($ee);
            $ee->_commit();
        }
        catch(Exception $e)
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

        }
//        catch(CitrusServiceException $se)
//        {
//            CitrusLogger::debug($se);
//            $se->_commit();
//        }
//        catch(CitrusErrorException $ee)
//        {
//            CitrusLogger::debug($ee);
//            $ee->_commit();
//        }
//        catch(Exception $e)
//        {
//            CitrusLogger::debug($e);
//        }
    }
}