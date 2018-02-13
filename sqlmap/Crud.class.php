<?php
/**
 * @copyright   Copyright 2017, Citrus/besidesplus All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Sqlmap;

use Citrus\CitrusLogger;
use Citrus\CitrusMessage;
use Citrus\CitrusSingleton;
use Citrus\Database\CitrusDatabaseColumn;
use Citrus\Database\CitrusDatabaseResult;

class CitrusSqlmapCrud extends CitrusSqlmapClient
{
    use CitrusSingleton
    {
        callSingleton as public sharedDao;
    }

    /** @var string target name */
    protected $target = '';



    /**
     * call sqlmap summaries
     *
     * @param CitrusDatabaseColumn $condition
     * @return CitrusDatabaseColumn[]
     * @throws CitrusSqlmapException
     */
    public function summaries(CitrusDatabaseColumn $condition)
    {
        return $this->queryForList('summary', $condition);
    }


    
    /**
     * call sqlmap summary
     *
     * @param CitrusDatabaseColumn $condition
     * @return CitrusDatabaseColumn|null
     * @throws CitrusSqlmapException
     */
    public function summary(CitrusDatabaseColumn $condition)
    {
        return $this->queryForObject('summary', $condition);
    }



    /**
     * call count
     *
     * @param CitrusDatabaseColumn $condition
     * @return int
     * @throws CitrusSqlmapException
     */
    public function count(CitrusDatabaseColumn $condition) : int
    {
        /** @var CitrusDatabaseResult $record */
        $record = $this->queryForObject('count', $condition);
        return $record->count;
    }



    /**
     * 最終レコードを返す
     *
     * @param CitrusDatabaseColumn $condition
     * @return CitrusDatabaseColumn
     * @throws CitrusSqlmapException
     */
    public function last(CitrusDatabaseColumn $condition)
    {
        /** @var CitrusSqlmapCondition $condition */
        $condition->limit   = 1;
        $condition->offset  = 0;
        $condition->orderby = 'modified_at DESC';
        return $this->queryForObject('detail', $condition);
    }



    /**
     * record exist
     *
     * @param CitrusDatabaseColumn $condition
     * @param CitrusDatabaseColumn|null $source
     * @return bool
     * @throws CitrusSqlmapException
     * @deprecated insert 時は insert ignore を使うようにしたい
     */
    public function exist(CitrusDatabaseColumn $condition, CitrusDatabaseColumn $source = null) : bool
    {
        // 主にregist時のcheckに使われる
        if (is_null($source) === true)
        {
            $record = $this->queryForObject('detail', $condition);
            $result = (empty($record) === false);
            unset($record);
            return $result;
        }
        // 主にmodify時のcheckに使われる
        else
        {
            $record = $this->queryForObject('detail', $condition);
            $compare= $this->queryForObject('detail', $source);

            if (empty($record) === false
                && empty($compare) === false
                && $record->rowid != $compare->rowid)
            {
                unset($record);
                unset($compare);
                return true;
            }
            else
            {
                unset($record);
                unset($compare);
                return false;
            }
        }
    }


    
    /**
     * call sqlmap names
     *
     * @param CitrusDatabaseColumn $condition
     * @return CitrusDatabaseResult[]
     * @throws CitrusSqlmapException
     */
    public function names(CitrusDatabaseColumn $condition)
    {
        return $this->queryForList('name', $condition);
    }



    /**
     * call sqlmap name
     *
     * @param CitrusDatabaseColumn $condition
     * @return CitrusDatabaseColumn
     * @throws CitrusSqlmapException
     */
    public function name(CitrusDatabaseColumn $condition)
    {
        return $this->queryForObject('name', $condition);
    }



    /**
     * call sqlmap details
     *
     * @param CitrusDatabaseColumn $condition
     * @return CitrusDatabaseColumn[]
     * @throws CitrusSqlmapException
     */
    public function details(CitrusDatabaseColumn $condition)
    {
        return $this->queryForList('detail', $condition);
    }



    /**
     * detail
     *
     * @param CitrusDatabaseColumn $condition
     * @return CitrusDatabaseColumn
     * @throws CitrusSqlmapException
     */
    public function detail(CitrusDatabaseColumn $condition)
    {
        return $this->queryForObject('detail', $condition);
    }



    /**
     * call sqlmap selections
     *
     * @param CitrusDatabaseColumn $condition
     * @return CitrusDatabaseColumn[]
     * @throws CitrusSqlmapException
     */
    public function selections(CitrusDatabaseColumn $condition)
    {
        return $this->queryForList('selection', $condition);
    }



    /**
     * call sqlmap selection
     *
     * @param CitrusDatabaseColumn $condition
     * @return CitrusDatabaseColumn
     * @throws CitrusSqlmapException
     */
    public function selection(CitrusDatabaseColumn $condition)
    {
        return $this->queryForObject('selection', $condition);
    }



    /**
     * call sqlmap faces summaries
     *
     * @param CitrusDatabaseColumn $condition
     * @return CitrusDatabaseColumn[]
     * @throws CitrusSqlmapException
     */
    public function facesSummaries(CitrusDatabaseColumn $condition)
    {
        return $this->queryForList('facesSummary', $condition);
    }



    /**
     * call sqlmap faces summary
     *
     * @param CitrusDatabaseColumn $condition
     * @return CitrusDatabaseColumn
     * @throws CitrusSqlmapException
     */
    public function facesSummary(CitrusDatabaseColumn $condition)
    {
        return $this->queryForObject('facesSummary', $condition);
    }



    /**
     * call sqlmap faces details
     *
     * @param CitrusDatabaseColumn $condition
     * @return CitrusDatabaseColumn[]
     * @throws CitrusSqlmapException
     */
    public function facesDetails(CitrusDatabaseColumn $condition)
    {
        return $this->queryForList('facesDetail', $condition);
    }



    /**
     * call sqlmap faces detail
     *
     * @param CitrusDatabaseColumn $condition
     * @return CitrusDatabaseColumn
     * @throws CitrusSqlmapException
     */
    public function facesDetail(CitrusDatabaseColumn $condition)
    {
        return $this->queryForObject('facesDetail', $condition);
    }



    /**
     * call sqlmap name summaries
     *
     * @param CitrusDatabaseColumn $condition
     * @return array
     * @throws CitrusSqlmapException
     */
    public function nameSummaries(CitrusDatabaseColumn $condition)
    {
        return $this->queryForList('nameSummary', $condition);
    }



    /**
     * call sqlmap name summary
     *
     * @param CitrusDatabaseColumn $condition
     * @return CitrusDatabaseColumn
     * @throws CitrusSqlmapException
     */
    public function nameSummary(CitrusDatabaseColumn $condition)
    {
        return $this->queryForObject('nameSummary', $condition);
    }



    /**
     * call sqlmap name count
     *
     * @param CitrusDatabaseColumn $condition
     * @return int
     * @throws CitrusSqlmapException
     */
    public function nameCount(CitrusDatabaseColumn $condition) : int
    {
        /** @var CitrusDatabaseResult $record */
        $record = $this->queryForObject('nameCount', $condition);
        return $record->count;
    }



    /**
     * regist
     *
     * @param CitrusDatabaseColumn $entity
     * @return bool
     * @throws CitrusSqlmapException
     */
    public function regist(CitrusDatabaseColumn $entity) : bool
    {
        // name
        $name = trim($this->getName($entity));

        $message = '';
        if (empty($name) === false)
        {
            $message = '[ '.$name.' ]';
        }

        try
        {
            // validate
            if ($this->isValidate() === true)
            {
                // duplicate check
                if ($entity->toCondition() != $entity->getCondition())
                {
                    if ($this->exist($entity->toCondition()) === true)
                    {
                        throw new CitrusSqlmapException(sprintf('対象の%sは既に登録されています。', $message));
                    }
                }
            }

            // transaction
            $this->insert('regist', $entity);

            // message
            if ($this->isMessage() === true)
            {
                CitrusMessage::addMessage(sprintf('%sが登録されました。', $message));
            }

            return true;
        }
        catch (CitrusSqlmapException $e)
        {
            // log
            CitrusLogger::error($e->getMessage());

            //message
            if ($this->isMessage() === true)
            {
                CitrusMessage::addError($e->getMessage());
            }

            throw new CitrusSqlmapException(sprintf('%sの登録に失敗しました。', $message));
        }
    }



    /**
     * modify
     *
     * @param CitrusDatabaseColumn $entity
     * @return bool
     * @throws CitrusSqlmapException
     */
    public function modify(CitrusDatabaseColumn $entity) : bool
    {

        // 全変更の危険を回避
        if (count(get_object_vars($entity->getCondition())) == 0)
        {
            // message
            if ($this->isMessage() === true)
            {
                CitrusMessage::addWarning($this->callTarget().'変更の条件が足りません。');
            }
            return false;
        }

        // name
        $name = trim($this->getName($entity));
        if (empty($name) === true)
        {
            $name = $this->callName($entity->getCondition());
        }

        $message = '';
        if (empty($name) === false)
        {
            $message = '[ '.$name.' ]';
        }

        try
        {
            // validate
            if ($this->isValidate() === true)
            {
                // duplicate check
                if ($this->exist($entity->getCondition(), $entity->toCondition()) === true)
                {
                    throw new CitrusSqlmapException(sprintf('%sは既に登録されています。', $message));
                }
            }

            $this->update('modify', $entity);

            // message
            if ($this->isMessage() === true)
            {
                CitrusMessage::addMessage(sprintf('%sが編集されました。', $message));
            }

            return true;
        }
        catch (CitrusSqlmapException $e)
        {
            // log
            CitrusLogger::error($e->getMessage());

            // message
            if ($this->isMessage() === true)
            {
                CitrusMessage::addError($e->getMessage());
            }

            throw new CitrusSqlmapException(sprintf('%sの編集に失敗しました。', $message));
        }
    }



    /**
     * remove
     *
     * @param CitrusDatabaseColumn $condition
     * @return bool
     * @throws CitrusSqlmapException
     */
    public function remove(CitrusDatabaseColumn $condition) : bool
    {
        // 全削除の危険を回避
        if (count(get_object_vars($condition)) == 0)
        {
            // message
            if ($this->isMessage() === true)
            {
                CitrusMessage::addWarning($this->callTarget().'削除の条件が足りません。');
            }
            return false;
        }

        // name
        $name = trim($this->callName($condition));

        $message = '';
        if (empty($name) === false)
        {
            $message = '[ '.$name.' ]';
        }

        try
        {
            // validate
            if ($this->isValidate() === true)
            {
                if ($this->exist($condition) === false)
                {
                    throw new CitrusSqlmapException(sprintf('削除対象の%sは登録されていないか、他ユーザに更新されています。', $message));
                }
            }

            $this->delete('remove', $condition);

            // message
            if ($this->isMessage() === true)
            {
                CitrusMessage::addMessage(sprintf('%sが削除されました。', $message));
            }

            return true;
        }
        catch (CitrusSqlmapException $e)
        {
            // message
            if ($this->isMessage() === true)
            {
                CitrusMessage::addError($e->getMessage());
            }

            throw new CitrusSqlmapException(sprintf('%sの削除に失敗しました。', $message));
        }
    }



    /**
     * call name
     *
     * @param   CitrusDatabaseColumn   $condition
     * @return  boolean
     * @deprecated
     */
    protected function callName($condition)
    {
        return '';
    }



    /**
     * get name
     *
     * @param   CitrusDatabaseColumn   $entity
     * @return  boolean
     * @deprecated
     */
    protected function getName($entity)
    {
        return '';
    }



    /**
     * call target
     *
     * @return string
     */
    protected function callTarget() : string
    {
        return $this->target;
    }
}