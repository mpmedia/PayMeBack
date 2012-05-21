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
class SpendingCategory extends BaseSpendingCategory {

    public function getSpendingsForUser($id)
    {
        return SpendingQuery::create()
            ->filterById($this->id)
            ->filterByUserId($id)
            ->find()
        ;
    }

} // SpendingCategory
