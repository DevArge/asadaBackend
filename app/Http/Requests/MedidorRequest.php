<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MedidorRequest extends FormRequest
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
            'idAbonado' => 'required',
            'idTipoDeMedidor' => 'required',
            'costoTotal' => 'required',
            'plazo' => 'required',
            'tipoDeuda' => 'required'
        ];
    }
}
