<?php

namespace App\Http\Controller;

use App\Repository\QuizzRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PagesController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function home(QuizzRepository $quizzRepository): Response
    {
        return $this->render(
            'pages/home.html.twig',
            ['quizzes' => $quizzRepository->findLatest()]
        );
    }
}
