<?php

namespace App\Http\Controllers;

use App\Exports\FormCompletionsExport;
use App\Models\BooleanInputAnswer;
use App\Models\DateInputAnswer;
use App\Models\Form;
use App\Models\FormCompletion;
use App\Models\FormPrivateAccessToken;
use App\Models\NumberInputAnswer;
use App\Models\SelectInputAnswer;
use App\Models\TextInputAnswer;
use DateTime;
use Exception;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Writer\Csv;

class FormCompletionController extends Controller
{
    //validate date format
    private function validateDate($date, $format = 'Y-m-d H:i:s')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($slug)
    {
        $user = Auth::user();
        if ($user) {
            $wantedFormResults = Form::where(['slug' => $slug, 'user_id' => $user->id])->with(
                'formElements.inputElement.textInput.textInputAnswers',
                'formElements.inputElement.numberInput.numberInputAnswers',
                'formElements.inputElement.dateInput.dateInputAnswers',
                'formElements.inputElement.booleanInput.booleanInputAnswers',
                'formElements.inputElement.selectInput.selectInputChoices.selectInputAnswers',
            )->first();
            if ($wantedFormResults) {
                $hasResults = FormCompletion::where(['form_id' => $wantedFormResults->id])->first();
                if ($hasResults) {
                    $wantedFormResults->results_table = (new FormCompletionsExport($wantedFormResults))->array();
                    return $wantedFormResults;
                }
                else return response("No content.", 204);
            } else return response("Not found.", 404);
        } else return response("Unauthorized.", 401);
    }

    public function publicIndex($slug) {
        $wantedFormResults = Form::where(['slug' => $slug, 'has_public_results' => true])->with(
            'formElements.inputElement.textInput.textInputAnswers',
            'formElements.inputElement.numberInput.numberInputAnswers',
            'formElements.inputElement.dateInput.dateInputAnswers',
            'formElements.inputElement.booleanInput.booleanInputAnswers',
            'formElements.inputElement.selectInput.selectInputChoices.selectInputAnswers',
        )->first();
        if ($wantedFormResults) {
            $hasResults = FormCompletion::where(['form_id' => $wantedFormResults->id])->first();

            $atLeastOnePublic = false;
            foreach ($wantedFormResults->formElements as $key => $element) {
                if ($element->inputElement) {
                    if (!$element->inputElement->has_public_results) unset($wantedFormResults->formElements[$key]);
                    else $atLeastOnePublic = true;
                }
            }

            if (!$atLeastOnePublic) return response("No content.", 204);

            if ($hasResults) {
                $wantedFormResults->results_table = (new FormCompletionsExport($wantedFormResults))->array();
                return $wantedFormResults;
            }
            else return response("No content.", 204);
        }
        else return response("Not found", 404);
    }

    public function export($slug) {
        $data = $this->index($slug);
        if ($data) {
            if ($data instanceof Form) { //means that some data were sent (so it's 200)
                return Excel::download(new FormCompletionsExport($data), 'export.xlsx');
            }
            else {
                return match ($data->getStatusCode()) {
                    204 => response("No content.", 204),
                    401 => response("Unauthorized.", 401),
                    404 => response("Not found.", 404),
                    default => response("Unhandled error.", 500),
                };
            }
        }
        else return response("Unhandled error (no data).", 500);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param $slug
     * @return \Illuminate\Http\Response
     * @throws Exception
     */
    public function store(Request $request, $slug, $privateForm = false) : \Illuminate\Http\Response
    {
        $answeredForm = Form::where('slug', $slug)->first();
        if (!$answeredForm) return response("Not Found", 404);

        if ($answeredForm->has_private_token) {
            if (!$privateForm) return response("Bad request (access denied)", 400);
        }

        //answering not published/expired form validation
        $currentTime = time();
        if ($currentTime > strtotime($answeredForm->end_time)) {
            return response("Bad request (answered form is expired)", 400);
        }
        if ($currentTime < strtotime($answeredForm->start_time)) {
            return response("Bad request (answered form has not been published yet)", 400);
        }

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
            } else { //useless? at least one must be mandatory
                //response is null (for forms that have some questions non-required)
                $corresponding['type'] = $type;
                $corresponding['id'] = $specificInputId;
                $corresponding['value'] = '';
                return $corresponding;
            }
        }

        //data validation
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
                                return response("Bad request (validation was not successful [boolean-{$answer['id']} is mandatory])", 400);
                            } else {
                                $answer['value'] = null;
                                $correspondingWithForm[] = $answer;
                            }
                            break;
                        }
                        default:
                            return response("Bad request (validation was not successful [boolean-{$answer['id']} is invalid])", 400);
                    }

                } else if ($value->inputElement->dateInput) {
                    $answer = getCorrespondingValue($request, "date", $value->inputElement->dateInput->id);
                    switch ($answer['value']) {
                        case null:
                        case 'null':
                        case '':
                        {
                            if ($isMandatory) {
                                return response("Bad request (validation was not successful [date-{$answer['id']} is mandatory])", 400);
                            } else {
                                $answer['value'] = null;
                                $correspondingWithForm[] = $answer;
                            }
                            break;
                        }
                        default:
                        {
                            

                            if (!$this->validateDate($answer['value'], 'Y-m-d')) {
                                return response("Bad request (validation was not successful [date-{$answer['id']} unsupported format])", 400);
                            }

                            //validate conditions
                            $answerDateTime = new DateTime($answer['value']);

                            if ($value->inputElement->dateInput->min) {
                                $minDateTime = new DateTime($value->inputElement->dateInput->min);
                                if ($answerDateTime < $minDateTime) {
                                    return response("Bad request (validation was not successful [date-{$answer['id']} is lower than expected])", 400);
                                }
                            }
                            if ($value->inputElement->dateInput->max) {
                                $maxDateTime = new DateTime($value->inputElement->dateInput->max);
                                if ($answerDateTime > $maxDateTime) {
                                    return response("Bad request (validation was not successful [date-{$answer['id']} is higher than expected])", 400);

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
                                return response("Bad request (validation was not successful [text-{$answer['id']} is mandatory])", 400);
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
                                    return response("Bad request (validation was not successful [text-{$answer['id']} doesn't fit strict length])", 400);
                                }
                            } else {
                                if ($value->inputElement->textInput->min_length) {
                                    if (mb_strlen($answer['value']) < $value->inputElement->textInput->min_length) {
                                        return response("Bad request (validation was not successful [text-{$answer['id']} has lower length than expected])", 400);
                                    }
                                }
                                if ($value->inputElement->textInput->max_length) {
                                    if (mb_strlen($answer['value']) > $value->inputElement->textInput->max_length) {
                                        return response("Bad request (validation was not successful [text-{$answer['id']} has higher length than expected])", 400);
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
                                return response("Bad request (validation was not successful [number-{$answer['id']} is mandatory])", 400);
                            } else {
                                $answer['value'] = null;
                                $correspondingWithForm[] = $answer;
                            }
                            break;
                        }
                        default:
                        {
                            if (preg_match('/(^(\+|-)?[0-9]+[,|.][0-9]+)|(^(\+|-)?[0-9]*$)/', $answer['value'])) {
                                if (is_numeric($answer['value'])) {

                                    if (!$value->inputElement->numberInput->can_be_decimal) {
                                        if (!preg_match('/^(\+|-)?[0-9]*$/', $answer['value'])) {
                                            return response("Bad request (validation was not successful [number-{$answer['id']} cannot be decimal])", 400);
                                        }
                                    }

                                    if ($value->inputElement->numberInput->min) {
                                        if ($answer['value'] < $value->inputElement->numberInput->min) {
                                            return response("Bad request (validation was not successful [number-{$answer['id']} is lower than expected])", 400);
                                        }
                                    }
                                    if ($value->inputElement->numberInput->max) {
                                        if ($answer['value'] > $value->inputElement->numberInput->max) {
                                            return response("Bad request (validation was not successful [number-{$answer['id']} is higher than expected])", 400);
                                        }
                                    }

                                    $correspondingWithForm[] = $answer;
                                } else {
                                    return response("Bad request (validation was not successful [number-{$answer['id']} isn't number])", 400);
                                }
                            } else {
                                return response("Bad request (validation was not successful [number-{$answer['id']} doesn't match number format])", 400);
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
                                    return response("Bad request (validation was not successful [select-{$answer['id']} (checkbox) is mandatory])", 400);
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
                                        return response("Bad request (validation was not successful [select-{$answer['id']} (checkbox) expects more answers])", 400);
                                    }
                                }

                                if ($value->inputElement->selectInput->max_amount_of_answers) {
                                    if (count($validatedChoices) > $value->inputElement->selectInput->max_amount_of_answers) {
                                        return response("Bad request (validation was not successful [select-{$answer['id']} (checkbox) expects less answers])", 400);
                                    }
                                }

                                if ($value->inputElement->selectInput->strict_amount_of_answers) {
                                    if (count($validatedChoices) != $value->inputElement->selectInput->strict_amount_of_answers) {
                                        return response("Bad request (validation was not successful [select-{$answer['id']} (checkbox) expects strict amount of answers])", 400);
                                    }
                                }

                                $correspondingWithForm = array_merge($correspondingWithForm, $validatedChoices);
                                if (!$validChoiceFound) {
                                    return response("Bad request (validation was not successful [select-{$answer['id']} (checkbox) invalid answers])", 400);
                                }
                            }
                        }

                    } else {
                        switch ($answer['value']) {
                            case null:
                            case 'null':
                            case '':
                            case 'choice-null':
                            {
                                if ($isMandatory) {
                                    return response("Bad request (validation was not successful [select-{$answer['id']} (radio) is mandatory])", 400);
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
                                        return response("Bad request (validation was not successful [select-{$answer['id']} (radio) invalid answer])", 400);
                                    }
                                } catch (Exception $exception) {
                                    return response("Bad request (validation was not successful [select-{$answer['id']} (radio) invalid data]) - {$exception->getMessage()}", 400);
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
            return response("Bad request (validation was not successful [Database manipulation failure])", 400);
        }
        return response('Answer has been proceeded successfully.', 200);
    }

    public function privateStore(Request $request, $token) {
        $formToken = FormPrivateAccessToken::where('token', $token)->first();
        if (!$formToken) return response("Bad request (token is invalid)", 400);
        if ($formToken->was_used) return response("Bad request (token has been already used)", 410);
        $formSlug = (Form::where('id', $formToken->form_id)->without('formElements')->first())->slug;

        $response = $this->store($request, $formSlug, true);
        if ($response->getStatusCode() === 200) {
            DB::transaction(function () use ($formToken) {
                $formToken->was_used = true;
                $formToken->save();
            });
        }
        return $response;
    }
}
