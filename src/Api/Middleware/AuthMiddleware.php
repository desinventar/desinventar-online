<?php

namespace Api\Middleware;

use Exception;
use Aura\Session\Segment;
use DesInventar\Database\Role;

class AuthMiddleware
{
    protected $session = null;
    protected $pdo = null;
    protected $minRoleValue = Role::ROLE_NONE;

    public function __construct(Segment $session, $pdo, $minRoleValue)
    {
        $this->session = $session;
        $this->pdo = $pdo;
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
        $role = new Role($this->pdo);
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
