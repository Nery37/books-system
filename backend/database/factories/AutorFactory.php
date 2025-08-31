<?php

namespace Database\Factories;

use App\Models\Autor;
use Illuminate\Database\Eloquent\Factories\Factory;

class AutorFactory extends Factory
{
    protected $model = Autor::class;

    public function definition()
    {
        return [
            'CodAu' => $this->faker->unique()->numberBetween(1, 999999),
            'Nome' => $this->faker->name()
        ];
    }
}
