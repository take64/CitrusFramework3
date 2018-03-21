<?php
/**
 * @copyright   Copyright 2017, Citrus/besidesplus All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus;


use Citrus\Database\CitrusDatabaseColumn;
use Citrus\Database\CitrusDatabaseResult;
use Citrus\Sqlmap\CitrusSqlmapClient;
use Citrus\Sqlmap\CitrusSqlmapCrud;
use Citrus\Sqlmap\CitrusSqlmapException;

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
     * @throws CitrusSqlmapException
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
     * @throws CitrusSqlmapException
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
     * @throws CitrusSqlmapException
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
     * @throws CitrusSqlmapException
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
     * @throws CitrusSqlmapException
     */
    public function exist(CitrusDatabaseColumn $condition)
    {
        return $this->callDao()->exist($condition);
    }



    /**
     * call name record
     *
     * @param CitrusDatabaseColumn $condition
     * @return CitrusDatabaseResult[]
     * @throws CitrusSqlmapException
     */
    public function names(CitrusDatabaseColumn $condition)
    {
        return $this->callDao()->names($condition);
    }



    /**
     * call name list
     *
     * @param CitrusDatabaseColumn $condition
     * @return array
     * @throws CitrusSqlmapException
     */
    public function nameForList(CitrusDatabaseColumn $condition)
    {
        $result = [];

        $entities = $this->names($condition);
        foreach ($entities as $entity)
        {
            $result[$entity->id] = $entity->name;
        }

        return $result;
    }



    /**
     * call detail record
     *
     * @param CitrusDatabaseColumn $condition
     * @return CitrusDatabaseColumn
     * @throws CitrusSqlmapException
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
     * @throws CitrusSqlmapException
     */
    public function details(CitrusDatabaseColumn $condition)
    {
        return $this->callDao()->details($condition);
    }



    /**
     * call detail record
     *
     * @param CitrusDatabaseColumn $condition
     * @return CitrusDatabaseColumn
     * @throws CitrusSqlmapException
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
     * @throws CitrusSqlmapException
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
     * @throws CitrusSqlmapException
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
     * @throws CitrusSqlmapException
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
     * @throws CitrusSqlmapException
     */
    public function facesSelections(CitrusDatabaseColumn $condition)
    {
        return $this->callDao()->facesSelections($condition);
    }



    /**
     * call summary record list
     *
     * @param CitrusDatabaseColumn $condition
     * @return CitrusDatabaseColumn[]
     * @throws CitrusSqlmapException
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
     * @throws CitrusSqlmapException
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
     * @throws CitrusSqlmapException
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
     * @throws CitrusSqlmapException
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
     * @throws CitrusSqlmapException
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
     * @throws CitrusSqlmapException
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
     * @throws CitrusSqlmapException
     */
    public function nameCount(CitrusDatabaseColumn $condition)
    {
        return $this->callDao()->nameCount($condition);
    }



    /**
     * call dao
     * なるべく継承しabstractとして扱う、エラー回避としてCitruSqlmapClientを返す
     *
     * @return CitrusSqlmapCrud
     * @throws CitrusSqlmapException
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