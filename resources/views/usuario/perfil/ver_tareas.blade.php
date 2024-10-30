@extends('adminlte::page')

@section('title', 'Ver Tareas')

@section('content_header')
@stop

@section('content')
    <div class="container-fluid">
        <!-- Search Box -->
        <div class="card card-success">
            <div class="card-header">
                <h3 class="card-title">Introduce tu ubicación y cuántos kilómetros deseas buscar tareas</h3>
            </div>
            <div class="card-body">
                <div class="input-group">
                    <!-- Campo para mostrar las coordenadas obtenidas -->
                    <input type="text" id="location-display" class="form-control" placeholder="Se almacenará tu latitud y longitud" readonly>
                    
                    <!-- Campo para ingresar la distancia en kilómetros -->
                    <input type="number" min="1" max="1000" id="kilometers-display" class="form-control" placeholder="A cuántos kilómetros deseas buscar tareas">
        
                    <div class="input-group-append">
                        <!-- Botón para obtener la ubicación -->
                        <button class="btn btn-success" onclick="obtenerUbicacion()">
                            <i class="fas fa-map-marker-alt"></i> Obtener Ubicación
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Campo oculto para almacenar las coordenadas (latitud, longitud) y la distancia -->
        <input type="hidden" id="location" name="location" />
        <input type="hidden" id="kilometers" name="kilometers" />
        
        <!-- Map Display -->
        <div class="card card-success mt-3">
            <div class="card-header">
                <h3 class="card-title">Mapa de Tareas Filtradas</h3>
            </div>
            <div class="card-body">
                <div id="map" style="height: 500px; width: 100%;"></div>
            </div>
        </div>
@stop

@section('css')
@stop
@section('js')
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBUMODgP-YRcT4swyj97MoXzGHE847idfs&callback=initMap"></script>
<script>
    let tasks = @json($tasks);  // Cargar las tareas en formato JSON desde el backend

    function initMap() {
        const defaultLocation = { lat: 21.82971537500233, lng: -105.21999395243319 };
        const mapElement = document.getElementById("map");

        if (!mapElement) {
            console.error("Elemento 'map' no encontrado.");
            return;
        }

        const map = new google.maps.Map(mapElement, {
            zoom: 10,
            center: defaultLocation,
        });

        // Crear marcadores para cada tarea
        tasks.forEach(task => {
            const [taskLat, taskLng] = task.location.split(',').map(coord => parseFloat(coord.trim()));

            const marker = new google.maps.Marker({
                position: { lat: taskLat, lng: taskLng },
                map: map,
                title: task.title
            });

            const startDate = new Date(task.start_date).toLocaleDateString(); 
            const endDate = new Date(task.end_date).toLocaleDateString();

            // Contenido del InfoWindow para cada tarea
            const infoWindowContent = `
            <div style="
                font-family: 'Arial', sans-serif;
                padding: 24px;
                max-width: 450px;
                min-width: 400px;
                background: linear-gradient(to bottom right, #f7fdf7, #ffffff);
                border-radius: 12px;
                box-shadow: 0 3px 8px rgba(0,0,0,0.12);
            ">
                <h3 style="
                    color: #2e7d32;
                    margin: 0 0 16px 0;
                    font-size: 22px;
                    border-bottom: 2px solid #a5d6a7;
                    padding-bottom: 12px;
                ">${task.title}</h3>
                
                <p style="
                    color: #37474f;
                    margin: 12px 0;
                    font-size: 15px;
                    line-height: 1.5;
                ">${task.description}</p>

                <div style="
                    background: #f1f8e9;
                    border-radius: 8px;
                    padding: 20px;
                    margin: 16px 0;
                ">
                    <p style="margin: 10px 0; font-size: 15px;">
                        <strong style="color: #1b5e20;">Empresa:</strong> 
                        <span style="margin-left: 8px;">${task.empresa.name}</span>
                    </p>
                    <p style="margin: 10px 0; font-size: 15px;">
                        <strong style="color: #1b5e20;">Fecha de inicio:</strong> 
                        <span style="margin-left: 8px;">${startDate}</span>
                    </p>
                    <p style="margin: 10px 0; font-size: 15px;">
                        <strong style="color: #1b5e20;">Fecha de finalización:</strong> 
                        <span style="margin-left: 8px;">${endDate}</span>
                    </p>
                    <p style="margin: 10px 0; font-size: 15px;">
                        <strong style="color: #1b5e20;">Ubicación:</strong> 
                        <span style="margin-left: 8px;">${task.location}</span>
                    </p>
                    
                    <div style="
                        display: inline-block;
                        background: #81c784;
                        color: #1b5e20;
                        padding: 8px 16px;
                        border-radius: 24px;
                        font-weight: bold;
                        margin-top: 12px;
                        font-size: 16px;
                    ">
                        <strong>Recompensa:</strong> ${task.reward}
                    </div>
                </div>

                <form action="${`/usuario/perfil/tareas/aceptar/${task.id}`}" method="POST" style="display: inline;">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="_method" value="PUT">
                    <button type="submit" style="
                        background: #43a047;
                        color: white;
                        border: none;
                        padding: 12px 24px;
                        border-radius: 6px;
                        font-size: 16px;
                        cursor: pointer;
                        transition: all 0.3s ease;
                        width: 100%;
                        margin-top: 12px;
                        font-weight: 600;
                    " onmouseover="this.style.background='#2e7d32'" 
                      onmouseout="this.style.background='#43a047'">
                        Aceptar tarea
                    </button>
                </form>
            </div>
        `;

            const infoWindow = new google.maps.InfoWindow({
                content: infoWindowContent,
            });

            // Mostrar el InfoWindow al hacer clic en el marcador
            marker.addListener("click", () => {
                infoWindow.open(map, marker);
            });
        });
    }

    function obtenerUbicacion() {
        const maxDistance = document.getElementById('kilometers-display').value;

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                const latitude = position.coords.latitude;
                const longitude = position.coords.longitude;
                const coordenadas = `${latitude},${longitude}`;
                
                window.location.href = `?location=${coordenadas}&kilometers=${maxDistance}`;
            }, function(error) {
                alert("Error al obtener la ubicación: " + error.message);
            });
        } else {
            alert("La geolocalización no es compatible con este navegador.");
        }
    }

    document.addEventListener("DOMContentLoaded", initMap);
</script>
@stop