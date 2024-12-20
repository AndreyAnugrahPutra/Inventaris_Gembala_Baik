<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class DetailBarangKeluar extends Model
{
    //
    use HasFactory;
    public $timestamps = false;
    protected
        $guarded = [],
        $table = 'detail_barang_keluar',
        $primaryKey = 'id_dbk',
        $keyType = 'string',
        $fillable = [];

    // deteksi kolom pada tabel dinamis
    public function construct(array $attributes = [])
    {
        parent::construct($attributes);
        $this->fillable = Schema::getColumnListing($this->table);
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_brg', 'id_brg');
    }

    public function barangKeluar()
    {
        return $this->belongsTo(BarangKeluar::class, 'id_bk', 'id_bk');
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id_dbk = self::generateIdDbk();
        });
    }

    public static function generateIdDbk()
    {
        $lastDbk = self::orderBy('id_dbk', 'desc')->first();

        if (!$lastDbk) {
            return 'dbk-'.date('m-Y').'-001';
        }

        $lastNumber = substr($lastDbk->id_dbk, strlen('dbk-'.date('m-Y').'-'));
        $newNumber = sprintf('%03d', intval($lastNumber) + 1);

        return 'dbk-'.date('m-Y').'-'.$newNumber;
    }
}
