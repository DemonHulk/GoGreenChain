@extends('adminlte::page')

@section('title', 'GoGreenChain')

@section('content_header')
@stop

@section('content')
<!-- Company Profile Card -->
<div class="card card-primary card-outline">
    <div class="card-body box-profile">
      <div class="d-flex align-items-center mb-3">
        <div class="mr-3">
          <img class="profile-user-img img-fluid img-circle"
               src="/storage/gogreenchain.jpg"
               alt="Company logo">
        </div>
        <div>
          <h3 class="profile-username"> {{$user->name}}</h3>
          <p class="text-muted mb-0">Innovación líder en tecnología blockchain</p>
        </div>
        <div class="ml-auto">
          <button id="editButton" class="btn btn-sm btn-primary">Editar Empresa</button>
        </div>
      </div>
  
    <!-- Tabla de datos del usuario -->
<div id="userDetails">
  <div class="row">
    <div class="col-md-6">
      <strong><i class="fas fa-envelope mr-1"></i> Correo Electrónico:</strong>
      <p class="text-muted">{{$user->email}}</p>

      <strong><i class="fas fa-map-marker-alt mr-1"></i> Dirección:</strong>
      <p class="text-muted">{{$user->address}}, {{$user->city}}, {{$user->state}}</p>

      <strong><i class="fas fa-mail-bulk mr-1"></i> Código Postal:</strong>
      <p class="text-muted">{{$user->postal_code}}</p>
    </div>
    
    <div class="col-md-6">
      <strong><i class="fas fa-phone mr-1"></i> Teléfono:</strong>
      <p class="text-muted">{{$user->phone}}</p>
      
      <strong><i class="fas fa-calendar mr-1"></i> Establecida:</strong>
      <p class="text-muted">{{$user->created_at}}</p>
    </div>
  </div>
</div>

<!-- Formulario de edición (oculto inicialmente) -->
<div class="row" id="editForm" style="display: none;">
  <form action="{{ route('empresa.actualizar_empresa', $user->id) }}" method="POST">
    @csrf
    @method('PUT')
    
    <div class="col-md-6">
      <strong><i class="fas fa-envelope mr-1"></i> Correo Electrónico:</strong>
      <input type="email" name="email" class="form-control" value="{{ $user->email }}">

      <strong><i class="fas fa-map-marker-alt mr-1"></i> Dirección:</strong>
      <input type="text" name="address" class="form-control" value="{{ $user->address }}">
      <input type="text" name="city" class="form-control mt-2" value="{{ $user->city }}">
      <input type="text" name="state" class="form-control mt-2" value="{{ $user->state }}">

      <strong><i class="fas fa-mail-bulk mr-1"></i> Código Postal:</strong>
      <input type="text" name="postal_code" class="form-control" value="{{ $user->postal_code }}">
    </div>
    
    <div class="col-md-6">
      <strong><i class="fas fa-phone mr-1"></i> Teléfono:</strong>
      <input type="text" name="phone" class="form-control" value="{{ $user->phone }}">

      <button type="submit" class="btn btn-success mt-3">Guardar cambios</button>
      <button type="button" class="btn btn-secondary mt-3" id="cancelButton">Cancelar</button>
    </div>
  </form>
</div>

    </div>
  </div>
  
  <!-- Tasks Section -->
  <div class="row">
    <!-- Active Tasks -->
    <div class="col-md-6">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Tareas activas</h3>
        </div>
        <div class="card-body p-0">
          <ul class="todo-list" data-widget="todo-list">
            <li>
              <span class="text">Blockchain Integration</span>
              <small class="badge badge-danger"><i class="far fa-clock"></i> Due: 15 Mar 2025</small>
            </li>
            <li>
              <span class="text">Smart Contract Audit</span>
              <small class="badge badge-danger"><i class="far fa-clock"></i> Due: 22 Mar 2025</small>
            </li>
            <li>
              <span class="text">DApp Development</span>
              <small class="badge badge-danger"><i class="far fa-clock"></i> Due: 30 Mar 2025</small>
            </li>
          </ul>
        </div>
        <div class="card-footer">
          <button type="button" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Crear nueva tarea
          </button>
        </div>
      </div>
    </div>
  
    <!-- Completed Tasks -->
    <div class="col-md-6">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Tareas completas</h3>
        </div>
        <div class="card-body p-0">
          <ul class="todo-list" data-widget="todo-list">
            <li>
              <span class="text">Whitepaper Creation</span>
              <small class="badge badge-success">Completed: 1 Feb 2025</small>
            </li>
            <li>
              <span class="text">Token Launch</span>
              <small class="badge badge-success">Completed: 15 Feb 2025</small>
            </li>
            <li>
              <span class="text">Security Assessment</span>
              <small class="badge badge-success">Completed: 28 Feb 2025</small>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
  
  <!-- Payment History -->
  <div class="card">
    <div class="card-header">
      <h3 class="card-title">Historial de pagos</h3>
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-striped">
          <thead>
            <tr>
              <th>Fecha</th>
              <th>Descripción</th>
              <th>Monto</th>
              <th>Estado</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>10 Mar 2025</td>
              <td>Blockchain Consultation</td>
              <td>$5,000</td>
              <td><span class="badge badge-success">Completed</span></td>
            </tr>
            <tr>
              <td>25 Feb 2025</td>
              <td>Smart Contract Development</td>
              <td>$12,000</td>
              <td><span class="badge badge-success">Completed</span></td>
            </tr>
            <tr>
              <td>15 Feb 2025</td>
              <td>Security Audit</td>
              <td>$8,000</td>
              <td><span class="badge badge-success">Completed</span></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  
  <!-- Custom CSS -->
  <style>
  .todo-list {
    list-style: none;
    margin: 0;
    padding: 0;
  }
  
  .todo-list > li {
    border-left: 2px solid #e9ecef;
    padding: 10px 15px;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  
  .profile-user-img {
    width: 100px;
    height: 100px;
    object-fit: cover;
  }
  
  .badge {
    font-weight: normal;
    padding: 5px 10px;
  }
  </style>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script> console.log('Hi!'); </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
      document.getElementById('editButton').addEventListener('click', function() {
        document.getElementById('userDetails').style.display = 'none'; // Oculta los detalles del usuario
        document.getElementById('editForm').style.display = 'block'; // Muestra el formulario de edición
      });
    
      document.getElementById('cancelButton').addEventListener('click', function() {
        document.getElementById('editForm').style.display = 'none'; // Oculta el formulario de edición
        document.getElementById('userDetails').style.display = 'block'; // Muestra los detalles del usuario
      });
    </script>
    
    
@stop