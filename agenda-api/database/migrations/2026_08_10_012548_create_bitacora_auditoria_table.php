<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('bitacora_auditoria', function (Blueprint $table) {
            $table->id();
            $table->string('accion', 100);
            $table->text('descripcion')->nullable();
            $table->unsignedBigInteger('target_usuario_id');
            $table->foreignId('superusuario_id')->constrained('usuarios')->onDelete('cascade');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down()
    {
        Schema::dropIfExists('bitacora_auditoria');
    }
};
