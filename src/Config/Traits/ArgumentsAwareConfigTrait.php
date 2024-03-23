<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 12/1/15 11:07 PM
*/

namespace Youshido\GraphQL\Config\Traits;


use Youshido\GraphQL\Field\InputField;

trait ArgumentsAwareConfigTrait
{
    protected $arguments = [];
    protected $_isArgumentsBuilt;

    public function buildArguments()
    {
        if ($this->_isArgumentsBuilt) {
            return;
        }

        if (!empty($this->data['args'])) {
            $this->addArguments($this->data['args']);
        }
        $this->_isArgumentsBuilt = true;
    }

    public function addArguments($argsList)
    {
        foreach ($argsList as $argumentName => $argumentInfo) {
            if ($argumentInfo instanceof InputField) {
                $this->arguments[$argumentInfo->getName()] = $argumentInfo;
                if (!$argumentInfo->getConfigValue('aliasOf')
                    && $aliases = $argumentInfo->getConfigValue('aliases')
                ) {
                    foreach ($aliases as $alias) {
                        if ($alias === $argumentInfo->getName()) continue;

                        $aliasArgumentInfo = clone $argumentInfo;

                        $aliasArgumentInfoConfig = clone $argumentInfo->getConfig();

                        $aliasArgumentInfoConfig->set('name', $alias);
                        $aliasArgumentInfoConfig->set('aliasOf', $argumentInfo->getName());
                        $aliasArgumentInfoConfig->set('description', 'Alias of "' . $argumentInfo->getName() . '". ' . $argumentInfo->getDescription());
                        $aliasArgumentInfo->setConfig($aliasArgumentInfoConfig);

                        $aliasArgumentInfo->setConfigValue('name', $alias);
                        $aliasArgumentInfo->setConfigValue('aliasOf', $argumentInfo->getName());
                        $aliasArgumentInfo->setDescription('Alias of "' . $argumentInfo->getName() . '". ' . $argumentInfo->getDescription());

                        $this->arguments[$alias] = $aliasArgumentInfo;
                    }
                }
                continue;
            } else {
                $this->addArgument($argumentName, $this->buildConfig($argumentName, $argumentInfo));
            }
        }

        return $this;
    }

    public function addArgument($argument, $argumentInfo = null)
    {
        if (!($argument instanceof InputField)) {
            $argument = new InputField($this->buildConfig($argument, $argumentInfo));
        }
        $this->arguments[$argument->getName()] = $argument;
        if (!$argument->getConfigValue('aliasOf')
            && $aliases = $argument->getConfigValue('aliases')
        ) {
            foreach ($aliases as $alias) {
                if ($alias === $argument->getName()) continue;

                $aliasArgument = clone $argument;

                $aliasArgumentConfig = clone $argument->getConfig();

                $aliasArgumentConfig->set('name', $alias);
                $aliasArgumentConfig->set('aliasOf', $argument->getName());
                $aliasArgumentConfig->set('description', 'Alias of "' . $argument->getName() . '". ' . $argument->getDescription());
                $aliasArgument->setConfig($aliasArgumentConfig);

                $aliasArgument->setConfigValue('name', $alias);
                $aliasArgument->setConfigValue('aliasOf', $argument->getName());
                $aliasArgument->setDescription('Alias of "' . $argument->getName() . '". ' . $argument->getDescription());

                $this->arguments[$alias] = $aliasArgument;
            }
        }

        return $this;
    }

    protected function buildConfig($name, $info = null)
    {
        if (!is_array($info)) {
            return [
                'type' => $info,
                'name' => $name
            ];
        }
        if (empty($info['name'])) {
            $info['name'] = $name;
        }

        return $info;
    }

    /**
     * @param $name
     *
     * @return InputField
     */
    public function getArgument($name)
    {
        return $this->hasArgument($name) ? $this->arguments[$name] : null;
    }

    /**
     * @param $name
     *
     * @return bool
     */
    public function hasArgument($name)
    {
        return array_key_exists($name, $this->arguments);
    }

    public function hasArguments()
    {
        return !empty($this->arguments);
    }

    /**
     * @return InputField[]
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    public function removeArgument($name)
    {
        if ($this->hasArgument($name)) {
            unset($this->arguments[$name]);
        }

        return $this;
    }

}
