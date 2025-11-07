<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $table = 'posts';
    protected $primaryKey = 'post_id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;
    
    protected $fillable = [
        'caption',
        'image_url',
        'uploaded_by',
        'created_at',
    ];

    // (Opsional) Relasi ke user kalau nanti ada tabel users
    public function user()
    {
        return $this->belongsTo(User::class, 'uploaded_by', 'id');
    }
}
