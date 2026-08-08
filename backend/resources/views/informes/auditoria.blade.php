<!DOCTYPE html>
<html>
<head><meta charset="utf-8"></head>
<body>
    <h1>Informe de Auditoría — {{ $auditoria->titulo }}</h1>
    <p>Estado: {{ $auditoria->estado }}</p>

    <h2>Objetivo</h2>
    <p>{{ $auditoria->objetivo }}</p>

    <h2>Alcance</h2>
    <p>{{ $auditoria->alcance }}</p>

    <h2>Equipo Auditor</h2>
    <p>Líder: {{ $auditoria->auditorLider->name ?? 'N/A' }}</p>
    <ul>
        @foreach ($auditoria->equipoAuditor as $miembro)
            <li>{{ $miembro->name }}</li>
        @endforeach
    </ul>

    <h2>Resumen Ejecutivo</h2>
    <p>Total de hallazgos: {{ $hallazgos->count() }}</p>
    <p>No Conformidad Mayor: {{ $hallazgos->where('tipo_hallazgo', 'No Conforme Mayor')->count() }}</p>
    <p>No Conformidad Menor: {{ $hallazgos->where('tipo_hallazgo', 'No Conforme Menor')->count() }}</p>

    <h2>Detalle de Hallazgos</h2>
    @foreach ($hallazgos as $h)
        <div>
            <strong>{{ $h->clausula_o_control }} — {{ $h->tipo_hallazgo }}</strong>
            <p>{{ $h->descripcion }}</p>
        </div>
    @endforeach

    <h2>Conclusiones</h2>
    <p>{{ $auditoria->conclusiones }}</p>
</body>
</html>