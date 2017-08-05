<?php
/**
 * Item.class.php.
 *
 *
 * PHP version 7
 *
 * @copyright   Copyright 2017, Citrus/besidesplus All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @package     Citrus
 * @subpackage  Message
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Message;


use Citrus\Citrus;

class CitrusMessageItem
{
    /** @var string message type */
    const TYPE_MESSAGE  = 'message';

    /** @var string message type */
    const TYPE_SUCCESS  = 'success';

    /** @var string message type */
    const TYPE_WARNING  = 'warning';

    /** @var string message type */
    const TYPE_ERROR    = 'error';



    /** @var string message type */
    const TAG_COMMON    = 'common';



    /** @var string date */
    public $date;

    /**  @var string message type */
    public $type;

    /**  @var string message name */
    public $name;

    /**  @var string message description */
    public $description;

    /**  @var string message tag */
    public $tag;




    /**
     * constructor
     *
     * @param string|string[] $description
     * @param string|null     $type
     * @param string|null     $name
     * @param string|null     $date
     * @param string|null     $tag
     */
    public function __construct($description = null, $type = null, $name = null, $date = null, $tag = null)
    {
        // 内容がなければ無効
        if (is_null($description) === true)
        {
            return ;
        }

        // date
        if (is_null($date) === true)
        {
            $date = Citrus::$TIMESTAMP_FORMAT;
        }

        // type
        if (is_null($type) === true)
        {
            $type = self::TYPE_MESSAGE;
        }

        // tag
        if (is_null($tag) === true)
        {
            $tag = self::TAG_COMMON;
        }

        // name
        if (is_null($name) === true)
        {
            switch($type)
            {
                case self::TYPE_MESSAGE :
                    $name = 'メッセージ';
                    break;
                case self::TYPE_WARNING :
                    $name = '注意';
                    break;
                case self::TYPE_SUCCESS :
                    $name = '成功';
                    break;
                case self::TYPE_ERROR   :
                    $name = 'エラー';
                    break;
            }
        }
        if (is_array($description) === true)
        {
            $result = '';
            foreach ($description as $row)
            {
                $result .= ' '.$row;
            }
            $description = $result;
        }

        $this->description  = $description;
        $this->type         = $type;
        $this->name         = $name;
        $this->date         = $date;
        $this->tag          = $tag;
    }
}