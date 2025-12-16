<?php

namespace App\Http\Controllers;

use App\Models\SolarLead;
use Illuminate\Http\Request;

class SolarCalculatorController extends Controller
{
    // Muestra la landing con la calculadora
    public function index()
    {
        return view('calculator');
    }

    // Procesa el formulario y guarda el lead
    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'         => 'required|string|max:255',
            'email'          => 'nullable|email|max:255',
            'telefono'       => 'required|string|max:30',
            'provincia'      => 'nullable|string|max:255',
            'localidad'      => 'nullable|string|max:255',
            'codigo_postal'  => 'nullable|string|max:10',

            'tipo_vivienda'  => 'nullable|in:unifamiliar,adosado,piso,comunidad,empresa',
            'superficie_m2'  => 'nullable|integer|min:0',
            'orientacion'    => 'nullable|in:sur,sureste,suroeste,este,oeste,norte',

            'factura_mensual'=> 'nullable|integer|min:0',
            'consumo_anual'  => 'nullable|integer|min:0',
        ]);

        // --------- CÁLCULO ORIENTATIVO (igual que en la calculadora JS) ----------
        $superficie = (float) ($data['superficie_m2'] ?? 0);
        $factura    = (float) ($data['factura_mensual'] ?? 0);
        $consumo    = (float) ($data['consumo_anual'] ?? 0);
        $orientacion = $data['orientacion'] ?? 'sur';

        // Potencia por superficie
        $kwpPorM2 = 0.18; // 180 Wp/m² aprox
        $potenciaPorSuperficie = $superficie * $kwpPorM2;

        // Si no nos dan consumo pero sí factura, lo estimamos
        if (!$consumo && $factura) {
            // kWh = factura*12 / 0.20 (sup. 0,20 €/kWh)
            $consumo = ($factura * 12) / 0.20;
        }

        $potenciaPorConsumo = $consumo ? $consumo / 1500 : 0; // 1 kWp ~ 1500 kWh/año

        // Combinamos las dos estimaciones
        if ($potenciaPorSuperficie && $potenciaPorConsumo) {
            $potencia = min($potenciaPorSuperficie, $potenciaPorConsumo);
        } else {
            $potencia = $potenciaPorSuperficie ?: $potenciaPorConsumo;
        }

        // Ajuste por orientación
        $factorOrientacion = match ($orientacion) {
            'sureste', 'suroeste' => 0.95,
            'este', 'oeste'       => 0.90,
            'norte'               => 0.80,
            default               => 1.0, // sur
        };

        $potencia *= $factorOrientacion;

        // Limitar a valores razonables
        if (!$potencia || $potencia < 1) {
            $potencia = 1;
        }
        if ($potencia > 20) {
            $potencia = 20;
        }

        // Cálculos derivados
        $potenciaPanel = 0.45; // 450 Wp
        $numeroPaneles = max(2, (int) round($potencia / $potenciaPanel));

        $precioPorKwp = 1200; // € / kWp (orientativo)
        $precioEstimado = (int) round($potencia * $precioPorKwp);

        $ahorroEstimado = (int) round(($factura ?: 60) * 12 * 0.6); // 60 % ahorro aprox.

        // Guardamos en BBDD
        $lead = SolarLead::create([
            ...$data,
            'potencia_recomendada_kwp' => $potencia,
            'numero_paneles'           => $numeroPaneles,
            'precio_estimado'          => $precioEstimado,
            'ahorro_estimado_anual'    => $ahorroEstimado,
        ]);

        // Podemos devolver una vista de "gracias" con el resumen
        return view('calculator-gracias', [
            'lead'    => $lead,
            'potencia'=> $potencia,
            'numero_paneles' => $numeroPaneles,
            'precio_estimado'=> $precioEstimado,
            'ahorro_estimado'=> $ahorroEstimado,
        ]);
    }
}
