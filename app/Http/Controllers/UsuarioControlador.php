<?php

namespace App\Http\Controllers;

use App\Models\CobrosModelo;
use App\Models\Tasks;
use App\Models\User;
use App\Providers\NearService;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
class UsuarioControlador extends Controller
{
   
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
            // Obtener el usuario autenticado
        $user = Auth::user();

        // Llamar a la API de Near para obtener el balance
        $balance = $this->getNearBalance($user->username_wallet);

        // Retornar una vista con los datos del usuario y el balance

        return view('usuario.index', compact('user', 'balance'));
    }
    
    
    public function ver_tareas_usuario(Request $request)
    {
        // Obtener el usuario autenticado
        $user = Auth::user();
    
        // Separar la ubicación del usuario en latitud y longitud si está disponible
        if ($user->location) {
            [$userLat, $userLng] = explode(',', $user->location);
        } else {
            // Si no hay ubicación en el perfil del usuario, retorna la vista sin tareas
            return view('usuario.perfil.ver_tareas', ['user' => $user, 'tasks' => json_encode([])]);
        }
    
        // Obtener la ubicación ingresada y la distancia máxima en kilómetros
        $userCoordinates = $request->input('location');
        $maxDistance = $request->input('kilometers') ?: 10;        
        
        // Inicializar la colección de tareas
        $tasks = collect();
        
        if ($userCoordinates) {
            [$lat, $lng] = explode(',', $userCoordinates);
    
            // Consulta para calcular la distancia con la fórmula Haversine utilizando Eloquent
            $tasks = Tasks::with('empresa') 
                ->selectRaw("*, 
                    (6371 * acos(
                        cos(radians(?)) * 
                        cos(radians(CAST(SPLIT_PART(location, ',', 1) AS DOUBLE PRECISION))) * 
                        cos(radians(CAST(SPLIT_PART(location, ',', 2) AS DOUBLE PRECISION)) - radians(?)) + 
                        sin(radians(?)) * 
                        sin(radians(CAST(SPLIT_PART(location, ',', 1) AS DOUBLE PRECISION)))
                    )) AS distance", [$lat, $lng, $lat])
                ->where('status', 'pendiente')
                ->whereRaw("(6371 * acos(
                        cos(radians(?)) * 
                        cos(radians(CAST(SPLIT_PART(location, ',', 1) AS DOUBLE PRECISION))) * 
                        cos(radians(CAST(SPLIT_PART(location, ',', 2) AS DOUBLE PRECISION)) - radians(?)) + 
                        sin(radians(?)) * 
                        sin(radians(CAST(SPLIT_PART(location, ',', 1) AS DOUBLE PRECISION)))
                    )) <= ?", [$lat, $lng, $lat, $maxDistance])
                ->orderBy("distance")
                ->get();
        }
    
        return view('usuario.perfil.ver_tareas', compact('user', 'tasks'));
    }
    
    public function aceptar_tarea_usuario($id)
    {
        try {
            // Obtener el usuario autenticado
            $user = Auth::user();
            
            // Buscar la tarea
            $tarea = Tasks::findOrFail($id);
            
            // Actualizar la tarea
            $tarea->update([
                'status' => 'aceptada',
                'id_usuario' => $user->id
            ]);
            
            // Redirigir a la vista de mis tareas del usuario después de aceptar la tarea
            return redirect()->route('usuario.perfil.mis_tareas')->with('success', 'Tarea aceptada correctamente');
            
        } catch (\Exception $e) {
            return redirect()->route('usuario.perfil.ver_tareas')->with('error', 'Ocurrió un error al aceptar la tarea');
        }
    }
    

    public function mis_tareas_usuario(Request $request)
    {
        // Obtener el usuario autenticado
        $user = Auth::user();
        
        // Obtener el filtro seleccionado en la vista
        $status = $request->input('status');
        
        // Iniciar la consulta base
        $tasksQuery = Tasks::where('id_usuario', $user->id);
        
        // Aplicar filtros según el estado seleccionado
        if ($status) {
            $tasksQuery->where('status', $status);
        }

        // Ordenar tareas para mostrar primero las 'aceptada' y luego las 'completada'
        $tasks = $tasksQuery->with('empresa')
            ->select('id', 'title', 'status', 'id_empresa', 'start_date', 'end_date', 'reward', 'location')
            ->orderByRaw("CASE WHEN status = 'aceptada' THEN 1 WHEN status = 'aceptada' THEN 2 ELSE 3 END") 
            ->get();
        
        // Obtener conteos para cada estado
        $totalTasks = Tasks::where('id_usuario', $user->id)->count();
        $aceptadaTasksCount = Tasks::where('id_usuario', $user->id)->where('status', 'aceptada')->count();
        $completadaTasksCount = Tasks::where('id_usuario', $user->id)->where('status', 'completada')->count();
        
        return view('usuario.perfil.mis_tareas', compact(
            'user',
            'tasks',
            'totalTasks',
            'aceptadaTasksCount',
            'completadaTasksCount'
        ));
    }

            
    public function getTaskDetails($id)
    {
        // Cargar la tarea con su empresa y usuario
        $task = Tasks::with(['empresa', 'usuario'])->find($id); 

        if (!$task) {
            return response()->json(['error' => 'Tarea no encontrada'], 404);
        }

        return response()->json([
            'title' => $task->title,
            'description' => $task->description,
            'start_date' => $task->start_date->format('Y-m-d'), 
            'end_date' => $task->end_date->format('Y-m-d'),
            'duration' => $task->duration,
            'nombre_empresa' => $task->empresa->name, 
            'price' => $task->price,
            'location' => $task->location,
            'reward' => $task->reward,
            'task_type' => $task->task_type,
            'location_empresa' => $task->empresa->location
        ]);
    }


    public function completar_tarea($id)
    {
        try {
            
            // Obtener la tarea
            $task = Tasks::findOrFail($id);
            
            
            // Actualizar el estado de la tarea
            $task->status = 'completada';
            $task->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Tarea aceptada con éxito',
                'task' => $task
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al completar la tarea: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function mi_historial_usuario(Request $request)
    {
    // Obtener el usuario autenticado
    $user = Auth::user();
        
    // Iniciar la consulta base
    $tasksQuery = Tasks::where('id_usuario', $user->id);

    // Ordenar tareas para mostrar primero las 'aceptada' y luego las 'completada'
    $tasks = $tasksQuery->with('empresa')
        ->select('id', 'title', 'status', 'id_empresa', 'paid', 'reward', 'start_date', 'end_date')
        ->get();
    
    // Obtener conteos para cada estado
    $totalTasks = Tasks::where('id_usuario', $user->id)->count();
    $completadas_sin_pagar = Tasks::where('id_usuario', $user->id)->where('paid', false)->count();
    $completadas_pagadas = Tasks::where('id_usuario', $user->id)->where('paid', true)->count();
    
    return view('usuario.perfil.mi_historial_tareas', compact(
        'user',
        'tasks',
        'totalTasks',
        'completadas_sin_pagar',
        'completadas_pagadas'
    ));
    }
    

    public function getNearBalance($walletUsername)
    {
        $client = new Client();
        $url = "https://rpc.testnet.near.org"; // Usamos testnet de Near para la solicitud

        try {
            // Realizar la solicitud a la API de Near para obtener el balance
            $response = $client->post($url, [
                'json' => [
                    "jsonrpc" => "2.0",
                    "id" => "dontcare",
                    "method" => "query",
                    "params" => [
                        "request_type" => "view_account",
                        "finality" => "final",
                        "account_id" => $walletUsername
                    ]
                ]
            ]);

            $data = json_decode($response->getBody(), true);
            // Obtener el balance en yoctoNEAR
            $yoctoNear = $data['result']['amount'] ?? '0';

            // Convertir yoctoNEAR a NEAR (1 NEAR = 10^24 yoctoNEAR)
            $nearBalance = bcdiv($yoctoNear, '1000000000000000000000000', 2);

            // Retornar el balance disponible en el campo 'amount'
            return $data['result']['amount'] ?? '0';
        } catch (\Exception $e) {
            // Si ocurre un error, retornar 0
            return '0';
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function show()
    {
        // Obtener el usuario autenticado
        $user = Auth::user();

        // Retornar una vista con los datos del usuario y el balance
        return view('usuario.perfil.mi_perfil', compact('user'));
    }

    public function registrar_tarea()
    {
        // Obtener el usuario autenticado
        $user = Auth::user();

        // Retornar una vista con los datos del usuario y el balance
        return view('empresa.perfil.registrar_tarea', compact('user'));
    }

    public function guardar_tarea(Request $request)
    {
        // Validar los datos recibidos del formulario
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reward' => 'required|numeric|min:0',
            'location' => 'required|string|max:255',
            'task_type' => 'required|string',
        ]);

        // Crear una nueva tarea con los datos validados
        Tasks::create([
            'id_empresa' => auth()->id(), // Asumiendo que el usuario está autenticado
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'reward' => $request->input('reward'),
            'location' => $request->input('location'),
            'task_type' => $request->input('task_type'),
            'status' => 'pendiente', // Estado inicial
        ]);

        // Redirigir al usuario con un mensaje de éxito
        return redirect()->route('empresa.perfil.ver_tareas')->with('success', 'Tarea publicada correctamente');
    }

    public function ver_tareas(Request $request)
    {
        $user = Auth::user();
        
        // Definimos los estados válidos
        $estadosValidos = ['pendiente', 'aceptada', 'completada'];
        
        // Iniciamos la consulta
        $tasks = Tasks::query();
        
        // Validamos que el status sea válido antes de aplicar el filtro
        if ($request->has('status') && in_array(strtolower($request->status), $estadosValidos)) {
            $tasks->where('status', strtolower($request->status));
        }
        
        // Contadores de estados usando una sola consulta
        $contadores = Tasks::selectRaw('status, count(*) as total')
                           ->whereIn('status', $estadosValidos)
                           ->groupBy('status')
                           ->pluck('total', 'status')
                           ->toArray();
        
        // Asignamos los contadores con valor por defecto 0
        $pendienteCount = $contadores['pendiente'] ?? 0;
        $aceptadaCount = $contadores['aceptada'] ?? 0;
        $completadaCount = $contadores['completada'] ?? 0;
        
        // Obtenemos las tareas
        $tasks = $tasks->get();
        
        return view('empresa.perfil.ver_tareas', compact(
            'tasks',
            'pendienteCount',
            'aceptadaCount',
            'completadaCount',
            'user'
        ));
    }

    public function ver_perfil_usuario($id)
    {
        // Buscar el usuario por su ID
        $usuario = User::findOrFail($id);

        // Retornar la vista del perfil, pasando los datos del usuario
        return view('empresa.perfil.ver_perfil_usuario', compact('usuario'));
    }


    public function ver_perfil_empresa($id)
    {
        // Buscar empresa por su ID
        $empresa = User::findOrFail($id);

        // Retornar la vista del perfil, pasando los datos del usuario
        return view('usuario.perfil.ver_perfil_empresa', compact('empresa'));
    }


    public function mi_empresa(Request $request)
    {
        // Obtener el usuario autenticado
        $user = Auth::user();
        
        // Iniciar la consulta correctamente para obtener las tareas donde id_empresa es el id del usuario actual
        $tasks = Tasks::where('id_empresa', $user->id);
        
        // Filtrar por estado si se especifica y no está vacío
        if ($request->filled('status')) {
            $tasks->where('status', $request->status);
        }
        
        // Obtener todas las tareas
        $allTasks = $tasks->get();
        
        // Separar las tareas en activas y aceptadas
        $activeTasks = $allTasks->where('status', 'pendiente');
        $completadaTasks = $allTasks->where('status', 'completada');
        
        // Retornar una vista con los datos del usuario y las tareas
        return view('empresa.perfil.mi_empresa', compact('user', 'activeTasks', 'completadaTasks'));
    }
    
        

    
    // Método para obtener los detalles de una tarea por ID
    public function obtenerTarea($id)
    {
        // Buscar la tarea con el ID proporcionado
        $task = Tasks::findOrFail($id);

        // Devolver la tarea como JSON
        return response()->json($task);
    }

    
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validaciones del formulario
        try {

            $ValidarDatos = $request->validate([
                'name' => 'required|string|regex:/^[\pL\s]+$/u',
                'email' => 'required|unique:users|email',
                'password' => 'required|string|regex:/^[a-zA-Z0-9]+$/',
                'address' => 'required|string',
                'city' => 'required|string',
                'state' => 'required|string',
                'postal_code' => 'required|string|regex:/^\d{5}$/',
                'phone' => 'required|string|regex:/^\d{10}$/',
                'username_wallet' => 'nullable|string',
                'id_wallet' => 'nullable|string',
                'profile_photo_path' => 'required|image|max:1024'
                ]);

                // Procesamos la imagen y obtenemos la ruta
                $profilePhotoPath = null;
                if (request()->hasFile('profile_photo_path')) {
                    $profilePhotoPath = request()->file('profile_photo_path')->store('profile_photos', 'public'); // Guardar la imagen en 'storage/app/public/profile_photos'
                }
    
            // Crear y guardar el usuario
            $usuario = new User();
            $usuario->name = $request->input('name');
            $usuario->email = $request->input('email');
            $usuario->password = Hash::make($request->input('password'));
            $usuario->address = $request->input('address');
            $usuario->city = $request->input('city');
            $usuario->state = $request->input('state');
            $usuario->postal_code = $request->input('postal_code');
            $usuario->phone = $request->input('phone');
            $usuario->website = $request->input('website');
            $usuario->rfc = $request->input('rfc');
            $usuario->username_wallet = $request->input('username_wallet');
            $usuario->id_wallet = $request->input('id_wallet');
            $usuario->profile_photo_path = $profilePhotoPath;
            $usuario->id_rol = $request->input('id_rol');
            $usuario->save();
    
              // Agregar automáticamente ".testnet" si no está incluido en username_wallet
            if (!empty($input['username_wallet']) && !str_ends_with($request['username_wallet'], '.testnet')) {
                $request['username_wallet'] .= '.testnet';
            }
        
            // Enviar mensaje de guardado exitoso
            $mensaje = [
                'success' => true,
                'message' => 'Cuenta registrada exitosamente',
            ];
        } catch (ValidationException $e) {
            // Si no se respeta la validación, mostrar excepción
            $mensaje = [
                'success' => false,
                'errors' => $e->validator->getMessageBag()->toArray(),
            ];
        }
    
        return response()->json($mensaje);
    }

    /**
     * Display the specified resource.
     */

    /**
     * Show the form for editing the specified resource.
     */

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string',
            'username_wallet' => 'required|string',
            'address' => 'required|string',
            'city' => 'required|string',
            'state' => 'required|string',
            'postal_code' => 'required|string',
            'phone' => 'required|string',
            'location' => 'required|string',
            'password' => 'required|string'
        ]);

        $usernameWallet = $request->input('username_wallet');

        if (!empty($usernameWallet) && !str_ends_with($usernameWallet, '.testnet')) {
            $usernameWallet .= '.testnet';
        }
        $request->merge(['username_wallet' => $usernameWallet]);

        $user = User::find($id);
        $user->name = $request->name;
        $user->username_wallet = $request->username_wallet;
        $user->address = $request->address;
        $user->city = $request->city;
        $user->state = $request->state;
        $user->postal_code = $request->postal_code;
        $user->phone = $request->phone;
        $user->location = $request->location; 
        $user->password = Hash::make($request->password);
        $user->save();
    
        return redirect()->back()->with('success', 'Datos actualizados con éxito');
    }
    
    

    public function actualizar_empresa(Request $request, $id)
    {
        $request->validate([
            'email' => 'required|email',
            'address' => 'required|string',
            'city' => 'required|string',
            'state' => 'required|string',
            'postal_code' => 'required|string',
            'phone' => 'required|string',
            'username_wallet' => 'required|string',
            'location' => 'required|string', // Validación para la ubicación
        ]);

        $usernameWallet = $request->input('username_wallet');

        if (!empty($usernameWallet) && !str_ends_with($usernameWallet, '.testnet')) {
            $usernameWallet .= '.testnet';
        }
        $request->merge(['username_wallet' => $usernameWallet]);

        $user = User::find($id);
        $user->email = $request->email;
        $user->address = $request->address;
        $user->city = $request->city;
        $user->state = $request->state;
        $user->postal_code = $request->postal_code;
        $user->phone = $request->phone;
        $user->location = $request->location; 
        $user->username_wallet = $request->username_wallet; 
        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->back()->with('success', 'Datos actualizados con éxito');
    }

    

    /**
     * Remove the specified resource from storage.
     */
    
    public function destroy()
    {
       
    }

    public function near_vista()
    {
        $user = Auth::user();
        
        // Obtenemos las tareas con sus usuarios relacionados
        $completadaTasks = Tasks::where('id_empresa', $user->id)
            ->where('status', 'completada')
            ->where('paid', false)           
            ->join('users', 'tasks.id_usuario', '=', 'users.id')  // Unimos con la tabla users
            ->select('tasks.*', 'users.name as usuario_nombre')  // Seleccionamos los campos que necesitamos
            ->get();
        
        return view('empresa.walletNear.near', compact('user', 'completadaTasks'));
    }
    
    public function pagar_tarea(Request $request, $id)
    {
        try {
            // Obtain the task by ID
            $task = Tasks::findOrFail($id);
    
            // Update the task status to paid
            $task->paid = true;
            $task->save();
    
            return response()->json([
                'success' => true,
                'message' => 'Task marked as paid successfully',
                'task' => $task
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while marking the task as paid: ' . $e->getMessage()
            ], 500);
        }
    }
        
}
