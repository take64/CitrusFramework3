<?php
/**
 * @copyright   Copyright 2017, Citrus/besidesplus All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus;


use Citrus\Useragent\CitrusUseragentCarrier;
use Citrus\Useragent\CitrusUseragentDevice;
use Citrus\Useragent\CitrusUseragentElement;

class CitrusUseragent
{
    /** ユーザーエージェントパターン配列のpregパターン */
    const PATTERN_KEY = 0;

    /** ユーザーエージェントパターン配列のpreg一致時のデバイス */
    const PATTERN_DEVICE = 1;

    /** ユーザーエージェントパターン配列のpreg一致時のキャリア */
    const PATTERN_CARRIER = 2;



    /** @var bool 初期化済みフラグ */
    public static $IS_INITIALIZED = false;



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

        // パターンチェック
        $is_match = false;
        $patterns = self::callPatterns();
        foreach ($patterns as $pattern)
        {
            if (preg_match($pattern[self::PATTERN_KEY], $useragent) === 1)
            {
                $element->device = $pattern[self::PATTERN_DEVICE];
                $element->carrier = $pattern[self::PATTERN_CARRIER];
                $is_match = true;
                break;
            }
        }
        // パターン一致しなかった場合
        if ($is_match === false)
        {
            $element->device = CitrusUseragentDevice::PC;
            $element->carrier= CitrusUseragentCarrier::OTHER;
        }

        return $element;
    }


    
    /**
     * ユーザーエージェントパターン配列
     *
     * @return array
     */
    private static function callPatterns()
    {
        $patterns = [];

        // Mac, Win ... PC
        $patterns[] = ['/(Macintosh|Windows)/',     CitrusUseragentDevice::PC,          CitrusUseragentCarrier::OTHER];
        // docomo mobile
        $patterns[] = ['/DoCoMo/',                  CitrusUseragentDevice::MOBILE,      CitrusUseragentCarrier::DOCOMO];
        // au mobile
        $patterns[] = ['/KDDI.*UP.Browser/',        CitrusUseragentDevice::MOBILE,      CitrusUseragentCarrier::AU];
        // softbank mobile
        $patterns[] = ['/SoftBank.*NetFront/',      CitrusUseragentDevice::MOBILE,      CitrusUseragentCarrier::SOFTBANK];
        //docomo Xperia
        $patterns[] = ['/Linux;.*Android.*c100/',   CitrusUseragentDevice::ANDROID,     CitrusUseragentCarrier::DOCOMO];
        // softbank iPhone
        $patterns[] = ['/iPhone;/',                 CitrusUseragentDevice::IPHONE,      CitrusUseragentCarrier::SOFTBANK];
        // apple iPhone simulator
        $patterns[] = ['/iPhone Simulator;/',       CitrusUseragentDevice::SMARTPHONE,  CitrusUseragentCarrier::SOFTBANK];
        // other iPad
        $patterns[] = ['/iPad;/',                   CitrusUseragentDevice::IPAD,        CitrusUseragentCarrier::OTHER];
        // other iPod
        $patterns[] = ['/iPod;/',                   CitrusUseragentDevice::SMARTPHONE,  CitrusUseragentCarrier::OTHER];
        // android
        $patterns[] = ['/Linux;.*Android/',         CitrusUseragentDevice::ANDROID,     CitrusUseragentCarrier::OTHER];
        // android
        $patterns[] = ['/Android/',                 CitrusUseragentDevice::ANDROID,     CitrusUseragentCarrier::OTHER];
        // bot
        $patterns[] = ['/(Googlebot|Baiduspider)/', CitrusUseragentDevice::PC,          CitrusUseragentCarrier::OTHER];

        return $patterns;
    }
}