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
        $table = 'detail_permohonan',
        $primaryKey = 'id_dp',
        $keyType = 'string';

    // deteksi kolom pada tabel dinamis
    public function construct(array $attributes = [])
    {
        parent::construct($attributes);
        $this->fillable = Schema::getColumnListing($this->table);
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