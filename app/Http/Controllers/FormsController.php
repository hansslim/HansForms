<?php

namespace App\Http\Controllers;

use App\Models\TextInput;
use Illuminate\Http\Request;
use App\Models\Form;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FormsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return Form::where(['user_id' => Auth::user()->id])->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($slug)
    {
        /*$response = [
            'textInputs' => [],
            'numberInputs' => [],
            'booleanInputs' => [],
            'dateInputs' => [],
            //'selectInputs' => [] //todo:
        ];*/

        /*//textInputs
        $response['textInputs'] = DB::table('forms')
            ->join('form_elements', 'forms.id', '=', 'form_elements.form_id')
            ->join('input_elements', 'form_elements.id', '=', 'input_elements.form_element_id')
            ->join('text_inputs', 'input_elements.id', '=', 'text_inputs.input_element_id')
            ->select('input_element_id', "form_elements.order", "input_elements.header")
            ->where("forms.slug", $slug)
            ->get();

        //numberInputs
        $response['numberInputs'] = DB::table('forms')
            ->join('form_elements', 'forms.id', '=', 'form_elements.form_id')
            ->join('input_elements', 'form_elements.id', '=', 'input_elements.form_element_id')
            ->join('number_inputs', 'input_elements.id', '=', 'number_inputs.input_element_id')
            ->select('input_element_id', "form_elements.order", "input_elements.header")
            ->where("forms.slug", $slug)
            ->get();

        //booleanInputs
        $response['numberInputs'] = DB::table('forms')
            ->join('form_elements', 'forms.id', '=', 'form_elements.form_id')
            ->join('input_elements', 'form_elements.id', '=', 'input_elements.form_element_id')
            ->join('boolean_inputs', 'input_elements.id', '=', 'boolean_inputs.input_element_id')
            ->select('input_element_id', "form_elements.order", "input_elements.header")
            ->where("forms.slug", $slug)
            ->get();*/

        return Form::where('slug', $slug)->get();

        //return $response;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
