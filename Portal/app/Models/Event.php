<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Crypt;

class Event extends Model
{
    use HasFactory;

     /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'slots',
        'image',
        'startDate',
        'endDate',
        'game_id',
        'ip',
        'password',
    ];

    protected $hidden = [
        'ip',
        'password',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function userStatus($userId){

        $user = $this->users()->withPivot('status')->find($userId);

        return $user ? $user->getOriginal('pivot_status') : -1;
    }

    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    public function tags(): BelongsToMany{
        return $this->belongsToMany(Tag::class);
    }

    public function invitations(){
        return $this->hasMany(Invitation::class);
    }

    public function setIpAttribute($value)
    {
        $this->attributes['ip'] = Crypt::encryptString($value);
    }

    public function getIpAttribute($value)
    {
        return Crypt::decryptString($value);
    }

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Crypt::encryptString($value);
    }

    public function getPasswordAttribute($value)
    {
        return Crypt::decryptString($value);
    }

}
