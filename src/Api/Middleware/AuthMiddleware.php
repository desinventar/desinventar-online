<?php

namespace Api\Middleware;

use Exception;
use Aura\Session\Segment;
use DesInventar\Models\Role;

class AuthMiddleware
{
    protected $pdo = null;
    protected $logger = null;
    protected $session = null;
    protected $minRoleValue = Role::ROLE_NONE;

    public function __construct($pdo, $logger, Segment $session, $minRoleValue)
    {
        $this->pdo = $pdo;
        $this->logger = $logger;
        $this->session = $session;
        $this->minRoleValue = $minRoleValue;
    }

    public function __invoke($request, $response, $next)
    {
        $userId = $this->session->get('userId');
        if (is_null($userId) || $userId === '') {
            throw new Exception('Access denied');
        }
        $routeInfo = $request->getAttribute('routeInfo', []);
        $regionId = isset($routeInfo[2]['regionId']) ?  $routeInfo[2]['regionId'] : '';
        $role = new Role($this->pdo, $this->logger);
        $userRole = Role::NONE;
        if ($regionId !== '') {
            $userRole = $role->getUserRole($userId, $regionId);
        }
        $userRoleValue = $role->convertUserRoleToValue($userRole);
        if ($userRoleValue < $this->minRoleValue) {
            throw new Exception('Access denied');
        }
        return $next(
            $request->withAttributes([
                'userId' => $userId,
                'regionId' => $regionId,
                'userRole' => $userRole,
                'userRoleValue' => $userRoleValue
            ]),
            $response
        );
    }
}
