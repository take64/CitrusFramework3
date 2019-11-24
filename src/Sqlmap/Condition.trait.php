<?php
/**
 * @copyright   Copyright 2017, CitrusFramework. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Sqlmap;

use Citrus\NVL;

trait Condition
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
     * constructor.
     */
    public function __construct()
    {
        $properties = get_object_vars($this);
        foreach ($properties as $ky => $vl)
        {
            if (in_array($ky, ['schema', 'condition']) === false)
            {
                $this->$ky = null;
            }
        }
    }



    /**
     * page limit offset
     *
     * @param int|null $page
     * @param int|null $limit
     */
    public function pageLimit(int $page = null, int $limit = null)
    {
        // page
        $page = NVL::coalesceNull($page, $this->page, 1);
        $this->page = $page;

        // limit
        $limit = NVL::coalesceNull($limit, $this->limit, 10);
        $this->limit = $limit;

        // offset
        $this->offset = NVL::coalesceNull($this->offset, function () use ($page, $limit) {
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
