<?php
// src/Controller/ProgramController.php
namespace App\Controller;

use App\Entity\Episode;
use App\Entity\Program;
use App\Entity\Season;
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
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;



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

        $session=$request->getSession();
        if ($session->has('nbVisite')) {
            $nbreVisite = $session->get('nbVisite')+1;

        }else {
            $nbreVisite = 1;
        }
        $session->set('nbVisite',$nbreVisite);

        $programs = $programRepository->findAll();

        return $this->render('program/index.html.twig', [
            'programs' => $programs
        ]);
    }

#[Route('/program/new',name : 'program_new')]
    public function new(Request $request, ProgramRepository $programRepository,SluggerInterface $slugger):Response
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
            $afeFilename = $slugger->slug($originalFilename);
            $newFilename = $afeFilename. '-' .uniqid().'.'.$file->guessExtension();
            try {
                $file->move(
                    $this->getParameter('file_directory'),
                    $newFilename
                );
            } catch (FileException $e) {
                echo 'handle exception if something happens during file upload';
            }
        }
        $program->setPoster($newFilename);
        $selectedActors = $form->get('actors')->getData();
        foreach ($selectedActors as $actor) {
            $program->addActor($actor);
            $actor->addProgram($program);
            }

        $programRepository->save($program, true);

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
        //$program = $programRepository->findOneBy(['id' => $id]);
        //$seasons = $seasonRepository->findby(['program' => $program]);
       // $episodes = $episodeRepository->findby(['season' => $seasons]);


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