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
                        <span class="info-box-number">{{ $acceptedTasksCount }}</span>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="info-box">
                    <span class="info-box-icon bg-success"><i class="fas fa-check"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Tareas Completadas</span>
                        <span class="info-box-number">{{ $completedTasksCount }}</span>
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
                                <option value="accepted" {{ request('status') == 'accepted' ? 'selected' : '' }}>
                                    Pendientes
                                </option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>
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
                                <th>Fecha</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tasks as $task)
                                <tr>
                                    <td><strong>{{ $task->title }}</strong></td>
                                    <td>
                                        @if($task->status === 'accepted')
                                            <span class="badge badge-info">{{ $task->status }}</span> <!-- Azul para 'accepted' -->
                                        @elseif($task->status === 'completed')
                                            <span class="badge badge-success">{{ $task->status }}</span> <!-- Verde para 'completed' -->
                                        @else
                                            <span class="badge badge-secondary">{{ $task->status }}</span> <!-- Color neutro para otros estados -->
                                        @endif
                                    </td>
                                    <td>Empieza:
                                        {{ \Carbon\Carbon::parse($task->start_date)->format('d/m/Y') }} Vence:
                                        {{ \Carbon\Carbon::parse($task->end_date)->format('d/m/Y') }}
                                    </td>
                                    <td>
                                        @if(strtolower($task->status) === 'accepted')
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

        @if($tasks->isNotEmpty()) 
        <div class="modal fade" id="taskDetailModal" tabindex="-1" role="dialog" aria-labelledby="taskDetailModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <!-- Modal Header -->
                    <div class="modal-header bg-primary">
                        <h4 class="modal-title" id="taskDetailModalLabel">
                            <i class="fas fa-tasks mr-2"></i>
                            Detalle de Tarea
                        </h4>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
        
                    <!-- Modal Body -->
                    <div class="modal-body">
                        <div class="card card-primary card-outline">
                            <div class="card-body">
                                <!-- Task Title -->
                                <h3 class="text-primary" id="taskTitle"></h3>
                                
                                <!-- Task Description -->
                                <div class="mt-4">
                                    <h5 class="text-bold">
                                        <i class="fas fa-file-alt mr-2"></i>
                                        Descripción
                                    </h5>
                                    <p id="taskDescription" class="text-muted"></p>
                                </div>

                                <!-- Money Info Box -->
                                <div class="mt-4">
                                    <div class="info-box bg-gradient-warning">
                                        <span class="info-box-icon">
                                            <i class="fas fa-money-bill-wave"></i>
                                        </span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">
                                                <h5 class="text-bold text-dark mb-0">Recompensa</h5>
                                            </span>
                                            <span class="info-box-number text-dark" id="reward"></span>
                                            <div class="progress">
                                                <div class="progress-bar" style="width: 100%"></div>
                                            </div>
                                            <span class="progress-description text-dark">
                                                Pago disponible al completar la tarea y verificarlo con un Administrador de la Empresa
                                            </span>
                                        </div>
                                    </div>
                                </div>
        
                                <!-- Task Info Boxes -->
                                <div class="row mt-4">
                                    <!-- Date Info Box -->
                                    <div class="col-md-6">
                                        <div class="info-box bg-gradient-info">
                                            <span class="info-box-icon">
                                                <i class="far fa-calendar-alt"></i>
                                            </span>
                                            <div class="info-box-content">
                                                <span class="info-box-text text-bold">Fecha</span>
                                                <span class="info-box-number" id="taskDate"></span>
                                            </div>
                                        </div>
                                    </div>
        
                                    <!-- Duration Info Box -->
                                    <div class="col-md-6">
                                        <div class="info-box bg-gradient-success">
                                            <span class="info-box-icon">
                                                <i class="far fa-clock"></i>
                                            </span>
                                            <div class="info-box-content">
                                                <span class="info-box-text text-bold">Duración estimada</span>
                                                <span class="info-box-number" id="taskDuration"></span>
                                                <span class="info-box-text" id="taskPrice"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
        
                                <!-- Company Info -->
                                <div class="mt-4">
                                    <div class="card card-widget widget-user-2">
                                        <div class="card-header">
                                            <h5 class="text-bold">
                                                <i class="fas fa-building mr-2"></i>
                                            </h5>
                                            <span class="info-box-number" id="nombre_empresa"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
        
                    <!-- Modal Footer -->
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">
                            <i class="fas fa-times mr-2"></i>
                            Cerrar
                        </button>
                        <button type="button" 
                                class="btn btn-success markAsCompletedAlert" 
                                data-id="{{ $task->id }}" 
                                data-location="{{ $task->empresa->location }}">
                            Marcar como completada
                        </button>                    
                    </div>
                </div>
            </div>
        </div>
        @endif
    </body>
@stop


@section('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

@stop

@section('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
$(document).ready(function() {
    $('.view-task-button').on('click', function() {
        let taskId = $(this).data('id'); // Obtener el id de la tarea

        // Generar la URL sin duplicar el prefijo usando el helper de Laravel
        let url = "{{ route('tareas.detalle', ['id' => ':id']) }}".replace(':id', taskId);

        // Realizar la solicitud AJAX para obtener los detalles de la tarea
        $.ajax({
            url: url,
            type: 'GET',
            success: function(response) {
                // Llenar el modal con los datos obtenidos de la respuesta
                $('#taskTitle').text(response.title);
                $('#taskDescription').text(response.description);
                
                // Formatear las fechas
                const startDate = new Date(response.start_date);
                const endDate = new Date(response.end_date);
                
                const options = { year: 'numeric', month: '2-digit', day: '2-digit' };
                const formattedStartDate = startDate.toLocaleDateString('es-ES', options);
                const formattedEndDate = endDate.toLocaleDateString('es-ES', options);
                
                $('#taskDate').text(`${formattedStartDate} - ${formattedEndDate}`);
                
                // Calcular la duración en días
                const durationInDays = Math.ceil((endDate - startDate) / (1000 * 60 * 60 * 24));
                $('#taskDuration').text(`${durationInDays} días`);

                $('#nombre_empresa').text(response.nombre_empresa);
                $('#taskPrice').text(response.price);
                $('#taskLocation').text(response.location);
                $('#reward').text(response.reward);
                $('#location_empresa').text(response.location_empresa);

                // Actualiza el ID del botón "Marcar como completada" con el ID correcto
                $('.markAsCompletedAlert').data('id', taskId);

                $('#taskDetailModal').modal('show');
            },
            error: function() {
                alert('Error al obtener los detalles de la tarea');
            }
        });
    });
});

    </script>
    <script>
$(document).ready(function() {
    // Cambiar de #markAsCompletedAlert a .markAsCompletedAlert
    $('.markAsCompletedAlert').on('click', function() {
        // Obtener la ubicación y el ID de la tarea desde los atributos data
        let locationEmpresa = $(this).data('location').trim();
        let taskId = $(this).data('id');

        // Crear contenido para mostrar en el Swal
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
        }).then((result) => {
            if (result.isConfirmed) {
                // Hacer la llamada AJAX para completar la tarea
                $.ajax({
                    url: `/usuario/perfil/tareas/completar_tarea/${taskId}`,
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        _method: 'PUT' // Especificar el método como PUT
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
                    error: function(xhr, status, error) {
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
    });
});
    </script>
@stop
