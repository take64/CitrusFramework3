<?php
/**
 * Pager.class.php.
 * 2017/08/25
 *
 * PHP version 7
 *
 * @copyright   Copyright 2017, Citrus/besidesplus All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @package     Citrus
 * @subpackage  Document
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Document;


class CitrusDocumentPager
{
    /** @var int first page no */
    public $first = null;

    /** @var int prev page no */
    public $prev = null;

    /** @var int current page no */
    public $current = null;

    /** @var int next page no */
    public $next = null;

    /** @var int last page no */
    public $last = null;

    /** @var int display range */
    public $range = null;

    /** @var int total item */
    public $total = null;

    /** @var int limit */
    public $limit = null;

    /** @var int[] view */
    public $view = [];

    /** @var int view from */
    public $view_from = null;

    /** @var int view to */
    public $view_to = null;



    /**
     * constructor.
     *
     * @param int $current
     * @param int $total
     * @param int $limit
     * @param int $range
     */
    public function __construct(int $current = 1, int $total = 1, int $limit = 1, int $range = 1)
    {
        if ($current == 0)
        {
            $current = 1;
        }

        $this->current  = intval($current);
        $this->total    = intval($total);
        $this->limit    = intval($limit);
        $this->range    = intval($range);

        $this->generate();
    }



    /**
     * pager generate
     */
    private function generate()
    {
        // variables
        $current    = $this->current;
        $last       = intval(ceil($this->total / $this->limit));
        $range      = ($this->range > $last ? $last : $this->range);

        if ($current === 1)
        {
            $range_from = 1;
            $range_to   = ($range > $last ? $last : $range);
        }
        else if ($current === $last)
        {
            $range_from = ($range > $last ? 1 : $last - $range + 1);
            $range_to   = $last;
        }
        else
        {
            $range_prev = intval(floor(($range - 1) / 2));
            $range_next = intval(ceil(($range - 1) / 2));
            $range_from = $current - $range_prev;
            if ($range_from < 1)
            {
//                $range_from = 1;
                $range_prev = $current - 1;
                $range_next = $range - $range_prev - 1;
            }

            $range_to   = $current + $range_next;
            if ($range_to > $last)
            {
//                $range_to   = $last;
                $range_next = $last - $current;
                $range_prev = $range - $range_next - 1;
            }

            $range_from = $current - $range_prev;
            $range_to   = $current + $range_next;
        }


        // range
        $this->range = intval($range);

        // first page
        $this->first = ($current <= 1 ? null : 1);

        // prev page
        $this->prev = ($current <= 1 ? null : $current - 1);

        // view
        $this->view = array();
        for ($i = $range_from; $i <= $range_to; $i++)
        {
            $this->view[] = $i;
        }

        if ($this->total == 0)
        {
            // next page
            $this->next = null;

            // last page
            $this->last = null;

            // view from
            $this->view_from = 0;

            // view to
            $this->view_to = 0;
        }
        else
        {
            // next page
            $this->next = ($last == $current ? null : $current + 1);

            // last page
            $this->last = ($last == $current ? null : $last);

            // view from
            $this->view_from = $this->limit * ($current - 1) + 1;

            // view to
            $this->view_to = ($last == $current ? $this->total : ($this->limit * $current));
        }
    }
}