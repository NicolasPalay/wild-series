<?php

namespace App\Controller;

use App\Repository\ProgramRepository;

use PharIo\Manifest\Email;
use Proxies\__CG__\App\Entity\Program;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;

class MailerController extends AbstractController
{
    #[Route('/mailer', name: 'app_mailer')]
    public function index(Program $program,ProgramRepository $programRepository): Response
    {

        $program = $programRepository->findOneBy([],['id' => 'DESC'],1);
        return $this->render('mailer/index.html.twig', [
            'program' => $program,
        ]);
    }

}
