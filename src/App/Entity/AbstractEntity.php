<?php

namespace App\Entity;

use Doctrine\ODM\PHPCR\Mapping\Annotations as PHPCR;

/**
 * Description of AbstractEntity
 *
 * @PHPCR\MappedSuperclass()
 *
 * @author alex
 */
abstract class AbstractEntity
{
    /**
     * @PHPCR\Id()
     */
    protected $path;

    /**
     * @PHPCR\ParentDocument()
     */
    protected $parent;

    /**
     * @PHPCR\NodeName()
     */
    protected $name;
}
