@extends('adminlte::page')

@section('title', 'Mi PeVer Tareasrfil')

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
                        <span class="info-box-number">{{ $tasks->where('status', 'pending')->count() }}</span>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="info-box">
                    <span class="info-box-icon bg-success"><i class="fas fa-check"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Tareas Completadas</span>
                        <span class="info-box-number">{{ $tasks->where('status', 'completed')->count() }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lista de Tareas -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Lista de Tareas</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <tbody>
                            @foreach ($tasks as $task)
                            <tr>
                                <td style="width: 40px;">
                                    <div class="icheck-primary">
                                        <input type="checkbox" id="task{{ $task->id }}" {{ $task->status == 'completed' ? 'checked' : '' }}>
                                        <label for="task{{ $task->id }}"></label>
                                    </div>
                                </td>
                                <td>
                                    <strong>{{ $task->title }}</strong><br>
                                    {{ $task->description }}
                                </td>
                                <td class="text-right text-muted">
                                    <small>{{ $task->status == 'completed' ? 'Completado el ' . $task->updated_at->format('d M, Y') : 'Vence ' . $task->end_date->format('d M, Y') }}</small>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
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
