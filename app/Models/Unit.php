<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class Unit extends Model
{
    //
    use HasFactory;
    public $timestamps = false;
    protected
        $table = 'unit',
        $primaryKey = 'id_unit',
        $keyType = 'string',
        $fillable = [];

    // deteksi kolom pada tabel dinamis
    public function construct(array $attributes = [])
    {
        parent::construct($attributes);
        $this->fillable = Schema::getColumnListing($this->table);
    }

    public function users()
    {
         return $this->hasMany(User::class, 'id_unit', 'id_unit');
    }
    
    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id_unit = self::generateIdUnit();
        });
    }

    public static function generateIdUnit()
    {
        $lastUnit = self::orderBy('id_unit', 'desc')->first();

        if (!$lastUnit) {
            return 'unit-' . date('Y') . '-001';
        }

        $lastNumber = substr($lastUnit->id_unit, strrpos($lastUnit->id_unit, '-') + 1);
        $newNumber = sprintf('%05d', intval($lastNumber) + 1);

        return 'unit-'.date('Y').'-'.$newNumber;
    }
}
