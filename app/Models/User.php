<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'opd_id',
    ];

    /**
     * Get the opd that owns the user.
     */
    public function opd()
    {
        return $this->belongsTo(Opd::class);
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

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
        ];
    }

    /**
     * Get user initials from name
     *
     * @return string
     */
    public function getInitialsAttribute(): string
    {
        $name = trim($this->name);
        $words = explode(' ', $name);
        
        if (count($words) >= 2) {
            // Get first letter of first word and first letter of last word
            return strtoupper(substr($words[0], 0, 1) . substr(end($words), 0, 1));
        } else {
            // Get first 2 letters of the name
            return strtoupper(substr($name, 0, 2));
        }
    }

    /**
     * Get background color class based on name
     *
     * @return string
     */
    public function getAvatarColorAttribute(): string
    {
        $colors = [
            'bg-primary',
            'bg-success',
            'bg-info',
            'bg-warning',
            'bg-danger',
            'bg-dark',
            'bg-secondary',
        ];
        
        $index = abs(crc32($this->name)) % count($colors);
        return $colors[$index];
    }
}
