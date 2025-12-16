<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Calculadora Solar</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link rel="stylesheet" href="{{ asset('css/calculator.css') }}">
</head>
<body>
  <div class="sc-page">
    <div class="sc-shell">

      <header class="sc-header">
        <h1 class="sc-h1">Calculadora de placas solares</h1>
        <p class="sc-lead">Responde a unas preguntas y obtén una estimación orientativa.</p>
      </header>

      @if ($errors->any())
        <div class="sc-alert">
          <div class="sc-alert-title">Revisa estos campos</div>
          <ul class="sc-alert-list">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <div class="sc-card">
        <form id="solar-form" method="POST" action="{{ route('calculator.store') }}">
          @csrf

          <!-- Hidden: resultado calculado (JS) -->
          <input type="hidden" name="calc_kwp" id="calc_kwp" value="">
          <input type="hidden" name="calc_presupuesto" id="calc_presupuesto" value="">
          <input type="hidden" name="calc_paneles" id="calc_paneles" value="">
          <input type="hidden" name="calc_ahorro_anual" id="calc_ahorro_anual" value="">

          <!-- Hidden: UTMs -->
          <input type="hidden" name="utm_source" id="utm_source" value="">
          <input type="hidden" name="utm_medium" id="utm_medium" value="">
          <input type="hidden" name="utm_campaign" id="utm_campaign" value="">
          <input type="hidden" name="utm_content" id="utm_content" value="">
          <input type="hidden" name="utm_term" id="utm_term" value="">

          <!-- Hidden: valores seleccionados -->
          <input type="hidden" name="tipo_vivienda" id="tipo_vivienda" value="{{ old('tipo_vivienda','unifamiliar') }}">
          <input type="hidden" name="orientacion" id="orientacion" value="{{ old('orientacion','sur') }}">
          <input type="hidden" name="consumo_modo" id="consumo_modo" value="{{ old('consumo_modo','') }}">

          <div class="sc-progress">
            <div class="sc-progress-top">
              <div class="sc-steptext" id="sc-steptext">Paso 1 de 10</div>
              <button type="button" class="sc-link" id="sc-reset">Reiniciar</button>
            </div>
            <div class="sc-bar">
              <div class="sc-bar-fill" id="sc-bar-fill" style="width:0%"></div>
            </div>
          </div>

          <div class="sc-panels">

            <!-- Paso 1: Tipo vivienda -->
            <section class="sc-panel active" data-step="1">
              <h2 class="sc-q">¿Qué tipo de vivienda es?</h2>
              <p class="sc-help">Selecciona una opción.</p>

              <div class="sc-options" data-pick="tipo_vivienda">
                <button type="button" class="sc-option" data-value="unifamiliar">Casa unifamiliar</button>
                <button type="button" class="sc-option" data-value="adosado">Adosado / Pareado</button>
                <button type="button" class="sc-option" data-value="piso">Piso / Ático</button>
                <button type="button" class="sc-option" data-value="comunidad">Comunidad de vecinos</button>
                <button type="button" class="sc-option" data-value="empresa">Empresa / Nave</button>
              </div>

              <div class="sc-nav">
                <button type="button" class="sc-btn sc-btn-ghost" disabled>Atrás</button>
                <button type="button" class="sc-btn sc-btn-primary" data-next>Siguiente</button>
              </div>
            </section>

            <!-- Paso 2: Superficie -->
            <section class="sc-panel" data-step="2">
              <h2 class="sc-q">¿Cuánta superficie útil de tejado tienes?</h2>
              <p class="sc-help">Aproximado. Si no lo sabes, pon una estimación.</p>

              <div class="sc-field">
                <label for="superficie_m2" class="sc-label">Superficie útil (m²)</label>
                <input
                  type="number"
                  id="superficie_m2"
                  name="superficie_m2"
                  min="5" max="500" step="1"
                  placeholder="Ej: 60"
                  value="{{ old('superficie_m2') }}"
                >
              </div>

              <div class="sc-nav">
                <button type="button" class="sc-btn sc-btn-ghost" data-prev>Atrás</button>
                <button type="button" class="sc-btn sc-btn-primary" data-next>Siguiente</button>
              </div>
            </section>

            <!-- Paso 3: Orientación -->
            <section class="sc-panel" data-step="3">
              <h2 class="sc-q">¿Cuál es la orientación principal del tejado?</h2>
              <p class="sc-help">Selecciona una opción.</p>

              <div class="sc-options" data-pick="orientacion">
                <button type="button" class="sc-option" data-value="sur">Sur</button>
                <button type="button" class="sc-option" data-value="sureste">Sureste</button>
                <button type="button" class="sc-option" data-value="suroeste">Suroeste</button>
                <button type="button" class="sc-option" data-value="este">Este</button>
                <button type="button" class="sc-option" data-value="oeste">Oeste</button>
                <button type="button" class="sc-option" data-value="norte">Norte</button>
              </div>

              <div class="sc-nav">
                <button type="button" class="sc-btn sc-btn-ghost" data-prev>Atrás</button>
                <button type="button" class="sc-btn sc-btn-primary" data-next>Siguiente</button>
              </div>
            </section>

            <!-- Paso 4: Método consumo -->
            <section class="sc-panel" data-step="4">
              <h2 class="sc-q">¿Cómo prefieres indicar tu consumo?</h2>
              <p class="sc-help">Elige una opción.</p>

              <div class="sc-options sc-options-1col" data-pick="consumo_modo">
                <button type="button" class="sc-option" data-value="factura">Por importe de factura</button>
                <button type="button" class="sc-option" data-value="kwh">Por kWh al año</button>
              </div>

              <div class="sc-nav">
                <button type="button" class="sc-btn sc-btn-ghost" data-prev>Atrás</button>
                <button type="button" class="sc-btn sc-btn-primary" data-next>Siguiente</button>
              </div>
            </section>

            <!-- Paso 5: Consumo -->
            <section class="sc-panel" data-step="5">
              <h2 class="sc-q" id="sc-consumo-title">Indica tu consumo</h2>
              <p class="sc-help" id="sc-consumo-help">Rellena el dato.</p>

              <div class="sc-field" id="sc-field-factura">
                <label for="factura_mensual" class="sc-label">Factura mensual (€)</label>
                <input
                  type="number"
                  id="factura_mensual"
                  name="factura_mensual"
                  min="0" step="1"
                  placeholder="Ej: 90"
                  value="{{ old('factura_mensual') }}"
                >
              </div>

              <div class="sc-field" id="sc-field-kwh">
                <label for="consumo_anual" class="sc-label">Consumo anual (kWh)</label>
                <input
                  type="number"
                  id="consumo_anual"
                  name="consumo_anual"
                  min="0" step="100"
                  placeholder="Ej: 3500"
                  value="{{ old('consumo_anual') }}"
                >
              </div>

              <div class="sc-nav">
                <button type="button" class="sc-btn sc-btn-ghost" data-prev>Atrás</button>
                <button type="button" class="sc-btn sc-btn-primary" data-next>Siguiente</button>
              </div>
            </section>

            <!-- Paso 6: Provincia -->
            <section class="sc-panel" data-step="6">
              <h2 class="sc-q">¿En qué provincia está la vivienda?</h2>
              <p class="sc-help">Esto ayuda a ajustar la estimación.</p>

              <div class="sc-field">
                <label for="provincia" class="sc-label">Provincia</label>
                <input
                  type="text"
                  id="provincia"
                  name="provincia"
                  placeholder="Ej: Madrid"
                  value="{{ old('provincia') }}"
                >
              </div>

              <div class="sc-nav">
                <button type="button" class="sc-btn sc-btn-ghost" data-prev>Atrás</button>
                <button type="button" class="sc-btn sc-btn-primary" data-next>Siguiente</button>
              </div>
            </section>

            <!-- Paso 7: Nombre -->
            <section class="sc-panel" data-step="7">
              <h2 class="sc-q">¿Cuál es tu nombre?</h2>
              <p class="sc-help">Lo usaremos para identificar tu solicitud.</p>

              <div class="sc-field">
                <label for="nombre" class="sc-label">Nombre</label>
                <input
                  type="text"
                  id="nombre"
                  name="nombre"
                  required
                  autocomplete="name"
                  placeholder="Tu nombre"
                  value="{{ old('nombre') }}"
                >
              </div>

              <div class="sc-nav">
                <button type="button" class="sc-btn sc-btn-ghost" data-prev>Atrás</button>
                <button type="button" class="sc-btn sc-btn-primary" data-next>Siguiente</button>
              </div>
            </section>

            <!-- Paso 8: Teléfono -->
            <section class="sc-panel" data-step="8">
              <h2 class="sc-q">¿Cuál es tu teléfono?</h2>
              <p class="sc-help">Para contactarte si quieres afinar el estudio.</p>

              <div class="sc-field">
                <label for="telefono" class="sc-label">Teléfono</label>
                <input
                  type="tel"
                  id="telefono"
                  name="telefono"
                  required
                  inputmode="tel"
                  autocomplete="tel"
                  placeholder="Tu teléfono"
                  value="{{ old('telefono') }}"
                >
              </div>

              <div class="sc-nav">
                <button type="button" class="sc-btn sc-btn-ghost" data-prev>Atrás</button>
                <button type="button" class="sc-btn sc-btn-primary" data-next>Siguiente</button>
              </div>
            </section>

            <!-- Paso 9: Email + consentimiento -->
            <section class="sc-panel" data-step="9">
              <h2 class="sc-q">¿Cuál es tu email?</h2>
              <p class="sc-help">Te enviaremos el resumen si lo necesitas.</p>

              <div class="sc-field">
                <label for="email" class="sc-label">Email</label>
                <input
                  type="email"
                  id="email"
                  name="email"
                  required
                  autocomplete="email"
                  placeholder="Tu email"
                  value="{{ old('email') }}"
                >
              </div>

              <div class="sc-consent">
                <label class="sc-check">
                  <input type="checkbox" name="consent_contacto" required>
                  Acepto que me contactéis para recibir información sobre la instalación fotovoltaica.
                </label>
                <div class="sc-legal">
                  Tus datos se utilizarán únicamente para atender tu solicitud, según la política de privacidad.
                </div>
              </div>

              <div class="sc-nav">
                <button type="button" class="sc-btn sc-btn-ghost" data-prev>Atrás</button>
                <button type="button" class="sc-btn sc-btn-primary" data-next>Ver resultado</button>
              </div>
            </section>

            <!-- Paso 10: Resultado + submit -->
            <section class="sc-panel" data-step="10">
              <h2 class="sc-q">Tu resultado orientativo</h2>
              <p class="sc-help">Estimación basada en los datos introducidos.</p>

              <div class="sc-result">
                <div class="sc-result-main" id="result-titulo">Instalación estimada: — kWp</div>
                <div class="sc-result-sub" id="result-precio">Presupuesto orientativo: — €</div>

                <div class="sc-tags">
                  <span class="sc-tag" id="result-placas">— paneles</span>
                  <span class="sc-tag" id="result-ahorro">Ahorro estimado: — €/año</span>
                </div>

                <div class="sc-note">
                  Esta cifra es orientativa. El precio final se ajusta con un estudio técnico.
                </div>
              </div>

              <div class="sc-nav">
                <button type="button" class="sc-btn sc-btn-ghost" data-prev>Atrás</button>
                <button type="submit" class="sc-btn sc-btn-primary">Enviar solicitud</button>
              </div>
            </section>

          </div>
        </form>
      </div>

    </div>
  </div>

  <div class="sc-toast" id="sc-toast" role="status" aria-live="polite"></div>

  <script>
  (function () {
    const totalSteps = 10;
    let currentStep = 1;

    const form = document.getElementById('solar-form');
    const panels = document.querySelectorAll('.sc-panel');
    const stepText = document.getElementById('sc-steptext');
    const barFill = document.getElementById('sc-bar-fill');
    const toastEl = document.getElementById('sc-toast');

    const hiddenTipo = document.getElementById('tipo_vivienda');
    const hiddenOri  = document.getElementById('orientacion');
    const hiddenModo = document.getElementById('consumo_modo');

    const fieldFacturaWrap = document.getElementById('sc-field-factura');
    const fieldKwhWrap = document.getElementById('sc-field-kwh');
    const consumoTitle = document.getElementById('sc-consumo-title');
    const consumoHelp = document.getElementById('sc-consumo-help');

    function fillUTMs(){
      const params = new URLSearchParams(window.location.search);
      ["utm_source","utm_medium","utm_campaign","utm_content","utm_term"].forEach(k => {
        const el = document.getElementById(k);
        if (el) el.value = params.get(k) || "";
      });
    }
    fillUTMs();

    function toast(msg){
      toastEl.textContent = msg;
      toastEl.classList.add('show');
      clearTimeout(toastEl._t);
      toastEl._t = setTimeout(() => toastEl.classList.remove('show'), 2200);
    }

    function setProgress(step){
      stepText.textContent = "Paso " + step + " de " + totalSteps;
      const pct = Math.round(((step - 1) / (totalSteps - 1)) * 100);
      barFill.style.width = pct + "%";
    }

    function showStep(step){
      currentStep = step;
      panels.forEach(p => p.classList.toggle('active', parseInt(p.dataset.step, 10) === step));
      setProgress(step);

      if (step === 5) paintConsumoStep();
      if (step === 10) calcularResultado();

      window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function paintSelection(groupEl, hiddenEl){
      const v = hiddenEl.value;
      groupEl.querySelectorAll('.sc-option').forEach(btn => {
        btn.classList.toggle('active', btn.dataset.value === v);
      });
    }

    function paintAllSelections(){
      document.querySelectorAll('.sc-options[data-pick]').forEach(group => {
        const name = group.getAttribute('data-pick');
        const hidden = document.getElementById(name);
        if (hidden) paintSelection(group, hidden);
      });
    }

    function paintConsumoStep(){
      const modo = (hiddenModo.value || "").trim();

      if (!modo){
        consumoTitle.textContent = "Indica tu consumo";
        consumoHelp.textContent = "Primero elige si lo indicarás por factura o por kWh.";
        fieldFacturaWrap.style.display = "none";
        fieldKwhWrap.style.display = "none";
        return;
      }

      if (modo === "factura"){
        consumoTitle.textContent = "¿Cuál es el importe medio de tu factura mensual?";
        consumoHelp.textContent = "Introduce un importe aproximado.";
        fieldFacturaWrap.style.display = "block";
        fieldKwhWrap.style.display = "none";
      } else {
        consumoTitle.textContent = "¿Cuál es tu consumo anual aproximado?";
        consumoHelp.textContent = "Introduce el consumo en kWh al año.";
        fieldFacturaWrap.style.display = "none";
        fieldKwhWrap.style.display = "block";
      }
    }

    function validateStep(step){
      if (step === 2) {
        const sup = document.getElementById('superficie_m2');
        if (sup.value) {
          const v = parseFloat(sup.value);
          if (isNaN(v) || v < 5) { toast('Indica una superficie válida (mínimo 5 m²).'); sup.focus(); return false; }
        }
      }

      if (step === 4) {
        if (!hiddenModo.value) { toast('Selecciona una opción.'); return false; }
      }

      if (step === 5) {
        const modo = hiddenModo.value;
        const factura = document.getElementById('factura_mensual');
        const kwh = document.getElementById('consumo_anual');

        if (modo === 'factura') {
          if (!factura.value) { toast('Indica el importe mensual de la factura.'); factura.focus(); return false; }
        } else if (modo === 'kwh') {
          if (!kwh.value) { toast('Indica el consumo anual en kWh.'); kwh.focus(); return false; }
        } else {
          toast('Selecciona primero si lo indicarás por factura o por kWh.');
          return false;
        }
      }

      if (step === 7 || step === 8 || step === 9) {
        const activePanel = document.querySelector('.sc-panel[data-step="' + step + '"]');
        const requiredEls = activePanel.querySelectorAll('[required]');
        for (const el of requiredEls) {
          if (!el.checkValidity()) { el.reportValidity(); return false; }
        }
      }

      return true;
    }

    document.querySelectorAll('[data-next]').forEach(btn => {
      btn.addEventListener('click', () => {
        if (!validateStep(currentStep)) return;
        if (currentStep < totalSteps) showStep(currentStep + 1);
      });
    });

    document.querySelectorAll('[data-prev]').forEach(btn => {
      btn.addEventListener('click', () => {
        if (currentStep > 1) showStep(currentStep - 1);
      });
    });

    document.querySelectorAll('.sc-options[data-pick]').forEach(group => {
      group.addEventListener('click', (e) => {
        const btn = e.target.closest('.sc-option');
        if (!btn) return;

        const pickName = group.getAttribute('data-pick');
        const hidden = document.getElementById(pickName);
        if (!hidden) return;

        hidden.value = btn.dataset.value;
        paintSelection(group, hidden);

        const panel = btn.closest('.sc-panel');
        if (panel) {
          const step = parseInt(panel.dataset.step, 10);
          setTimeout(() => {
            if (step === currentStep && currentStep < totalSteps) showStep(currentStep + 1);
          }, 120);
        }
      });
    });

    const resetBtn = document.getElementById('sc-reset');
    resetBtn.addEventListener('click', () => {
      hiddenTipo.value = 'unifamiliar';
      hiddenOri.value = 'sur';
      hiddenModo.value = '';

      const sup = document.getElementById('superficie_m2'); if (sup) sup.value = '';
      const fac = document.getElementById('factura_mensual'); if (fac) fac.value = '';
      const kwh = document.getElementById('consumo_anual'); if (kwh) kwh.value = '';
      const prov = document.getElementById('provincia'); if (prov) prov.value = '';
      const nom = document.getElementById('nombre'); if (nom) nom.value = '';
      const tel = document.getElementById('telefono'); if (tel) tel.value = '';
      const em  = document.getElementById('email'); if (em) em.value = '';
      const consent = form.querySelector('input[name="consent_contacto"]'); if (consent) consent.checked = false;

      paintAllSelections();
      showStep(1);
    });

    function calcularResultado() {
      const superficie = parseFloat(document.getElementById('superficie_m2').value) || 0;
      const factura = parseFloat(document.getElementById('factura_mensual').value) || 0;
      const consumoInput = parseFloat(document.getElementById('consumo_anual').value) || 0;
      const orientacion = (document.getElementById('orientacion').value || 'sur');

      let consumo = consumoInput;

      const kwpPorM2 = 0.18;
      const potenciaPorSuperficie = superficie * kwpPorM2;

      if (!consumo && factura) {
        consumo = (factura * 12) / 0.20;
      }

      let potenciaPorConsumo = 0;
      if (consumo) potenciaPorConsumo = consumo / 1500;

      let potenciaRecomendada = 0;
      if (potenciaPorSuperficie && potenciaPorConsumo) potenciaRecomendada = Math.min(potenciaPorSuperficie, potenciaPorConsumo);
      else potenciaRecomendada = potenciaPorSuperficie || potenciaPorConsumo;

      let factorOrientacion = 1;
      if (orientacion === 'sureste' || orientacion === 'suroeste') factorOrientacion = 0.95;
      if (orientacion === 'este' || orientacion === 'oeste') factorOrientacion = 0.90;
      if (orientacion === 'norte') factorOrientacion = 0.80;

      potenciaRecomendada = potenciaRecomendada * factorOrientacion;

      if (!potenciaRecomendada || potenciaRecomendada < 1) potenciaRecomendada = 1;
      potenciaRecomendada = Math.min(potenciaRecomendada, 20);

      const potenciaPanel = 0.45;
      let numeroPaneles = Math.round(potenciaRecomendada / potenciaPanel);
      if (numeroPaneles < 2) numeroPaneles = 2;

      const precioPorKwp = 1200;
      const presupuesto = Math.round(potenciaRecomendada * precioPorKwp);

      const ahorroAnual = Math.round((factura || 60) * 12 * 0.6);

      document.getElementById('result-titulo').textContent = 'Instalación estimada: ' + potenciaRecomendada.toFixed(1) + ' kWp';
      document.getElementById('result-precio').textContent = 'Presupuesto orientativo: ' + presupuesto.toLocaleString('es-ES') + ' €';
      document.getElementById('result-placas').textContent = numeroPaneles + ' paneles aprox.';
      document.getElementById('result-ahorro').textContent = 'Ahorro estimado: ' + ahorroAnual.toLocaleString('es-ES') + ' €/año';

      document.getElementById('calc_kwp').value = potenciaRecomendada.toFixed(1);
      document.getElementById('calc_presupuesto').value = String(presupuesto);
      document.getElementById('calc_paneles').value = String(numeroPaneles);
      document.getElementById('calc_ahorro_anual').value = String(ahorroAnual);
    }

    paintAllSelections();
    showStep(1);
  })();
  </script>
</body>
</html>
