@extends('adminlte::page')

@section('title', 'Registrar tareas')

@section('content')
    <body>
        <!-- Create Task Card -->
        <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">Crear Nueva Tarea</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('guardar_tarea') }}" method="POST">
                @csrf
                <!-- Título de la Tarea -->
                <div class="form-group">
                    <label for="taskTitle">Título de la Tarea</label>
                    <input type="text" class="form-control" id="taskTitle" name="title" placeholder="Ingresa el título de la tarea" required>
                </div>
            
                <!-- Descripción de la Tarea -->
                <div class="form-group">
                    <label for="taskDescription">Descripción de la Tarea</label>
                    <textarea class="form-control" id="taskDescription" name="description" rows="4" placeholder="Ingresa la descripción de la tarea" required></textarea>
                </div>
            
                <!-- Rango de Fechas -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Fecha de Inicio</label>
                            <div class="input-group date" data-target-input="nearest">
                                <input type="date" class="form-control datetimepicker-input" id="startDate" name="start_date" placeholder="YYYY-MM-DD" required>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Fecha de Fin</label>
                            <div class="input-group date" data-target-input="nearest">
                                <input type="date" class="form-control datetimepicker-input" id="endDate" name="end_date" placeholder="YYYY-MM-DD" required>
                            </div>
                        </div>
                    </div>
                </div>
            
                <!-- Recompensa -->
                <div class="form-group">
                    <label>Recompensa (en tokens)</label>
                    <input type="number" class="form-control" name="reward" placeholder="Ingresa la cantidad de recompensa" required>
                </div>
            
                <!-- Ubicación de la Tarea -->
                <div class="form-group">
                    <label>Ubicación de la Tarea</label>
                    <input type="text" class="form-control" name="location" placeholder="Ingresa la ubicación o usa la ubicación actual" required>
                </div>
            
                <!-- Tipo de Tarea -->
                <div class="form-group">
                    <label>Tipo de Tarea</label>
                    <select class="form-control" name="task_type" required>
                        <option selected disabled>Selecciona un tipo de tarea</option>
                        <option value="Servicio Comunitario">Servicio Comunitario</option>
                        <option value="Ambiental">Ambiental</option>
                        <option value="Educativa">Educativa</option>
                        <option value="Técnica">Técnica</option>
                    </select>
                </div>
            
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary btn-block">Publicar Tarea</button>
                </div>
            </form>
            
        </div>
        </div>

<!-- Required JavaScript -->
<script>
$(function () {
  //Initialize Select2 Elements
  $('.select2').select2();

  //Initialize Datetimepicker
  $('#startDate').datetimepicker({
    format: 'L'
  });
  $('#endDate').datetimepicker({
    format: 'L'
  });
});
</script>

<!-- Additional CSS -->
<style>
.card-primary:not(.card-outline) > .card-header {
  background-color: #007bff;
}

.select2-container--default .select2-selection--multiple {
  border-color: #ced4da;
}

.input-group-text {
  border-color: #ced4da;
}
</style>
    </body>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script src="https://kit.fontawesome.com/42813926db.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

@stop
