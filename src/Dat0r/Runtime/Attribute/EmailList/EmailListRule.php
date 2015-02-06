<?php

namespace Dat0r\Runtime\Attribute\EmailList;

use Dat0r\Runtime\Validator\Result\IncidentInterface;
use Dat0r\Runtime\Validator\Rule\Rule;
use Egulias\EmailValidator\EmailLexer;
use Egulias\EmailValidator\EmailParser;
use Egulias\EmailValidator\EmailValidator;
use InvalidArgumentException;
use ReflectionClass;

class EmailListRule extends Rule
{
    protected function execute($values)
    {
        $cast_to_array = $this->toBoolean($this->getOption(EmailListAttribute::OPTION_CAST_TO_ARRAY, true));
        if (!$cast_to_array && !is_array($values)) {
            $this->throwError('not_an_array');
            return false;
        }

        $emails = [];
        if (is_array($values)) {
            $emails = $values;
        } else {
            $emails = [ $values => '' ];
        }

        if (!empty($emails) && !$this->isAssoc($emails)) {
            $this->throwError('non_assoc_array', []);
            return false;
        }

        $count = count($emails);

        // minimum number of emails
        if ($this->hasOption(EmailListAttribute::OPTION_MIN_COUNT)) {
            $min_count = $this->getOption(EmailListAttribute::OPTION_MIN_COUNT, 0);
            if ($count < (int)$min_count) {
                $this->throwError('min_count', [ 'count' => $count, 'min_count' => $min_count ]);
                return false;
            }
        }

        // maximum number of emails
        if ($this->hasOption(EmailListAttribute::OPTION_MAX_COUNT)) {
            $max_count = $this->getOption(EmailListAttribute::OPTION_MAX_COUNT, 0);
            if ($count > (int)$max_count) {
                $this->throwError('max_count', [ 'count' => $count, 'max_count' => $max_count ]);
                return false;
            }
        }

        $allowed_labels = [];
        if ($this->hasOption(EmailListAttribute::OPTION_ALLOWED_LABELS)) {
            $allowed_labels = $this->getAllowedLabels();
        }

        $allowed_emails = [];
        if ($this->hasOption(EmailListAttribute::OPTION_ALLOWED_EMAILS)) {
            $allowed_emails = $this->getAllowedEmails();
        }

        $allowed_pairs = [];
        if ($this->hasOption(EmailListAttribute::OPTION_ALLOWED_PAIRS)) {
            $allowed_pairs = $this->getAllowedPairs();
        }

        $sanitized = [];

        $parser = new EmailParser(new EmailLexer());
        foreach ($emails as $email => $label) {
            $email = trim($email);
            if (empty($email)) {
                $this->throwError('empty_email', [ 'email' => $email, 'label' => $label ]);
                return false;
            }

            // check for valid email address
            try {
                $parser->parse($email);
                $warnings = $parser->getWarnings();
            } catch (InvalidArgumentException $parse_error) {
                $error_const = $parse_error->getMessage();
                $validator_reflection = new ReflectionClass(new EmailValidator());
                if ($validator_reflection->hasConstant($error_const)) {
                    $reason = $error_const;
                }
                $this->throwError('invalid_email', [ 'reason' => $reason, 'email' => $email, 'label' => $label ]);
                return false;
            }

            // check for allowed email address formats
            if ($this->hasOption(EmailListAttribute::OPTION_ALLOWED_EMAILS)) {
                if (!in_array($email, $allowed_emails, true)) {
                    $this->throwError(
                        EmailListAttribute::OPTION_ALLOWED_EMAILS,
                        [
                            EmailListAttribute::OPTION_ALLOWED_EMAILS => $allowed_emails,
                            'email' => $email
                        ]
                    );
                    return false;
                }
            }

            if (!is_string($label)) {
                $this->throwError('non_string_label', [ 'email' => $email ]);
                return false;
            }

            // check minimum label string length
            if ($this->hasOption(EmailListAttribute::OPTION_MIN)) {
                $min = filter_var($this->getOption(EmailListAttribute::OPTION_MIN), FILTER_VALIDATE_INT);
                if ($min === false) {
                    throw new InvalidConfigException('Minimum label length is not interpretable as integer.');
                }

                if (mb_strlen($label) < $min) {
                    $this->throwError(EmailListAttribute::OPTION_MIN, [
                        EmailListAttribute::OPTION_MIN => $min,
                        'email' => $email,
                        'label' => $label
                    ]);
                    return false;
                }
            }

            // check maximum label string length
            if ($this->hasOption(EmailListAttribute::OPTION_MAX)) {
                $max = filter_var($this->getOption(EmailListAttribute::OPTION_MAX), FILTER_VALIDATE_INT);
                if ($max === false) {
                    throw new InvalidConfigException('Maximum label length is not interpretable as integer.');
                }

                if (mb_strlen($label) > $max) {
                    $this->throwError(EmailListAttribute::OPTION_MAX, [
                        EmailListAttribute::OPTION_MAX => $max,
                        'email' => $email,
                        'label' => $label
                    ]);
                    return false;
                }
            }

            // check for allowed labels
            if ($this->hasOption(EmailListAttribute::OPTION_ALLOWED_LABELS)) {
                if (!in_array($label, $allowed_labels, true)) {
                    $this->throwError(
                        EmailListAttribute::OPTION_ALLOWED_LABELS,
                        [
                            EmailListAttribute::OPTION_ALLOWED_LABELS => $allowed_labels,
                            'email' => $email,
                            'label' => $label
                        ]
                    );
                    return false;
                }
            }

            // check for allowed email => label pairs
            if ($this->hasOption(EmailListAttribute::OPTION_ALLOWED_PAIRS)) {
                if (!(array_key_exists($email, $allowed_pairs) && $allowed_pairs[$email] === $label)) {
                    $this->throwError(
                        EmailListAttribute::OPTION_ALLOWED_PAIRS,
                        [
                            EmailListAttribute::OPTION_ALLOWED_PAIRS => $allowed_pairs,
                            'email' => $email,
                            'label' => $label
                        ]
                    );
                    return false;
                }
            }

            $sanitized[$email] = $label;
        }

        $this->setSanitizedValue($sanitized);

        return true;
    }

    /**
     * @return bool true if argument is an associative array. False otherwise.
     */
    protected function isAssoc(array $emails)
    {
        foreach (array_keys($emails) as $email => $label) {
            if ($email !== $label) {
                return true;
            }
        }

        return false;
    }

    protected function getAllowedLabels()
    {
        $allowed_labels = [];

        $configured_allowed_labels = $this->getOption(EmailListAttribute::OPTION_ALLOWED_LABELS, []);
        if (!is_array($configured_allowed_labels)) {
            throw new InvalidConfigException('Configured allowed_labels must be an array of permitted values.');
        }

        foreach ($configured_allowed_labels as $email => $label) {
            $allowed_labels[(string)$email] = (string)$label;
        }

        return $allowed_labels;
    }

    protected function getAllowedEmails()
    {
        $allowed_emails = [];

        $configured_allowed_emails = $this->getOption(EmailListAttribute::OPTION_ALLOWED_EMAILS, []);
        if (!is_array($configured_allowed_emails)) {
            throw new InvalidConfigException('Configured allowed_emails must be an array of permitted key names.');
        }

        foreach ($configured_allowed_emails as $email) {
            $allowed_emails[] = (string)$email;
        }

        return $allowed_emails;
    }

    protected function getAllowedPairs()
    {
        $allowed_pairs = [];

        $configured_allowed_pairs = $this->getOption(EmailListAttribute::OPTION_ALLOWED_PAIRS, []);
        if (!is_array($configured_allowed_pairs)) {
            throw new InvalidConfigException('Configured allowed_pairs must be an array of permitted values.');
        }

        foreach ($configured_allowed_pairs as $email => $label) {
            $allowed_pairs[(string)$email] = (string)$label;
        }

        return $allowed_pairs;
    }
}
