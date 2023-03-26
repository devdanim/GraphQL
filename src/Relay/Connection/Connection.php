<?php
/**
 * Date: 10.05.16
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Relay\Connection;


use Youshido\GraphQL\Exception\ConfigurationException;
use Youshido\GraphQL\Relay\Type\PageInfoType;
use Youshido\GraphQL\Type\AbstractType;
use Youshido\GraphQL\Type\ListType\ListType;
use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Type\Scalar\IntType;
use Youshido\GraphQL\Type\TypeMap;

class Connection
{

    public static function connectionArgs()
    {
        return array_merge(self::forwardArgs(), self::backwardArgs());
    }

    public static function forwardArgs()
    {
        return [
            'after' => ['type' => TypeMap::TYPE_STRING, 'description' => 'Returns the elements in the list that come after the specified cursor'],
            'first' => ['type' => TypeMap::TYPE_INT, 'description' => 'Returns the first _n_ elements from the list']
        ];
    }

    public static function backwardArgs()
    {
        return [
//            'before' => ['type' => TypeMap::TYPE_STRING, 'description' => 'Returns the elements in the list that come before the specified cursor'],
//            'last'   => ['type' => TypeMap::TYPE_INT, 'description' => 'Returns the last _n_ elements from the list']
        ];
    }

    /**
     * @param AbstractType $type
     * @param null|string  $name
     * @param array        $config
     * @option string  edgeFields
     *
     * @return ObjectType
     */
    public static function edgeDefinition(AbstractType $type, $name = null, $config = [])
    {
        $name       = $name ?: $type->getName();
        $edgeFields = !empty($config['edgeFields']) ? $config['edgeFields'] : [];

        return new ObjectType([
            'name'        => $name . 'Edge',
            'description' => 'An edge in a connection.',
            'fields'      => array_merge([
                'node'   => [
                    'type'        => $type,
                    'description' => 'The item at the end of the edge',
                    'resolve'     => [__CLASS__, 'getNode'],
                ],
                'cursor' => [
                    'type'        => TypeMap::TYPE_STRING,
                    'description' => 'A cursor for use in pagination'
                ]
            ], $edgeFields)
        ]);
    }

    /**
     * @param AbstractType $type
     * @param null|string $name
     * @param array $config
     * @option string  connectionFields
     *
     * @return ObjectType
     * @throws ConfigurationException
     * @throws ConfigurationException
     */
    public static function connectionDefinition(AbstractType $type, $name = null, $config = [])
    {
        $name             = $name ?: $type->getName();
        $connectionFields = !empty($config['connectionFields']) ? $config['connectionFields'] : [];

        return new ObjectType([
            'name'        => $name . 'Connection',
            'description' => 'A connection to a list of items.',
            'fields'      => array_replace_recursive([
                'totalCount' => [
                    'type'        => new NonNullType(new IntType()),
                    'description' => 'How many nodes.',
                    'resolve'     => [__CLASS__, 'getTotalCount'],
                ],
                'pageInfo' => [
                    'type'        => new NonNullType(new PageInfoType()),
                    'description' => 'Information to aid in pagination.',
                    'resolve'     => [__CLASS__, 'getPageInfo'],
                ],
                'edges'    => [
                    'type'        => new ListType(self::edgeDefinition($type, $name, $config)),
                    'description' => 'A list of edges.',
                    'resolve'     => [__CLASS__, 'getEdges'],
                ]
            ], $connectionFields)
        ]);
    }

    public static function getTotalCount($value)
    {
        return $value['totalCount'] ?? -1;
    }

    public static function getEdges($value)
    {
        return $value['edges'] ?? null;
    }

    public static function getPageInfo($value)
    {
        return $value['pageInfo'] ?? null;
    }

    public static function getNode($value)
    {
        return $value['node'] ?? null;
    }
}
