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

class UsuarioControlador extends Controller
{
   
    /**
     * Display a listing of the resource.
     */
    public function index(){
            // Obtener el usuario autenticado
        $user = Auth::user();

        // Llamar a la API de Near para obtener el balance
        $balance = $this->getNearBalance($user->username_wallet);

        // Retornar una vista con los datos del usuario y el balance

        return view('usuario.index', compact('user', 'balance'));
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

    public function sendNear(Request $request)
    {
        // Validar la entrada del formulario
        $request->validate([
            'recipient' => 'required|string',
            'amount' => 'required|numeric|min:0.01', // Mínimo 0.01 NEAR
        ]);
    
        $user = Auth::user();
        $privateKey = 'YOUR_PRIVATE_KEY_HERE'; // Obtén la clave privada de manera segura
        $publicKey = 'YOUR_PUBLIC_KEY_HERE'; // Clave pública del usuario
    
        // Convertir NEAR a yoctoNEAR
        $amountInYocto = bcmul($request->amount, '1000000000000000000000000', 0); // 1 NEAR = 10^24 yoctoNEAR
    
        // Obtener el nonce actual
        $nonce = $this->getNonce($user->username_wallet);
    
        // Realizar la solicitud a la API de Near
        $url = "https://rpc.testnet.near.org";
        $client = new Client();
    
        try {
            $response = $client->post($url, [
                'json' => [
                    "jsonrpc" => "2.0",
                    "id" => "dontcare",
                    "method" => "broadcast_tx_commit",
                    "params" => [
                        "signed_transaction" => $this->createTransaction($user->username_wallet, $request->recipient, $amountInYocto, $privateKey, $publicKey, $nonce)
                    ]
                ]
            ]);
    
            $data = json_decode($response->getBody(), true);
    
            if (isset($data['error'])) {
                return redirect()->back()->withErrors(['msg' => 'Error al enviar: ' . $data['error']['message']]);
            }
    
            return redirect()->back()->with('success', 'Transacción exitosa. Hash: ' . $data['result']['transaction']['hash']);
        } catch (RequestException $e) {
            return redirect()->back()->withErrors(['msg' => 'Error al enviar: ' . $e->getMessage()]);
        }
    }
    
    private function createTransaction($sender, $recipient, $amountInYocto, $privateKey, $publicKey, $nonce)
    {
        // Debes implementar la firma de la transacción aquí
        return [
            "receiver_id" => $recipient,
            "amount" => $amountInYocto,
            "gas" => "100000000000000", // Establece un límite de gas
            "nonce" => $nonce, // Usa el nonce actual
            "public_key" => $publicKey,
            // Agrega aquí el resto de la transacción, incluyendo la firma
        ];
    }
    
    public function getNonce($walletUsername)
    {
        $client = new Client();
        $url = "https://rpc.testnet.near.org";
    
        try {
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
            // El nonce es el número de veces que ha enviado transacciones
            return $data['result']['nonce'];
        } catch (\Exception $e) {
            return 0; // Devuelve 0 si hay un error al obtener el nonce
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
            'id_user' => auth()->id(), // Asumiendo que el usuario está autenticado
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

    public function ver_tareas()
    {
        // Obtener el usuario autenticado
        $user = Auth::user();
    
        // Obtener las tareas del usuario
        $tasks = $user->tasks; // Asumiendo que tienes una relación definida entre Usuario y Tarea
    
        // Retornar la vista con las tareas
        return view('empresa.perfil.ver_tareas', compact('user', 'tasks'));
    }
    
    public function mi_empresa()
    {
        // Obtener el usuario autenticado
        $user = Auth::user();

        // Retornar una vista con los datos del usuario y el balance
        return view('empresa.perfil.mi_empresa', compact('user'));
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
                'id_wallet' => 'nullable|string'
                ]);
    
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
            'email' => 'required|email',
            'address' => 'required|string',
            'city' => 'required|string',
            'state' => 'required|string',
            'postal_code' => 'required|string',
            'phone' => 'required|string',
        ]);
    
        $user = User::find($id);
        $user->email = $request->email;
        $user->address = $request->address;
        $user->city = $request->city;
        $user->state = $request->state;
        $user->postal_code = $request->postal_code;
        $user->phone = $request->phone;
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
        ]);
    
        $user = User::find($id);
        $user->email = $request->email;
        $user->address = $request->address;
        $user->city = $request->city;
        $user->state = $request->state;
        $user->postal_code = $request->postal_code;
        $user->phone = $request->phone;
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
