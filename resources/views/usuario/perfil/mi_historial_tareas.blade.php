@extends('adminlte::page')

@section('title', 'Mis Tareas')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
    <body>
        <!-- Resumen General -->
        <h4 class="mb-4">Historial de tareas completadas y pagadas</h4>
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
                        <span class="info-box-text">Tareas Completadas sin pagar</span>
                        <span class="info-box-number">{{ $completadas_sin_pagar }}</span>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="info-box">
                    <span class="info-box-icon bg-success"><i class="fas fa-check"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Tareas Completadas y pagadas</span>
                        <span class="info-box-number">{{ $completadas_pagadas }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de Tareas con Filtro -->
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Tarea</th>
                                <th>Estado</th>
                                <th>Fecha</th>
                                <th>Pago recibido</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tasks as $task)
                            <tr>
                                <td><strong>{{ $task->title }}</strong></td>
                                <td class="text-center">
                                    @if($task->paid)
                                        <span class="badge badge-success" style="font-size: 14px;">
                                            <i class="fas fa-check-circle mr-1"></i>PAGADO
                                        </span>
                                    @else
                                        <span class="badge badge-danger" style="font-size: 14px;">
                                            <i class="fas fa-times-circle mr-1"></i>PENDIENTE
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <small class="text-muted">
                                        <i class="fas fa-calendar-alt mr-1"></i>Empezó:
                                        {{ \Carbon\Carbon::parse($task->start_date)->format('d/m/Y') }}
                                    </small>
                                    <br>
                                    <small class="text-muted">
                                        <i class="fas fa-calendar-check mr-1"></i>Venció:
                                        {{ \Carbon\Carbon::parse($task->end_date)->format('d/m/Y') }}
                                    </small>
                                </td>
                                <td class="text-center">
                                    <h4>
                                        <span class="badge badge-info" style="font-size: 16px;">
                                            <i class="fas fa-coins mr-1"></i>
                                            {{ number_format($task->reward, 2) }} NEAR
                                        </span>
                                    </h4>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-primary btn-sm view-task-button" data-id="{{ $task->id }}">
                                        <i class="fas fa-eye mr-1"></i>Ver tarea
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">
                                    <div class="alert alert-warning mb-0">
                                        <i class="fas fa-exclamation-triangle mr-2"></i>
                                        No hay tareas que coincidan con los filtros
                                    </div>
                                </td>
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

@stop

@section('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            $('.view-task-button').on('click', function() {
                let taskId = $(this).data('id'); // Obtener el id de la tarea

                // Generar la URL usando el helper de Laravel
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
                                    <h5 class="text-bold">
                                        <i class="fas fa-file-alt mr-2"></i>
                                        Descripción
                                    </h5>
                                    <p class="text-muted">${response.description}</p>
                                    <h5 class="text-bold">
                                        <i class="fas fa-money-bill-wave mr-2"></i>
                                        Recompensa
                                    </h5>
                                    <p class="text-dark">${response.reward}</p>
                                    <h5 class="text-bold">
                                        <i class="far fa-calendar-alt mr-2"></i>
                                        Fecha
                                    </h5>
                                    <p>${formattedStartDate} - ${formattedEndDate}</p>
                                    <h5 class="text-bold">
                                        <i class="far fa-clock mr-2"></i>
                                        Duración Estimada
                                    </h5>
                                    <p>${durationInDays} días</p>
                                    <h5 class="text-bold">
                                        <i class="fas fa-building mr-2"></i>
                                        Empresa
                                    </h5>
                                    <p>${response.nombre_empresa}</p>
                                    <h5 class="text-bold">
                                        <i class="fas fa-map-marker-alt mr-2"></i>
                                        Ubicación
                                    </h5>
                                    <p>${response.location}</p>
                                </div>
                            `,
                            showCloseButton: true,
                            confirmButtonText: 'Cerrar',
                            width: '600px',
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
