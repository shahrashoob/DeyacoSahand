<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Haruncpi\LaravelUserActivity\Traits\Loggable;
use App\Models\Post\PostUser;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use Loggable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'profile_photo_url',
    ];

    public function posts(){
        return $this->hasMany( PostUser::class );
    }
    public function fullname($type="with_gender_2")
    {
        $text= $this->firstname . " " . $this->lastname;
        if($type=="with_gender_2" && $this->gender){
            $text=$this->gender->caption2." ".$text;
        }
        return $text;
    }
    public function checkButtonPermission($button_id){

        return $this->posts->first()->checkButtonPermission($button_id);
    }

    public static function GetBearerToken()
    {
        return session("bearer_token");
    }
}
