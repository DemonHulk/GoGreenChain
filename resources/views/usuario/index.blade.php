@extends('adminlte::page')

@section('title', 'GoGreenChain')

@section('content_header')
@stop

@section('content')
<div class="card card-primary card-outline">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-wallet mr-2"></i>
            Balance de mi cartera Near
        </h3>
    </div>
    <div class="card-body">
        <!-- Balance Amount -->
        <div class="text-center mb-5">
            <?php
            // Convertir yoctoNEAR a NEAR dividiendo entre 10^24
            $nearBalance = bcdiv($balance, '1000000000000000000000000', 5);
            ?>
            <h2 class="display-4 font-weight-bold mb-4"> {{ $nearBalance }} NEAR </h2>
            <p class="text-muted">
                Balance disponible 
                <i class="fas fa-info-circle" data-toggle="tooltip" title="Balance actual en su cuenta"></i>
            </p>
        </div>

        <!-- Action Buttons -->
        <div class="row">
            <div class="col-md-6">
                <div class="text-center mb-3">
                    <a href="{{ route('usuario.perfil.ver_tareas') }}" class="btn btn-success btn-lg btn-block">
                        <i class="fas fa-list-ul mr-2"></i>
                        Ver Tareas
                    </a>
                </div>
            </div>
            <div class="col-md-6">
                <div class="text-center mb-3">
                    <a href="{{ route('usuario.perfil.mi_historial_tareas') }}" class="btn btn-info btn-lg btn-block">
                        <i class="fas fa-history mr-2"></i>
                        Ver Historial
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Initialize tooltips -->
<script>
$(function () {
    $('[data-toggle="tooltip"]').tooltip()
})
</script>

@stop

@section('css')
@stop

@section('js')
<script>
  $(document).ready(function() {
    // Initialize the modal
    $('#taskDetailModal').modal({
      show: false
    });
  
    // Handle the "Ver tarea" button click
    $('.button.btn-primary').click(function() {
      $('#taskDetailModal').modal('show');
    });
  });
  </script>

@stop