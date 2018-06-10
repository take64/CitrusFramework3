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
    use CitrusSqlmapValidation;

    /** id summary */
    const ID_SUMMARY = 'summary';

    /** id detail */
    const ID_DETAIL = 'detail';

    /** id name */
    const ID_NAME = 'name';

    /** id name summary */
    const ID_NAME_SUMMARY = 'nameSummary';

    /** id name count */
    const ID_NAME_COUNT = 'nameCount';

    /** id count */
    const ID_COUNT = 'count';

    /** id regist */
    const ID_REGIST = 'regist';

    /** id modify */
    const ID_MODIFY = 'modify';

    /** id remove */
    const ID_REMOVE = 'remove';

    /** id faces summary */
    const ID_FACES_SUMMARY = 'facesSummary';

    /** id faces detail */
    const ID_FACES_DETAIL = 'facesDetail';

    /** id faces selection */
    const ID_FACES_SELECTION = 'facesSelection';



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
        return $this->queryForList(self::ID_SUMMARY, $condition);
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
        return $this->queryForObject(self::ID_SUMMARY, $condition);
    }



    /**
     * call count
     *
     * @param CitrusDatabaseColumn $condition
     * @return int
     * @throws CitrusSqlmapException
     */
    public function count(CitrusDatabaseColumn $condition): int
    {
        /** @var CitrusDatabaseResult $record */
        $record = $this->queryForObject(self::ID_COUNT, $condition);
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
        return $this->queryForObject(self::ID_DETAIL, $condition);
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
    public function exist(CitrusDatabaseColumn $condition, CitrusDatabaseColumn $source = null): bool
    {
        // 主にregist時のcheckに使われる
        if (is_null($source) === true)
        {
            $record = $this->queryForObject(self::ID_DETAIL, $condition);
            $result = (empty($record) === false);
            unset($record);
            return $result;
        }
        // 主にmodify時のcheckに使われる
        else
        {
            $record = $this->queryForObject(self::ID_DETAIL, $condition);
            $compare= $this->queryForObject(self::ID_DETAIL, $source);

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
        return $this->queryForList(self::ID_NAME, $condition);
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
        return $this->queryForObject(self::ID_NAME, $condition);
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
        return $this->queryForList(self::ID_DETAIL, $condition);
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
        return $this->queryForObject(self::ID_DETAIL, $condition);
    }



    /**
     * call sqlmap selections
     *
     * @param CitrusDatabaseColumn $condition
     * @return CitrusDatabaseColumn[]
     * @throws CitrusSqlmapException
     */
    public function facesSelections(CitrusDatabaseColumn $condition)
    {
        return $this->queryForList(self::ID_FACES_SELECTION, $condition);
    }



    /**
     * call sqlmap selection
     *
     * @param CitrusDatabaseColumn $condition
     * @return CitrusDatabaseColumn
     * @throws CitrusSqlmapException
     */
    public function facesSelection(CitrusDatabaseColumn $condition)
    {
        return $this->queryForObject(self::ID_FACES_SELECTION, $condition);
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
        return $this->queryForList(self::ID_FACES_SUMMARY, $condition);
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
        return $this->queryForObject(self::ID_FACES_SUMMARY, $condition);
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
        return $this->queryForList(self::ID_FACES_DETAIL, $condition);
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
        return $this->queryForObject(self::ID_FACES_DETAIL, $condition);
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
        return $this->queryForList(self::ID_NAME_SUMMARY, $condition);
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
        return $this->queryForObject(self::ID_NAME_SUMMARY, $condition);
    }



    /**
     * call sqlmap name count
     *
     * @param CitrusDatabaseColumn $condition
     * @return int
     * @throws CitrusSqlmapException
     */
    public function nameCount(CitrusDatabaseColumn $condition): int
    {
        /** @var CitrusDatabaseResult $record */
        $record = $this->queryForObject(self::ID_NAME_COUNT, $condition);
        return $record->count;
    }



    /**
     * regist
     *
     * @param CitrusDatabaseColumn $entity
     * @return bool
     * @throws CitrusSqlmapException
     */
    public function regist(CitrusDatabaseColumn $entity): bool
    {
        try
        {
            // validate for duplicate check
            if ($this->isValidate() === true
            && $entity->toCondition() != $entity->getCondition() && $this->exist($entity->toCondition()) === true)
            {
                throw new CitrusSqlmapException('対象は既に登録されています。');
            }

            // transaction
            $this->insert(self::ID_REGIST, $entity);

            // message
            if ($this->isMessage() === true)
            {
                CitrusMessage::addMessage('正常に登録されました。');
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

            throw new CitrusSqlmapException('登録に失敗しました。');
        }
    }



    /**
     * modify
     *
     * @param CitrusDatabaseColumn $entity
     * @return bool
     * @throws CitrusSqlmapException
     */
    public function modify(CitrusDatabaseColumn $entity): bool
    {
        // 全変更の危険を回避
        if (false === $this->validateEssentialModify($entity))
        {
            return false;
        }

        try
        {
            // validate for duplicate check
            if ($this->isValidate() === true
            && $this->exist($entity->getCondition(), $entity->toCondition()) === true)
            {
                throw new CitrusSqlmapException('既に登録されています。');
            }

            $this->update(self::ID_MODIFY, $entity);

            // message
            if ($this->isMessage() === true)
            {
                CitrusMessage::addMessage('正常に編集されました。');
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

            throw new CitrusSqlmapException('編集に失敗しました。');
        }
    }



    /**
     * remove
     *
     * @param CitrusDatabaseColumn $condition
     * @return bool
     * @throws CitrusSqlmapException
     */
    public function remove(CitrusDatabaseColumn $condition): bool
    {
        // 全削除の危険を回避
        if (count(get_object_vars($condition)) == 0)
        {
            // message
            if ($this->isMessage() === true)
            {
                CitrusMessage::addWarning('削除の条件が足りません。');
            }
            return false;
        }

        try
        {
            // validate
            if ($this->isValidate() === true && $this->exist($condition) === false)
            {
                throw new CitrusSqlmapException('削除対象は登録されていないか、他ユーザに更新されています。');
            }

            $this->delete(self::ID_REMOVE, $condition);

            // message
            if ($this->isMessage() === true)
            {
                CitrusMessage::addMessage('正常に削除されました。');
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

            throw new CitrusSqlmapException('削除に失敗しました。');
        }
    }



    /**
     * call target
     *
     * @return string
     */
    public function callTarget(): string
    {
        return $this->target;
    }
}