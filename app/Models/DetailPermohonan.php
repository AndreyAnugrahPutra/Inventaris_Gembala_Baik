<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class DetailPermohonan extends Model
{
    //
    use HasFactory;
    public $timestamps = false;
    protected
        $guarded = [],
        $table = 'detail_permohonan',
        $primaryKey = 'id_dp',
        $keyType = 'string',
        $fillable = [];

    // deteksi kolom pada tabel dinamis
    public function construct(array $attributes = [])
    {
        parent::construct($attributes);
        $this->fillable = Schema::getColumnListing($this->table);
    }



    public function permohonan()
    {
        return $this->belongsTo(Permohonan::class, 'id_permo', 'id_permo');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class,'id_brg','id_brg');
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id_dp = self::generateIdDp();
        });
    }

    public static function generateIdDp()
    {
        $lastDp = self::orderBy('id_dp', 'desc')->first();

        if (!$lastDp) {
            return 'dp-'.date('m').date('Y').'-001';
        }

        $lastNumber = substr($lastDp->id_dp, strrpos($lastDp->id_dp, '-') + 1);
        $newNumber = sprintf('%03d', intval($lastNumber) + 1);

        return 'dp-'.date('m').date('Y').'-'.$newNumber;
    }
}
