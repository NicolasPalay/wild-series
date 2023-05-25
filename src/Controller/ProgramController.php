<?php
// src/Controller/ProgramController.php
namespace App\Controller;

use App\Entity\Program;
use App\Form\ProgramType;
use App\Repository\ProgramRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
class ProgramController extends AbstractController
{
    /**
     * @Route("/program/", name="program_index")
     */
    #[Route('/program/', name: 'program_index')]

    public function index(ProgramRepository $programRepository): Response
    {
        $programs = $programRepository->findAll();
        return $this->render('program/index.html.twig', [
            'programs' => $programs,
        ]);
    }
    #[Route('/show/{id<^[0-9]+$>}', name: 'program_show')]
    public function show(ProgramRepository $programRepository, int $id): Response
    {
        $program = $programRepository->findOneBy(['id' => $id]);
        if (!$program) {
            throw $this->createNotFoundException(
                'No program with id : '.$id.' found in program\'s table.'
            );
        }
        return $this->render('program/show.html.twig', [
            'program' => $program,
        ]);
    }
#[Route('/program/new',name : 'program_new')]
    public function new(Request $request, ProgramRepository $programRepository,SluggerInterface $slugger):Response
    {
    $program = new Program();
    $form = $this->createForm(ProgramType::class,$program);
    $form->handleRequest($request);
    if ($form->isSubmitted()) {
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
        $programRepository->save($program, true);
        return $this->redirectToRoute('program_index');
    }
    return $this->render('program/new.html.twig', [
        'form'=>$form
    ]);
    }

        #[Route('/program/list/{page}', requirements: ['page'=>'\d+'], name:'program_list')]
     public function list(int $page = 1): Response
     {
         return $this->render('program/list.html.twig', ['page' => $page]);
     }

    #[Route('/program/{id}', requirements: ['id'=>'\d+'],methods: ['GET'], name:'program_id')]
    public function show2(int $id = 1): Response
    {
        return $this->render('program/show.html.twig', ['id' => $id]);
    }
}