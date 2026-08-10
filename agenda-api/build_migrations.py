import os
import glob

base_path = r"d:\UNIANDES\8VO\HERRAMIENTAS DE DESARROLLO DE SOFTWARE\CLASES\Tarea Semana 4\agenda-api\database\migrations"

mig1 = glob.glob(os.path.join(base_path, "*_create_usuarios_table.php"))[0]
mig2 = glob.glob(os.path.join(base_path, "*_create_catalogo_preguntas_table.php"))[0]
mig3 = glob.glob(os.path.join(base_path, "*_create_preguntas_recuperacion_table.php"))[0]
mig4 = glob.glob(os.path.join(base_path, "*_create_contactos_table.php"))[0]
mig5 = glob.glob(os.path.join(base_path, "*_create_bitacora_auditoria_table.php"))[0]

c1 = r"""<?php
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
"""

c2 = r"""<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('catalogo_preguntas', function (Blueprint $table) {
            $table->id();
            $table->string('pregunta');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('catalogo_preguntas');
    }
};
"""

c3 = r"""<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('preguntas_recuperacion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('usuarios')->onDelete('cascade');
            $table->foreignId('catalogo_pregunta_id')->constrained('catalogo_preguntas')->onDelete('cascade');
            $table->string('respuesta');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('preguntas_recuperacion');
    }
};
"""

c4 = r"""<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('contactos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('usuarios')->onDelete('cascade');
            $table->string('nombres');
            $table->string('apellidos');
            $table->enum('tipo', ['Trabajo', 'Personal', 'Proveedores', 'Otros']);
            $table->string('direccion');
            $table->string('telefono', 20);
            $table->string('sitio_web')->nullable();
            $table->string('empresa')->nullable();
            $table->string('cargo')->nullable();
            $table->tinyInteger('eliminado')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('contactos');
    }
};
"""

c5 = r"""<?php
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
"""

with open(mig1, "w") as f: f.write(c1)
with open(mig2, "w") as f: f.write(c2)
with open(mig3, "w") as f: f.write(c3)
with open(mig4, "w") as f: f.write(c4)
with open(mig5, "w") as f: f.write(c5)
