<?php

namespace App\Http\Controllers;

use App\Models\BooleanInputAnswer;
use App\Models\DateInputAnswer;
use App\Models\Form;
use App\Models\FormCompletion;
use App\Models\NumberInputAnswer;
use App\Models\SelectInputAnswer;
use App\Models\TextInputAnswer;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use mysql_xdevapi\Exception;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class FormCompletionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($slug)
    {
        $userId = Auth::user()->id;
        if ($userId) {
            $formId = Form::where(['user_id'=> $userId, 'slug' => $slug])->first()->id;
            if ($formId) {
                $results = FormCompletion::where(['form_id'=> $formId])->get();
                return $results;
            }
            else return response("Not found.", 404);
        }
        else return response("Unauthorized.", 401);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    /*public function create()
    {
        //
    }*/

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $slug)
    {
        //todo: rewrite error_log and abort to return response("error", 400);
        //data validation
        $answeredForm = Form::where('slug', $slug)->first();
        //todo: on time validation
        //todo: answer only with permissions

        //todo: data validation

        function getCorrespondingValue($request, $type, $specificInputId): array
        {
            $corresponding = [];

            if ($request->all()) {
                foreach ($request->all() as $answerKey => $answerValue) {
                    $answerFields = explode('-', $answerKey);

                    if (count($answerFields) == 2) {
                        $aType = $answerFields[0];
                        $aId = $answerFields[1];
                        if ($aType == $type) {
                            if ($aId == $specificInputId) {
                                //proper id found
                                $corresponding['type'] = $type;
                                $corresponding['id'] = $specificInputId;
                                $corresponding['value'] = $answerValue;

                                return $corresponding;
                            }
                        }
                    }
                }
                //proper id not found
                $corresponding['type'] = $type;
                $corresponding['id'] = $specificInputId;
                $corresponding['value'] = '';
                return $corresponding;
            } else {
                //response is null (for forms that have all questions non-required)
                $corresponding['type'] = $type;
                $corresponding['id'] = $specificInputId;
                $corresponding['value'] = '';
                return $corresponding;
            }
        }

        $correspondingWithForm = [];
        foreach ($answeredForm->formElements as $key => $value) {
            if ($value->inputElement) {
                $isMandatory = $value->inputElement->is_mandatory;

                if ($value->inputElement->booleanInput) {
                    $answer = getCorrespondingValue($request, "boolean", $value->inputElement->booleanInput->id);
                    switch ($answer['value']) {
                        case 'true':
                        {
                            $answer['value'] = true;
                            $correspondingWithForm[] = $answer;
                            break;
                        }
                        case 'false':
                        {
                            $answer['value'] = false;
                            $correspondingWithForm[] = $answer;
                            break;
                        }
                        case "":
                        case null:
                        case 'null':
                        {
                            if ($isMandatory) {
                                error_log("Bad request (validation was not successful [boolean-{$answer['id']} is mandatory])", 400);
                                abort(400);
                            } else {
                                $answer['value'] = null;
                                $correspondingWithForm[] = $answer;
                            }
                            break;
                        }
                        default:
                            error_log("Bad request (validation was not successful [boolean-{$answer['id']} is invalid])", 400);
                            abort(400);

                    }

                } else if ($value->inputElement->dateInput) {
                    $answer = getCorrespondingValue($request, "date", $value->inputElement->dateInput->id);
                    switch ($answer['value']) {
                        case null:
                        case 'null':
                        case '':
                        {
                            if ($isMandatory) {
                                error_log("Bad request (validation was not successful [date-{$answer['id']} is mandatory])", 400);
                                abort(400);
                            } else {
                                $answer['value'] = null;
                                $correspondingWithForm[] = $answer;
                            }
                            break;
                        }
                        default:
                        {
                            //validate date format
                            function validateDate($date, $format = 'Y-m-d H:i:s')
                            {
                                $d = DateTime::createFromFormat($format, $date);
                                return $d && $d->format($format) == $date;
                            }

                            if (!validateDate($answer['value'], 'Y-m-d')) {
                                error_log("Bad request (validation was not successful [date-{$answer['id']} unsupported format])", 400);
                                abort(400);
                            }

                            //validate conditions
                            $answerDateTime = new DateTime($answer['value']);

                            if ($value->inputElement->dateInput->min) {
                                $minDateTime = new DateTime($value->inputElement->dateInput->min);
                                if ($answerDateTime < $minDateTime) {
                                    error_log("Bad request (validation was not successful [date-{$answer['id']} is lower than expected])", 400);
                                    abort(400);
                                }
                            }
                            if ($value->inputElement->dateInput->max) {
                                $maxDateTime = new DateTime($value->inputElement->dateInput->max);
                                if ($answerDateTime > $maxDateTime) {
                                    error_log("Bad request (validation was not successful [date-{$answer['id']} is higher than expected])", 400);
                                    abort(400);
                                }
                            }

                            $correspondingWithForm[] = $answer;
                        }
                    }

                } else if ($value->inputElement->textInput) {
                    $answer = getCorrespondingValue($request, "text", $value->inputElement->textInput->id);
                    switch ($answer['value']) {
                        case null:
                        case 'null':
                        case '':
                        {
                            if ($isMandatory) {
                                error_log("Bad request (validation was not successful [text-{$answer['id']} is mandatory])", 400);
                                abort(400);
                            } else {
                                $answer['value'] = null;
                                $correspondingWithForm[] = $answer;
                            }
                            break;
                        }
                        default:
                        {
                            if ($value->inputElement->textInput->strict_length) {
                                if (mb_strlen($answer['value']) != $value->inputElement->textInput->strict_length) {
                                    error_log("Bad request (validation was not successful [text-{$answer['id']} doesn't fit strict length])", 400);
                                    abort(400);
                                }
                            } else {
                                if ($value->inputElement->textInput->min_length) {
                                    if (mb_strlen($answer['value']) < $value->inputElement->textInput->min_length) {
                                        error_log("Bad request (validation was not successful [text-{$answer['id']} has lower length than expected])", 400);
                                        abort(400);
                                    }
                                }
                                if ($value->inputElement->textInput->max_length) {
                                    if (mb_strlen($answer['value']) > $value->inputElement->textInput->max_length) {
                                        error_log("Bad request (validation was not successful [text-{$answer['id']} has higher length than expected])", 400);
                                        abort(400);
                                    }
                                }
                            }

                            $correspondingWithForm[] = $answer;
                        }
                    }

                } else if ($value->inputElement->numberInput) {
                    $answer = getCorrespondingValue($request, "number", $value->inputElement->numberInput->id);
                    switch ($answer['value']) {
                        case null:
                        case 'null':
                        case '':
                        {
                            if ($isMandatory) {
                                error_log("Bad request (validation was not successful [number-{$answer['id']} is mandatory])", 400);
                                abort(400);
                            } else {
                                $answer['value'] = null;
                                $correspondingWithForm[] = $answer;
                            }
                            break;
                        }
                        default:
                        {
                            if (preg_match('/(^[0-9]+[,|.][0-9]+)|(^[0-9]*$)/', $answer['value'])) {
                                if (is_numeric($answer['value'])) {

                                    if (!$value->inputElement->numberInput->can_be_decimal) {
                                        if (!preg_match('/^[0-9]*$/', $answer['value'])) {
                                            error_log("Bad request (validation was not successful [number-{$answer['id']} cannot be decimal])", 400);
                                            abort(400);
                                        }
                                    }

                                    if ($value->inputElement->numberInput->min) {
                                        if ($answer['value'] < $value->inputElement->numberInput->min) {
                                            error_log("Bad request (validation was not successful [number-{$answer['id']} is lower than expected])", 400);
                                            abort(400);
                                        }
                                    }
                                    if ($value->inputElement->numberInput->max) {
                                        if ($answer['value'] > $value->inputElement->numberInput->max) {
                                            error_log("Bad request (validation was not successful [number-{$answer['id']} is higher than expected])", 400);
                                            abort(400);
                                        }
                                    }

                                    $correspondingWithForm[] = $answer;
                                } else {
                                    error_log("Bad request (validation was not successful [number-{$answer['id']} isn't number])", 400);
                                    abort(400);
                                }
                            } else {
                                error_log("Bad request (validation was not successful [number-{$answer['id']} doesn't match number format])", 400);
                                abort(400);
                            }
                        }
                    }

                } else if ($value->inputElement->selectInput) {
                    $answer = getCorrespondingValue($request, "select", $value->inputElement->selectInput->id);

                    if ($value->inputElement->selectInput->is_multiselect) {
                        switch ($answer['value']) {
                            case null:
                            case 'null':
                            case '':
                            {
                                if ($isMandatory) {
                                    error_log("Bad request (validation was not successful [select-{$answer['id']} (checkbox) is mandatory])", 400);
                                    abort(400);
                                } else {
                                    $answer['value'] = null;
                                    $correspondingWithForm[] = $answer;
                                }
                                break;
                            }
                            default:
                            {
                                $validChoiceFound = false;
                                $validatedChoices = [];

                                foreach ($value->inputElement->selectInput->selectInputChoices as $cValue) {
                                    foreach ($answer['value'] as $aValue) {
                                        $a = explode('-', $aValue)[1];

                                        if ($cValue->id == $a) {
                                            $validChoiceFound = true;

                                            $validatedChoices[] = [
                                                "type" => "select",
                                                "id" => $value->inputElement->selectInput->id,
                                                "value" => $a
                                            ];
                                            break;
                                        }
                                    }
                                }

                                if ($value->inputElement->selectInput->min_amount_of_answers) {
                                    if (count($validatedChoices) < $value->inputElement->selectInput->min_amount_of_answers) {
                                        error_log("Bad request (validation was not successful [select-{$answer['id']} (checkbox) expects more answers])", 400);
                                        abort(400);
                                    }
                                }

                                if ($value->inputElement->selectInput->max_amount_of_answers) {
                                    if (count($validatedChoices) > $value->inputElement->selectInput->max_amount_of_answers) {
                                        error_log("Bad request (validation was not successful [select-{$answer['id']} (checkbox) expects less answers])", 400);
                                        abort(400);
                                    }
                                }

                                if ($value->inputElement->selectInput->strict_amount_of_answers) {
                                    if (count($validatedChoices) != $value->inputElement->selectInput->strict_amount_of_answers) {
                                        error_log("Bad request (validation was not successful [select-{$answer['id']} (checkbox) expects strict amount of answers])", 400);
                                        abort(400);
                                    }
                                }


                                $correspondingWithForm = array_merge($correspondingWithForm, $validatedChoices);
                                if (!$validChoiceFound) {
                                    error_log("Bad request (validation was not successful [select-{$answer['id']} (checkbox) invalid answers])", 400);
                                    abort(400);
                                }
                            }
                        }

                    } else {
                        switch ($answer['value']) {
                            case null:
                            case 'null':
                            case '':
                            {
                                if ($isMandatory) {
                                    error_log("Bad request (validation was not successful [select-{$answer['id']} (radio) is mandatory])", 400);
                                    abort(400);
                                } else {
                                    $answer['value'] = null;
                                    $correspondingWithForm[] = $answer;
                                }
                                break;
                            }
                            default:
                            {
                                try {
                                    $aChoice = explode('-', $answer['value'])[1];
                                    $validChoiceFound = false;

                                    foreach ($value->inputElement->selectInput->selectInputChoices as $cValue) {
                                        if ($cValue['id'] == $aChoice) {
                                            $validChoiceFound = true;
                                            $answer['value'] = $aChoice;
                                            $correspondingWithForm[] = $answer;
                                            break;
                                        }
                                    }

                                    if (!$validChoiceFound) {
                                        error_log("Bad request (validation was not successful [select-{$answer['id']} (radio) invalid answer])", 400);
                                        abort(400);
                                    }
                                } catch (Exception $exception) {
                                    error_log("Bad request (validation was not successful [select-{$answer['id']} (radio) invalid data])", 400);
                                    abort(400);
                                }
                            }
                        }
                    }
                }
            }
        }

        try {
            DB::transaction(function () use ($answeredForm, $correspondingWithForm) {
                $formCompletion = FormCompletion::create(['form_id' => $answeredForm->id]);
                foreach ($correspondingWithForm as $key => $value) {
                    switch ($value["type"]) {
                        case "number":
                        {
                            NumberInputAnswer::create([
                                "form_completion_id" => $formCompletion->id,
                                "value" => $value['value'],
                                "number_input_id" => $value['id']
                            ]);

                            break;
                        }
                        case "text":
                        {
                            TextInputAnswer::create([
                                "form_completion_id" => $formCompletion->id,
                                "value" => $value['value'],
                                "text_input_id" => $value['id']
                            ]);

                            break;
                        }
                        case "date":
                        {
                            DateInputAnswer::create([
                                "form_completion_id" => $formCompletion->id,
                                "value" => $value['value'],
                                "date_input_id" => $value['id']
                            ]);

                            break;
                        }
                        case "boolean":
                        {
                            BooleanInputAnswer::create([
                                "form_completion_id" => $formCompletion->id,
                                "value" => $value['value'],
                                "boolean_input_id" => $value['id']
                            ]);

                            break;
                        }
                        case "select":
                        {
                            SelectInputAnswer::create([
                                "form_completion_id" => $formCompletion->id,
                                "select_input_id" => $value['id'],
                                "select_choice_id" => $value['value'] ?? null
                            ]);
                            break;
                        }
                        default:
                            break;
                    }
                }
            });
        } catch (Exception $exception) {
            error_log("Bad request (validation was not successful [Database manipulation failure])", 400);
            abort(400);
        }
        return response('Answer has been proceeded successfully.', 200);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\FormCompletion $formCompletion
     * @return \Illuminate\Http\Response
     */
    public function show(FormCompletion $formCompletion)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\FormCompletion $formCompletion
     * @return \Illuminate\Http\Response
     */
    public function edit(FormCompletion $formCompletion)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\FormCompletion $formCompletion
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, FormCompletion $formCompletion)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\FormCompletion $formCompletion
     * @return \Illuminate\Http\Response
     */
    public function destroy(FormCompletion $formCompletion)
    {
        //
    }
}
