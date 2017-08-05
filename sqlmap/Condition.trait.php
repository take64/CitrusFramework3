<?php
/**
 * Condition.trait.php.
 *
 *
 * PHP version 7
 *
 * @copyright   Copyright 2017, Citrus/besidesplus All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @package     Citrus
 * @subpackage  Sqlmap
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Sqlmap;


use Citrus\CitrusNVL;

trait CitrusSqlmapCondition
{
    /** @var string keyword */
    public $keyword;

    /** @var int page */
    public $page;

    /** @var int limit */
    public $limit;

    /** @var int offset */
    public $offset;

    /** @var string orderby */
    public $orderby = null;

    /** @var bool is count */
    public $is_count = false;



    /**
     * page limit offset
     *
     * @param int|null $page
     * @param int|null $limit
     */
    public function pageLimit(int $page = null, int $limit = null)
    {
        // page
        $page = CitrusNVL::EmptyVL($page, $this->page);
        $page = CitrusNVL::EmptyVL($page, 1);
        $this->page = $page;

        // limit
        $limit = CitrusNVL::EmptyVL($limit, $this->limit);
        $limit = CitrusNVL::EmptyVL($limit, 10);
        $this->limit = $limit;

        // offset
        $this->offset = CitrusNVL::EmptyVL($this->offset, function () use ($page, $limit) {
            return $limit * ($page - 1);
        });
    }



    /**
     * 曖昧一致
     *
     * @param string|array|null $property
     */
    public function toLike($property = null)
    {
        if (is_array($property) === true)
        {
            foreach ($property as $one)
            {
                $this->toLike($one);
            }
        }
        elseif (is_string($this->$property) === true)
        {
            $this->$property = self::like($this->$property);
        }
    }



    /**
     * 前方一致
     *
     * @param string|null $property
     */
    public function toLikePrefix(string $property = null)
    {
        if (is_string($this->$property) === true)
        {
            $this->$property = self::likePrefix($this->$property);
        }
    }



    /**
     * 後方一致
     *
     * @param string|null $property
     */
    public function toLikeSuffix(string $property = null)
    {
        if (is_string($this->$property) === true)
        {
            $this->$property = self::likeSuffix($this->$property);
        }
    }



    /**
     * 曖昧一致
     *
     * @param string|null $property
     * @return string
     */
    public static function like(string $property = null) : string
    {
        if (is_string($property) === true)
        {
            return str_replace(
                '%%',
                '%',
                '%' . $property . '%'
            );
        }
        return null;
    }



    /**
     * 前方一致
     *
     * @param string|null $property
     * @return string
     */
    public static function likePrefix(string $property = null) : string
    {
        if (is_string($property) === true)
        {
            return str_replace(
                '%%',
                '%',
                '%' . $property
            );
        }
        return null;
    }



    /**
     * 後方一致
     *
     * @param string|null $property
     * @return string
     */
    public static function likeSuffix(string $property = null) : string
    {
        if (is_string($property) === true)
        {
            return str_replace(
                '%%',
                '%',
                $property . '%'
            );
        }
        return null;
    }
}