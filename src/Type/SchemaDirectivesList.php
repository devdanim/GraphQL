<?php
/**
 * Date: 3/24/17
 *
 * @author Volodymyr Rashchepkin <rashepkin@gmail.com>
 */

namespace Youshido\GraphQL\Type;

use Youshido\GraphQL\Directive\DirectiveInterface;


class SchemaDirectivesList
{

    protected $directivesList = [];

    /**
     * @param array $directives
     *
     * @throws
     * @return $this
     */
    public function addDirectives($directives)
    {
        if (!is_array($directives)) {
            throw new \Exception('addDirectives accept only array of directives');
        }
        foreach ($directives as $directive) {
            $this->addDirective($directive);
        }

        return $this;
    }

    /**
     * @param DirectiveInterface $directive
     *
     * @return $this
     */
    public function addDirective(DirectiveInterface $directive)
    {
        $directiveName = $this->getDirectiveName($directive);
        if ($this->isDirectiveNameRegistered($directiveName)) return $this;

        $this->directivesList[$directiveName] = $directive;

        return $this;
    }

    protected function getDirectiveName($directive)
    {
        if (is_string($directive)) return $directive;
        if (is_object($directive) && $directive instanceof DirectiveInterface) {
            return $directive->getName();
        }
        throw new \Exception('Invalid directive passed to Schema');
    }

    public function isDirectiveNameRegistered($directiveName)
    {
        return (isset($this->directivesList[$directiveName]));
    }

    public function getDirectives()
    {
        return $this->directivesList;
    }

}