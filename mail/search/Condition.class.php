<?php
/**
 * @copyright   Copyright 2018, Citrus/besidesplus All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Mail\Search;


use Citrus\CitrusObject;

class CitrusMailSearchCondition extends CitrusObject
{
    /** サーバーフォルダのINBOX */
    const FOLDER_INBOX = 'INBOX';

    /** @var string フォルダ名 */
    public $folder_path = self::FOLDER_INBOX;

    /** @var int[] uid の配列 */
    public $uids = [];

    /** @var int[] msgno の配列  */
    public $msgnos = [];

    /** @var int 検索開始UID */
    public $uid_from = 1;



    /**
     * 条件を生成
     *
     * @param string $folder_path フォルダパス
     * @return CitrusMailSearchCondition
     */
    public static function generateCondition(string $folder_path = self::FOLDER_INBOX) : CitrusMailSearchCondition
    {
        $condition = new static();
        $condition->folder_path = $folder_path;
        return $condition;
    }



    /**
     * uid 配列の条件を生成
     *
     * @param int[]  $uids        uid配列
     * @param string $folder_path フォルダパス
     * @return CitrusMailSearchCondition
     */
    public static function generateUids(array $uids, string $folder_path = self::FOLDER_INBOX) : CitrusMailSearchCondition
    {
        $condition = self::generateCondition($folder_path);
        $condition->uids = $uids;
        return $condition;
    }



    /**
     * msgno 配列の条件を生成
     *
     * @param int[]  $msgnos      msgno配列
     * @param string $folder_path フォルダパス
     * @return CitrusMailSearchCondition
     */
    public static function generateMsgnos(array $msgnos, string $folder_path = self::FOLDER_INBOX) : CitrusMailSearchCondition
    {
        $condition = self::generateCondition($folder_path);
        $condition->msgnos = $msgnos;
        return $condition;
    }
}