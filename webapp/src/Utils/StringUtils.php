<?php

namespace App\Utils;

use Normalizer;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class StringUtils
{
    public static function formatValidationErrors(ConstraintViolationListInterface $errors): string
    {
        $formattedErrorMessage = ['Invalid entity :'];
        /** @var ConstraintViolation $violation */
        foreach ($errors as $violation) {
            $formattedErrorMessage[] = sprintf('%s : %s', $violation->getPropertyPath(), $violation->getMessage());
        }

        return join("\n", $formattedErrorMessage);
    }
}
