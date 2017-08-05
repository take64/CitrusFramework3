<?php
/**
 * Device.class.php.
 *
 *
 * PHP version 7
 *
 * @copyright   Copyright 2017, Citrus/besidesplus All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @package     Citrus
 * @subpackage  Useragent
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Useragent;


class CitrusUseragentDevice
{

    /** @var string */
    const DEFAULT = 'default';

    /** @var string */
    const PC = 'pc';

    /** @var string */
    const MOBILE = 'mobile';

    /** @var string */
    const IPHONE = 'iphone';

    /** @var string */
    const IPAD = 'ipad';

    /** @var string */
    const ANDROID = 'android';

    /** @var string */
    const SMARTPHONE = 'smartphone';

    /** @var string */
    const ROBOT = 'robot';

    /** @var string */
    const SIMULATOR = 'simulator';

    /** @var string */
    const OTHER = 'other';



    /**
     * call device list
     *
     * @return array
     */
    public static function callDeviceList() : array
    {
        return [
            self::DEFAULT,
            self::PC,
            self::MOBILE,
            self::IPHONE,
            self::IPAD,
            self::ANDROID,
            self::SMARTPHONE,
            self::ROBOT,
            self::SIMULATOR,
            self::OTHER,
        ];
    }
}