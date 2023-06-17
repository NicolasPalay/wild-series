<?php
// src/Controller/ProgramController.php
namespace App\Controller;

use App\Entity\Episode;
use App\Entity\Program;
use App\Entity\Season;
use App\Form\SearchProgramType;
use App\Services\ProgramDuration;
use App\Form\ProgramType;
use App\Repository\EpisodeRepository;
use App\Repository\ProgramRepository;
use App\Repository\SeasonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
//use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;


class ProgramController extends AbstractController
{


    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    /**
     * @Route("/program/", name="program_index")
     */
    #[Route('/program/', name: 'program_index')]

    public function index(ProgramRepository $programRepository, Request $request): Response
    {
        $form = $this->createForm(SearchProgramType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $search = $form->getData()['search'];
            $programs = $programRepository->findLikeName($search);
        } else {
            $programs = $programRepository->findAll();
        }

        $session=$request->getSession();
        if ($session->has('nbVisite')) {
            $nbreVisite = $session->get('nbVisite')+1;

        }else {
            $nbreVisite = 1;
        }
        $session->set('nbVisite',$nbreVisite);



        return $this->render('program/index.html.twig', [
            'programs' => $programs, 'form' => $form,
        ]);
    }

#[Route('/program/new',name : 'program_new')]
    public function new(Request $request,  MailerInterface $mailer,ProgramRepository $programRepository,SluggerInterface $slugger):Response
    {
    $program = new Program();
    $form = $this->createForm(ProgramType::class,$program);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        /** @var UploadedFile $file */
        $file = $form->get('poster')->getData();
        //dd($file);

        if ($file) {
            $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename. '-' .uniqid().'.'.$file->guessExtension();
            try {
                $file->move(
                    $this->getParameter('file_directory'),
                    $newFilename
                );
            } catch (FileException $e) {
                echo 'handle exception if something happens during file upload';
            }

        $program->setPoster($newFilename);
        $selectedActors = $form->get('actors')->getData();
        foreach ($selectedActors as $actor) {
            $program->addActor($actor);
            $actor->addProgram($program);
            }
    }
        $program->setOwner($this->getUser());
        $programRepository->save($program, true);
        $email = (new Email())
         ->from('your_email@example.com')
            ->to('your_email@example.com')
            ->subject('Une nouvelle série vient d\'être publiée !')
            ->html('<p>Une nouvelle série vient d\'être publiée sur Wild Séries !</p>');

        $mailer->send($email);
        $this->getParameter('mailer_from');
        $this->addFlash('success', 'The new program has been created');
        return $this->redirectToRoute('program_index');
    }
    return $this->render('program/new.html.twig', [
        'form'=>$form,'program'=>$program
    ]);
    }

    #[Route('/program/{slug}', name: 'program_show')]
    public function show(Program $program,Season $season, Episode $episode, ProgramDuration $programDuration
    ): Response
    {
          return $this->render('program/show.html.twig', [
           'program' => $program,
           'seasons'=>$season,
           'episodes'=>$episode,
           'programDuration' => $programDuration->calculate($program,$season, $episode)
        ]);
    }
    #[Route('/program/{id}', name: 'program_delete', methods: ['POST'])]
    public function delete(Request $request, Program $program, ProgramRepository $programRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$program->getId(), $request->request->get('_token'))) {
            $programRepository->remove($program, true);
        }

        return $this->redirectToRoute('program_index', [], Response::HTTP_SEE_OTHER);
    }

}