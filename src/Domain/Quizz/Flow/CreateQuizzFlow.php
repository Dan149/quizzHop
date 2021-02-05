<?php
namespace App\Domain\Quizz\Flow;

use App\Http\Form\QuizzType;
use Craue\FormFlowBundle\Form\FormFlow;

class CreateQuizzFlow extends FormFlow
{
    protected function loadStepsConfig()
    {
        return [
            [
                'label' => 'Description',
                'form_type' => QuizzType::class
            ],
            [
                'label' => 'Game',
                'form_type' => QuizzType::class
            ],
            [
                'label' => 'Overview'
            ]
        ];
    }
}
