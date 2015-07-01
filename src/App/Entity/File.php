<?php

namespace App\Entity;

use Doctrine\ODM\PHPCR\Mapping\Annotations as PHPCR;

/**
 * Description of File
 *
 * @PHPCR\Document(
 *  versionable="full",
 *  mixins={"mix:created", "mix:lastModified"}
 * )
 *
 * @author alex
 */
class File extends AbstractEntity
{
    /**
    * @PHPCR\Depth()
    */
    private $depth;

    /**
     * @PHPCR\VersionName
     */
    private $versionName;

    /**
     * @PHPCR\VersionCreated
     */
    private $versionCreated;
}
