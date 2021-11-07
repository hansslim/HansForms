<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\FormCompletion;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use mysql_xdevapi\Exception;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class FormCompletionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $slug)
    {
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
                        case 'false':
                        {
                            $correspondingWithForm[] = $answer;
                            break;
                        }
                        case "":
                        case null:
                        case 'null':
                        {
                            if ($isMandatory) {
                                abort(400);
                            } else {
                                $answer['value'] = null;
                                $correspondingWithForm[] = $answer;
                            }
                            break;
                        }
                        default:
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

                            if (!validateDate($answer['value'], 'Y-m-d')) abort(400);

                            //validate conditions
                            $answerDateTime = new DateTime($answer['value']);

                            if ($value->inputElement->dateInput->min) {
                                $minDateTime = new DateTime($value->inputElement->dateInput->min);
                                if ($answerDateTime < $minDateTime) abort(400);
                            }
                            if ($value->inputElement->dateInput->max) {
                                $maxDateTime = new DateTime($value->inputElement->dateInput->max);
                                if ($answerDateTime > $maxDateTime) abort(400);
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
                                if (mb_strlen($answer['value']) != $value->inputElement->textInput->strict_length) abort(400);
                            } else {
                                if ($value->inputElement->textInput->min_length) {
                                    if (mb_strlen($answer['value']) < $value->inputElement->textInput->min_length) abort(400);
                                }
                                if ($value->inputElement->textInput->max_length) {
                                    if (mb_strlen($answer['value']) > $value->inputElement->textInput->max_length) abort(400);
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
                                        if (!preg_match('/^[0-9]*$/', $answer['value'])) abort(400);
                                    }

                                    if ($value->inputElement->numberInput->min) {
                                        if ($answer['value'] < $value->inputElement->numberInput->min) abort(400);
                                    }
                                    if ($value->inputElement->numberInput->max) {
                                        if ($answer['value'] > $value->inputElement->numberInput->max) abort(400);
                                    }

                                    $correspondingWithForm[] = $answer;
                                } else abort(400);
                            } else abort(400);
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

                                //todo: validate min/max amount of answers,...
                                $correspondingWithForm[] = $validatedChoices;

                                if (!$validChoiceFound) abort(400);
                            }
                        }

                    } else {
                        switch ($answer['value']) {
                            case null:
                            case 'null':
                            case '':
                            {
                                if ($isMandatory) {
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

                                    if (!$validChoiceFound) abort(400);
                                } catch (Exception $exception) {
                                    abort(400);
                                }
                            }
                        }
                    }
                }
            }
        }


        return $correspondingWithForm;
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
