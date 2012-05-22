<?php

namespace PayMeBack\Model;

use PayMeBack\Model\om\BaseUser;


/**
 * Skeleton subclass for representing a row from the 'user' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.PayMeBack.Model
 */
class User extends BaseUser
{
    /**
     * @param \Criteria|null $criteria
     * @param null|\PropelPDO $con
     * @return array|mixed|\PropelObjectCollection
     */
    public function getTotalSpendings(\Criteria $criteria = null, \PropelPDO $con = null)
    {
        return UserPeer::getTotalSpendings($this->id, $criteria, $con);
    }

    /**
     * @param \Criteria|null $criteria
     * @param null|\PropelPDO $con
     * @return array|mixed|\PropelObjectCollection
     */
    public function getTotalAdvances(\Criteria $criteria = null, \PropelPDO $con = null)
    {
        return UserPeer::getTotalAdvances($this->id, $criteria, $con);
    }
} // User
