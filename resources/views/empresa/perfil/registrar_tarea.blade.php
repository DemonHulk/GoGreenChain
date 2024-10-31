@extends('adminlte::page')

@section('title', 'Registrar tareas')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Registrar Nueva Tarea</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Inicio</a></li>
                    <li class="breadcrumb-item active">Registrar Tarea</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-tasks mr-2"></i>Crear Nueva Tarea</h3>
                </div>
                <form action="{{ route('guardar_tarea') }}" method="POST" id="taskForm">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <!-- Título de la Tarea -->
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="taskTitle">
                                        <i class="fas fa-heading mr-1"></i>Título de la Tarea
                                    </label>
                                    <input type="text" class="form-control" id="taskTitle" name="title" 
                                           placeholder="Ingresa el título de la tarea" required>
                                </div>
                            </div>

                            <!-- Descripción de la Tarea -->
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="taskDescription">
                                        <i class="fas fa-align-left mr-1"></i>Descripción de la Tarea
                                    </label>
                                    <textarea class="form-control" id="taskDescription" name="description" 
                                        rows="4" placeholder="Ingresa la descripción detallada de la tarea" required></textarea>
                                </div>
                            </div>

                            <!-- Fechas -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>
                                        <i class="far fa-calendar-alt mr-1"></i>Fecha de Inicio
                                    </label>
                                    <div class="input-group date" id="startDatePicker" data-target-input="nearest">
                                        <input type="text" class="form-control datetimepicker-input" 
                                               data-target="#startDatePicker" name="start_date" required/>
                                        <div class="input-group-append" data-target="#startDatePicker" 
                                             data-toggle="datetimepicker">
                                            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>
                                        <i class="far fa-calendar-check mr-1"></i>Fecha de Fin
                                    </label>
                                    <div class="input-group date" id="endDatePicker" data-target-input="nearest">
                                        <input type="text" class="form-control datetimepicker-input" 
                                               data-target="#endDatePicker" name="end_date" required/>
                                        <div class="input-group-append" data-target="#endDatePicker" 
                                             data-toggle="datetimepicker">
                                            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Recompensa -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>
                                        <i class="fas fa-coins mr-1"></i>Recompensa (en Near)
                                    </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="fas fa-dollar-sign"></i>
                                            </span>
                                        </div>
                                        <input type="number" class="form-control" name="reward" 
                                               placeholder="Cantidad" min="0.1" max="25" required>
                                    </div>
                                </div>
                            </div>

                            <!-- Tipo de Tarea -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>
                                        <i class="fas fa-tags mr-1"></i>Tipo de Tarea
                                    </label>
                                    <select class="form-control select" name="task_type" style="width: 100%;" required>
                                        <option selected disabled>Selecciona un tipo de tarea</option>
                                        <option value="Servicio Comunitario">
                                            <i class="fas fa-hands-helping"></i> Servicio Comunitario
                                        </option>
                                        <option value="Ambiental">
                                            <i class="fas fa-leaf"></i> Ambiental
                                        </option>
                                        <option value="Educativa">
                                            <i class="fas fa-graduation-cap"></i> Educativa
                                        </option>
                                        <option value="Técnica">
                                            <i class="fas fa-tools"></i> Técnica
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <!-- Ubicación -->
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>
                                        <i class="fas fa-map-marker-alt mr-1"></i>Ubicación de la Tarea
                                    </label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="location" 
                                               id="location" placeholder="Ingresa la ubicación" required>
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-info" id="getCurrentLocation">
                                                <i class="fas fa-location-arrow"></i> Usar ubicación actual
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-2"></i>Publicar Tarea
                        </button>
                        <button type="reset" class="btn btn-secondary float-right">
                            <i class="fas fa-undo mr-2"></i>Restablecer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/css/tempusdominus-bootstrap-4.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container--default .select2-selection--single {
            height: calc(2.25rem + 2px);
            padding: .375rem .75rem;
            border: 1px solid #ced4da;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 31px;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 24px;
        }
        .card-primary:not(.card-outline) > .card-header {
            background-color: #007bff;
        }
    </style>
@stop

@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/js/tempusdominus-bootstrap-4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('.select2').select2({
                theme: 'bootstrap4'
            });

            // Initialize Datetime pickers
            $('#startDatePicker').datetimepicker({
                format: 'YYYY-MM-DD',
                minDate: moment()
            });

            $('#endDatePicker').datetimepicker({
                format: 'YYYY-MM-DD',
                useCurrent: false
            });

            // Ensure end date is after start date
            $("#startDatePicker").on("change.datetimepicker", function (e) {
                $('#endDatePicker').datetimepicker('minDate', e.date);
            });

            $("#endDatePicker").on("change.datetimepicker", function (e) {
                $('#startDatePicker').datetimepicker('maxDate', e.date);
            });

            $('#getCurrentLocation').click(function() {
                if ("geolocation" in navigator) {
                    navigator.geolocation.getCurrentPosition(
                        function(position) {
                            const latitude = position.coords.latitude;
                            const longitude = position.coords.longitude;
                            
                            $('#location').val(`${latitude}, ${longitude}`);
                            
                            Swal.fire({
                                icon: 'success',
                                title: 'Ubicación obtenida',
                                text: 'Se ha establecido tu ubicación actual'
                            });
                        },
                        function(error) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'No se pudo obtener la ubicación: ' + error.message
                            });
                        },
                        { enableHighAccuracy: true } // Aquí se establece alta precisión
                    );
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Tu navegador no soporta geolocalización'
                    });
                }
            });

            // Form validation
            $('#taskForm').on('submit', function(e) {
                e.preventDefault();
                
                // Validate form
                let isValid = true;
                $(this).find('[required]').each(function() {
                    if (!$(this).val()) {
                        isValid = false;
                        $(this).addClass('is-invalid');
                    } else {
                        $(this).removeClass('is-invalid');
                    }
                });

                if (!isValid) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Por favor completa todos los campos requeridos'
                    });
                    return false;
                }

                // Submit form if valid
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: "¿Deseas publicar esta tarea?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, publicar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit();
                    }
                });
            });
        });
    </script>
@stop