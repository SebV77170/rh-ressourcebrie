<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class EmployeeDirectory extends Model
{
    protected $table = 'employes';

    public $timestamps = false;

    public function getConnectionName(): ?string
    {
        return config('database.auth_connection', parent::getConnectionName());
    }

    public static function activeDirectoryQuery(): Builder
    {
        return static::query()
            ->selectRaw('MIN(employes.id) as id, users.uuid_user, users.prenom, users.nom, users.mail')
            ->join('users', 'users.uuid_user', '=', 'employes.uuid_user')
            ->groupBy('users.uuid_user', 'users.prenom', 'users.nom', 'users.mail')
            ->orderBy('users.prenom')
            ->orderBy('users.nom');
    }
}
