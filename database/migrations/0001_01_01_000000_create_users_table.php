<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // tabel role
        Schema::create('role', function (Blueprint $table) {
            $table->id('id_role')->primary();
            $table->string('nama_role');
            $table->dateTime('created_at');
            $table->dateTime('updated_at')->nullable();
        });
        // tabel unit 
        Schema::create('unit',
            function (Blueprint $table) {
                $table->string('id_unit')->primary();
                $table->string('nama_unit');
                $table->dateTime('created_at');
                $table->dateTime('updated_at')->nullable();
            }
        );
        // tabel kategori barang
        Schema::create('kategori', function (Blueprint $table) {
            $table->string('string_ktg')->primary();
            $table->string('nama_kategori');
            $table->dateTime('created_at');
            $table->dateTime('updated_at')->nullable();
        });
        // tabel barang
        Schema::create('barang', function (Blueprint $table) {
            $table->string('id_brg')->primary();
            $table->string('id_ktg')->references('id_ktg')->on('kategori')->onDelete('cascade');
            $table->string('nama_brg');
            $table->integer('stok_brg');
            $table->string('satuan');
            $table->dateTime('created_at');
            $table->dateTime('updated_at')->nullable();
        });
        // tabel permohonan barang
        Schema::create('permohonan', function (Blueprint $table) {
            $table->string('id_permo')->primary();
            $table->dateTime('tgl_permo');
            $table->string('bukti_permo');
            $table->text('status');
            $table->dateTime('created_at');
            $table->dateTime('updated_at')->nullable();
        });
        // tabel permohonan barang
        Schema::create('detail_permohonan', function (Blueprint $table) {
            $table->string('id_dp')->primary();
            $table->string('id_permo')->references('id_permo')->on('permohonan')->onDelete('cascade');
            $table->string('id_brg')->references('id_brg')->on('barang')->onDelete('cascade');
            $table->integer('jumlah_per');
            $table->integer('jumlah_setuju_b');
            $table->text('ket_permo');
            $table->dateTime('created_at');
            $table->dateTime('updated_at')->nullable();
        });
        // tabel users
        Schema::create('users', function (Blueprint $table) {
            $table->string('id_user')->primary();
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->text('password');
            $table->foreignId('id_role')->references('id_role')->on('role')->onDelete('cascade');
            $table->string('id_unit')->references('id_unit')->on('unit')->onDelete('cascade');
            $table->rememberToken();
            $table->dateTime('created_at');
            $table->dateTime('updated_at')->nullable();
        });
        // tabel barang keluar
        Schema::create('barang_keluar', function (Blueprint $table) {
            $table->string('id_bk')->primary();
            $table->string('id_user')->references('id_user')->on('users')->onDelete('cascade');
            $table->string('bukti_bk');
            $table->string('status_bk');
            $table->dateTime('created_at');
            $table->dateTime('updated_at')->nullable();
        });
        // tabel detail barang keluar
        Schema::create('detail_barang_keluar', function (Blueprint $table) {
            $table->string('id_dbk')->primary();
            $table->string('id_bk')->references('id_bk')->on('barang_keluar')->onDelete('cascade');
            $table->string('id_brg')->references('id_brg')->on('barang')->onDelete('cascade');
            $table->integer('jum_bk');
            $table->integer('jum_setuju_bk');
            $table->text('ket_bk');
            $table->dateTime('created_at');
            $table->dateTime('updated_at')->nullable();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            // $table->foreignId('user_id')->nullable()->primary();
            $table->string('user_id')->nullable()->index()->references('id_user')->on('users')->onDelete('cascade');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
