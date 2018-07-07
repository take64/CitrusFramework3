<?php
/**
 * @copyright   Copyright 2017, Citrus/besidesplus All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus;


use Citrus\Dmm\CitrusDmmActress;
use Citrus\Dmm\CitrusDmmCondition;
use Citrus\Dmm\CitrusDmmItem;

class CitrusDmm
{
    /** API_IDのキー */
    const API_ID_KEY = 'api_id';

    /** AFFILIATE_IDのキー */
    const AFFILIATE_ID_KEY = 'affiliate_id';

    /** CitrusConfigureキー */
    const CONFIGURE_KEY = 'dmm';



    /** @var string dmm api id */
    public static $API_ID = null;

    /** @var string dmm affiliate id */
    public static $AFFILIATE_ID = null;

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
        $configure = CitrusConfigure::configureMerge(self::CONFIGURE_KEY);

        // ids
        self::$API_ID       = $configure[self::API_ID_KEY];
        self::$AFFILIATE_ID = $configure[self::AFFILIATE_ID_KEY];

        // initialize
        self::$IS_INITIALIZED = true;
    }



    /**
     * search dmm items
     *
     * @param CitrusDmmCondition $condition
     * @return CitrusDmmItem[]
     */
    public static function searchItems(CitrusDmmCondition $condition) : array
    {
        // initialize
        self::initialize();

        $baseurl = 'https://api.dmm.com/affiliate/v3/ItemList';
        $params = [
            self::API_ID_KEY        => self::$API_ID,
            self::AFFILIATE_ID_KEY  => self::$AFFILIATE_ID,
            'site'                  => $condition->site,
            'service'               => $condition->service,
            'hits'                  => $condition->hits,
            'sort'                  => $condition->sort,
            'offset'                => $condition->offset,
            'output'                => 'json',
        ];
        if (is_null($condition->floor) === false)
        {
            $params['floor'] = $condition->floor;
        }
        if (is_null($condition->keyword) === false)
        {
            $keyword = $condition->keyword;
            // DMMは「CLI」という単語を受け付けない
            if (false !== strpos($keyword, 'CLI'))
            {
                return [];
            }
            $keyword = mb_convert_encoding($keyword, 'UTF-8', 'ASCII,JIS,UTF-8,eucjp-win,sjis-win');
            $params['keyword'] = $keyword;

        }
        $params['sort'] = CitrusNVL::ArrayVL($params, 'sort', CitrusDmmCondition::SORT_ITEM_RANK);


        // パラメータの順序を昇順に並び替え
        ksort($params);

        // query を作成します
        $http_query = http_build_query($params);

        // URL を作成します
        $url = $baseurl . '?' . $http_query;

        // url request
        $data = file_get_contents($url);

        if (empty($data) === true)
        {
            return null;
        }

        $data = json_decode($data, true, null, JSON_OBJECT_AS_ARRAY);
        $items = $data['result']['items'];

        $results = [];
        foreach ($items as $one)
        {
            $results[] = self::convertItem($one);
        }

        return $results;
    }



    /**
     * convert dmm item
     *
     * @param array $data
     * @return CitrusDmmItem
     */
    private static function convertItem(array $data) : CitrusDmmItem
    {
        $item = new CitrusDmmItem();

        $item->service_code     = $data['service_code'];
        $item->service_name     = $data['service_name'];
        $item->floor_code       = $data['floor_code'];
        $item->floor_name       = $data['floor_name'];
        $item->category_name    = $data['category_name'];
        $item->content_id       = $data['content_id'];
        $item->product_id       = $data['product_id'];
        $item->title            = $data['title'];
        $item->URL              = $data['URL'];
        $item->URLsp            = CitrusNVL::ArrayVL($data, 'URLsp', '');
        $item->affiliateURL     = $data['affiliateURL'];
        $item->affiliateURLsp   = CitrusNVL::ArrayVL($data, 'affiliateURLsp', '');
        $item->date             = $data['date'];
        $item->imageURL         = CitrusNVL::ArrayVL($data, 'imageURL', null);
        $item->sampleImageURL   = CitrusNVL::ArrayVL($data, 'sampleImageURL', null);
        $item->sampleMovieURL   = CitrusNVL::ArrayVL($data, 'sampleMovieURL', null);
        $item->iteminfo         = CitrusNVL::ArrayVL($data, 'iteminfo', null);
        $item->review           = CitrusNVL::ArrayVL($data, 'review', null);

        if (isset($data['prices']) === true)
        {
            $item->prices = $data['prices'];
            $item->prices['price'] = str_replace('~', '', $item->prices['price']);
        }


        $volume = 0;
        $volume_key = 'volume';
        if (isset($data[$volume_key]) === true)
        {
            if (strpos((string)$data[$volume_key], ':') !== false)
            {
                $volumes = explode(':', substr($data[$volume_key], 0, -3));   // 1:54:00対応
                rsort($volumes);
                foreach ($volumes as $ky => $vl)
                {
                    if ($ky == 0)
                    {
                        $volume += $vl;
                    }
                    else
                    {
                        $volume += ($vl * 60);
                    }
                }
            }
            else
            {
                $volume = (string)$data['volume'];
            }
        }
        $item->volume = $volume;

        return $item;
    }



    /**
     * search dmm actorss
     *
     * @param CitrusDmmCondition $condition
     * @return CitrusDmmItem[]
     */
    public static function searchActresses(CitrusDmmCondition $condition) : array
    {
        // initialize
        self::initialize();

        $baseurl = 'https://api.dmm.com/affiliate/v3/ActressSearch';
        $params = [
            'api_id'        => self::$API_ID,
            'affiliate_id'  => self::$AFFILIATE_ID,
            'hits'          => $condition->hits,
            'sort'          => $condition->sort,
            'offset'        => $condition->offset,
            'output'        => 'json',
        ];
        if (is_null($condition->keyword) === false)
        {
            $params['keyword'] = mb_convert_encoding($condition->keyword, 'UTF-8', 'ASCII,JIS,UTF-8,eucjp-win,sjis-win');
        }
        if (is_null($condition->actress_id) === false)
        {
            $params['actress_id'] = $condition->actress_id;
        }
        $params['sort'] = CitrusNVL::ArrayVL($params, 'sort', CitrusDmmCondition::SORT_ACTORERSS_ID_ASC);

        // パラメータの順序を昇順に並び替え
        ksort($params);

        // query を作成します
        $http_query = http_build_query($params);

        // URL を作成します
        $url = $baseurl . '?' . $http_query;

        // url request
        $data = file_get_contents($url);

        if (empty($data) === true)
        {
            return null;
        }

        $data = json_decode($data, true, null, JSON_OBJECT_AS_ARRAY);
        if (isset($data['result']['actress']) === true)
        {
            $items = $data['result']['actress'];
        }
        else
        {
            $items = [];
        }

        $results = [];
        foreach ($items as $one)
        {
            $results[] = self::convertActress($one);
        }

        return $results;
    }



    /**
     * convert dmm actress
     *
     * @param array $data
     * @return CitrusDmmActress
     */
    private static function convertActress(array $data) : CitrusDmmActress
    {
        $item = new CitrusDmmActress();

        $item->id           = $data['id'];
        $item->name         = $data['name'];
        $item->ruby         = $data['ruby'];
        $item->bust         = $data['bust'];
        $item->cup          = CitrusNVL::ArrayVL($data, 'cup', null);   // cupは何故か有る場合とない場合が有る
        $item->waist        = $data['waist'];
        $item->hip          = $data['hip'];
        $item->height       = $data['height'];
        $item->birthday     = $data['birthday'];
        $item->blood_type   = $data['blood_type'];
        $item->hobby        = $data['hobby'];
        $item->prefectures  = $data['prefectures'];
        $item->imageURL     = CitrusNVL::ArrayVL($data, 'imageURL', null);
        $item->listURL      = CitrusNVL::ArrayVL($data, 'listURL', null);

        return $item;
    }
}
