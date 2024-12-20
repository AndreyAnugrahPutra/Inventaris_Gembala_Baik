<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class Permohonan extends Model
{
    //
    use HasFactory;
    public $timestamps = false;
    protected
        $guarded = [],
        $table = 'permohonan',
        $primaryKey = 'id_permo',
        $keyType = 'string',
        $fillable = [];

    // deteksi kolom pada tabel dinamis
    public function construct(array $attributes = [])
    {
        parent::construct($attributes);
        $this->fillable = Schema::getColumnListing($this->table);
    }

    public function details()
    {
         return $this->hasMany(DetailPermohonan::class, 'id_permo','id_permo');
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id_permo = self::generateIdPermo();
        });
    }

    public static function generateIdPermo()
    {
        $lastPermo = self::orderBy('id_permo', 'desc')->first();

        if (!$lastPermo) {
            return 'per-'.date('Y').'-001';
        }

        $lastNumber = substr($lastPermo->id_permo, strlen('per-'.date('Y').'-'));
        $newNumber = sprintf('%03d', intval($lastNumber) + 1);

        return 'per-'.date('Y').'-'.$newNumber;
    }
}
