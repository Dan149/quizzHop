<?php

namespace App\Domain\Quizz\EntityListener;

use App\Domain\Quizz\Entity\Quizz;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\String\Slugger\SluggerInterface;

class QuizzEntityListener
{
    private $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public function prePersist(Quizz $quizz, LifecycleEventArgs $event)
    {
        $quizz->computeSlug($this->slugger);
    }

    public function preUpdate(Quizz $quizz, LifecycleEventArgs $event)
    {
        $quizz->computeSlug($this->slugger);
    }
}
