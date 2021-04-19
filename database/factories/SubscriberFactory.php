<?php

namespace Database\Factories;

use App\Models\Subscriber;
use App\Utils\SecurityUtils;
use DateTime;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class SubscriberFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Subscriber::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $date = new DateTime();

        return [
            'client_id'     => Str::random() . $date->getTimestamp(),
            'client_secret' => SecurityUtils::getEncrypted(Str::random(32)),
        ];
    }
}
