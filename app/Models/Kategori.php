<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class Kategori extends Model
{
    //
    use HasFactory;
    public $timestamps = false;
    protected
        $guarded = [],
        $table = 'kategori',
        $primaryKey = 'id_ktg',
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
        return $this->hasMany(Barang::class, 'id_ktg', 'id_ktg');
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id_ktg = self::generateIdKtg();
        });
    }

    public static function generateIdKtg()
    {
        $lastKategori = self::orderBy('id_ktg', 'desc')->first();
        // dd($lastKategori);

        if (!$lastKategori) {
            return 'ktg-001';
        }

        $lastNumber = substr($lastKategori->id_ktg, strlen('ktg-'));
        $newNumber = sprintf('%03d', intval($lastNumber) + 1);

        return 'ktg-' . $newNumber;
    }
}
