<?php

namespace App\Service\OpenGL;

use App\Model\OpenGL\Group as GroupModel;
use App\Repository\OpenGL\Group as GroupRepository;
use Fusio\Engine\ContextInterface;
use Fusio\Engine\DispatcherInterface;
//use Fusio\Engine\ConnectorInterface;
use Fusio\Engine\Connector;
use PSX\CloudEvents\Builder;
use PSX\Framework\Util\Uuid;
use PSX\Http\Exception as StatusCode;
use Doctrine\DBAL\Connection;

/**
 * Groups service which is responsible to create, update and delete a post. Please take a look at the page service for
 * more details
 */
class Group
{
    /**
     * @var Repository\OpenGL\Group
     */
    private $repository;

    /**
     * @var DispatcherInterface
     */
    private $dispatcher;
	
	/**
	* @var ConnectorInterface 
	*/
	private $connector;
	/**
	* @var tenantId
	*/
	//private $tenantId;

    public function __construct(GroupRepository $repository, DispatcherInterface $dispatcher,  Connector $connector)
    {
        $this->repository = $repository;
        $this->dispatcher = $dispatcher;
		//$this->tenantId = $tenantId;
		$this->connector = $connector;
    }
	
	private function setupTenantConnection($tenantId)
	{
		if (empty($tenantId)) {
            throw new StatusCode\NotFoundException('Provided request does not include with tenantId!');
        }
		//switch repository to use current tenantId connection database
		$this->repository->setupConnection($this->connector->getConnection('gl-'.$tenantId));
	}

    public function create(GroupModel $group, ContextInterface $context): int
    {
		$this->assertGroup($group);
		$this->setupTenantConnection($group->getTenantId());
        

        $id = $this->repository->insert(
            $group->getId(),
            //$context->getUser()->getId(),
			$group->getParentId(),
            $group->getName(),
			$group->getCode(),
			$group->getAffectsGross()
        );

        $row = $this->repository->findById($id);
        $this->dispatchEvent('opengl_group_created', $row, $id);

        return $id;
    }

    public function update(int $id, GroupModel $group): int
    {
		$this->assertGroup($group);
		$this->setupTenantConnection($group->getTenantId());
        $row = $this->repository->findById($id);
        if (empty($row)) {
            throw new StatusCode\NotFoundException('Provided group does not exist');
        }

        

        $this->repository->update($id, $group->getParentId(), $group->getName(),$group->getCode(),$group->getAffectsGross());

        $row = $this->repository->findById($id);
        $this->dispatchEvent('opengl_group_updated', $row, $id);

        return $id;
    }

    public function delete(int $id, GroupModel $group): int
    {
		$this->assertGroup($group);
		$this->setupTenantConnection($group->getTenantId());
        $row = $this->repository->findById($id);
        if (empty($row)) {
            throw new StatusCode\NotFoundException('Provided group does not exist');
        }

        $this->repository->delete($id);

        $this->dispatchEvent('opengl_group_deleted', $row, $id);

        return $id;
    }

    private function dispatchEvent(string $type, array $data, int $id)
    {
        $event = (new Builder())
            ->withId(Uuid::pseudoRandom())
            ->withSource('/opengl/group/' . $id)
            ->withType($type)
            ->withDataContentType('application/json')
            ->withData($data)
            ->build();

        $this->dispatcher->dispatch($type, $event);
    }

    private function assertGroup(GroupModel $group)
    {
		$tenantId = $group->getTenantId();
        if ($tenantId === null) {
            throw new StatusCode\BadRequestException('No TenantId provided');
        }
		
        $id = $group->getId();
        if ($id === null) {
            throw new StatusCode\BadRequestException('No Id provided');
        }
		
		$parentId = $group->getParentId();
        if ($parentId === null) {
            throw new StatusCode\BadRequestException('No Parent Id provided');
        }

        $name = $group->getName();
        if (empty($name)) {
            throw new StatusCode\BadRequestException('No name provided');
        }
    }
}
