<?php
/**
 * Dmm.class.php.
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


use Citrus\Dmm\CitrusDmmCondition;
use Citrus\Dmm\CitrusDmmItem;

class CitrusDmm
{
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
        $configure = [];
        $configure = array_merge($configure, CitrusNVL::ArrayVL(CitrusConfigure::$CONFIGURE_PLAIN_DEFAULT, 'dmm', []));
        $configure = array_merge($configure, CitrusNVL::ArrayVL(CitrusConfigure::$CONFIGURE_PLAIN_DOMAIN, 'dmm', []));

        // ids
        self::$API_ID       = $configure['api_id'];
        self::$AFFILIATE_ID = $configure['affiliate_id'];

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
            'api_id'        => self::$API_ID,
            'affiliate_id'  => self::$AFFILIATE_ID,
            'site'          => $condition->site,
            'service'       => $condition->service,
            'hits'          => $condition->hits,
            'sort'          => $condition->sort,
            'offset'        => $condition->offset,
            'output'        => 'json',
        ];
        if (is_null($condition->floor) === false)
        {
            $params['floor'] = $condition->floor;
        }
        if (is_null($condition->keyword) === false)
        {
            $params['keyword'] = mb_convert_encoding($condition->keyword, 'UTF-8', 'ASCII,JIS,UTF-8,eucjp-win,sjis-win');
        }


        // パラメータの順序を昇順に並び替え
        ksort($params);

        // query を作成します
        $http_query = http_build_query($params);

        // URL を作成します
        $url = $baseurl . '?' . $http_query;

        CitrusLogger::debug('DMM request URL = %s', $url);

        // url request
        $data = file_get_contents($url);
        // $data = @simplexml_load_file($url);
// CitrusLogger::debug(array('load' => $data));
// var_dump($data);
        if (empty($data) === true)
        {
            return null;
        }

        $data = json_decode($data, true, null, JSON_OBJECT_AS_ARRAY);
        $items = $data['result']['items'];
//var_dump($items);
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
//        $item->price            = $data['prices->price'];
        $item->date             = $data['date'];

        if (isset($data['imageURL']) === true)
        {
            $item->imageURL = $data['imageURL'];
        }
        if (isset($data['sampleImageURL']) === true)
        {
            $item->sampleImageURL = $data['sampleImageURL'];
        }
        if (isset($data['sampleMovieURL']) === true)
        {
            $item->sampleMovieURL = $data['sampleMovieURL'];
        }
        if (isset($data['prices']) === true)
        {
            $item->prices = $data['prices'];
            $item->prices['price'] = str_replace('~', '', $item->prices['price']);
        }
        if (isset($data['iteminfo']) === true)
        {
            $item->iteminfo = $data['iteminfo'];
        }
        if (isset($data['review']) === true)
        {
            $item->review = $data['review'];
        }



        $volume = 0;
        if (isset($data['volume']) === true)
        {
            if (strpos((string)$data['volume'], ':00') !== false)
            {
                $volumes = explode(':', substr($data['volume'], 0, -3));   // 1:54:00対応
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


//        if (isset($data->iteminfo->maker) == true)
//        {
//            $item->maker            = array(
//                'name'  => $data->iteminfo->maker[0]->name,
//                'id'    => $data->iteminfo->maker[0]->id,
//            );
//        }
//        if (isset($data->iteminfo->genre) == true)
//        {
//            foreach ($data->iteminfo->genre as $keyword)
//            {
//                $item->keyword[]    = array(
//                    'name'  => $keyword->name,
//                    'id'    => $keyword->id,
//                );
//            }
//        }
//        if (isset($data->iteminfo->series) == true)
//        {
//            $item->series           = array(
//                'name'  => $data->iteminfo->series[0]->name,
//                'id'    => $data->iteminfo->series[0]->id,
//            );
//        }
//        if (isset($data->iteminfo->label) == true)
//        {
//            $item->label            = array(
//                'name'  => $data->iteminfo->label[0]->name,
//                'id'    => $data->iteminfo->label[0]->id,
//            );
//        }
//        if (isset($data->iteminfo->actress) == true)
//        {
//            foreach ($data->iteminfo->actress as $actor)
//            {
//                if (strpos((string)$actor->id, '_classify') !== false)
//                {
//                    continue;
//                }
//
//                $item->actor[]      = array(
//                    'name'  => $actor->name,
//                    'id'    => $actor->id,
//                );
//            }
//        }
//        if (isset($data->sampleImageURL->sample_s->image) == true)
//        {
//            foreach ($data->sampleImageURL->sample_s->image as $image)
//            {
//                $item->sampleImageURL[]      = $image;
//            }
//        }
//        if (isset($data->sampleMovieURL) == true)
//        {
//            $item->size_476_306            = $data->sampleMovieURL->size_476_306;
//            $item->size_560_360            = $data->sampleMovieURL->size_560_360;
//            $item->size_644_414            = $data->sampleMovieURL->size_644_414;
//            $item->size_720_480            = $data->sampleMovieURL->size_720_480;
//        }

        return $item;
    }

//    /**
//     * amazon access key id
//     *
//     * @access  public
//     * @since   0.1.0.2 2015.01.28
//     * @version 0.1.0.2 2015.01.28
//     * @var     string
//     */
//    public static $api_id = null;
//
//    /**
//     * amazon secret access key
//     *
//     * @access  public
//     * @since   0.1.0.2 2015.01.28
//     * @version 0.1.0.2 2015.01.28
//     * @var     string
//     */
//    public static $affiliate_id = null;



//    /**
//     * initialize
//     *
//     * @access  public
//     * @since   0.1.0.2 2015.01.28
//     * @version 0.1.0.2 2015.01.28
//     * @param   string  $path_configure
//     */
//    public static function initialize($path_configure = null)
//    {
//        // initialize
//        if (self::$api_id === null
//            || self::$affiliate_id === null)
//        {
//            // configure path
//            if ($path_configure === null)
//            {
//                $path_configure = CitrusConfigure::$PATH_CONFIGURE;
//            }
//
//            // load xml
//            $xml = CitrusConfigure::callConfigureXML($path_configure);
//            $xpath = new DOMXPath($xml);
//
//            self::$api_id       = ($xpath->query('/citrus2/dmm/property[@name="api_id"]/@value')->length > 0 ?
//                $xpath->query('/citrus2/dmm/property[@name="api_id"]/@value')->item(0)->value : '');
//            self::$affiliate_id = ($xpath->query('/citrus2/dmm/property[@name="affiliate_id"]/@value')->length > 0 ?
//                $xpath->query('/citrus2/dmm/property[@name="affiliate_id"]/@value')->item(0)->value : '');
//        }
//    }

//    /**
//     * call item
//     *
//     * @access  public
//     * @since   0.1.0.2 2015.01.28
//     * @version 0.1.0.2 2015.01.28
//     * @param   CitrusDmmCondition $condition
//     * @return  CitrusDmmItem
//     */
//    public static function searchItem(CitrusDmmCondition $condition)
//    {
//        // initialize
//        self::initialize();
//
//        // Memcachedに情報が無い場合はDmmから情報を取得
//        $baseurl = 'https://api.dmm.com/affiliate/v3/ItemList';
//        $params = array('api_id'        => self::$api_id,
//            'affiliate_id'  => self::$affiliate_id,
//            // 'site'          => 'DMM.R18',
//            // 'service'       => 'digital',
//            // 'hits'          => $limit,
//            // 'sort'          => 'rank',
//            'site'          => $condition->site,
//            'service'       => $condition->service,
//            'hits'          => $condition->hits,
//            'sort'          => $condition->sort,
//            'offset'        => $condition->offset,
//            'keyword'       => mb_convert_encoding($condition->keyword, 'UTF-8', 'ASCII,JIS,UTF-8,eucjp-win,sjis-win'),
//            // 'keyword'       => $keyword,
//            'output'        => 'json',
//        );
//        if (is_null($condition->floor) === false)
//        {
//            $params['floor'] = $condition->floor;
//        }
//
//
//        // パラメータの順序を昇順に並び替え
//        ksort($params);
//
//        // canonical string を作成します
//        $canonical_string = '';
//        foreach ($params as $ky => $vl) {
//            $canonical_string .= '&'.$ky.'='.$vl;
//        }
//        $canonical_string = substr($canonical_string, 1);
//
//        // URL を作成します
//        $url = $baseurl.'?'.$canonical_string;
//
//        var_dump($url);
//        // url request
//        $data = file_get_contents($url);
//        // $data = @simplexml_load_file($url);
//// CitrusLogger::debug(array('load' => $data));
//// var_dump($data);
//        if (empty($data) === true)
//        {
//            return null;
//        }
//
//        $data = json_decode($data);
//        $data = $data->result->items;
//// var_dump($data);
//        $results = [];
//        foreach ($data as $one)
//        {
//            $results[] = self::convertItem($one);
//        }
//
//        return $results;
//    }
//
//
//
//    /**
//     * convert
//     *
//     * @access  public
//     * @since   0.1.0.2 2015.01.28
//     * @version 0.1.0.2 2015.01.28
//     * @param   SimpleXMLElement    $data
//     * @return  CitrusDmmItem
//     */
//    public static function convertItem($data)
//    {
//        // var_dump($data);
//        // CitrusLogger::debug($data);
//        $item = new CitrusDmmItem();
//
//        $item->service_name             = $data->service_name;
//        $item->floor_name               = $data->floor_name;
//        $item->category_name            = $data->category_name;
//        $item->content_id               = $data->content_id;
//        $item->product_id               = $data->product_id;
//        $item->title                    = $data->title;
//        $item->URL                      = $data->URL;
//        $item->affiliateURL             = $data->affiliateURL;
//        $item->price                    = $data->prices->price;
//        $item->date                     = $data->date;
//
//        if (isset($data->imageURL) === true)
//        {
//            $item->imageURLlist             = $data->imageURL->list;
//            $item->imageURLsmall            = $data->imageURL->small;
//            $item->imageURLlarge            = $data->imageURL->large;
//        }
//
//
//
//        $volume = 0;
//        if (isset($data['volume']) === true)
//        {
//            if (strpos((string)$data['volume'], ':00') !== false)
//            {
//                $volumes = explode(':', substr($data['volume'], 0, -3));   // 1:54:00対応
//                // var_dump($volumes);
//                rsort($volumes);
//                // var_dump($volumes);
//                foreach ($volumes as $ky => $vl)
//                {
//                    if ($ky == 0)
//                    {
//                        $volume += $vl;
//                    }
//                    else
//                    {
//                        $volume += ($vl * 60);
//                    }
//                }
//            }
//            else
//            {
//                $volume = (string)$data['volume'];
//            }
//        }
//        // var_dump($item->title . ' ' . (string)$data['volume'] . ' => ' . $volume);
//        $item['volume'] = $volume;
//
//
//        if (isset($data->iteminfo->maker) == true)
//        {
//            $item->maker            = array(
//                'name'  => $data->iteminfo->maker[0]->name,
//                'id'    => $data->iteminfo->maker[0]->id,
//            );
//        }
//        if (isset($data->iteminfo->genre) == true)
//        {
//            foreach ($data->iteminfo->genre as $keyword)
//            {
//                $item->keyword[]    = array(
//                    'name'  => $keyword->name,
//                    'id'    => $keyword->id,
//                );
//            }
//        }
//        if (isset($data->iteminfo->series) == true)
//        {
//            $item->series           = array(
//                'name'  => $data->iteminfo->series[0]->name,
//                'id'    => $data->iteminfo->series[0]->id,
//            );
//        }
//        if (isset($data->iteminfo->label) == true)
//        {
//            $item->label            = array(
//                'name'  => $data->iteminfo->label[0]->name,
//                'id'    => $data->iteminfo->label[0]->id,
//            );
//        }
//        if (isset($data->iteminfo->actress) == true)
//        {
//            foreach ($data->iteminfo->actress as $actor)
//            {
//                if (strpos((string)$actor->id, '_classify') !== false)
//                {
//                    continue;
//                }
//
//                $item->actor[]      = array(
//                    'name'  => $actor->name,
//                    'id'    => $actor->id,
//                );
//            }
//        }
//        if (isset($data->sampleImageURL->sample_s->image) == true)
//        {
//            foreach ($data->sampleImageURL->sample_s->image as $image)
//            {
//                $item->sampleImageURL[]      = $image;
//            }
//        }
//        if (isset($data->sampleMovieURL) == true)
//        {
//            $item->size_476_306            = $data->sampleMovieURL->size_476_306;
//            $item->size_560_360            = $data->sampleMovieURL->size_560_360;
//            $item->size_644_414            = $data->sampleMovieURL->size_644_414;
//            $item->size_720_480            = $data->sampleMovieURL->size_720_480;
//        }
//
//// var_dump($item);
//
//
//        return $item;
//    }
//
//    /**
//     * call actor
//     *
//     * @access  public
//     * @since   0.1.0.2 2015.01.28
//     * @version 0.1.0.2 2015.01.28
//     * @param   string  $keyword
//     * @return  CitrusDmmActor
//     */
//    public static function searchActor($keyword = null, $limit = 100)
//    {
//        // initialize
//        self::initialize();
//
//        // Memcachedに情報が無い場合はDmmから情報を取得
//        $baseurl = 'https://api.dmm.com/affiliate/v3/ActressSearch';
//        $params = array('api_id'        => self::$api_id,
//            'affiliate_id'  => self::$affiliate_id,
//            'keyword'       => mb_convert_encoding($keyword, 'UTF-8', 'ASCII,JIS,UTF-8,eucjp-win,sjis-win'),
//            // 'keyword'       => $keyword,
//            'output'        => 'json',
//        );
//
//        // パラメータの順序を昇順に並び替え
//        ksort($params);
//
//        // canonical string を作成します
//        $canonical_string = '';
//        foreach ($params as $ky => $vl) {
//            $canonical_string .= '&'.$ky.'='.$vl;
//        }
//        $canonical_string = substr($canonical_string, 1);
//
//        // URL を作成します
//        $url = $baseurl.'?'.$canonical_string;
//
//        var_dump($url);
//        // url request
//        $data = file_get_contents($url);
//        // $data = @simplexml_load_file($url);
//// CitrusLogger::debug(array('load' => $data));
//// var_dump($data);
//        if (empty($data) === true)
//        {
//            return null;
//        }
//
//        $data = json_decode($data);
//        // $data = $data->result->items;
//// var_dump($data);
//        $results = [];
//        if (isset($data->result->actress) == true)
//        {
//            foreach ($data->result->actress as $one)
//            {
//                $results[] = self::convertActor($one);
//            }
//        }
//
//        return $results;
//    }
//
//    /**
//     * convert
//     *
//     * @access  public
//     * @since   0.1.0.2 2015.01.28
//     * @version 0.1.0.2 2015.01.28
//     * @param   stdClass    $data
//     * @return  CitrusDmmActor
//     */
//    public static function convertActor($data)
//    {
//        $item = new CitrusDmmActor();
//
//        $item->actor_id         = $data->id;
//        $item->name             = $data->name;
//        $item->kana             = $data->ruby;
//        if (isset($data->imageURL) == true)
//        {
//            $item->image_url_small  = $data->imageURL->small;
//            $item->image_url_large  = $data->imageURL->large;
//        }
//        else
//        {
//            $item->image_url_small  = '';
//            $item->image_url_large  = '';
//        }
//
//// var_dump($item);
//
//        return $item;
//    }
}