<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class BarangKeluar extends Model
{
    //
    use HasFactory;
    public $timestamps = false;
    protected
        $table = 'barang_keluar',
        $primaryKey = 'id_bk',
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
        return $this->hasMany(DetailBarangKeluar::class, 'id_bk','id_bk');
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id_bk = self::generateIdBk();
        });
    }

    public static function generateIdBk()
    {
        $lastBk = self::orderBy('id_bk', 'desc')->first();

        if (!$lastBk) {
            return 'bk-' . date('Y') . '-001';
        }

        $lastNumber = substr($lastBk->id_bk, strrpos($lastBk->id_bk, '-') + 1);
        $newNumber = sprintf('%03d', intval($lastNumber) + 1);

        return 'bk-'.date('Y').'-'.$newNumber;
    }
}
