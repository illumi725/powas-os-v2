<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Http\Traits\UserTraits;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use HasRoles;
    use UserTraits;
    use SoftDeletes;

    protected $primaryKey = 'user_id';
    public $incrementing = false;
    protected $guard_name = 'web';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'username',
        'email',
        'contact_number',
        'password',
        'powas_id',
        'account_status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    public function userinfo(): HasOne
    {
        return $this->hasOne(UserInfo::class, 'user_id', 'user_id');
    }

    public function powas(): BelongsTo
    {
        return $this->belongsTo(Powas::class, 'powas_id', 'powas_id');
    }

    public function manage_application(): HasMany
    {
        return $this->hasMany(PowasApplications::class, 'by_user_id', 'user_id');
    }

    public function transanctions(): HasMany
    {
        return $this->hasMany(Transactions::class, 'recorded_by_id', 'user_id');
    }

    public function userlogs(): HasMany
    {
        return $this->hasMany(PowasOsLogs::class, 'user_id', 'user_id');
    }

    public function loginlogs(): HasMany
    {
        return $this->hasMany(LoginLogs::class, 'user_id', 'user_id');
    }

    public function userreading(): HasMany
    {
        return $this->hasMany(Readings::class, 'recorded_by', 'user_id');
    }

    public function userbilling(): HasMany
    {
        return $this->hasMany(Billings::class, 'recorded_by', 'user_id');
    }

    public function userbillspayments(): HasMany
    {
        return $this->hasMany(BillsPayments::class, 'recorded_by', 'user_id');
    }

    public function usermicrosavings(): HasMany
    {
        return $this->hasMany(MicroSavings::class, 'recorded_by', 'user_id');
    }

    public function userreconnectionfee(): HasMany
    {
        return $this->hasMany(ReconnectionFees::class, 'recorded_by', 'member_id');
    }

    public function uservoucher(): HasMany
    {
        return $this->hasMany(Vouchers::class, 'recorded_by', 'member_id');
    }

    public function userdamayan(): HasMany
    {
        return $this->hasMany(DamayanDisbursements::class, 'recorded_by', 'user_id');
    }

    public function usercollection(): HasMany
    {
        return $this->hasMany(DamayanCollections::class, 'recorded_by', 'user_id');
    }
}
