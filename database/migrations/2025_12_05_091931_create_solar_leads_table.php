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
        Schema::create('solar_leads', function (Blueprint $table) {
            $table->id();

            // Datos de contacto
            $table->string('nombre')->nullable();
            $table->string('email')->nullable();
            $table->string('telefono', 30)->nullable();
            $table->string('provincia')->nullable();
            $table->string('localidad')->nullable();
            $table->string('codigo_postal', 10)->nullable();

            // Datos introducidos en la calculadora
            $table->string('tipo_vivienda')->nullable();          
            $table->unsignedInteger('superficie_m2')->nullable(); 
            $table->string('orientacion')->nullable();            

            $table->unsignedInteger('factura_mensual')->nullable(); 
            $table->unsignedInteger('consumo_anual')->nullable();   

            // Resultados calculados
            $table->decimal('potencia_recomendada_kwp', 8, 2)->nullable();
            $table->unsignedInteger('numero_paneles')->nullable();
            $table->unsignedInteger('precio_estimado')->nullable();      
            $table->unsignedInteger('ahorro_estimado_anual')->nullable(); 

            // Extra (por si quieres guardar algo más en bruto)
            $table->json('datos_extra')->nullable();

            $table->timestamps();

            // Índices útiles
            $table->index('email');
            $table->index('telefono');
            $table->index('provincia');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solar_leads');
    }
};
