<?php
/**
 * Session.class.php.
 *
 *
 * PHP version 7
 *
 * @copyright   Copyright 2017, Citrus/besidesplus All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @package     .
 * @subpackage  .
 * @license     http://www.citrus.tk/
 */

namespace Citrus;

use Citrus\Document\CitrusDocumentRouter;
use Citrus\Session\CitrusSessionItem;

class CitrusSession extends CitrusObject
{
    /** @var CitrusSessionItem $_SESSION values 'data' -> 'element' */
    public static $session;

    /** @var CitrusSessionItem $_GET values */
    public static $getdata;

    /** @var CitrusSessionItem $_POST values */
    public static $postdata;

    /** @var CitrusSessionItem $_FILES values */
    public static $filedata;

    /** @var CitrusSessionItem $_POST + $_GET values */
    public static $request;

    /** @var CitrusDocumentRouter routing element */
    public static $router;

    /** @var string session id */
    public static $sessionId;



    /**
     * session run
     *
     * @deprecated
     */
    public static function run()
    {
        self::factory(true);
    }

    /**
     * session run page
     */
    public static function page()
    {
        self::factory(true);
    }

    /**
     * session run part
     */
    public static function part()
    {
        self::factory(false);
    }

    /**
     * session factory method
     *
     * @param boolean $use_ticket
     */
    public static function factory($use_ticket = true)
    {
        session_name('CITRUSSESSID');

        if ($use_ticket == true) {
            // get ticket
//            $citrusTicketKey = $_REQUEST['ctk'];
            $citrus_ticket_key = CitrusNVL::ArrayVL($_REQUEST, 'ctk', '');
            if (empty($citrus_ticket_key) === true)
            {
                $citrus_ticket_key = md5(uniqid(rand()));
            }
            session_id($citrus_ticket_key);
        }
        else
        {
            // sessing session id
            session_id();
        }

        // connect session
        session_start();

        // save old session data
        self::$session  = new CitrusSessionItem(isset($_SESSION['data']) ? $_SESSION['data'] : false);
        self::$getdata  = new CitrusSessionItem($_GET);
        self::$postdata = new CitrusSessionItem($_POST);
        self::$filedata = new CitrusSessionItem($_FILES);
        self::$request  = new CitrusSessionItem($_REQUEST);
        self::$router   = CitrusDocumentRouter::factory($_REQUEST);

        if ($use_ticket)
        {
            // disconnect session
            session_destroy();

            // create session id
            self::$sessionId = md5(uniqid(rand()));

            // sessing session id
            session_id(self::$sessionId);

            // session start
            session_start();
        }
    }



    /**
     * clear
     */
    public static function clear()
    {
        self::$session  = null;
    }



    /**
     * commiter
     */
    public static function commit()
    {
        $_SESSION['data'] = self::$session;
    }
}