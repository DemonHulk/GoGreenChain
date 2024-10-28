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
          src="{{ $user->profile_photo_path ? asset('storage/' . $user->profile_photo_path) : asset('storage/default-profile.png') }}"
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
            <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>

            <strong><i class="fas fa-map-marker-alt mr-1"></i> Dirección:</strong>
            <input type="text" name="address" class="form-control" value="{{ $user->address }}" required>
            <input type="text" name="city" class="form-control mt-2" value="{{ $user->city }}" required>
            <input type="text" name="state" class="form-control mt-2" value="{{ $user->state }}" required>

            <strong><i class="fas fa-mail-bulk mr-1"></i> Código Postal:</strong>
            <input type="text" name="postal_code" class="form-control" value="{{ $user->postal_code }}" required>
          </div>
          
          <div class="col-md-6">
            <strong><i class="fas fa-phone mr-1"></i> Teléfono:</strong>
            <input type="text" name="phone" class="form-control" value="{{ $user->phone }}" required>

            <!-- Campo para almacenar la ubicación -->
            <input type="hidden" id="location" name="location" />

            <!-- Botón para obtener ubicación -->
            <button type="button" class="btn btn-success mt-3" onclick="obtenerUbicacion()">Obtener Ubicación</button>

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
                  @foreach ($activeTasks as $task)
                      <li>
                          <span class="text">{{ $task->title }}</span>
                          <small class="badge badge-danger">
                              <i class="far fa-clock"></i> Vence: {{ \Carbon\Carbon::parse($task->end_date)->format('d M Y') }}
                          </small>
                      </li>
                  @endforeach
              </ul>
          </div>
          <div class="card-footer">
              <a href="{{ route('empresa.perfil.registrar_tarea') }}" class="btn btn-primary btn-sm">
                  <i class="fas fa-plus"></i> Crear nueva tarea
              </a>
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
                  @foreach ($completedTasks as $task)
                      <li>
                          <span class="text">{{ $task->title }}</span>
                          <small class="badge badge-success">
                              Completada: {{ \Carbon\Carbon::parse($task->updated_at)->format('d M Y') }}
                          </small>
                      </li>
                  @endforeach
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
@stop

@section('js')
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
    
    <script>
      function obtenerUbicacion() {
          if (navigator.geolocation) {
              navigator.geolocation.getCurrentPosition(mostrarPosicion, manejarError);
          } else {
              alert("La geolocalización no es compatible con este navegador.");
          }
      }
  
      function mostrarPosicion(position) {
          const latitud = position.coords.latitude;
          const longitud = position.coords.longitude;
          document.getElementById("location").value = `${latitud}, ${longitud}`; // Almacena latitud y longitud
          obtenerDireccion(latitud, longitud);
      }
  
      function obtenerDireccion(lat, lon) {
          const url = `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}&addressdetails=1`;
          fetch(url)
              .then(response => response.json())
              .then(data => {
                  const direccion = data.display_name;
                  const ciudad = data.address.city || data.address.town || data.address.village || "No disponible";
                  const estado = data.address.state || "No disponible";
                  const cp = data.address.postcode || "No disponible";
  
                  document.querySelector("input[name='address']").value = direccion; // Completa el campo de dirección
                  document.querySelector("input[name='city']").value = ciudad; // Completa el campo de ciudad
                  document.querySelector("input[name='state']").value = estado; // Completa el campo de estado
                  document.querySelector("input[name='postal_code']").value = cp; // Completa el campo de código postal
              })
              .catch(error => {
                  console.error("Error al obtener la dirección:", error);
              });
      }
  
      function manejarError(error) {
          switch(error.code) {
              case error.PERMISSION_DENIED:
                  alert("Usuario denegó la solicitud de Geolocalización.");
                  break;
              case error.POSITION_UNAVAILABLE:
                  alert("La ubicación no está disponible.");
                  break;
              case error.TIMEOUT:
                  alert("La solicitud para obtener la ubicación ha expirado.");
                  break;
              case error.UNKNOWN_ERROR:
                  alert("Ocurrió un error desconocido.");
                  break;
          }
      }
  </script>
  
@stop