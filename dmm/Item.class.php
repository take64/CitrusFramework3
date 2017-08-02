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
 * @subpackage  Dmm
 * @license     http://www.besidesplus.net/
 */

namespace Citrus\Dmm;


class CitrusDmmItem
{
    public $service_code = null;
    public $service_name = null;
    public $floor_code = null;
    public $floor_name = null;
    public $category_name = null;
    public $content_id = null;
    public $product_id = null;
    public $title = null;
    public $volume = null;
    public $review = [
        'count' => 0,
        'average' => 0.0,
    ];
    public $URL = null;
    public $URLsp = null;
    public $affiliateURL = null;
    public $affiliateURLsp = null;
    public $imageURL = [
        'list' => null,
        'small' => null,
        'large' => null,
    ];
    public $sampleImageURL = [
        'sample_s' => [
            'image' => [],
        ],
    ];
    public $sampleMovieURL = [
        'size_476_306' => null,
        'size_560_360' => null,
        'size_644_414' => null,
        'size_720_480' => null,
        'pc_flag' => null,
        'sp_flag' => null,
    ];
    public $prices = [
        'price' => null,
        'deliveries' => [
            'delivery' => []
        ],
    ];
    public $date = null;
    public $iteminfo = [
        'genre' => [],
        'maker' => [],
        'label' => [],
    ];
}