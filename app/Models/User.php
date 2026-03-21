<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    public const STATUS_ADMIN = 'admin';
    public const STATUS_EMPLOYEE = 'employee';
    public const STATUS_PAYROLL_MANAGER = 'payroll_manager';

    public const STATUSES = [
        self::STATUS_ADMIN,
        self::STATUS_EMPLOYEE,
        self::STATUS_PAYROLL_MANAGER,
    ];

    protected $fillable = [
        'name',
        'email',
        'password',
        'status',
        'prenom',
        'nom',
        'mail',
        'admin',
        'uuid_user',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function getConnectionName(): ?string
    {
        return config('database.auth_connection', parent::getConnectionName());
    }

    public function getKeyName(): string
    {
        return $this->hasLegacyAuthSchema() ? 'uuid_user' : parent::getKeyName();
    }

    public function usesTimestamps(): bool
    {
        return ! $this->hasLegacyAuthSchema();
    }

    public function loginIdentifierColumn(): string
    {
        return $this->hasLegacyAuthPseudoColumn() ? 'pseudo' : 'email';
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getNameAttribute(?string $value): string
    {
        if ($value !== null && $value !== '') {
            return $value;
        }

        return trim(implode(' ', array_filter([$this->attributes['prenom'] ?? null, $this->attributes['nom'] ?? null])));
    }

    public function getEmailAttribute(?string $value): ?string
    {
        return $value ?: ($this->attributes['mail'] ?? null);
    }

    public function getStatusAttribute(?string $value): string
    {
        if ($value !== null && $value !== '') {
            return $value;
        }

        if ((int) ($this->attributes['admin'] ?? 0) === 2) {
            return self::STATUS_ADMIN;
        }

        if ($this->isPayrollManager()) {
            return self::STATUS_PAYROLL_MANAGER;
        }

        if ($this->isEmployee()) {
            return self::STATUS_EMPLOYEE;
        }

        return self::STATUS_EMPLOYEE;
    }

    public function hasStatus(string ...$statuses): bool
    {
        return in_array($this->status, $statuses, true);
    }

    public function isEmployee(): bool
    {
        if (array_key_exists('status', $this->attributes) && $this->attributes['status'] === self::STATUS_EMPLOYEE) {
            return true;
        }

        if (! $this->hasLegacyAuthSchema() || ! $this->getAuthIdentifier()) {
            return false;
        }

        return DB::connection($this->getConnectionName())
            ->table('employes')
            ->where('uuid_user', $this->getAuthIdentifier())
            ->exists();
    }

    public function isPayrollManager(): bool
    {
        if (array_key_exists('status', $this->attributes) && $this->attributes['status'] === self::STATUS_PAYROLL_MANAGER) {
            return true;
        }

        if (! $this->getAuthIdentifier() || ! $this->hasTable(config('database.connections.'.config('database.default').'.database'), 'payroll_manager', config('database.default'))) {
            return false;
        }

        return DB::connection(config('database.default'))
            ->table('payroll_manager')
            ->where('uuid_user', $this->getAuthIdentifier())
            ->exists();
    }

    protected function hasLegacyAuthSchema(): bool
    {
        return $this->hasTable(config('database.connections.'.$this->getConnectionName().'.database'), 'users', $this->getConnectionName())
            && Schema::connection($this->getConnectionName())->hasColumn('users', 'uuid_user');
    }

    protected function hasLegacyAuthPseudoColumn(): bool
    {
        return $this->hasLegacyAuthSchema()
            && Schema::connection($this->getConnectionName())->hasColumn('users', 'pseudo');
    }

    protected function hasTable(?string $database, string $table, ?string $connection = null): bool
    {
        if ($database === null && $connection === null) {
            return false;
        }

        return Schema::connection($connection)->hasTable($table);
    }
}
