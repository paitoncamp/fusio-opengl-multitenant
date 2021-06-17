<?php

namespace App\Action\OpenGL\Group;

use Fusio\Adapter\Sql\Action\SqlBuilderAbstract;
use Fusio\Engine\ContextInterface;
use Fusio\Engine\ParametersInterface;
use Fusio\Engine\RequestInterface;
use PSX\Sql\Builder;
use PSX\Sql\Condition;

/**
 * Action which returns a collection response of all groups. It shows how to build complex nested JSON structures
 * based on SQL queries
 */
class GetAll extends SqlBuilderAbstract
{
    public function handle(RequestInterface $request, ParametersInterface $configuration, ContextInterface $context)
    {
		$tenantId = $request->getHeader('tenantId');
        /** @var \Doctrine\DBAL\Connection $connection */
        $connection = $this->connector->getConnection('gl-'.$tenantId);
        $builder    = new Builder($connection);

        $startIndex = (int) $request->get('startIndex');
        $startIndex = $startIndex <= 0 ? 0 : $startIndex;
        $condition  = $this->getCondition($request);

        $sql = "SELECT '$tenantId' as `tenantId`,
					   groups.id,
                       groups.parent_id,
                       groups.name,
                       groups.code,
					   groups.affects_gross
                  FROM groups
                 WHERE " . $condition->getExpression($connection->getDatabasePlatform()) . "
              ORDER BY groups.code, groups.id ASC";

        $parameters = array_merge($condition->getValues(), ['startIndex' => $startIndex]);
        $definition = [
            'totalResults' => $builder->doValue('SELECT COUNT(*) AS cnt FROM groups', [], $builder->fieldInteger('cnt')),
            'startIndex' => $startIndex,
            'entries' => $builder->doCollection($sql, $parameters, [
				'tenantId' => 'tenantId' ,
                'id' => $builder->fieldInteger('id'),
                'parentId' => $builder->fieldInteger('parent_id'),
                'name' => 'name',
				'code' => 'code',
				'affectsGross' => $builder->fieldInteger('affects_gross'),
                //'insertDate' => $builder->fieldDateTime('insert_date'),
                'links' => [
                    'self' => $builder->fieldReplace('/opengl/group/{id}'),
                ]
            ])
        ];

        return $this->response->build(200, [], $builder->build($definition));
    }

    private function getCondition(RequestInterface $request): Condition
    {
        $condition = new Condition();

        $ref = $request->get('id');
        if (!empty($ref)) {
            $condition->equals('groups.id', (int) $ref);
        }

        $name = $request->get('name');
        if (!empty($name)) {
            $condition->like('groups.name', '%' . $name . '%');
        }

        return $condition;
    }
}
