@extends('adminlte::page')

@section('title', 'Ver Tareas')

@section('content')
    <body>
        <!-- Resumen General -->
        <h4 class="mb-4">Resumen de Tareas</h4>

        <!-- Info Boxes -->
        <div class="row">
            <div class="col-md-4">
                <div class="info-box">
                    <span class="info-box-icon bg-info"><i class="fas fa-tasks"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total de Tareas</span>
                        <span class="info-box-number">{{ $tasks->count() }}</span>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="info-box">
                    <span class="info-box-icon bg-warning"><i class="fas fa-clock"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Tareas Activas</span>
                        <span class="info-box-number">{{ $pendienteCount + $aceptadaCount  }}</span> <!-- Total de tareas pendientes -->
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="info-box">
                    <span class="info-box-icon bg-success"><i class="fas fa-check"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Tareas Completadas</span>
                        <span class="info-box-number">{{ $completadaCount }}</span> <!-- Total de tareas completadas -->
                    </div>
                </div>
            </div>
            
        </div>
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Lista de Tareas</h3>
                
            <!-- Filtros -->
            <div class="mt-3">
                <form action="{{ route('empresa.perfil.ver_tareas') }}" method="GET" class="row">
                    <div class="col-md-4">
                        <select name="status" class="form-control" onchange="this.form.submit()">
                            <option value="">Todos los estados</option>
                            <option value="pendiente" {{ request('status') == 'pendiente' ? 'selected' : '' }}>
                                Pendientes sin asignar ({{ $pendienteCount }})
                            </option>
                            <option value="aceptada" {{ request('status') == 'aceptada' ? 'selected' : '' }}>
                                Pendientes asignadas ({{ $aceptadaCount }})
                            </option>
                            <option value="completada" {{ request('status') == 'completada' ? 'selected' : '' }}>
                                Completadas ({{ $completadaCount }})
                            </option>
                        </select>
                    </div>
                </form>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Tarea</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($tasks as $task)
                                <tr>
                                    <td>
                                        <strong>{{ $task->title }}</strong><br>
                                        {{ $task->description }}
                                    </td>
                                    <td>
                                        @php
                                            $status = strtolower($task->status);
                                            $statusClass = in_array($status, ['pendiente', 'aceptada', 'completada']) ? $status : 'default';
                                        @endphp
                                    
                                        <span class="status-{{ $statusClass }}">
                                            {{ ucfirst($task->status) }}
                                        </span>
                                    
                                        {{-- Mostrar el usuario si el estado es "aceptada" o "completada" --}}
                                        @if($status === 'aceptada' || $status === 'completada')
                                            <span class="ml-2 text-muted">
                                                por <a href="{{ route('ver_perfil', ['id' => $task->usuario->id]) }}" class="text-primary">
                                                    {{ $task->usuario->name }}
                                                </a>
                                    
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <button type="button" 
                                                class="btn btn-primary btn-sm"
                                                data-toggle="modal" 
                                                data-target="#taskDetailModal"
                                                data-task-id="{{ $task->id }}"
                                                onclick="loadTaskDetails({{ $task->id }})">
                                            Ver tarea
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">No hay tareas que coincidan con los filtros</td>
                                </tr>
                            @endforelse
                        </tbody>
                        </table>
                </div>
            </div>
        </div>

        <div class="modal fade" id="taskDetailModal" tabindex="-1" role="dialog" aria-labelledby="taskDetailModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="taskDetailModalLabel">Detalle de Tarea</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="card">
                            <div class="card-body">
                                <h4 id="taskTitle"></h4>
            
                                <div class="mt-4">
                                    <h5>Descripción</h5>
                                    <p id="taskDescription"></p>
                                </div>
            
                                <div class="row mt-4">
                                    <div class="col-md-6">
                                        <div class="info-box bg-light">
                                            <div class="info-box-content">
                                                <span class="info-box-text">Fecha</span>
                                                <span class="info-box-number" id="taskDate"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-box bg-light">
                                            <div class="info-box-content">
                                                <span class="info-box-text">Duración estimada</span>
                                                <span class="info-box-number" id="taskDuration"></span><br>
                                                <span class="info-box-text mt-2" id="taskPrice"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
            
                                <div class="mt-4 float-left w-100" style="clear: both;">
                                    <div class="text-left w-100">
                                        <h5 class="text-left">Cliente</h5>
                                        <div class="d-flex align-items-center justify-content-start">
                                            <div class="text-left">
                                                <h6 class="mb-0">{{ $user->name }}</h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <h5>Tipo de tarea</h5>
                                    <ul class="list-unstyled" id="taskRequirements">
                                    </ul>
                                </div>
                                <div class="mt-4">
                                    <h5>Ubicación</h5>
                                    <p><i class="fas fa-map-marker-alt"></i> <span id="taskLocation"></span></p>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    </body>
@stop


@section('css')
<style>
    /* Estilo para el estado 'pendiente' */
    .status-pendiente {
    background-color: #ffeeba; /* Fondo amarillo claro */
    color: #856404;            /* Texto marrón oscuro */
    font-weight: bold;         /* Negrita */
    padding: 5px 10px;        /* Espaciado interno */
    border-radius: 5px;       /* Bordes redondeados */
    }

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

@section('js')
    <script src="https://kit.fontawesome.com/42813926db.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        function loadTaskDetails(taskId) {
            // Realizar una solicitud AJAX para obtener los detalles de la tarea
            $.ajax({
                url: "{{ route('empresa.perfil.obtenerTarea', '') }}/" + taskId,
                method: "GET",
                success: function(task) {
                    // Formatear las fechas
                    const startDate = new Date(task.start_date);
                    const endDate = new Date(task.end_date);

                    // Formatear las fechas a formato "dd/mm/yyyy"
                    const options = { day: '2-digit', month: '2-digit', year: 'numeric' };
                    const formattedStartDate = startDate.toLocaleDateString('es-ES', options);
                    const formattedEndDate = endDate.toLocaleDateString('es-ES', options);

                    // Calcular la duración en días
                    const durationInDays = Math.round((endDate - startDate) / (1000 * 60 * 60 * 24)); // Dif. en ms -> días

                    // Actualizar contenido del modal
                    $('#taskTitle').text(task.title);
                    $('#taskId').text(task.id);
                    $('#taskDescription').text(task.description);
                    $('#taskDate').html(`Desde ${formattedStartDate} <br> hasta ${formattedEndDate}`);
                    $('#taskDuration').text(`${durationInDays} días`);
                    $('#taskPrice').text(`Recompensa: ${task.reward} tokens`);

                    // Mostrar ubicación con enlace a Google Maps usando coordenadas
                    $('#taskLocation').html(`
                        ${task.location} 
                        <a href="https://www.google.com/maps/search/?api=1&query=${task.latitude},${task.longitude}" target="_blank" class="btn btn-link">
                            Ver ubicación
                        </a>
                    `);


                    // Mostrar el modal
                    $('#taskDetailModal').modal('show');
                },
                error: function(xhr, status, error) {
                    console.error("Error al obtener los detalles de la tarea:", error);
                }
            });
        }
    </script>
@stop
