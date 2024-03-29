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


abstract class AlternativeCategories_Entity_All extends Zikula_EntityAccess
{
    
    /**
     * The following are annotations which define the id field.
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * The following are annotations which define the name field.
     *
     * @ORM\Column(type="string")
     */
    private $name;
    
    
    public function getName()
    {
        return $this->name;
    }
    
    public function setName($name)
    {
        $this->name = $name;
    }
    
 
    
}