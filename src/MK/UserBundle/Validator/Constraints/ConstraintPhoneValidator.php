<?php
namespace MK\UserBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;


class ConstraintPhoneValidator extends ConstraintValidator
{
    public $message = 'Invalid phone number.';

    public function validate($value, Constraint $constraint)
    {
        if (!empty($value) && !preg_match('/^[0-9]{9}+$/', $value, $matches)) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}