<?php
/**
 * Item.abstract.php.
 *
 *
 * PHP version 7
 *
 * @copyright   Copyright 2017, Citrus/besidesplus All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @package     Citrus
 * @subpackage  Session
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Session;

use Citrus\CitrusObject;

class CitrusSessionItem extends CitrusObject
{
    /**
     * constructor.
     *
     * @param CitrusSessionItem|null $session
     */
    public function __construct($session = null)
    {
        // is null
        if (is_null($session) === true)
        {
            return ;
        }

        if ($session instanceof CitrusSessionItem)
        {
            $this->bind($session->properties());
        }
        else if (is_array($session) === true)
        {
            foreach ($session as $ky => $vl)
            {
                $this->$ky = serialize($vl);
            }
        }
    }



    /**
     * session value parse method
     *
     * @param CitrusSessionItem $element
     */
    public function parseItem(CitrusSessionItem $element)
    {
        $this->bindObject($element);
    }



    /**
     * session value regist method
     *
     * @param string $key
     * @param object $value
     */
    public function regist(string $key, $value)
    {
        $this->$key = serialize($value);
    }



    /**
     * session value call
     *
     * @param string $key
     * @return mixed|null
     */
    public function call(string $key)
    {
        if (isset($this->$key) === true)
        {
            return unserialize($this->$key);
        }
        return null;
    }
}