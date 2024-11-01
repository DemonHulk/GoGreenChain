@extends('adminlte::page')

@section('title', 'Mis Tareas')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
    <body>
        <!-- Resumen General -->
        <h4 class="mb-4">Resumen de Tareas</h4>
        <!-- Info Boxes -->
        <div class="row">
            <div class="col-md-4">
                <div class="info-box">
                    <span class="info-box-icon bg-primary"><i class="fas fa-tasks"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total de Tareas</span>
                        <span class="info-box-number">{{ $totalTasks }}</span>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="info-box">
                    <span class="info-box-icon bg-info"><i class="fas fa-clock"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Tareas Activas</span>
                        <span class="info-box-number">{{ $aceptadaTasksCount }}</span>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="info-box">
                    <span class="info-box-icon bg-success"><i class="fas fa-check"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Tareas Completadas</span>
                        <span class="info-box-number">{{ $completadaTasksCount }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de Tareas con Filtro -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Lista de Tareas</h3>
                
                <!-- Filtros -->
                <div class="mt-3">
                    <form action="{{ route('usuario.perfil.mis_tareas') }}" method="GET" class="row">
                        <div class="col-md-4">
                            <select name="status" class="form-control" onchange="this.form.submit()">
                                <option value="" {{ request('status') == '' ? 'selected' : '' }}>Todos los estados</option>
                                <option value="aceptada" {{ request('status') == 'aceptada' ? 'selected' : '' }}>
                                    Pendientes
                                </option>
                                <option value="completada" {{ request('status') == 'completada' ? 'selected' : '' }}>
                                    Completadas
                                </option>
                            </select>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Tarea</th>
                                <th>Estado</th>
                                <th>Empresa</th>
                                <th>Fecha</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tasks as $task)
                                <tr>
                                    <td><strong>{{ $task->title }}</strong></td>
                                    <td>
                                        <span class="status-{{ strtolower($task->status) }}">
                                            {{ $statusTranslations[$task->status] ?? $task->status }} 
                                        </span>
                                    </td>
                                    <td>
                                        <strong>
                                            <a href="{{ route('ver_perfil_empresa', ['id' => $task->empresa->id]) }}" class="text-primary">
                                                {{ $task->empresa->name }}
                                            </a>
                                        </strong>
                                    </td>
                                    <td>Empieza:
                                        {{ \Carbon\Carbon::parse($task->start_date)->format('d/m/Y') }} Vence:
                                        {{ \Carbon\Carbon::parse($task->end_date)->format('d/m/Y') }}
                                    </td>
                                    <td>
                                        @if(strtolower($task->status) === 'aceptada')
                                            <button type="button" class="btn btn-primary btn-sm view-task-button" data-id="{{ $task->id }}">
                                                Ver tarea
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">No hay tareas que coincidan con los filtros</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </body>
@stop


@section('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<style>
    /* Estilo para el estado 'aceptada' */
.status-aceptada {
    background-color: #cce5ff; /* Fondo azul claro */
    color: #004085;            /* Texto azul oscuro */
    font-weight: bold;         /* Negrita */
    padding: 5px 10px;        /* Espaciado interno */
    border-radius: 5px;       /* Bordes redondeados */
}

/* Estilo para el estado 'completada' */
.status-completada {
    background-color: #d4edda; /* Fondo verde claro */
    color: #155724;            /* Texto verde oscuro */
    font-weight: bold;         /* Negrita */
    padding: 5px 10px;        /* Espaciado interno */
    border-radius: 5px;       /* Bordes redondeados */
}

</style>
@stop
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
 $(document).ready(function() {
        $('.view-task-button').on('click', function() {
            let taskId = $(this).data('id'); // Obtener el id de la tarea
            let url = "{{ route('tareas.detalle', ['id' => ':id']) }}".replace(':id', taskId);

            // Realizar la solicitud AJAX para obtener los detalles de la tarea
            $.ajax({
                url: url,
                type: 'GET',
                success: function(response) {
                    // Formatear las fechas
                    const startDate = new Date(response.start_date);
                    const endDate = new Date(response.end_date);
                    const options = { year: 'numeric', month: '2-digit', day: '2-digit' };
                    const formattedStartDate = startDate.toLocaleDateString('es-ES', options);
                    const formattedEndDate = endDate.toLocaleDateString('es-ES', options);
                    
                    // Calcular la duración en días
                    const durationInDays = Math.ceil((endDate - startDate) / (1000 * 60 * 60 * 24));

                    // Mostrar los detalles de la tarea en SweetAlert
                    Swal.fire({
                        title: `<h3 style="text-align:left;">${response.title}</h3>`,
                        html: `
                            <div style="text-align:left; line-height:1.5;">
                                <p><strong>Descripción:</strong> ${response.description}</p>
                                <p><strong>Empresa:</strong> ${response.nombre_empresa}</p>
                                <p><strong>Fecha:</strong> ${formattedStartDate} - ${formattedEndDate}</p>
                                <p><strong>Duración:</strong> ${durationInDays} días</p>
                                <p><strong>Precio:</strong> ${response.price}</p>
                                <p><strong>Ubicación:</strong> ${response.location}</p>
                                <p><strong>Recompensa:</strong> ${response.reward}</p>
                                <p><strong>Ubicación de la Empresa:</strong> ${response.location_empresa}</p>
                            </div>
                        `,
                        showCloseButton: true,
                        showCancelButton: true,
                        confirmButtonText: 'Marcar como completada',
                        cancelButtonText: 'Cerrar',
                        width: '600px',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Aquí creamos el contenido para mostrar la ubicación
                            let locationEmpresa = response.location_empresa ? response.location_empresa.trim() : '';
                            let mapHtml;

                            if (locationEmpresa && locationEmpresa.includes(',')) {
                                // Si location_empresa tiene coordenadas
                                const coords = locationEmpresa.split(',');
                                const latitude = coords[0].trim();
                                const longitude = coords[1].trim();

                                mapHtml = `
                                    <p>¿Quieres marcar esta tarea como completada? Necesitarás entregar la evidencia de forma presencial.</p>
                                    <a href="https://www.google.com/maps/search/?api=1&query=${latitude},${longitude}" target="_blank" class="btn btn-link">
                                        Ver ubicación de la empresa
                                    </a>
                                `;
                            } else {
                                // Si location_empresa no tiene coordenadas, se usa como dirección
                                mapHtml = `
                                    <p>¿Quieres marcar esta tarea como completada? Necesitarás entregar la evidencia de forma presencial.</p>
                                    <a href="https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(locationEmpresa)}" target="_blank" class="btn btn-link">
                                        Ver ubicación
                                    </a>
                                `;
                            }

                            // Configurar el SweetAlert con el contenido de ubicación
                            Swal.fire({
                                title: '¿Estás seguro?',
                                html: mapHtml,
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#28a745',
                                cancelButtonColor: '#dc3545',
                                confirmButtonText: 'Sí, marcar como completada',
                                cancelButtonText: 'No, cancelar'
                            }).then((confirmResult) => {
                                if (confirmResult.isConfirmed) {
                                    // Hacer la llamada AJAX para completar la tarea usando PUT
                                    $.ajax({
                                        url: `/usuario/perfil/tareas/completar_tarea/${taskId}`,
                                        type: 'PUT',  // Manteniendo el método PUT
                                        data: {
                                            _token: "{{ csrf_token() }}", // Añadiendo el token CSRF
                                            id: taskId // Pasando el ID de la tarea
                                        },
                                        success: function(response) {
                                            Swal.fire({
                                                title: '¡Éxito!',
                                                text: 'La tarea ha sido marcada como completada',
                                                icon: 'success'
                                            }).then(() => {
                                                window.location.reload(); // Recargar la página para ver los cambios
                                            });
                                        },
                                        error: function(xhr) {
                                            console.error('Error:', xhr.responseText);
                                            Swal.fire({
                                                title: 'Error',
                                                text: 'Ocurrió un error al completar la tarea',
                                                icon: 'error'
                                            });
                                        }
                                    });
                                }
                            });
                        }
                    });
                },
                error: function() {
                    Swal.fire('Error', 'Error al obtener los detalles de la tarea', 'error');
                }
            });
        });
    });
</script>
@stop
