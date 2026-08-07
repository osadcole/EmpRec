<?php

namespace App\Api\Dto;
use JMS\Serializer\Annotation as JMS;

#[JMS\ExclusionPolicy("all")]
class RolesAddInput
{
    #[JMS\Expose]
    #[JMS\Type("array<int>")]
    public ?array $roleIds = null;
}
