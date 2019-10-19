<?php
/**
 * @copyright   Copyright 2017, CitrusFramework. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus;

use Citrus\Database\Column;
use Citrus\Database\Result;
use Citrus\Sqlmap\Client;
use Citrus\Sqlmap\Crud;
use Citrus\Sqlmap\SqlmapException;

class Service
{
    use Singleton
    {
        callSingleton as public sharedService;
    }

    /** @var Crud citrus object */
    protected $dao = null;



    /**
     * call summary record list
     *
     * @param Column $condition
     * @return Column[]
     * @throws SqlmapException
     */
    public function summaries(Column $condition)
    {
        return $this->callDao()->summaries($condition);
    }



    /**
     * call summary record
     *
     * @param Column $condition
     * @return Column
     * @throws SqlmapException
     */
    public function summary(Column $condition)
    {
        return $this->callDao()->summary($condition);
    }



    /**
     * call count
     *
     * @param Column $condition
     * @return int
     * @throws SqlmapException
     */
    public function count(Column $condition)
    {
        return $this->callDao()->count($condition);
    }



    /**
     * call last record
     *
     * @param Column $condition
     * @return Column
     * @throws SqlmapException
     */
    public function last(Column $condition)
    {
        return $this->callDao()->last($condition);
    }



    /**
     * call last record
     *
     * @param Column $condition
     * @return bool
     * @throws SqlmapException
     */
    public function exist(Column $condition)
    {
        return $this->callDao()->exist($condition);
    }



    /**
     * call name record
     *
     * @param Column $condition
     * @return Result[]
     * @throws SqlmapException
     */
    public function names(Column $condition)
    {
        return $this->callDao()->names($condition);
    }



    /**
     * call name list
     *
     * @param Column $condition
     * @return array
     * @throws SqlmapException
     */
    public function nameForList(Column $condition)
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
     * @param Column $condition
     * @return Column
     * @throws SqlmapException
     */
    public function name(Column $condition)
    {
        return $this->callDao()->name($condition);
    }



    /**
     * call detail record
     *
     * @param Column $condition
     * @return Column[]
     * @throws SqlmapException
     */
    public function details(Column $condition)
    {
        return $this->callDao()->details($condition);
    }



    /**
     * call detail record
     *
     * @param Column $condition
     * @return Column
     * @throws SqlmapException
     */
    public function detail(Column $condition)
    {
        return $this->callDao()->detail($condition);
    }



    /**
     * regist record
     *
     * @param Column $entity
     * @return bool
     * @throws SqlmapException
     */
    public function regist(Column $entity)
    {
        // column complete
        $entity->completeRegistColumn();

        return $this->callDao()->regist($entity);
    }


    
    /**
     * modify record
     *
     * @param Column $entity
     * @return bool
     * @throws SqlmapException
     */
    public function modify(Column $entity)
    {
        // column complete
        $entity->completeModifyColumn();

        return $this->callDao()->modify($entity);
    }



    /**
     * remove record
     *
     * @param Column $condition
     * @return bool
     * @throws SqlmapException
     */
    public function remove(Column $condition)
    {
        return $this->callDao()->remove($condition);
    }



    /**
     * call summary record list
     *
     * @param Column $condition
     * @return Column[]
     * @throws SqlmapException
     */
    public function facesSelections(Column $condition)
    {
        return $this->callDao()->facesSelections($condition);
    }



    /**
     * call summary record list
     *
     * @param Column $condition
     * @return Column[]
     * @throws SqlmapException
     */
    public function facesSummaries(Column $condition)
    {
        return $this->callDao()->facesSummaries($condition);
    }



    /**
     * call summary record
     *
     * @param Column $condition
     * @return Column
     * @throws SqlmapException
     */
    public function facesSummary(Column $condition)
    {
        return $this->callDao()->facesSummary($condition);
    }



    /**
     * call detail record
     *
     * @param Column $condition
     * @return Column[]
     * @throws SqlmapException
     */
    public function facesDetails(Column $condition)
    {
        return $this->callDao()->facesDetails($condition);
    }



    /**
     * call detail record
     *
     * @param Column $condition
     * @return Column
     * @throws SqlmapException
     */
    public function facesDetail(Column $condition)
    {
        return $this->callDao()->facesDetail($condition);
    }



    /**
     * call name record
     *
     * @param Column $condition
     * @return array
     * @throws SqlmapException
     */
    public function nameSummaries(Column $condition)
    {
        return $this->callDao()->nameSummaries($condition);
    }



    /**
     * call name record
     *
     * @param Column $condition
     * @return Column
     * @throws SqlmapException
     */
    public function nameSummary(Column $condition)
    {
        return $this->callDao()->nameSummary($condition);
    }



    /**
     * call name record count
     *
     * @param Column $condition
     * @return int
     * @throws SqlmapException
     */
    public function nameCount(Column $condition)
    {
        return $this->callDao()->nameCount($condition);
    }



    /**
     * call dao
     * なるべく継承しabstractとして扱う、エラー回避としてCitruSqlmapClientを返す
     *
     * @return Crud
     * @throws SqlmapException
     */
    public function callDao()
    {
        if(is_null($this->dao) === true)
        {
            $this->dao = new Client();
        }
        return $this->dao;
    }
}