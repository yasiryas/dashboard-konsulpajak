<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Concerns\HasTeams;
use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property Carbon|null $two_factor_confirmed_at
 * @property string|null $remember_token
 * @property int|null $current_team_id
 * @property UserRole $role
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Team|null $currentTeam
 * @property-read Collection<int, Team> $ownedTeams
 * @property-read Collection<int, Membership> $teamMemberships
 * @property-read Collection<int, Team> $teams
 * @property-read ClientProfile|null $clientProfile
 */
#[Fillable(['name', 'email', 'password', 'current_team_id', 'role'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasTeams, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
        ];
    }

/**
     * Get the user's client profiles.
     *
     * @return HasMany<ClientProfile, $this>
     */
    public function clientProfiles(): HasMany
    {
        return $this->hasMany(ClientProfile::class);
    }

    /**
     * Get the user's active client profile.
     *
     * @return ClientProfile|null
     */
    public function activeClientProfile(): ?ClientProfile
    {
        $id = session('active_client_profile_id');

        return $id ? $this->clientProfiles()->find($id) : null;
    }

    /**
     * Switch to the given client profile.
     *
     * @param  int  $clientProfileId
     * @return $this
     */
    public function switchClientProfile(int $clientProfileId)
    {
        session()->put('active_client_profile_id', $clientProfileId);

        return $this;
    }
}
