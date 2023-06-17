<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use App\Repository\ProgramRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[Route('/category',name:'category_')]
class CategoryController extends AbstractController
{
    /**
     * List all category order by DESC and limit 3
     *
     * @param CategoryRepository $categoryRepository
     * @return Response
     */
        #[Route('/', name: 'index')]
        #[IsGranted('ROLE_ADMIN')]
    public function index(CategoryRepository $categoryRepository): Response
    {

        $categories = $categoryRepository->findAll();

        return $this->render('category/index.html.twig', [
            'categories' => $categories,
        ]);
    }
    /**
     * @Route("/category/new", name="category_new")
     */
    #[Route('/new', name: 'new')]
    public function new(Request $request, CategoryRepository $categoryRepository): Response
    {

        $category = new Category();

        // Create the form, linked with $category
        $form = $this->createForm(CategoryType::class, $category);
        //creer la requete
        $form->handleRequest($request);
        //soumet a requete
        if ($form->isSubmitted()) {

            $categoryRepository->save($category, true);

            // Redirect to categories list
            return $this->redirectToRoute('category_index');
        }

        // Render the form
        return $this->render('category/new.html.twig', [
            'form' => $form,
        ]);
    }

    /**
     *List one category with programs
     *
     * @param ProgramRepository $programRepository
     * @param CategoryRepository $categoryRepository
     * @return Response
     *
     * @Route("/category/{categoryName}", name="category_show")
     */
    #[Route('/{categoryName}', name: 'show')]
    public function show(ProgramRepository $programRepository, CategoryRepository $categoryRepository, string $categoryName): Response
    {
        /** @var  $category */
        $category = $categoryRepository->findOneBy(['name' => $categoryName]);
       // $programs =$category->
        $id = $category->getId();
        $programs = $programRepository->findBy(['category' => $id],['id' => 'DESC'],1);
        //$programs = $programRepository->findByCategory($category,['id' => 'DESC'],1);


        if(null===$category) {
            throw $this->createNotFoundException('No category');
        }

        return $this->render('category/show.html.twig', [
            'category' => $category,
            'programs' => $programs
        ]);
    }


}