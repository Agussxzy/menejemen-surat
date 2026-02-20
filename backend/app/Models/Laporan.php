<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Laporan extends Model
{
    use HasFactory;

    protected $fillable = [
        'judul',
        'jenis',
        'tanggal_mulai',
        'tanggal_selesai',
        'data_laporan',
        'user_id',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'data_laporan' => 'array',
    ];

    protected $hidden = [
        'created_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}