<?php

namespace DesInventar\Models;

class Role extends Record
{
    public const NONE = 'NONE';
    public const OBSERVER = 'OBSERVER';
    public const USER = 'USER';
    public const SUPERVISOR = 'SUPERVISOR';
    public const ADMINREGION = 'ADMINREGION';
    public const ADMINPORTAL = 'ADMINPORTAL';

    public const ROLE_NONE = 0;
    public const ROLE_OBSERVER = 10;
    public const ROLE_USER = 20;
    public const ROLE_SUPERVISOR = 30;
    public const ROLE_ADMINREGION = 40;
    public const ROLE_ADMINPORTAL = 50;

    public function getUserRole(string $userId, string $regionId)
    {
        if (!$userId || $userId === '') {
            return self::NONE;
        }
        if (!$regionId || $regionId === '') {
            return self::NONE;
        }
        if ($userId === 'root') {
            return self::ADMINPORTAL;
        }
        $query = $this->factory->newSelect();
        $query->from('RegionAuth')
            ->cols(['AuthAuxValue'])
            ->where('AuthKey="ROLE"')
            ->where('RegionId=:regionId', ['regionId' => $regionId])
            ->where('UserId=:userId', ['userId' => $userId])
            ->orderBy(['UserId, RegionId']);
        $role = $this->readFirst($query);
        if (is_null($role)) {
            return self::NONE;
        }
        return $role['AuthAuxValue'];
    }

    public function convertUserRoleToValue($role)
    {
        $roleValues = [
            self::NONE => self::ROLE_NONE,
            self::OBSERVER => self::ROLE_OBSERVER,
            self::USER => self::ROLE_USER,
            self::SUPERVISOR => self::ROLE_SUPERVISOR,
            self::ADMINREGION => self::ROLE_ADMINREGION,
            self::ADMINPORTAL => self::ROLE_ADMINPORTAL
        ];
        return isset($roleValues[$role]) ? $roleValues[$role] : self::ROLE_NONE;
    }

    public function getUserRoleValue($userId, $regionId)
    {
        $userRole = $this->getUserRole($userId, $regionId);
        return $this->convertUserRoleToValue($userRole);
    }
}
