<?php

namespace PayMeBack\Model;

use PayMeBack\Model\om\BaseSpendingCategoryPeer;


/**
 * Skeleton subclass for performing query and update operations on the 'spending_category' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.PayMeBack.Model
 */
class SpendingCategoryPeer extends BaseSpendingCategoryPeer
{
    /**
     * @param int $categoryId
     * @param int $userId
     * @param \Criteria|null $criteria
     * @param null|\PropelPDO $con
     * @return array|mixed|\PropelObjectCollection
     */
    public static function getSpendingsByUserId($categoryId, $userId, \Criteria $criteria = null, \PropelPDO $con = null)
    {
        return SpendingQuery::create($criteria)
            ->filterByCategoryId($categoryId)
            ->filterByUserId($userId)
            ->find($con)
        ;
    }

    /**
     * @param int $categoryId
     * @param int $userId
     * @param \Criteria|null $criteria
     * @param null|\PropelPDO $con
     * @return array|mixed|\PropelObjectCollection
     */
    public static function getAmountForUser($categoryId, $userId, \Criteria $criteria = null, \PropelPDO $con = null)
    {
        $total = 0;

        $spendings = SpendingQuery::create()
            ->filterByCategoryId($categoryId)
            ->filterByUserId($userId)
            ->withColumn('SUM(' . SpendingPeer::AMOUNT. ')', 'Sum')
            ->select(array('Sum'))
            ->findOne($con)
        ;

        $total = $spendings;
        if(null === $total)
        {
            $total = 0;
        }

        return $total;
    }
} // SpendingCategoryPeer
