<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Schema;

class User extends Authenticatable
{
    use HasFactory, Notifiable;
    public $timestamps = false;
    protected 
    $table = 'users',
    $primaryKey = 'id_user';
    
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
            $model->id_user = self::generateIdUser();
        });
    }

    public static function generateIdUser()
    {
        $lastUnit = self::orderBy('id_user', 'desc')->first();

        if (!$lastUnit) {
            return 'id_001';
        }

        $lastNumber = substr($lastUnit->id_user, strlen('id_'));
        $newNumber = sprintf('%03d', intval($lastNumber) + 1);

        return 'id_'. $newNumber;
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }
}
