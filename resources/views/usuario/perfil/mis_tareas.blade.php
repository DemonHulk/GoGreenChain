@extends('adminlte::page')

@section('title', 'Mis Tareas')

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
                        <span class="info-box-number">{{ $totalTasks }}</span>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="info-box">
                    <span class="info-box-icon bg-warning"><i class="fas fa-clock"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Tareas Activas</span>
                        <span class="info-box-number">{{ $pendingTasksCount }}</span>
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
                                <option value="pending_unassigned" {{ request('status') == 'pending_unassigned' ? 'selected' : '' }}>
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
                                        @switch($task->status)
                                            @case('pending')
                                                <span class="badge bg-warning text-dark">Pendiente</span>
                                                @break
                                            @case('accepted')
                                                <span class="badge bg-info text-white">En Proceso</span>
                                                @break
                                            @case('completed')
                                                <span class="badge bg-success text-white">Completada</span>
                                                @break
                                            @default
                                                <span class="badge bg-secondary text-white">{{ $task->status }}</span>
                                        @endswitch
                                    </td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($task->start_date)->format('d/m/Y') }} - 
                                        {{ \Carbon\Carbon::parse($task->end_date)->format('d/m/Y') }}
                                    </td>
                                    <td>
                                        <!-- Boton pendiente para que abra el modal -->

                                        <button type="button" class="btn btn-primary btn-sm">
                                            Ver tarea
                                        </button>
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
@stop

@section('js')
    <script src="https://kit.fontawesome.com/42813926db.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function showTaskDetails(id, title, description, startDate, endDate, reward, location, taskType) {
            // Formatear las fechas
            const start = new Date(startDate).toLocaleDateString('es-ES');
            const end = new Date(endDate).toLocaleDateString('es-ES');
            
            // Actualizar el contenido del modal
            document.getElementById('taskTitle').textContent = title;
            document.getElementById('taskDescription').textContent = description;
            document.getElementById('taskDate').textContent = `${start} - ${end}`;
            document.getElementById('taskLocation').textContent = location;
            document.getElementById('taskRequirements').textContent = taskType;
            document.getElementById('taskPrice').textContent = `Recompensa: ${reward} tokens`;
            
            // Calcular duración en días
            const startDateTime = new Date(startDate);
            const endDateTime = new Date(endDate);
            const duration = Math.ceil((endDateTime - startDateTime) / (1000 * 60 * 60 * 24));
            document.getElementById('taskDuration').textContent = `${duration} días`;
            
            // Mostrar el modal
            $('#taskDetailModal').modal('show');
        }
        </script>
@stop
