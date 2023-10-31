<?php

namespace App\Controller;

use App\Entity\Author;
use App\Form\AuthorType;
use App\Repository\AuthorRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AuthorController extends AbstractController
{
    /**
     * @Route("/author", name="app_author")
     */
    public function index(): Response
    {
        return $this->render('author/index.html.twig', [
            'controller_name' => 'AuthorController',
        ]);
    }
    /**
     * @Route("/fetch", name="show-Author")
     */
    public function show(AuthorRepository $repo):Response
    {
        $result=$repo->listAuthorByEmail();
        return $this->render('author/showAuthor.html.twig',[
            'res'=>$result
        ]);
    }
    /**
     * @Route("/addS",name="Add_Stat")
     */
    public function addS(ManagerRegistry $mr):Response
    {
        $a=new Author();
        $a->setUsername("Username");
        $a->setEmail("Email");
        $em=$mr->getManager();
        $em->persist($a);
        $em->flush();
        return $this->redirectToRoute('show-Author');
    }
    /**
     * @Route("/addForm",name="Add")
     */
    public function addAuthor(ManagerRegistry $mr,Request $req):Response
    {
       $p=new Author(); 
       $form=$this->createForm(AuthorType::class,$p);
       $form->handleRequest($req);
       if($form->isSubmitted()){
         
        $em=$mr->getManager();
        $em->persist($p);
        $em->flush();
        return $this->redirectToRoute('show-Author');
     }
        return $this->render("author/addAuthor.html.twig",[
         "fr"=>$form->createView()
        ]);
    }
    /**
     * @Route("/removeAuthor/{id}",name="remove")
     */
    public function remove(ManagerRegistry $mr,AuthorRepository $rep,$id):Response
    {
        $p=$rep->find($id);
        $em=$mr->getManager();
        $em->remove($p);
        $em->flush();
        return $this->redirectToRoute("show-Author");
        
    }
    /**
     * @Route("/updateAuthor/{id}",name="update")
     */
    public function update(ManagerRegistry $mr,AuthorRepository $rep,$id, Request $req):Response
    {
        $p=$rep->find($id); 
        $form=$this->createForm(AuthorType::class,$p);
        $form->handleRequest($req);
        if($form->isSubmitted()){
          
         $em=$mr->getManager();
        
         $em->flush();
         return $this->redirectToRoute('show-Author');
      }   
      return $this->render("author/updateAuthor.html.twig",[
        "fr"=>$form->createView()
       ]);
    }
}
