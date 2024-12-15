<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class Role extends Model
{
    //
    use HasFactory;
    public $timestamps = false;
    protected
        $table = 'role',
        $primaryKey = 'id_role';

    // deteksi kolom pada tabel dinamis
    public function construct(array $attributes = [])
    {
        parent::construct($attributes);
        $this->fillable = Schema::getColumnListing($this->table);
    }

    public function users()
    {
        return $this->hasMany(User::class,'id_role','id_role');
    }
}
