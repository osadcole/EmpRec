<?php

namespace App\Utils;

use Symfony\Component\Validator\Validator\ValidatorInterface;

class Validation
{
    public static function validateObject(ValidatorInterface $validator, $object): ?array
    {
        $warnings = $validator->validate($object);
        if (count($warnings) > 0) {
            $errors = [];
            foreach ($warnings as $warning)
                $errors[$warning->getPropertyPath()] = $warning->getMessage();

            return $errors;
        }
        return null;
    }
}
