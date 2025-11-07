<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamMember extends Model
{
    use HasFactory;

    // Nama tabel
    protected $table = 'team_members';

    // Primary key
    protected $primaryKey = 'member_id';

    // Kolom yang bisa diisi
    protected $fillable = [
        'team_id',
        'user_id',
        'role',
        'joined_at',
    ];

    protected $dates = ['joined_at'];

    // Relasi: satu anggota milik satu tim
    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }

    // Relasi: satu anggota adalah satu user
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

