<?php

namespace PayMeBack\Model;

use PayMeBack\Model\om\BaseUserPeer;


/**
 * Skeleton subclass for performing query and update operations on the 'user' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.PayMeBack.Model
 */
class UserPeer extends BaseUserPeer
{
    /**
     * @static
     * @param int $userId
     * @param \Criteria|null $criteria
     * @param null|\PropelPDO $con
     * @return int|null
     */
    public static function getTotalSpendings($userId, \Criteria $criteria = null, \PropelPDO $con = null)
    {
        $total = 0;

        $spendings = SpendingQuery::create()
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

    /**
     * @static
     * @param int $userId
     * @param \Criteria|null $criteria
     * @param null|\PropelPDO $con
     * @return int|null
     */
    public static function getTotalAdvances($userId, \Criteria $criteria = null, \PropelPDO $con = null)
    {
        $total = 0;

        $spendings = AdvanceQuery::create()
            ->filterByUserId($userId)
            ->withColumn('SUM(' . AdvancePeer::AMOUNT. ')', 'Sum')
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
} // UserPeer
