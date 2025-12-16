<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Opd extends Model
{
    use HasFactory;

    protected $table = 'opd';

    protected $fillable = [
        'nama_instansi',
        'singkatan',
        'nomor_hp',
    ];

    /**
     * Get the users for the opd.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
