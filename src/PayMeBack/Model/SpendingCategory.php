<?php

namespace PayMeBack\Model;

use PayMeBack\Model\om\BaseSpendingCategory;


/**
 * Skeleton subclass for representing a row from the 'spending_category' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.PayMeBack.Model
 */
class SpendingCategory extends BaseSpendingCategory
{
    /**
     * @param int $userId
     * @param \Criteria|null $criteria
     * @param null|\PropelPDO $con
     * @return array|mixed|\PropelObjectCollection
     */
    public function getAmountForUser($userId, \Criteria $criteria = null, \PropelPDO $con = null)
    {
        return SpendingCategoryPeer::getAmountForUser($this->id, $userId, $criteria, $con);
    }

    /**
     * @param int $userId
     * @param \Criteria|null $criteria
     * @param null|\PropelPDO $con
     * @return array|mixed|\PropelObjectCollection
     */
    public function getSpendingsForUser($userId, \Criteria $criteria = null, \PropelPDO $con = null)
    {
        return SpendingCategoryPeer::getSpendingsByUserId($this->id, $userId, $criteria, $con);
    }

} // SpendingCategory
