<?php
/**
 * @copyright   Copyright 2017, CitrusFramework. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Sqlmap;

use Citrus\Database\Column;
use Citrus\Message;

trait Validation
{
    /**
     * validate essential modify
     * modify実行時の必須チェック
     *
     * @param Column $entity
     * @return bool
     */
    public function validateEssentialModify(Column $entity): bool
    {
        // 全変更の危険を回避
        if (count(get_object_vars($entity->getCondition())) == 0)
        {
            // message
            /** @var Crud $this */
            if ($this->isMessage() === true)
            {
                Message::addWarning($this->callTarget().'変更の条件が足りません。');
            }
            return false;
        }
        return true;
    }
}
