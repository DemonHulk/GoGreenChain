<?php

namespace App\Http\Controllers;

use App\Models\CobrosModelo;
use App\Models\RolModelo;
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
                ->where('status', 'pending')
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
                'status' => 'accepted',
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
    
    // Obtener las tareas
    $tasks = $tasksQuery->get();
    
    // Obtener conteos para cada estado
    $totalTasks = Tasks::where('id_usuario', $user->id)->count();
    $pendingTasksCount = Tasks::where('id_usuario', $user->id)->where('status', 'pending')->count();
    $acceptedTasksCount = Tasks::where('id_usuario', $user->id)->where('status', 'accepted')->count();
    $completedTasksCount = Tasks::where('id_usuario', $user->id)->where('status', 'completed')->count();
    
    return view('usuario.perfil.mis_tareas', compact(
        'user',
        'tasks',
        'totalTasks',
        'pendingTasksCount',
        'acceptedTasksCount',
        'completedTasksCount'
    ));
}
    
    
    public function mi_historial_usuario()
    {
        // Obtener el usuario autenticado
    $user = Auth::user();

    // Retornar una vista con los datos del usuario 

    return view('usuario.perfil.mi_historial_tareas', compact('user'));
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
            'status' => 'pending', // Estado inicial
        ]);

        // Redirigir al usuario con un mensaje de éxito
        return redirect()->route('empresa.perfil.ver_tareas')->with('success', 'Tarea publicada correctamente');
    }
    public function ver_tareas(Request $request)
    {
        // Obtener el usuario autenticado
        $user = Auth::user();
    
        // Obtener todas las tareas
        $tasks = Tasks::all();
    
        // Contar tareas según el estado
        $pendingUnassignedCount = $tasks->where('status', 'pending')->whereNull('id_usuario')->count();
        $pendingAssignedCount = $tasks->where('status', 'pending')->whereNotNull('id_usuario')->count();
        $completedCount = $tasks->where('status', 'completed')->whereNotNull('id_usuario')->count();
    
        // Aplicar filtro según el estado seleccionado
        $filteredTasks = Tasks::query();
        if ($request->filled('status')) {
            switch ($request->status) {
                case 'pending_unassigned':
                    $filteredTasks->where('status', 'pending')->whereNull('id_usuario');
                    break;
                case 'pending_assigned':
                    $filteredTasks->where('status', 'pending')->whereNotNull('id_usuario');
                    break;
                case 'completed':
                    $filteredTasks->where('status', 'completed')->whereNotNull('id_usuario');
                    break;
            }
        }
    
        // Obtener las tareas filtradas
        $tasks = $filteredTasks->get();
    
        return view('empresa.perfil.ver_tareas', compact(
            'user',
            'tasks',
            'pendingUnassignedCount',
            'pendingAssignedCount',
            'completedCount'
        ));
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
        
        // Separar las tareas en activas y completadas
        $activeTasks = $allTasks->where('status', 'pending');
        $completedTasks = $allTasks->where('status', 'completed');
        
        // Retornar una vista con los datos del usuario y las tareas
        return view('empresa.perfil.mi_empresa', compact('user', 'activeTasks', 'completedTasks'));
    }
    
        

    
    // Método para obtener los detalles de una tarea por ID
    public function obtenerTarea($id)
    {
        // Buscar la tarea con el ID proporcionado
        $task = Tasks::findOrFail($id);

        // Devolver la tarea como JSON
        return response()->json($task);
    }

    
    
    public function create()
    {
        $roles = RolModelo::all();
        return view('admin.usuarios.registrar_usuario', ['roles' => $roles]);
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
    public function edit($id)
    {
        $usuario = User::find($id);
        return view('admin.usuarios.editar_usuario',['usuario' => $usuario, 'roles' => RolModelo::all()]);
    }

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
            'location' => 'required|string', // Validación para la ubicación
        ]);
    
        $user = User::find($id);
        $user->name = $request->name;
        $user->username_wallet = $request->username_wallet;
        $user->address = $request->address;
        $user->city = $request->city;
        $user->state = $request->state;
        $user->postal_code = $request->postal_code;
        $user->phone = $request->phone;
        $user->location = $request->location; // Guardar ubicación
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
            'location' => 'required|string', // Validación para la ubicación
        ]);

        $user = User::find($id);
        $user->email = $request->email;
        $user->address = $request->address;
        $user->city = $request->city;
        $user->state = $request->state;
        $user->postal_code = $request->postal_code;
        $user->phone = $request->phone;
        $user->location = $request->location; // Guardar ubicación
        $user->save();

        return redirect()->back()->with('success', 'Datos actualizados con éxito');
    }

    

    /**
     * Remove the specified resource from storage.
     */
    
    public function destroy()
    {
       
    }
    
}
