<?php
/**
 * Crud.class.php.
 *
 *
 * PHP version 7
 *
 * @copyright   Copyright 2017, Citrus/besidesplus All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @package     Citrus
 * @subpackage  Sqlmap
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Sqlmap;

use Citrus\CitrusLogger;
use Citrus\CitrusMessage;
use Citrus\Database\CitrusDatabaseColumn;
use Citrus\Database\CitrusDatabaseResult;

class CitrusSqlmapCrud extends CitrusSqlmapClient
{
    /** @var CitrusSqlmapCrud  */
    private static $INSTANCE = null;

    /** @var string target name */
    protected $target = '';


    /**
     * call sqlmap summaries
     *
     * @param CitrusDatabaseColumn $condition
     * @return CitrusDatabaseColumn[]
     * @throws CitrusSqlmapException
     */
    public function summaries(CitrusDatabaseColumn $condition) : array
    {
        try
        {
            return $this->queryForList('summary', $condition);
        }
        catch (CitrusSqlmapException $e)
        {
            throw $e;
        }
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
        try
        {
            return $this->queryForObject('summary', $condition);
        }
        catch (CitrusSqlmapException $e)
        {
            throw $e;
        }
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
        try
        {
            /** @var CitrusDatabaseResult $record */
            $record = $this->queryForObject('count', $condition);
            return $record->count;
        }
        catch (CitrusSqlmapException $e)
        {
            throw $e;
        }
    }



    /**
     * 最終レコードを返す
     *
     * @param CitrusDatabaseColumn $condition
     * @return CitrusDatabaseColumn
     * @throws CitrusSqlmapException
     */
    public function last(CitrusDatabaseColumn $condition) : CitrusDatabaseColumn
    {
        try
        {
            /** @var CitrusSqlmapCondition $condition */
            $condition->limit   = 1;
            $condition->offset  = 0;
            $condition->orderby = 'modified_at DESC';
            return $this->queryForObject('detail', $condition);;
        }
        catch (CitrusSqlmapException $e)
        {
            throw $e;
        }
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
        try
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
        catch (CitrusSqlmapException $e)
        {
            throw $e;
        }
    }


    
    /**
     * call sqlmap names
     *
     * @param CitrusDatabaseColumn $condition
     * @return CitrusDatabaseColumn[]
     * @throws CitrusSqlmapException
     */
    public function names(CitrusDatabaseColumn $condition) : array
    {
        try
        {
            return $this->queryForList('name', $condition);
        }
        catch (CitrusSqlmapException $e)
        {
            throw $e;
        }
    }



    /**
     * call sqlmap name
     *
     * @param CitrusDatabaseColumn $condition
     * @return CitrusDatabaseColumn
     * @throws CitrusSqlmapException
     */
    public function name(CitrusDatabaseColumn $condition) : CitrusDatabaseColumn
    {
        try
        {
            return $this->queryForObject('name', $condition);
        }
        catch (CitrusSqlmapException $e)
        {
            throw $e;
        }
    }



    /**
     * call sqlmap details
     *
     * @param CitrusDatabaseColumn $condition
     * @return CitrusDatabaseColumn[]
     * @throws CitrusSqlmapException
     */
    public function details(CitrusDatabaseColumn $condition) : array
    {
        try
        {
            return $this->queryForList('detail', $condition);
        }
        catch (CitrusSqlmapException $e)
        {
            throw $e;
        }
    }



    /**
     * detail
     *
     * @param CitrusDatabaseColumn $condition
     * @return CitrusDatabaseColumn
     * @throws CitrusSqlmapException
     */
    public function detail(CitrusDatabaseColumn $condition) : CitrusDatabaseColumn
    {
        try
        {
            return $this->queryForObject('detail', $condition);
        }
        catch (CitrusSqlmapException $e)
        {
            throw $e;
        }
    }



    /**
     * call sqlmap selections
     *
     * @param CitrusDatabaseColumn $condition
     * @return CitrusDatabaseColumn[]
     * @throws CitrusSqlmapException
     */
    public function selections(CitrusDatabaseColumn $condition) : array
    {
        try
        {
            return $this->queryForList('selection', $condition);
        }
        catch (CitrusSqlmapException $e)
        {
            throw $e;
        }
    }



    /**
     * call sqlmap selection
     *
     * @param CitrusDatabaseColumn $condition
     * @return CitrusDatabaseColumn
     * @throws CitrusSqlmapException
     */
    public function selection(CitrusDatabaseColumn $condition) : CitrusDatabaseColumn
    {
        try
        {
            return $this->queryForObject('selection', $condition);
        }
        catch (CitrusSqlmapException $e)
        {
            throw $e;
        }
    }



    /**
     * call sqlmap faces summaries
     *
     * @param CitrusDatabaseColumn $condition
     * @return CitrusDatabaseColumn[]
     * @throws CitrusSqlmapException
     */
    public function facesSummaries(CitrusDatabaseColumn $condition) : array
    {
        try
        {
            return $this->queryForList('facesSummary', $condition);
        }
        catch (CitrusSqlmapException $e)
        {
            throw $e;
        }
    }



    /**
     * call sqlmap faces summary
     *
     * @param CitrusDatabaseColumn $condition
     * @return CitrusDatabaseColumn
     * @throws CitrusSqlmapException
     */
    public function facesSummary(CitrusDatabaseColumn $condition) : CitrusDatabaseColumn
    {
        try
        {
            return $this->queryForObject('facesSummary', $condition);
        }
        catch (CitrusSqlmapException $e)
        {
            throw $e;
        }
    }



    /**
     * call sqlmap faces details
     *
     * @param CitrusDatabaseColumn $condition
     * @return CitrusDatabaseColumn[]
     * @throws CitrusSqlmapException
     */
    public function facesDetails(CitrusDatabaseColumn $condition) : array
    {
        try
        {
            return $this->queryForList('facesDetail', $condition);
        }
        catch (CitrusSqlmapException $e)
        {
            throw $e;
        }
    }



    /**
     * call sqlmap faces detail
     *
     * @param CitrusDatabaseColumn $condition
     * @return CitrusDatabaseColumn
     * @throws CitrusSqlmapException
     */
    public function facesDetail(CitrusDatabaseColumn $condition) : CitrusDatabaseColumn
    {
        try
        {
            return $this->queryForObject('facesDetail', $condition);
        }
        catch (CitrusSqlmapException $e)
        {
            throw $e;
        }
    }



    /**
     * call sqlmap name summaries
     *
     * @param CitrusDatabaseColumn $condition
     * @return array
     * @throws CitrusSqlmapException
     */
    public function nameSummaries(CitrusDatabaseColumn $condition) : array
    {
        try
        {
            return $this->queryForList('nameSummary', $condition);
        }
        catch (CitrusSqlmapException $e)
        {
            throw $e;
        }
    }



    /**
     * call sqlmap name summary
     *
     * @param CitrusDatabaseColumn $condition
     * @return CitrusDatabaseColumn
     * @throws CitrusSqlmapException
     */
    public function nameSummary(CitrusDatabaseColumn $condition) : CitrusDatabaseColumn
    {
        try
        {
            return $this->queryForObject('nameSummary', $condition);
        }
        catch (CitrusSqlmapException $e)
        {
            throw $e;
        }
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
        try
        {
            /** @var CitrusDatabaseResult $record */
            $record = $this->queryForObject('nameCount', $condition);
            return $record->count;
        }
        catch (CitrusSqlmapException $e)
        {
            throw $e;
        }
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
//                // exist check
//                if ($this->exist($entity->getCondition()) === false)
//                {
//                    throw new CitrusSqlmapException(sprintf('編集対象の%sは登録されていないか、他ユーザに更新されています。', $message));
//                }

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
     * @access  protected
     * @since   0.0.1.2 2012.02.06
     * @version 0.0.1.2 2012.02.06
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
     * @access  protected
     * @since   0.0.1.2 2012.02.06
     * @version 0.0.1.2 2012.02.06
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



    /**
     * call shared instance
     *
     * @return CitrusSqlmapCrud
     */
    public static function sharedDao() : CitrusSqlmapCrud
    {
        if (is_null(self::$INSTANCE) === true)
        {
            self::$INSTANCE = new static();
        }
        return self::$INSTANCE;
    }
}