<?php

namespace App\Action\OpenGL\Group;

use Fusio\Adapter\Sql\Action\SqlBuilderAbstract;
use Fusio\Engine\ContextInterface;
use Fusio\Engine\ParametersInterface;
use Fusio\Engine\RequestInterface;
use PSX\Sql\Builder;
use App\Model\OpenGL\Group;

/**
 * Action which returns a specific comment
 */
class Get extends SqlBuilderAbstract
{
	
	
    public function handle(RequestInterface $request, ParametersInterface $configuration, ContextInterface $context)
    {

		$tenantId = $request->getHeader('tenantId');

        /** @var \Doctrine\DBAL\Connection $connection */
        $connection = $this->connector->getConnection('gl-'.$tenantId);
        $builder    = new Builder($connection);

        $sql = "SELECT '$tenantId' as `tenantId`,
					   groups.id,
                       groups.parent_id,
                       groups.name,
                       groups.code,
					   groups.affects_gross
                  FROM groups
                 WHERE groups.id = :id";

        $parameters = ['id' => (int) $request->get('group_id')];
        $definition = $builder->doEntity($sql, $parameters, [
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
        ]);

        return $this->response->build(200, [], $builder->build($definition));
    }
}
