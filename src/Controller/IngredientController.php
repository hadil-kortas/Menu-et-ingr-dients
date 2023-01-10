<?php

namespace App\Controller;

use App\Entity\Ingredient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\String\Slugger\SluggerInterface;


class IngredientController extends AbstractController

{
    /**
     * @Route ("/Addi", name="Add_ingredient")
     */
    public function ajouter( Request $request, SluggerInterface $slugger)
    {
        $ingredient= new Ingredient();
        $form= $this->createForm("App\Form\IngredientType", $ingredient);
        $form->handleRequest($request);
        if ($form->isSubmitted())
        {
            $image = $form->get('image')->getData();
            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($image) {
                $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$image->guessExtension();
                // Move the file to the directory where brochures are stored
                try {
                    $image->move(
                        $this->getParameter('Gimage_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }


                $ingredient->setImage($newFilename);
            }
            $em=$this->getDoctrine()->getManager();
            $em->persist($ingredient);
            $em->flush();
            return $this->redirectToRoute('list_ingredient');
        }
        //Utiliser la methode createView() pour que l'objet soit exploitable par la vue
        return $this->render('ingredient/ajouter.html.twig',
            ['f'=> $form->createView()]);
    }
    /**
     * @Route ("/ingredient", name="list_ingredient")
     */
    public function home()
    {
        $em=$this->getDoctrine()->getManager();
        $repo = $em->getRepository(ingredient::class);
        $lesingredients= $repo->findAll();
        return $this->render('ingredient/home.html.twig', ['lesingredients'=>$lesingredients]);
    }

    /**
     * @Route ("/ingredientuser", name="list_ingredient_user")
     */
    public function user()
    {
        $em=$this->getDoctrine()->getManager();
        $repo = $em->getRepository(ingredient::class);
        $lesingredients= $repo->findAll();
        return $this->render('ingredient/home1.html.twig', ['lesingredients'=>$lesingredients]);
    }
    /**
     * @Route("/ingredient/{id}", name="show_ingredient")
     */
    public function show($id)
    {
        $ingredient = $this->getDoctrine()
            ->getRepository(ingredient::class)
            ->find($id);
        if (!$ingredient) {
            throw $this->createNotFoundException(
                'Aucun ingredient nest trouvee  '.$id
            );
        }
        return $this->render('ingredient/show.html.twig', ['ingredient' =>$ingredient]);
    }
}
