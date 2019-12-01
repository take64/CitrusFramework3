<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2017, CitrusFramework. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Sqlmap;

use Citrus\Database\Column;
use Citrus\Database\Result;
use Citrus\Logger;
use Citrus\Message;
use Citrus\Variable\Singleton;

/**
 * sqlmap CRUD
 */
class Crud extends Client
{
    use Singleton;
    use Validation;

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
     * @param Column $condition
     * @return Column[]
     * @throws SqlmapException
     */
    public function summaries(Column $condition)
    {
        return $this->queryForList(self::ID_SUMMARY, $condition);
    }



    /**
     * call sqlmap summary
     *
     * @param Column $condition
     * @return Column|null
     * @throws SqlmapException
     */
    public function summary(Column $condition)
    {
        return $this->queryForObject(self::ID_SUMMARY, $condition);
    }



    /**
     * call count
     *
     * @param Column $condition
     * @return int
     * @throws SqlmapException
     */
    public function count(Column $condition): int
    {
        /** @var Result $record */
        $record = $this->queryForObject(self::ID_COUNT, $condition);
        return $record->count;
    }



    /**
     * 最終レコードを返す
     *
     * @param Column $condition
     * @return Column
     * @throws SqlmapException
     */
    public function last(Column $condition)
    {
        /** @var Condition $condition */
        $condition->limit   = 1;
        $condition->offset  = 0;
        $condition->orderby = 'modified_at DESC';
        return $this->queryForObject(self::ID_DETAIL, $condition);
    }



    /**
     * record exist
     *
     * @param Column $condition
     * @param Column|null $source
     * @return bool
     * @throws SqlmapException
     * @deprecated insert 時は insert ignore を使うようにしたい
     */
    public function exist(Column $condition, Column $source = null): bool
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
     * @param Column $condition
     * @return Result[]
     * @throws SqlmapException
     */
    public function names(Column $condition)
    {
        return $this->queryForList(self::ID_NAME, $condition);
    }



    /**
     * call sqlmap name
     *
     * @param Column $condition
     * @return Column
     * @throws SqlmapException
     */
    public function name(Column $condition)
    {
        return $this->queryForObject(self::ID_NAME, $condition);
    }



    /**
     * call sqlmap details
     *
     * @param Column $condition
     * @return Column[]
     * @throws SqlmapException
     */
    public function details(Column $condition)
    {
        return $this->queryForList(self::ID_DETAIL, $condition);
    }



    /**
     * detail
     *
     * @param Column $condition
     * @return Column
     * @throws SqlmapException
     */
    public function detail(Column $condition)
    {
        return $this->queryForObject(self::ID_DETAIL, $condition);
    }



    /**
     * call sqlmap selections
     *
     * @param Column $condition
     * @return Column[]
     * @throws SqlmapException
     */
    public function facesSelections(Column $condition)
    {
        return $this->queryForList(self::ID_FACES_SELECTION, $condition);
    }



    /**
     * call sqlmap selection
     *
     * @param Column $condition
     * @return Column
     * @throws SqlmapException
     */
    public function facesSelection(Column $condition)
    {
        return $this->queryForObject(self::ID_FACES_SELECTION, $condition);
    }



    /**
     * call sqlmap faces summaries
     *
     * @param Column $condition
     * @return Column[]
     * @throws SqlmapException
     */
    public function facesSummaries(Column $condition)
    {
        return $this->queryForList(self::ID_FACES_SUMMARY, $condition);
    }



    /**
     * call sqlmap faces summary
     *
     * @param Column $condition
     * @return Column
     * @throws SqlmapException
     */
    public function facesSummary(Column $condition)
    {
        return $this->queryForObject(self::ID_FACES_SUMMARY, $condition);
    }



    /**
     * call sqlmap faces details
     *
     * @param Column $condition
     * @return Column[]
     * @throws SqlmapException
     */
    public function facesDetails(Column $condition)
    {
        return $this->queryForList(self::ID_FACES_DETAIL, $condition);
    }



    /**
     * call sqlmap faces detail
     *
     * @param Column $condition
     * @return Column
     * @throws SqlmapException
     */
    public function facesDetail(Column $condition)
    {
        return $this->queryForObject(self::ID_FACES_DETAIL, $condition);
    }



    /**
     * call sqlmap name summaries
     *
     * @param Column $condition
     * @return array
     * @throws SqlmapException
     */
    public function nameSummaries(Column $condition)
    {
        return $this->queryForList(self::ID_NAME_SUMMARY, $condition);
    }



    /**
     * call sqlmap name summary
     *
     * @param Column $condition
     * @return Column
     * @throws SqlmapException
     */
    public function nameSummary(Column $condition)
    {
        return $this->queryForObject(self::ID_NAME_SUMMARY, $condition);
    }



    /**
     * call sqlmap name count
     *
     * @param Column $condition
     * @return int
     * @throws SqlmapException
     */
    public function nameCount(Column $condition): int
    {
        /** @var Result $record */
        $record = $this->queryForObject(self::ID_NAME_COUNT, $condition);
        return $record->count;
    }



    /**
     * regist
     *
     * @param Column $entity
     * @return bool
     * @throws SqlmapException
     */
    public function regist(Column $entity): bool
    {
        try
        {
            // validate for duplicate check
            if ($this->isValidate() === true
            && $entity->toCondition() != $entity->getCondition() && $this->exist($entity->toCondition()) === true)
            {
                throw new SqlmapException('対象は既に登録されています。');
            }

            // transaction
            $this->insert(self::ID_REGIST, $entity);

            // message
            if ($this->isMessage() === true)
            {
                Message::addMessage('正常に登録されました。');
            }

            return true;
        }
        catch (SqlmapException $e)
        {
            // log
            Logger::error($e->getMessage());

            //message
            if ($this->isMessage() === true)
            {
                Message::addError($e->getMessage());
            }

            throw new SqlmapException('登録に失敗しました。');
        }
    }



    /**
     * modify
     *
     * @param Column $entity
     * @return bool
     * @throws SqlmapException
     */
    public function modify(Column $entity): bool
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
                throw new SqlmapException('既に登録されています。');
            }

            $this->update(self::ID_MODIFY, $entity);

            // message
            if ($this->isMessage() === true)
            {
                Message::addMessage('正常に編集されました。');
            }

            return true;
        }
        catch (SqlmapException $e)
        {
            // log
            Logger::error($e->getMessage());

            // message
            if ($this->isMessage() === true)
            {
                Message::addError($e->getMessage());
            }

            throw new SqlmapException('編集に失敗しました。');
        }
    }



    /**
     * remove
     *
     * @param Column $condition
     * @return bool
     * @throws SqlmapException
     */
    public function remove(Column $condition): bool
    {
        // 全削除の危険を回避
        if (count(get_object_vars($condition)) == 0)
        {
            // message
            if ($this->isMessage() === true)
            {
                Message::addWarning('削除の条件が足りません。');
            }
            return false;
        }

        try
        {
            // validate
            if ($this->isValidate() === true && $this->exist($condition) === false)
            {
                throw new SqlmapException('削除対象は登録されていないか、他ユーザに更新されています。');
            }

            $this->delete(self::ID_REMOVE, $condition);

            // message
            if ($this->isMessage() === true)
            {
                Message::addMessage('正常に削除されました。');
            }

            return true;
        }
        catch (SqlmapException $e)
        {
            // message
            if ($this->isMessage() === true)
            {
                Message::addError($e->getMessage());
            }

            throw new SqlmapException('削除に失敗しました。');
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
