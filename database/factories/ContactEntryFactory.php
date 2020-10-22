<?php

namespace Database\Factories;

use App\Models\ContactEntry;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContactEntryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ContactEntry::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $types = ['email', 'phone'];
        $key = array_rand($types);
        if ($types[$key] === 'email') {
            $contactDetail = $this->faker->email;
        } else {
            $contactDetail = $this->faker->phoneNumber;
        }
        return [
            'type' => $types[$key],
            'value' => $contactDetail,
        ];
    }
}
