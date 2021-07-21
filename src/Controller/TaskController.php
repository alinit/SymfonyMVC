<?php


namespace App\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TaskController extends AbstractController
{
    /**
     * @Route("/tasker/{animal}")
     */
    public function index($animal): Response
    {
        return $this->json(['animal' => $animal, 'status' =>  Response::HTTP_OK]);
    }
}