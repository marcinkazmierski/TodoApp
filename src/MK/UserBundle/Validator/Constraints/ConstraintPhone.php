<?php
namespace MK\UserBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ConstraintPhone extends Constraint
{
    public $message = 'Invalid phone number: the string "%string%" contains an illegal character: it can only contain letters or numbers.';

    public function validatedBy()
    {
        return get_class($this) . 'Validator';
    }
}