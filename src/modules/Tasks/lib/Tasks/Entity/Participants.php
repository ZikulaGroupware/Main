<?php

/**
 * Copyright Tasks Team 2011
 *
 * This work is contributed to the Zikula Foundation under one or more
 * Contributor Agreements and licensed to You under the following license:
 *
 * @license GNU/GPLv3 (or at your option, any later version).
 * @package Tasks
 * @link https://github.com/phaidon/Tasks
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
 * @ORM\Entity
 * @ORM\Table(name="tasks_participants",
 *            uniqueConstraints={@ORM\UniqueConstraint(name="cat_unq",columns={"uname", "entityId"})})
 */


class Tasks_Entity_Participants extends Zikula_EntityAccess
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
     * @ORM\ManyToOne(targetEntity="Tasks_Entity_Tasks", inversedBy="participants")
     * @ORM\JoinColumn(name="entityId", referencedColumnName="tid")
     * @var ExampleDoctrine_Entity_User
     */
    private $entity;
    
    public function getEntity()
    {
        return $this->entity;
    }

    public function setEntity($entity)
    {
        $this->entity = $entity;
    }
    
    
    
    /**
     * The following are annotations which define the id field.
     *
     * @ORM\Column(type="string")
     */
    private $uname;
    
    
     public function __construct($uname, $entity) {
         $this->uname  = $uname;
         $this->entity = $entity;
     }
    
    
}