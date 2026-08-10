<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id();
            $table->string('cedula', 10)->unique();
            $table->string('nombre');
            $table->string('correo');
            $table->string('password');
            $table->enum('rol', ['usuario', 'superusuario'])->default('usuario');
            $table->tinyInteger('is_active')->default(1);
            $table->tinyInteger('primer_login')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('usuarios');
    }
};
