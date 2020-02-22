<?php
/*
* This file is a part of GraphQL project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 5/19/16 8:58 AM
*/

namespace Youshido\GraphQL\Execution;

use Youshido\GraphQL\Execution\Context\ExecutionContext;
use Youshido\GraphQL\Execution\Context\ExecutionContextInterface;
use Youshido\GraphQL\Field\FieldInterface;
use Youshido\GraphQL\Parser\Ast\Field;
use Youshido\GraphQL\Parser\Ast\Query;
use Youshido\GraphQL\Type\AbstractType;
use Youshido\GraphQL\Parser\Ast\Interfaces\FieldInterface as AstFieldInterface;

class ResolveInfo
{
    /** @var Field */
    protected $field;

    /** @var AstFieldInterface */
    protected $fieldAST;

    /** @var ExecutionContext */
    protected $executionContext;

    /**
     * This property is to be used for DI in various scenario
     * Added to original class to keep backward compatibility
     * because of the way AbstractField::resolve has been declared
     *
     * @var mixed $container
     */
    protected $container;

    public function __construct(FieldInterface $field, $fieldAST, ExecutionContextInterface $executionContext)
    {
        $this->field            = $field;
        $this->fieldAST         = $fieldAST;
        $this->executionContext = $executionContext;
    }

    /**
     * @return ExecutionContext
     */
    public function getExecutionContext()
    {
        return $this->executionContext;
    }

    /**
     * @return Field
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @param string|null $fieldName
     *
     * @return null|Query|Field|AstFieldInterface
     */
    public function getFieldAST($fieldName = null)
    {
        if (!$fieldName) return $this->fieldAST;

        foreach ($this->fieldAST->getFields() as $ast)
            /** @var AstFieldInterface $ast */
            if ($ast->getName() === $fieldName)
                return $ast;

        return null;
    }

    /**
     * @return AbstractType
     */
    public function getReturnType()
    {
        return $this->field->getType();
    }

    public function getContainer()
    {
        return $this->executionContext->getContainer();
    }


}
