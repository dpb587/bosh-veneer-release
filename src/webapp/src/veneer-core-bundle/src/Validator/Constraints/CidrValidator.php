<?php

namespace Veneer\CoreBundle\Validator\Constraints;

use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class CidrValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof Cidr) {
            throw new UnexpectedTypeException($constraint, Cidr::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!is_scalar($value) && !(is_object($value) && method_exists($value, '__toString'))) {
            throw new UnexpectedTypeException($value, 'string');
        }

        $context = $this->context instanceof ExecutionContextInterface ? $this->context : $this;

        $value = (string) $value;

        $split = explode('/', $value, 2);

        if (1 == count($split)) {
            $split[1] = '32';
        } elseif (2 != count($split)) {
            $context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $this->formatValue($value))
                ->addViolation();
        }

        if (!filter_var($split[0], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $context->buildViolation($constraint->messageIp)
                ->setParameter('{{ value }}', $this->formatValue($value))
                ->addViolation();
        }

        $netmask = (int) $split[1];

        if ((0 > $netmask) || (32 < $netmask)) {
            $context->buildViolation($constraint->messageNetmask)
                ->setParameter('{{ value }}', $this->formatValue($value))
                ->addViolation();
        }
    }
}
