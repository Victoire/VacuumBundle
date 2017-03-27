<?php

namespace Victoire\DevTools\VacuumBundle\Pipeline;

use Doctrine\ORM\EntityManager;

interface PersisterStagesInterface extends StageInterface {

    public function __construct(EntityManager $entityManager);
}