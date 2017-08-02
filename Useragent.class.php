<?php
/**
 * Useragent.class.php.
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


use Citrus\Useragent\CitrusUseragentCarrier;
use Citrus\Useragent\CitrusUseragentDevice;
use Citrus\Useragent\CitrusUseragentElement;

class CitrusUseragent
{
    /** @var bool */
    public static $INITIALIZED = false;



    /**
     * call useragent
     *
     * @param string|null $useragent
     * @return CitrusUseragentElement
     */
    public static function callUseragent(string $useragent = null) : CitrusUseragentElement
    {
        return self::vague($useragent);
    }



    /**
     * useragent vague
     * ユーザエージェントについて曖昧な情報を返す
     *
     * @param   string $useragent
     * @return  CitrusUseragentElement
     */
    public static function vague(string $useragent = null) : CitrusUseragentElement
    {
        // 指定が無い場合はデフォルト値
        if (empty($useragent) === true)
        {
            if (isset($_SERVER['HTTP_USER_AGENT']) === true)
            {
                $useragent = $_SERVER['HTTP_USER_AGENT'];
            }
            else
            {
                $useragent = null;
            }
        }
        $useragent = trim($useragent);

        // element
        $element = new CitrusUseragentElement();
        $element->useragent = $useragent;

        // Mac, Win ... PC
        if (preg_match('/(Macintosh|Windows)/', $useragent) === 1)
        {
            $element->device = CitrusUseragentDevice::PC;
            $element->carrier= CitrusUseragentCarrier::OTHER;
        }
        // docomo mobile
        else if (preg_match('/DoCoMo/', $useragent) === 1)
        {
            $element->device = CitrusUseragentDevice::MOBILE;
            $element->carrier= CitrusUseragentCarrier::DOCOMO;
        }
        // au mobile
        else if (preg_match('/KDDI.*UP.Browser/', $useragent) === 1)
        {
            $element->device = CitrusUseragentDevice::MOBILE;
            $element->carrier= CitrusUseragentCarrier::AU;
        }
        // softbank mobile
        else if (preg_match('/SoftBank.*NetFront/', $useragent) === 1)
        {
            $element->device = CitrusUseragentDevice::MOBILE;
            $element->carrier= CitrusUseragentCarrier::SOFTBANK;
        }
        //docomo Xperia
        else if (preg_match('/Linux;.*Android.*c100/', $useragent) === 1)
        {
            $element->device = CitrusUseragentDevice::ANDROID;
            $element->carrier= CitrusUseragentCarrier::DOCOMO;
        }
        // softbank iPhone
        else if (preg_match('/iPhone;/', $useragent) === 1)
        {
            $element->device = CitrusUseragentDevice::IPHONE;
            $element->carrier= CitrusUseragentCarrier::SOFTBANK;
        }
        // apple iPhone simulator
        else if (preg_match('/iPhone Simulator;/', $useragent) === 1)
        {
            $element->device = CitrusUseragentDevice::SMARTPHONE;
            $element->carrier= CitrusUseragentCarrier::SOFTBANK;
        }
        // other iPod
        else if (preg_match('/iPod;/', $useragent) === 1)
        {
            $element->device = CitrusUseragentDevice::SMARTPHONE;
            $element->carrier= CitrusUseragentCarrier::OTHER;
        }
        // android
        else if (preg_match('/Linux;.*Android/', $useragent) === 1)
        {
            $element->device = CitrusUseragentDevice::ANDROID;
            $element->carrier= CitrusUseragentCarrier::OTHER;
        }
        // android
        else if (preg_match('/Android/', $useragent) === 1)
        {
            $element->device = CitrusUseragentDevice::ANDROID;
            $element->carrier= CitrusUseragentCarrier::OTHER;
        }
        // bot
        else if (preg_match('/(Googlebot|Baiduspider)/', $useragent) === 1)
        {
            $element->device = CitrusUseragentDevice::PC;
            $element->carrier= CitrusUseragentCarrier::OTHER;
        }
        else
        {
            $element->device = CitrusUseragentDevice::PC;
            $element->carrier= CitrusUseragentCarrier::OTHER;
        }

        return $element;
    }
}