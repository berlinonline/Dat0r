<?php

namespace Dat0r\Runtime\Attribute\Email;

use Dat0r\Runtime\Validator\Rule\Rule;
use Dat0r\Runtime\Validator\Result\IncidentInterface;
use Egulias\EmailValidator\EmailLexer;
use Egulias\EmailValidator\EmailParser;
use Egulias\EmailValidator\EmailValidator;
use InvalidArgumentException;
use ReflectionClass;

class EmailRule extends Rule
{
    protected function execute($value)
    {
        if (!is_scalar($value) || !is_string($value)) {
            $this->throwError('invalid_type', array(), IncidentInterface::CRITICAL);
            return false;
        }

        $warnings = array();
        $reason = null;

        try {
            $parser = new EmailParser(new EmailLexer());
            $parser->parse($value);
            $warnings = $parser->getWarnings();
        } catch (InvalidArgumentException $parse_error) {
            $error_const = $parse_error->getMessage();
            $validator_reflection = new ReflectionClass(new EmailValidator());
            if ($validator_reflection->hasConstant($error_const)) {
                $reason = $error_const;
            }
            $this->throwError('invalid_format', array('reason' => $reason), IncidentInterface::ERROR);

            return false;
        }

        if (count($warnings) > 0) {
            // @todo map warnings to errors and raise critical
            // @todo raise critical as soon as max number of warnings reached
            // @todo non-mapped warnings are raised as notice
        }

        $this->setSanitizedValue($value);

        return true;
    }
}
