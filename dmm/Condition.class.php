<?php
/**
 * Condition.class.php.
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


class CitrusDmmCondition
{
    /** @var string site dmm.com */
    const SITE_DMM_COM = 'DMM.com';

    /** @var string site dmm.r18 */
    const SITE_DMM_R18 = 'DMM.R18';

    /** @var string service dmm.com AKB48グループ, 月額動画 */
    const SERVICE_COM_MONTHLY = 'monthly';

    /** @var string service dmm.com 動画 */
    const SERVICE_COM_DIGITAL = 'digital';

    /** @var string service dmm.com 電子書籍 */
    const SERVICE_COM_EBOOK = 'ebook';

    /** @var string service dmm.com PCソフト */
    const SERVICE_COM_PCSOFT = 'pcsoft';

    /** @var string service dmm.com 通販 */
    const SERVICE_COM_MONO = 'mono';

    /** @var string service dmm.com いろいろレンタル */
    const SERVICE_COM_RENTAL = 'rental';

    /** @var string service dmm.com 通販 */
    const SERVICE_COM_NANDEMO = 'nandemo';

    /** @var string service dmm.r18 動画 */
    const SERVICE_R18_DIGITAL = 'digital';

    /** @var string service dmm.r18 月額動画 */
    const SERVICE_R18_MONTHLY = 'monthly';

    /** @var string service dmm.r18 10円動画 */
    const SERVICE_R18_PPM = 'ppm';

    /** @var string service dmm.r18 DVDレンタル */
    const SERVICE_R18_RENTAL = 'rental';

    /** @var string service dmm.r18 通販 */
    const SERVICE_R18_MONO = 'mono';

    /** @var string service dmm.r18 美少女ゲーム */
    const SERVICE_R18_PCGAME = 'pcgame';

    /** @var string service dmm.r18 同人 */
    const SERVICE_R18_DOUJIN = 'doujin';

    /** @var string service dmm.r18 電子書籍 */
    const SERVICE_R18_EBOOK = 'ebook';

    /** @var string sort 人気 */
    const SORT_RANK = 'rank';

    /** @var string sort 価格が高い順 */
    const SORT_PRICE_DESC = 'price';

    /** @var string sort 価格が安い順 */
    const SORT_PRICE_ASC = '-price';

    /** @var string sort 新着 */
    const SORT_DATE = 'date';

    /** @var string sort 評価 */
    const SORT_REVIEW = 'review';




    /** @var string site */
    public $site = self::SITE_DMM_R18;

    /** @var string service */
    public $service = null;

    /** @var string floor 使うのはしんどい */
    public $floor = null;

    /** @var int hits */
    public $hits = 100;

    /** @var int offset */
    public $offset = 1;

    /** @var string keyword */
    public $keyword = null;

    /** @var string sort */
    public $sort = self::SORT_RANK;

    /** @var string 絞り込み TODO: 後で */
    public $article = null;

    /** @var string 絞り込み TODO: 後で */
    public $article_id = null;
}