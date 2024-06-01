<?php

namespace App\Validator;

use App\Domain\Captcha\ChallengeInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ChallengeValidator extends ConstraintValidator
{
    public function __construct(
        private readonly ChallengeInterface $challenge
    ) {
    }

    /**
     * @param array $value
     * @param Constraint $constraint
     * @return void
     */
    public function validate($value, Constraint $constraint)
    {
        /* @var Challenge $constraint */

        if (null === $value || '' === $value) {
            return;
        }

        if ($this->challenge->verify($value['challenge'] ?? '', $value['answer'] ?? '')) {
            return;
        }


        // TODO: implement the validation here
        $this->context->buildViolation($constraint->message)
            ->addViolation();
    }
}
