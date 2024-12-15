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

        $lastNumber = substr($lastBrg->id_brg, strrpos($lastBrg->id_brg, '-') + 1);
        $newNumber = sprintf('%04d', intval($lastNumber) + 1);

        return 'brg-' . $newNumber;
    }
}
