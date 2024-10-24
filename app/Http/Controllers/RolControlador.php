<?php

namespace App\Http\Controllers;

use App\Models\RolModelo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class RolControlador extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.roles.registrar_rol');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $ValidarDatos = $request->validate([
            'tipo' => 'required|unique:rol|string|regex:/^[a-zA-Z]+(\s[a-zA-Z]+)?$/'
        ]);

        $rol = new RolModelo();
        $rol ->tipo = $request->input('tipo');
        $rol->save();

        //Enviar mensaje de guardado exitoso
        $mensaje = [
            'success' => true,
            'message' => 'Rol guardado exitosamente',
        ];
        //Si no se respeta la validación entonces que muestre excepción
        } catch (ValidationException $e) {
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
    public function show()
    {
        $roles = RolModelo::all();
        return view('admin.roles.ver_roles', ['roles' => $roles]);  
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id_rol)
    {
        $roles = RolModelo::find($id_rol);
        return view('admin.roles.editar_roles', ['rol' => $roles]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id_rol)
    {
        //Método para validar los datos ingresados
        $request -> validate([
            'tipo' => 'required|string|regex:/^[a-zA-Z]+(\s[a-zA-Z]+)?$/|unique:rol,tipo,' . $id_rol . ',id_rol',
        ]);

        //Método para encontrar el id y poder actualizar sus datos
        $rol = RolModelo::find($id_rol);
        $rol ->tipo = $request->input('tipo');
        $rol->save();
        flash()->addPreset('registro_guardado');

        //Método que nos direcciona a la ruta para ver los roles
        return redirect()->route('admin.roles.ver_roles');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id_rol)
    {
        $rol = RolModelo::find($id_rol);

        if ($rol) {
            // Verificar si existen claves relacionadas en la tabla "contratos"
            $rolRelacionado = User::where('id_rol', $id_rol)->exists();

            if ($rolRelacionado) {
                flash()->addPreset('registro_validado');
            } else {
                $rol->delete();
                flash()->addPreset('registro_eliminado');
            }
        } else {
            flash()->error('No se encontró el rol que se intentó eliminar.');
        }

        return redirect()->route('admin.roles.ver_roles');
    }
}
