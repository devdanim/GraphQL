<?php
/**
 * Date: 27.11.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Validator\ErrorContainer;


interface ErrorContainerInterface
{

    public function addError(\Exception $exception);
    public function addWarning(\Exception $exception);

    public function mergeErrors(ErrorContainerInterface $errorContainer);
    public function mergeWarnings(ErrorContainerInterface $errorContainer);

    public function hasErrors();
    public function hasWarnings();

    public function getErrors();
    public function getWarnings();

    public function getErrorsArray();
    public function getWarningsArray();

    public function clearErrors();
    public function clearWarnings();

}