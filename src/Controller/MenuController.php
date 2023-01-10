<?php

namespace App\Controller;

use App\Entity\Menu;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;


class MenuController extends AbstractController
{
    /**
     * @Route ("/Addm", name="Add_menu")
     */
    public function ajouter( Request $request)
    {
        $menu= new Menu();
        $form= $this->createForm("App\Form\MenuType", $menu);
        $form->handleRequest($request);
        if ($form->isSubmitted())
        {
            $em=$this->getDoctrine()->getManager();
            $em->persist($menu);
            $em->flush();
            return $this->redirectToRoute('list_menu');
        }
        //Utiliser la methode createView() pour que l'objet soit exploitable par la vue
        return $this->render('menu/ajouter.html.twig',
            ['f'=> $form->createView()]);
    }
    /**
     * @Route ("/menu", name="list_menu")
     */
    public function home()
    {
        $em=$this->getDoctrine()->getManager();
        $repo = $em->getRepository(Menu::class);
        $lesmenus= $repo->findAll();
        return $this->render('menu/home.html.twig', ['lesmenus'=>$lesmenus]);
    }
    /**
     * @Route ("/menuuser", name="list_menu_user")
     */
    public function user()
    {
        $em=$this->getDoctrine()->getManager();
        $repo = $em->getRepository(Menu::class);
        $lesmenus= $repo->findAll();
        return $this->render('menu/home1.html.twig', ['lesmenus'=>$lesmenus]);
    }
    /**
     * @Route ("/supp/{id}" , name="delete_menu")
     */
    public function delete(Request $request, $id): Response
    {
        $c=$this->getDoctrine()
            ->getRepository(Menu::class)
            ->find($id);
        if (!$c)
        {
            throw $this->createNotFoundException(
                'No menu was found for id ',$id);
        }
        $entityManager= $this->getDoctrine()->getManager();
        $entityManager->remove($c);
        $entityManager->flush();
        return $this->redirectToRoute('list_menu');
    }
    /**
     * @Route("/editU/{id}", name="edit_menu")
     * Method({"GET","POST"})
     */
    public function edit(Request $request, $id)
    { $menu = new Menu();
        $menu = $this->getDoctrine()
            ->getRepository(Menu::class)
            ->find($id);
        if (!$menu) {
            throw $this->createNotFoundException(
                'No menu found for id '.$id
            );
        }
        $fb = $this->createFormBuilder($menu)
            ->add('titre', TextType::class, array("label" => "nom du menu"))
            ->add('type', TextType::class, array("label" => "type de menu"))
            ->add('nbrCalories', IntegerType::class, array("label" => "Nombre de calories"))
            ->add('prix', IntegerType::class, array("label" => "prix"))
            ->add('Valider', SubmitType::class);
// générer le formulaire à partir du FormBuilder
        $form = $fb->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
            return $this->redirectToRoute('list_menu');
        }
        return $this->render('menu/ajouter.html.twig',
            ['f' => $form->createView()] );
    }




}
