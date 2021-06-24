<?php

namespace App\Action\OpenGL\Group;

use App\Model\Message;
use App\Service\OpenGL\Group;
//use App\Service\Post;
use Fusio\Engine\ActionAbstract;
use Fusio\Engine\ContextInterface;
use Fusio\Engine\ParametersInterface;
use Fusio\Engine\RequestInterface;
use PSX\Http\Exception\InternalServerErrorException;
use PSX\Http\Exception\StatusCodeException;

/**
 * Action which updates a group. Similar to the create action it only invokes the group service to update a specific
 * group
 */
class Update extends ActionAbstract
{
    /**
     * @var Group
     */
    private $groupService;

    public function __construct(Group $groupService)
    {
        $this->groupService = $groupService;
    }

    public function handle(RequestInterface $request, ParametersInterface $configuration, ContextInterface $context)
    {
		$tenantId = $request->getHeader('tenantId');
        try {
            $id = $this->groupService->update(
                (int) $request->get('group_id'),
                $request->getPayload(),
				$tenantId
            );

            $message = new Message();
            $message->setSuccess(true);
            $message->setMessage('Group successful updated');
            $message->setId($id);
        } catch (StatusCodeException $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw new InternalServerErrorException($e->getMessage());
        }

        return $this->response->build(200, [], $message);
    }
}
