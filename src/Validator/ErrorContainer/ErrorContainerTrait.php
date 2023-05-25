<?php
/**
 * Date: 01.12.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Validator\ErrorContainer;

use Youshido\GraphQL\Exception\Interfaces\ExtendedExceptionInterface;
use Youshido\GraphQL\Exception\Interfaces\LocationableExceptionInterface;

trait ErrorContainerTrait
{

    /** @var \Exception[] */
    protected $errors = [];

    /** @var \Exception[] */
    protected $warnings = [];

    public function addError(\Exception $exception)
    {
        $this->errors[] = $exception;

        return $this;
    }

    public function addWarning(\Exception $exception)
    {
        $this->warnings[] = $exception;

        return $this;
    }

    public function hasErrors()
    {
        return ! empty($this->errors);
    }

    public function hasWarnings()
    {
        return ! empty($this->errors);
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function getWarnings()
    {
        return $this->warnings;
    }

    public function mergeErrors(ErrorContainerInterface $errorContainer)
    {
        if ($errorContainer->hasErrors()) {
            foreach ($errorContainer->getErrors() as $error) {
                $this->addError($error);
            }
        }

        return $this;
    }

    public function mergeWarnings(ErrorContainerInterface $errorContainer)
    {
        if ($errorContainer->hasWarnings()) {
            foreach ($errorContainer->getWarnings() as $warning) {
                $this->addWarning($warning);
            }
        }

        return $this;
    }

    public function getErrorsArray($inGraphQLStyle = true, bool $warningLevel = false, bool $debug = false)
    {
        $errors = [];

        foreach (($warningLevel ? $this->warnings : $this->errors) as $error) {
            if ($inGraphQLStyle) {
                // All errors have a message
                $graphQLError = [
                    'message' => $error->getMessage() . ($debug ? ' ' . $error->getTraceAsString() : '')
                ];

                // Add code if it's non-zero
                if ($error->getCode()) {
                    $graphQLError['code'] = $error->getCode();
                }

                // Add location data when available
                if ($error instanceof LocationableExceptionInterface && $error->getLocation()) {
                    $graphQLError['locations'] = [$error->getLocation()->toArray()];
                }

                // Add extensions when available
                if ($error instanceof ExtendedExceptionInterface && $error->getExtensions()) {
                    $graphQLError['extensions'] = $error->getExtensions();
                }

                $errors[] = $graphQLError;
            } else {
                $errors[] = $error->getMessage();
            }
        }

        return $errors;
    }

    public function getWarningsArray($inGraphQLStyle = true)
    {
        return $this->getErrorsArray($inGraphQLStyle, true);
    }

    public function clearErrors()
    {
        $this->errors = [];

        return $this;
    }

    public function clearWarnings()
    {
        $this->warnings = [];

        return $this;
    }

}
