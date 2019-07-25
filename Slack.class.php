<?php
/**
 * @copyright   Copyright 2017, Citrus/besidesplus All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus;

use Citrus\Slack\Attachments;
use Citrus\Slack\Item;

class Slack
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
        $configure = Configure::configureMerge('slack');

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
     * @param Item $item
     */
    public static function send(string $key, Item $item)
    {
        // initialize
        self::initialize();

        $slack_data = [];
        if ($item instanceof Attachments)
        {
            $slack_data['attachments'] = [ $item->properties() ];
        }

        Http::post(self::$WEBHOOK_URLS[$key], json_encode($slack_data));
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