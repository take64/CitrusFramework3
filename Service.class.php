<?php
/**
 * Service.class.php.
 * 2017/08/25
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


use Citrus\Database\CitrusDatabaseColumn;
use Citrus\Sqlmap\CitrusSqlmapClient;
use Citrus\Sqlmap\CitrusSqlmapCrud;

class CitrusService
{
    use CitrusSingleton
    {
        callSingleton as public sharedService;
    }

    /** @var CitrusSqlmapCrud citrus object */
    protected $dao = null;



    /**
     * call summary record list
     *
     * @param CitrusDatabaseColumn $condition
     * @return CitrusDatabaseColumn[]
     */
    public function summaries(CitrusDatabaseColumn $condition)
    {
        return $this->callDao()->summaries($condition);
    }



    /**
     * call summary record
     *
     * @param CitrusDatabaseColumn $condition
     * @return CitrusDatabaseColumn
     */
    public function summary(CitrusDatabaseColumn $condition)
    {
        return $this->callDao()->summary($condition);
    }



    /**
     * call count
     *
     * @param CitrusDatabaseColumn $condition
     * @return int
     */
    public function count(CitrusDatabaseColumn $condition)
    {
        return $this->callDao()->count($condition);
    }



    /**
     * call last record
     *
     * @param CitrusDatabaseColumn $condition
     * @return CitrusDatabaseColumn
     */
    public function last(CitrusDatabaseColumn $condition)
    {
        return $this->callDao()->last($condition);
    }



    /**
     * call last record
     *
     * @param CitrusDatabaseColumn $condition
     * @return bool
     */
    public function exist(CitrusDatabaseColumn $condition)
    {
        return $this->callDao()->exist($condition);
    }



    /**
     * call name record
     *
     * @param CitrusDatabaseColumn $condition
     * @return CitrusDatabaseColumn[]
     */
    public function names(CitrusDatabaseColumn $condition)
    {
        return $this->callDao()->names($condition);
    }



    /**
     * call detail record
     *
     * @param CitrusDatabaseColumn $condition
     * @return CitrusDatabaseColumn
     */
    public function name(CitrusDatabaseColumn $condition)
    {
        return $this->callDao()->name($condition);
    }



    /**
     * call detail record
     *
     * @param CitrusDatabaseColumn $condition
     * @return CitrusDatabaseColumn[]
     */
    public function details(CitrusDatabaseColumn $condition)
    {
        return $this->callDao()->details($condition);
    }

    /**
     * call detail record
     *
     * @access  public
     * @since   0.0.6.1 2012.03.20
     * @version 0.0.6.1 2012.03.20
     * @param   CitrusDatabaseColumn   $condition
     * @return  CitrusDatabaseColumn
     */
    public function detail(CitrusDatabaseColumn $condition)
    {
        return $this->callDao()->detail($condition);
    }



    /**
     * regist record
     *
     * @param CitrusDatabaseColumn $entity
     * @return bool
     */
    public function regist(CitrusDatabaseColumn $entity)
    {
        // column complete
        $entity->completeRegistColumn();

        return $this->callDao()->regist($entity);
    }


    
    /**
     * modify record
     *
     * @param CitrusDatabaseColumn $entity
     * @return bool
     */
    public function modify(CitrusDatabaseColumn $entity)
    {
        // column complete
        $entity->completeModifyColumn();

        return $this->callDao()->modify($entity);
    }



    /**
     * remove record
     *
     * @param CitrusDatabaseColumn $condition
     * @return bool
     */
    public function remove(CitrusDatabaseColumn $condition)
    {
        return $this->callDao()->remove($condition);
    }



    /**
     * call summary record list
     *
     * @param CitrusDatabaseColumn $condition
     * @return CitrusDatabaseColumn[]
     */
    public function selections(CitrusDatabaseColumn $condition)
    {
        return $this->callDao()->selections($condition);
    }



    /**
     * call summary record list
     *
     * @param CitrusDatabaseColumn $condition
     * @return CitrusDatabaseColumn[]
     */
    public function facesSummaries(CitrusDatabaseColumn $condition)
    {
        return $this->callDao()->facesSummaries($condition);
    }



    /**
     * call summary record
     *
     * @param CitrusDatabaseColumn $condition
     * @return CitrusDatabaseColumn
     */
    public function facesSummary(CitrusDatabaseColumn $condition)
    {
        return $this->callDao()->facesSummary($condition);
    }



    /**
     * call detail record
     *
     * @param CitrusDatabaseColumn $condition
     * @return CitrusDatabaseColumn[]
     */
    public function facesDetails(CitrusDatabaseColumn $condition)
    {
        return $this->callDao()->facesDetails($condition);
    }



    /**
     * call detail record
     *
     * @param CitrusDatabaseColumn $condition
     * @return CitrusDatabaseColumn
     */
    public function facesDetail(CitrusDatabaseColumn $condition)
    {
        return $this->callDao()->facesDetail($condition);
    }



    /**
     * call name record
     *
     * @param CitrusDatabaseColumn $condition
     * @return array
     */
    public function nameSummaries(CitrusDatabaseColumn $condition)
    {
        return $this->callDao()->nameSummaries($condition);
    }



    /**
     * call name record
     *
     * @param CitrusDatabaseColumn $condition
     * @return CitrusDatabaseColumn
     */
    public function nameSummary(CitrusDatabaseColumn $condition)
    {
        return $this->callDao()->nameSummary($condition);
    }



    /**
     * call name record count
     *
     * @param CitrusDatabaseColumn $condition
     * @return int
     */
    public function nameCount(CitrusDatabaseColumn $condition)
    {
        return $this->callDao()->nameCount($condition);
    }



    /**
     * call dao
     * なるべく継承しabstractとして扱う、エラー回避としてCitruSqlmapClientを返す
     *
     * @return  CitrusSqlmapCrud
     */
    public function callDao()
    {
        if(is_null($this->dao) === true)
        {
            $this->dao = new CitrusSqlmapClient();
        }
        return $this->dao;
    }
}