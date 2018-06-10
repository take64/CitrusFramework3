<?php
/**
 * @copyright   Copyright 2017, Citrus/besidesplus All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Sqlmap;


use Citrus\CitrusMessage;
use Citrus\Database\CitrusDatabaseColumn;

trait CitrusSqlmapValidation
{
    /**
     * validate essential modify
     * modify実行時の必須チェック
     *
     * @param CitrusDatabaseColumn $entity
     * @return bool
     */
    public function validateEssentialModify(CitrusDatabaseColumn $entity): bool
    {
        // 全変更の危険を回避
        if (count(get_object_vars($entity->getCondition())) == 0)
        {
            // message
            /** @var CitrusSqlmapCrud $this */
            if ($this->isMessage() === true)
            {
                CitrusMessage::addWarning($this->callTarget().'変更の条件が足りません。');
            }
            return false;
        }
        return true;
    }
}