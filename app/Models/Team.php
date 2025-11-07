<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Team extends Model
{
    use HasFactory;

    // Nama tabel (opsional kalau sudah sesuai konvensi)
    protected $table = 'teams';

    // Primary key
    protected $primaryKey = 'team_id';

    // Kolom yang boleh diisi mass-assignment
    protected $fillable = [
        'team_name',
        'team_desc',
        'leader_id',
        'member_count',
        'member_limit',
        'terms',
        'team_status',
    ];

    // Kolom yang dianggap bertipe tanggal
    protected $dates = ['created_at'];

    // Relasi: satu tim punya satu leader (user)
    public function leader()
    {
        return $this->belongsTo(User::class, 'leader_id');
    }

    // Relasi: satu tim punya banyak anggota
    public function members()
    {
        return $this->hasMany(TeamMember::class, 'team_id');
    }
}
