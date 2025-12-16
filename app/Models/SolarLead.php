<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SolarLead extends Model
{
    protected $table = 'solar_leads';

    protected $fillable = [
        'nombre',
        'email',
        'telefono',
        'provincia',
        'localidad',
        'codigo_postal',

        'tipo_vivienda',
        'superficie_m2',
        'orientacion',
        'factura_mensual',
        'consumo_anual',

        'potencia_recomendada_kwp',
        'numero_paneles',
        'precio_estimado',
        'ahorro_estimado_anual',

        'datos_extra',
    ];

    protected $casts = [
        'datos_extra' => 'array',
    ];
}
