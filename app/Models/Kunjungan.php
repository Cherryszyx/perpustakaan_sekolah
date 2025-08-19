<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kunjungan extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'waktu_kunjungan', 'via_qr'];

    public $timestamps = false; 
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}