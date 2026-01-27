<?php

namespace App\Models;

use App\Models\Role;
use App\Models\Spks;
use App\Models\Itineraries;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, HasApiTokens;
    protected $fillable = [
        'name', 'email', 'password', 'type', 'username', 'code', 'profileimg', 'phone', 'address', 'country', 'office','position','status','is_approved','approved_at','comment','session_id','email_verified_at', 'remember_token','subscriber','unsubscribe_reason'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    public function spks(){
        return $this->hasMany(Spks::class,'operator_id'); //good
    }
    public function itineraries(){
        return $this->hasMany(Itineraries::class,'user_id'); //good
    }

    public function getPhotoAttribute()
    {
        return 'https://www.gravatar.com/avatar/' . md5(strtolower($this->email)) . '.jpg?s=200&d=mm';
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function assignRole(Role $role)
    {
        return $this->roles()->save($role);
    }

    public function isWeddingRsv()
    {
        return $this->roles()->where('name', 'WeddingRsv')->exists();
    }
    public function isWeddingAuthor()
    {
        return $this->roles()->where('name', 'WeddingAuthor')->exists();
    }
    public function isWeddingSls()
    {
        return $this->roles()->where('name', 'WeddingSls')->exists();
    }
    public function isWeddingDvl()
    {
        return $this->roles()->where('name', 'WeddingDvl')->exists();
    }

    public function isAdmin()
    {
        return $this->roles()->where('name', 'Admin')->exists();
    }

    public function isUser()
    {
        return $this->roles()->where('name', 'User')->exists();
    }
    public function isRsv()
    {
        return $this->roles()->where('name', 'Reservation')->exists();
    }
    public function isAuthor()
    {
        return $this->roles()->where('name', 'Author')->exists();
    }
    public function isDev()
    {
        return $this->roles()->where('name', 'Developer')->exists();
    }
    
    public function messages()
    {
    return $this->hasMany(Message::class);
    }

}
