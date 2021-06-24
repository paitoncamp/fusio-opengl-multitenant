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
 * Action which creates a group. Similar to the page create action it only
 * invokes the group service to create a specific group
 */
class Create extends ActionAbstract
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
            $id = $this->groupService->create(
                $request->getPayload(),
                $context,
				$tenantId
            );

            $message = new Message();
            $message->setSuccess(true);
            $message->setMessage('Group successful created');
            $message->setId($id);
        } catch (StatusCodeException $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw new InternalServerErrorException($e->getMessage());
        }

        return $this->response->build(201, [], $message);
    }
}
