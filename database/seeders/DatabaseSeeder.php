<?php

namespace Database\Seeders;

use App\Models\BooleanInput;
use App\Models\Form;
use App\Models\FormElement;
use App\Models\InputElement;
use App\Models\NewPage;
use App\Models\NumberInput;
use App\Models\TextInput;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        InputElement::truncate();
        FormElement::truncate();
        Form::truncate();
        User::truncate();

        User::create([
            'name' => "Jan Vývojář",
            'email' => "v@v.v",
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
        ]);
        User::factory(3)->create();

        Form::factory(3)->create(['user_id' => 1]);

        $myForm = Form::factory()->create(['user_id' => 1]);

        for ($i = 0; $i < 5; $i++) {
            $formElement = FormElement::create([
                'order' => $i,
                'form_id' => $myForm->id
            ]);
            if ($i !== 3) {
                $inputElement = InputElement::factory()->create(['form_element_id' => $formElement->id]);

                $randomInput = random_int(0,2);
                switch ($randomInput) {
                    case 0: NumberInput::create(['input_element_id' => $inputElement->id]); break;
                    case 1: TextInput::create(['input_element_id' => $inputElement->id]); break;
                    default: BooleanInput::create(['input_element_id' => $inputElement->id]); break;
                }
            }
            else {
                NewPage::create(['form_element_id' => $formElement->id]);
            }
        }
    }
}
