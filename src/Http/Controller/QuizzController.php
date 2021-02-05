<?php

namespace App\Http\Controller;

use App\Domain\Quizz\Entity\Quizz;
use App\Domain\Quizz\Flow\CreateQuizzFlow;
use App\Http\Form\QuizzType;
use App\Domain\Quizz\Repository\QuizzRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/quizz")
 */
class QuizzController extends AbstractController
{
    /**
     * @Route("/", name="quizz_index", methods={"GET"})
     */
    public function index(QuizzRepository $quizzRepository): Response
    {
        return $this->render('quizz/index.html.twig', [
            'quizzes' => $quizzRepository->findBy(['isPrivate' => false], ['id' => 'DESC']),
        ]);
    }

    /**
     * @Route("/new", name="quizz_new", methods={"GET","POST"})
     */
    public function new(Request $request, CreateQuizzFlow $flow): Response
    {
        $quizz = new Quizz();

        $flow->bind($quizz);
        $form = $flow->createForm();

        if ($flow->isValid($form)) {
            $flow->saveCurrentStepData($form);

            if ($flow->nextStep()) {
                $form = $flow->createForm();
            } else {
                $user = $this->getUser();

                if ($user) {
                    $quizz->setCreator($user);
                }

                $entityManager = $this->getDoctrine()->getManager();

                foreach ($quizz->getQuestions() as $question) {
                    $question->setQuizz($quizz);
                    $entityManager->persist($question);
                }

                $entityManager->persist($quizz);
                $entityManager->flush();

                $flow->reset();

                return $this->redirectToRoute('quizz_index');
            }
        }

        return $this->render('quizz/new.html.twig', [
            'quizz' => $quizz,
            'form' => $form->createView(),
            'flow' => $flow
        ]);
    }

    /**
     * @Route("/{slug}", name="quizz_show", methods={"GET"})
     */
    public function show(Quizz $quizz): Response
    {
        return $this->render('quizz/show.html.twig', [
            'quizz' => $quizz,
        ]);
    }

    /**
     * @Route("/{slug}/edit", name="quizz_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Quizz $quizz, CreateQuizzFlow $flow): Response
    {
        $flow->bind($quizz);
        $form = $flow->createForm();

        if ($flow->isValid($form)) {
            $flow->saveCurrentStepData($form);

            if ($flow->nextStep()) {
                $form = $flow->createForm();
            } else {
                $this->getDoctrine()->getManager()->flush();

                return $this->redirectToRoute('quizz_index');
            }
        }

        return $this->render('quizz/edit.html.twig', [
            'quizz' => $quizz,
            'form' => $form->createView(),
            'flow' => $flow
        ]);
    }

    /**
     * @Route("/{slug}", name="quizz_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Quizz $quizz): Response
    {
        if ($this->isCsrfTokenValid('delete'.$quizz->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($quizz);
            $entityManager->flush();
        }

        return $this->redirectToRoute('quizz_index');
    }
}
