<?php
/**
 * Slack.class.php.
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



use Citrus\Slack\CitrusSlackAttachments;
use Citrus\Slack\CitrusSlackItem;

class CitrusSlack
{
    /** @var array webhook_urls */
    public static $WEBHOOK_URLS = [];

    /** @var bool */
    private static $IS_INITIALIZED = false;



    /**
     * initialize
     */
    public static function initialize()
    {
        // is initialize
        if (self::$IS_INITIALIZED === true)
        {
            return ;
        }

        // configure
        $configure = [];
        $configure = array_merge($configure, CitrusNVL::ArrayVL(CitrusConfigure::$CONFIGURE_PLAIN_DEFAULT, 'slack', []));
        $configure = array_merge($configure, CitrusNVL::ArrayVL(CitrusConfigure::$CONFIGURE_PLAIN_DOMAIN, 'slack', []));

        // ids
        self::$WEBHOOK_URLS = [];
        foreach ($configure as $ky => $vl)
        {
            self::$WEBHOOK_URLS[$ky] = $vl['webhook_url'];
        }

        // initialize
        self::$IS_INITIALIZED = true;
    }



    /**
     * slack send
     *
     * @param string          $key
     * @param CitrusSlackItem $item
     */
    public static function send(string $key, CitrusSlackItem $item)
    {
        // initialize
        self::initialize();

        $slack_data = [];
        if ($item instanceof CitrusSlackAttachments)
        {
            $slack_data['attachments'] = [ $item->properties() ];
        }

        CitrusHttp::post(self::$WEBHOOK_URLS[$key], json_encode($slack_data));
    }



    /**
     * slack attachments fields 用の配列に変換
     *
     * @param string $title
     * @param string $value
     * @param bool $short
     * @return array
     */
    public static function toFields(string $title, string $value, bool $short = true)
    {
        return [
            'title' => $title,
            'value' => $value,
            'short' => $short,
        ];
    }
}