<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;

class AbonadoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'nombre' => 'required',
            'cedula' => 'required|unique:abonados',
            'apellido1' => 'required',
            'apellido2' => 'required',
            'telefono' => 'required',
            'direccion' => 'required',
            'email'    => 'email|unique:abonados',
        ];
    }
    
}
