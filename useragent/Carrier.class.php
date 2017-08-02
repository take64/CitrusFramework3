<?php
/**
 * Carrier.class.php.
 *
 *
 * PHP version 7
 *
 * @copyright   Copyright 2017, Citrus/besidesplus All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @package     Citrus
 * @subpackage  Useragent
 * @license     http://www.besidesplus.net/
 */

namespace Citrus\Useragent;


class CitrusUseragentCarrier
{
    /** @var string */
    const DOCOMO = 'docomo';

    /** @var string */
    const AU = 'au';

    /** @var string */
    const SOFTBANK = 'softbank';

    /** @var string */
    const GOOGLE = 'google';

    /** @var string */
    const PROVIDER = 'provider';

    /** @var string */
    const OTHER = 'other';



    /**
     * call carrier list
     *
     * @return array
     */
    public static function callCarrierList() : array
    {
        return [
            self::DOCOMO,
            self::AU,
            self::SOFTBANK,
            self::GOOGLE,
            self::PROVIDER,
            self::OTHER,
        ];
    }
}
