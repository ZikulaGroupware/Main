<?php

/**
 * Copyright AlternativeCategories Team 2011
 *
 * This work is contributed to the Zikula Foundation under one or more
 * Contributor Agreements and licensed to You under the following license:
 *
 * @license GNU/GPLv3 (or at your option, any later version).
 * @package AlternativeCategories
 * @link https://github.com/phaidon/AlternativeCategories
 *
 * Please see the NOTICE file distributed with this source code for further
 * information regarding copyright and licensing.
 */

use Doctrine\ORM\Mapping as ORM;

/**
 * Wikula links entity class.
 *
 * Annotations define the entity mappings to database.
 *
 * @ORM\MappedSuperclass
 */


abstract class AlternativeCategories_Entity_Membership extends Zikula_EntityAccess
{
    
    /**
     * The following are annotations which define the id field.
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    
    /**
     * The following are annotations which define the id field.
     *
     * @ORM\Column(type="integer")
     */
    private $categoryId;
    
    public function __construct($category, $entity) {
        $this->categoryId = $category;
        $this->setEntity($entity);
    }
     
    
    public abstract function setEntity($entity);

    
}
