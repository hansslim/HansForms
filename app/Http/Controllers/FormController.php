<?php

namespace App\Http\Controllers;

use App\Models\TextInput;
use DateTime;
use Exception;
use http\Env\Response;
use Illuminate\Http\Request;
use App\Models\Form;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
        //todo: validate data
        if (!$request->all()) return response("Invalid data (expected array of questions).", 400);

        $order = 0;
        $validatedData = [];

        foreach ($request->all() as $item) {
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

                if ($item['order'] !== $order) return response("Invalid order value.", 400);
                else $order++;

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
                case "date":{
                    try {
                        function validateDate($date, $format = 'Y-m-d H:i:s')
                        {
                            $d = DateTime::createFromFormat($format, $date);
                            return $d && $d->format($format) == $date;
                        }

                        //props: min, max
                        $min = null;
                        $max = null;

                        if (array_key_exists('min', $item)) {
                            if (validateDate($item['min'], 'Y-m-d')) $min = $item['min'];
                        }

                        if (array_key_exists('max', $item)) {
                            if (validateDate($item['max'], 'Y-m-d')) $max = $item['max'];
                        }

                        if ($max && $min) {
                            if (new DateTime($min) < new DateTime($max)) {
                                $validatedQuestion['min_length'] = $min;
                                $validatedQuestion['max_length'] = $max;
                            }
                        } else if ($max) $validatedQuestion['max_length'] = $max;
                        else if ($min) $validatedQuestion['min_length'] = $min;
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
                                    $validatedQuestion['min_length'] = $min;
                                    $validatedQuestion['max_length'] = $max;
                                }
                            } else if ($min) $validatedQuestion['min_length'] = $min;
                            else if ($max) $validatedQuestion['max_length'] = $max;
                            $validatedQuestion['can_be_decimal'] = $can_be_decimal;

                        } catch (Exception $exception) {
                            return response("Unhandled input error (in type-specific values). ({$exception->getMessage()})", 400);
                        }
                        break;
                    }
                case "select":
                {
                    try {
                        $is_multiselect = false;
                        $min_amount_of_answers = null;
                        $max_amount_of_answers = null;
                        $strict_amount_of_answers = null;
                        $choices = [];

                        if (array_key_exists('choices', $item)) {
                            if (!is_array($item['choices']))
                                return response("Invalid data for select question (expected array of choices).", 400);
                            if (count($item['choices']) >= 2)
                                $choices = $item['choices'];
                            else return response("Invalid amount of choices for select question (there should be at least two).", 400);
                        }
                        else return response("Invalid data for select question (choices are missing).", 400);

                        if (array_key_exists('is_multiselect', $item)) {
                            if (is_bool($item['is_multiselect'])) $is_multiselect = $item['is_multiselect'];
                        }
                        if (array_key_exists('min_amount_of_answers', $item)) {
                            if (intval($item['min_amount_of_answers'])) $min_amount_of_answers = intval($item['min_amount_of_answers']);
                        }
                        if (array_key_exists('max_amount_of_answers', $item)) {
                            if (intval($item['max_amount_of_answers'])) $max_amount_of_answers = intval($item['max_amount_of_answers']);
                        }
                        if (array_key_exists('strict_amount_of_answers', $item)) {
                            if (intval($item['strict_amount_of_answers'])) $strict_amount_of_answers = intval($item['strict_amount_of_answers']);
                        }

                        //todo: choice format and logic validation

                        if ($is_multiselect) {
                            if ($strict_amount_of_answers) {
                                $validatedQuestion['strict_amount_of_answers'] = $strict_amount_of_answers;
                            }
                            else {
                                if ($max_amount_of_answers && $min_amount_of_answers) {
                                    if ($min_amount_of_answers < $max_amount_of_answers) {
                                        $validatedQuestion['min_amount_of_answers'] = $min_amount_of_answers;
                                        $validatedQuestion['max_amount_of_answers'] = $max_amount_of_answers;
                                    }
                                } else if ($min_amount_of_answers) $validatedQuestion['min_amount_of_answers'] = $min_amount_of_answers;
                                else if ($max_amount_of_answers) $validatedQuestion['max_amount_of_answers'] = $max_amount_of_answers;
                            }
                        }

                    } catch (Exception $exception) {
                        return response("Unhandled input error (in type-specific values). ({$exception->getMessage()})", 400);
                    }
                    dd($item);
                    break;
                }
                //default: return response("Invalid question type.", 400);
            }
            $validatedData[] = $validatedQuestion;
        }

        //todo: create uuid

        //todo: save data

        return response("Form has been created.", 499);
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
