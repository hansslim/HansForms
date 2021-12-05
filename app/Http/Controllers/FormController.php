<?php

namespace App\Http\Controllers;

use App\Models\BooleanInput;
use App\Models\DateInput;
use App\Models\FormElement;
use App\Models\InputElement;
use App\Models\NumberInput;
use App\Models\SelectInput;
use App\Models\SelectInputChoice;
use App\Models\TextInput;
use DateTime;
use Exception;
use http\Env\Response;
use Illuminate\Http\Request;
use App\Models\Form;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;
use function MongoDB\BSON\toJSON;

class FormController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return Form::where('user_id', Auth::user()->id)
            ->without('formElements', 'user')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $userId = -1;

        $formProps = [];
        $formProps['header'] = null;
        $formProps['description'] = null;

        $formProps['start_time'] = null;
        $formProps['end_time'] = null;

        if (array_key_exists('start_time', $request->all()) && array_key_exists('end_time', $request->all())) {
            $startDate = str_replace("T", " ", $request->all()['start_time']);
            $endDate = str_replace("T", " ", $request->all()['end_time']);

            function validateDate($date, $format = 'Y-m-d H:i')
            {
                $d = DateTime::createFromFormat($format, $date);
                return $d && $d->format($format) == $date;
            }

            if (validateDate($startDate) && validateDate($endDate)) {
                if (new DateTime($request->all()['start_time']) < new DateTime($request->all()['end_time'])) {
                    $formProps['start_time'] = new DateTime($request->all()['start_time']);
                    $formProps['end_time'] = new DateTime($request->all()['end_time']);
                } else return response("Invalid data (start is higher than end).", 400);
            } else {
                return response("Invalid data (invalid start/end date).", 400);
            }
        } else return response("Invalid data (missing start/end date).", 400);

        //todo: validate and process these values
        $formProps['has_private_token'] = false; //wip

        if (!Auth::user()) return response("Unauthorized - log in to create forms...", 401);
        else $userId = Auth::user()->id;

        if (!$request->all()) return response("Invalid data (expected data).", 400);

        if (!array_key_exists('items', $request->all()))
            return response("Invalid data (missing array of questions).", 400);

        if (!is_array($request->all()['items']))
            return response("Invalid data (expected array of questions).", 400);

        if (count($request->all()['items']) < 1)
            return response("Invalid data (expected non-empty array of questions).", 400);

        if (array_key_exists('header', $request->all())) {
            if (strval($request->all()['header']) !== "") {
                $formProps['header'] = strval($request->all()['header']);
            } else return response("Invalid data (expected non-empty header of form).", 400);
        } else return response("Invalid data (missing header of form).", 400);

        if (array_key_exists('description', $request->all())) {
            if (strval($request->all()['description']) !== "") {
                $formProps['description'] = strval($request->all()['description']);
            }
        }

        $questionOrder = 0;
        $validatedQuestions = [];

        foreach ($request->all()['items'] as $item) {
            $validatedQuestion = [];
            //main props: header, is_mandatory, order, type
            try {
                if (!$item) return response("Invalid data (expected array of questions).", 400);

                if (
                    !array_key_exists('type', $item) ||
                    !array_key_exists('header', $item) ||
                    !array_key_exists('is_mandatory', $item) ||
                    !array_key_exists('order', $item)
                ) return response("Invalid data (missing required values).", 400);

                if (!is_string($item['type'])) return response("Invalid type value type.", 400);

                if (!is_string($item['header'])) return response("Invalid header value type.", 400);

                if (!is_bool($item['is_mandatory'])) return response("Invalid is_mandatory value type.", 400);

                if (!is_int($item['order'])) return response("Invalid order value type.", 400);


                if ($item['order'] !== $questionOrder) {
                    //dd("order error", $item['order'], $questionOrder, $request->all()['items'], $request->all()); //debugging only
                    return response("Invalid order value.", 400);
                } else $questionOrder++;

                //validated
                $validatedQuestion['type'] = $item['type'];
                $validatedQuestion['header'] = $item['header'];
                $validatedQuestion['is_mandatory'] = $item['is_mandatory'];
                $validatedQuestion['order'] = $item['order'];
            } catch (Exception $exception) {
                return response("Unhandled input error (in basic values).", 400);
            }
            //check type
            switch ($item['type']) {
                case "text":
                {
                    //props: min_length, max_length, strict_length
                    try {
                        $min = null;
                        $max = null;
                        $strict = null;

                        $validatedQuestion['min_length'] = $min;
                        $validatedQuestion['max_length'] = $max;
                        $validatedQuestion['strict_length'] = $strict;

                        if (array_key_exists('min_length', $item)) {
                            if (
                                intval($item['min_length']) &&
                                intval($item['min_length']) > 0
                            ) $min = intval($item['min_length']);
                        }
                        if (array_key_exists('max_length', $item)) {
                            if (
                                intval($item['max_length']) &&
                                intval($item['max_length']) > 0
                            ) $max = intval($item['max_length']);
                        }
                        if (array_key_exists('strict_length', $item)) {
                            if (
                                intval($item['strict_length']) &&
                                intval($item['strict_length']) > 0
                            ) $strict = intval($item['strict_length']);
                        }

                        if ($strict) {
                            $validatedQuestion['strict_length'] = $strict;
                        } else if ($max && $min) {
                            if ($min < $max) {
                                $validatedQuestion['min_length'] = $min;
                                $validatedQuestion['max_length'] = $max;
                            }
                        } else if ($min) $validatedQuestion['min_length'] = $min;
                        else if ($max) $validatedQuestion['max_length'] = $max;

                    } catch (Exception $exception) {
                        return response("Unhandled input error (in type-specific values). ({$exception->getMessage()})", 400);
                    }
                    break;
                }
                case "date":
                {
                    try {
                        function validateDate($date, $format = 'Y-m-d H:i:s')
                        {
                            $d = DateTime::createFromFormat($format, $date);
                            return $d && $d->format($format) == $date;
                        }

                        //props: min, max
                        $min = null;
                        $max = null;

                        $validatedQuestion['min'] = $min;
                        $validatedQuestion['max'] = $max;

                        if (array_key_exists('min', $item)) {
                            if (validateDate($item['min'], 'Y-m-d')) $min = $item['min'];
                        }

                        if (array_key_exists('max', $item)) {
                            if (validateDate($item['max'], 'Y-m-d')) $max = $item['max'];
                        }

                        if ($max && $min) {
                            if (new DateTime($min) < new DateTime($max)) {
                                $validatedQuestion['min'] = $min;
                                $validatedQuestion['max'] = $max;
                            }
                        } else if ($max) $validatedQuestion['max'] = $max;
                        else if ($min) $validatedQuestion['min'] = $min;
                    } catch (Exception $exception) {
                        return response("Unhandled input error (in type-specific values). ({$exception->getMessage()})", 400);
                    }
                    break;
                }
                case "boolean":
                    break;
                case "number":
                {
                    //props: min, max, can_be_decimal
                    try {
                        $min = null;
                        $max = null;
                        $can_be_decimal = false;

                        $validatedQuestion['min'] = $min;
                        $validatedQuestion['max'] = $max;
                        $validatedQuestion['can_be_decimal'] = $can_be_decimal;

                        if (array_key_exists('min', $item)) {
                            if (intval($item['min'])) $min = intval($item['min']);
                        }
                        if (array_key_exists('max', $item)) {
                            if (intval($item['max'])) $max = intval($item['max']);
                        }
                        if (array_key_exists('can_be_decimal', $item)) {
                            if (is_bool($item['can_be_decimal'])) $can_be_decimal = $item['can_be_decimal'];
                        }

                        if ($max && $min) {
                            if ($min < $max) {
                                $validatedQuestion['min'] = $min;
                                $validatedQuestion['max'] = $max;
                            }
                        } else if ($min) $validatedQuestion['min'] = $min;
                        else if ($max) $validatedQuestion['max'] = $max;
                        $validatedQuestion['can_be_decimal'] = $can_be_decimal;

                    } catch (Exception $exception) {
                        return response("Unhandled input error (in type-specific values). ({$exception->getMessage()})", 400);
                    }
                    break;
                }
                case "select":
                {
                    try {
                        //todo: validate max amount concerning the choices amount
                        $is_multiselect = false;
                        $min_amount_of_answers = null;
                        $max_amount_of_answers = null;
                        $strict_amount_of_answers = null;
                        $has_hidden_label = false;
                        $choices = [];

                        $validatedQuestion['is_multiselect'] = $is_multiselect;
                        $validatedQuestion['min_amount_of_answers'] = $min_amount_of_answers;
                        $validatedQuestion['max_amount_of_answers'] = $max_amount_of_answers;
                        $validatedQuestion['strict_amount_of_answers'] = $strict_amount_of_answers;
                        $validatedQuestion['has_hidden_label'] = $has_hidden_label;


                        if (array_key_exists('choices', $item)) {
                            if (!is_array($item['choices'])) {
                                return response("Invalid data for select question (expected array of choices).", 400);
                            }
                            if (count($item['choices']) >= 2) {
                                if (array_key_exists('has_hidden_label', $item)) {
                                    if (is_bool($item["has_hidden_label"])) $has_hidden_label = $item["has_hidden_label"];
                                } else return response("Invalid data (missing has_hidden_label value).", 400);

                                $choiceOrder = 0;
                                $uniqueHiddenLabels = [];
                                foreach ($item['choices'] as $choice) {
                                    $hidden_label = null;
                                    $text = null;
                                    $thisChoiceOrder = null;
                                    //order check
                                    if (array_key_exists('order', $choice)) {
                                        if (intval($choice['order']) !== $choiceOrder) {
                                            return response("Invalid data (order is not valid).", 400);
                                        } else {
                                            $thisChoiceOrder = $choiceOrder;
                                            $choiceOrder++;
                                        }
                                    } else return response("Invalid data (order is missing).", 400);
                                    //hidden label check
                                    if ($has_hidden_label) {
                                        if (array_key_exists('hidden_label', $choice)) {
                                            if ($choice['hidden_label'] === "0") {
                                                $hidden_label = 0;
                                                if (in_array($hidden_label, $uniqueHiddenLabels)) {
                                                    return response("Invalid data (hidden_label is not unique).", 400);
                                                } else array_push($uniqueHiddenLabels, $hidden_label);
                                            } else if (intval($choice['hidden_label'])) {
                                                $hidden_label = intval($choice['hidden_label']);
                                                if (in_array($hidden_label, $uniqueHiddenLabels)) {
                                                    return response("Invalid data (hidden_label is not unique).", 400);
                                                } else array_push($uniqueHiddenLabels, $hidden_label);
                                            } else return response("Invalid data (invalid hidden_label type in choice).", 400);
                                        } else return response("Invalid data (missing hidden_label in choice).", 400);
                                    }
                                    //text check
                                    if (array_key_exists('text', $choice)) {
                                        if ($choice['text'] === "0") $text = $choice['text'];
                                        else if (mb_strlen($choice['text']) && !is_null($choice['text'])) $text = strval($choice['text']);
                                        else return response("Invalid data (empty text in choice).", 400);
                                    } else return response("Invalid data (text is missing).", 400);

                                    $choices[] = ['text' => $text, "hidden_label" => $hidden_label, "order" => $thisChoiceOrder];
                                }
                            } else return response("Invalid amount of choices for select question (there should be at least two).", 400);
                        } else return response("Invalid data for select question (choices are missing).", 400);

                        if (array_key_exists('is_multiselect', $item)) {
                            if (is_bool($item['is_multiselect'])) $is_multiselect = $item['is_multiselect'];
                        }
                        if (array_key_exists('min_amount_of_answers', $item)) {
                            if (intval($item['min_amount_of_answers']) && intval($item['min_amount_of_answers']) >= 0) $min_amount_of_answers = intval($item['min_amount_of_answers']);
                        }
                        if (array_key_exists('max_amount_of_answers', $item)) {
                            if (intval($item['max_amount_of_answers']) && intval($item['max_amount_of_answers']) > 0) $max_amount_of_answers = intval($item['max_amount_of_answers']);
                        }
                        if (array_key_exists('strict_amount_of_answers', $item)) {
                            if (intval($item['strict_amount_of_answers']) && intval($item['strict_amount_of_answers']) > 0) $strict_amount_of_answers = intval($item['strict_amount_of_answers']);
                        }

                        $validatedQuestion['is_multiselect'] = $is_multiselect;
                        if ($is_multiselect) {
                            if ($strict_amount_of_answers) {
                                $validatedQuestion['strict_amount_of_answers'] = $strict_amount_of_answers;
                            } else {
                                if ($max_amount_of_answers && $min_amount_of_answers) {
                                    if ($min_amount_of_answers < $max_amount_of_answers) {
                                        $validatedQuestion['min_amount_of_answers'] = $min_amount_of_answers;
                                        $validatedQuestion['max_amount_of_answers'] = $max_amount_of_answers;
                                    }
                                } else if ($min_amount_of_answers) $validatedQuestion['min_amount_of_answers'] = $min_amount_of_answers;
                                else if ($max_amount_of_answers) $validatedQuestion['max_amount_of_answers'] = $max_amount_of_answers;
                            }
                        }

                        $validatedQuestion['has_hidden_label'] = $has_hidden_label;
                        $validatedQuestion['choices'] = $choices;

                    } catch (Exception $exception) {
                        return response("Unhandled input error (in type-specific values). ({$exception->getMessage()})", 400);
                    }

                    break;
                }
                default:
                    return response("Invalid question type.", 400);
            }
            $validatedQuestions[] = $validatedQuestion;
        }

        try {
            DB::transaction(function () use ($validatedQuestions, $userId, $formProps) {
                $formSlug = Uuid::uuid4()->toString();
                $newForm = Form::create([
                    'user_id' => $userId,
                    'slug' => $formSlug,
                    'name' => $formProps['header'],
                    'description' => $formProps['description'],
                    'start_time' => $formProps['start_time'],
                    'end_time' => $formProps['end_time'],
                    'has_private_token' => $formProps['has_private_token'],
                ]);

                foreach ($validatedQuestions as $item) {
                    $newFormElement = FormElement::create([
                        'order' => $item['order'],
                        'form_id' => $newForm->id
                    ]);
                    if ($item !== "new_page") {
                        $newInputElement = InputElement::create([
                            'header' => $item['header'],
                            'is_mandatory' => $item['is_mandatory'],
                            'form_element_id' => $newFormElement->id
                        ]);
                        switch ($item['type']) {
                            case 'text':
                            {
                                TextInput::create([
                                    'min_length' => $item['min_length'],
                                    'max_length' => $item['max_length'],
                                    'strict_length' => $item['strict_length'],
                                    'input_element_id' => $newInputElement->id
                                ]);
                                break;
                            }
                            case 'number':
                            {
                                NumberInput::create([
                                    'min' => $item['min'],
                                    'max' => $item['max'],
                                    'can_be_decimal' => $item['can_be_decimal'],
                                    'input_element_id' => $newInputElement->id
                                ]);
                                break;
                            }
                            case 'date':
                            {
                                DateInput::create([
                                    'min' => $item['min'],
                                    'max' => $item['max'],
                                    'input_element_id' => $newInputElement->id
                                ]);
                                break;
                            }
                            case 'boolean':
                            {
                                BooleanInput::create([
                                    'input_element_id' => $newInputElement->id
                                ]);
                                break;
                            }
                            case 'select':
                            {
                                $newSelectInput = SelectInput::create([
                                    'is_multiselect' => $item['is_multiselect'],
                                    'min_amount_of_answers' => $item['min_amount_of_answers'],
                                    'max_amount_of_answers' => $item['max_amount_of_answers'],
                                    'strict_amount_of_answers' => $item['strict_amount_of_answers'],
                                    'has_hidden_label' => $item['has_hidden_label'],
                                    'input_element_id' => $newInputElement->id
                                ]);

                                foreach ($item['choices'] as $choice) {
                                    SelectInputChoice::create([
                                        'text' => $choice['text'],
                                        'hidden_label' => $choice['hidden_label'],
                                        'order' => $choice['order'],
                                        'select_input_id' => $newSelectInput->id
                                    ]);
                                }
                                break;
                            }
                        }
                    }
                }
            });
        } catch (Exception $exception) {
            dd($exception);
            //return response("{$exception->getMessage()}", 500);
        }

        return response("Form has been created.", 200);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($slug)
    {
        $form = Form::where('slug', $slug)
            ->with("user")
            ->first();
        if ($form) {
            unset($form->user->email);
            return $form;
        } else return response('Not Found', 404);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //todo: refuse update when is start_time > server time
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($slug)
    {
        if (!Auth::user()) return response("Unauthorized - log in to delete forms...", 401);
        else {
            DB::transaction(function () use ($slug) {
                $form = Form::where('slug', $slug)->first();
                if ($form) {
                    if ($form->user_id == Auth::user()->id) {
                        if ($form->delete()) {
                            return response("Form was deleted.", 200);
                        } else return response("Form deletion failed.", 500);
                    }
                    return response("Form was not deleted - you are not the owner of this form.", 500);
                } else return response("Requested form (delete) was not found", 404);
            });
        }
    }
}
