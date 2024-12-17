<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class Barang extends Model
{
    //
    use HasFactory;
    public $timestamps = false;
    protected
        $guarded = [],
        $table = 'barang',
        $primaryKey = 'id_brg',
        $keyType = 'string',
        $fillable = [];

    // deteksi kolom pada tabel dinamis
    public function construct(array $attributes = [])
    {
        parent::construct($attributes);
        $this->fillable = Schema::getColumnListing($this->table);
    }

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'id_ktg', 'id_ktg');
    }

    public function permo_details()
    {
        return $this->hasMany(DetailPermohonan::class,'id_brg','id_brg');
    }

    public function bk_details()
    {
        return $this->hasMany(BarangKeluar::class,'id_brg','id_brg');
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id_brg = self::generateIdBrg();
        });
    }

    public static function generateIdBrg()
    {
        $lastBrg = self::orderBy('id_brg', 'desc')->first();

        if (!$lastBrg) {
            return 'brg-001';
        }

        $lastNumber = substr($lastBrg->id_brg, strlen('ktg-'));
        $newNumber = sprintf('%03d', intval($lastNumber) + 1);

        return 'brg-'.$newNumber;
    }
}
