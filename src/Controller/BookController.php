<?php

namespace App\Controller;

use App\Entity\Author;
use App\Entity\Book;
use App\Form\BookType;
use App\Repository\AuthorRepository;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends AbstractController
{
    /**
     * @Route("/book", name="app_book")
     */
    public function index(): Response
    {
        return $this->render('book/index.html.twig', [
            'controller_name' => 'BookController',
        ]);
    }
    /**
     * @Route("/addBook",name="AddBook")
     */
    public function addBook(ManagerRegistry $mr,Request $req):Response
    {
      
       $p=new Book(); 
       $form=$this->createForm(BookType::class,$p);
       $form->handleRequest($req);
       if($form->isSubmitted()){
        $p->setPublished(true);
         $a=$p->getAuthor();
         $a->setNbBooks($a->getNbBooks()+1);
        $em=$mr->getManager();
        $em->persist($p);
      $em->persist($a);
        $em->flush();
        return $this->redirectToRoute('lsBooks');
     }
        return $this->render("book/addBooks.html.twig",[
         "fr"=>$form->createView()
        ]);
    }
    /**
     * @Route("/listeBooks",name="lsBooks")
     */
    public function list(BookRepository $repo,EntityManagerInterface $em):Response
    {
        $result = $repo->createQueryBuilder('b')
                    ->join('b.author', 'a')
                    ->where('b.publicationDate < :year')
                    ->andWhere('a.nb_books > 10')
                    ->setParameter('year', '2023-01-01')
                    ->getQuery()
                    ->getResult();
        $books = $repo->findBy(['category' => 'Science-Fiction']);

        foreach ($books as $book) {
            $book->setCategory('Romance');
        }
        
        $em->flush();
        $n=0;
        $m=0;
        for($i=1;$i<=sizeof($result);$i++){
            if($result[0]->isPublished()){
                $n++;
            }else{
                $m++;
            }
        }
        return $this->render("book/listBooks.html.twig",[
            "rs"=>$result,
            "nb_p"=>$n,
            "nb_np"=>$m
        ]);
    }
    /**
     * @Route("updateBook/{id}",name="UpdateBook")
     */
    public function update(ManagerRegistry $mr,BookRepository $rep,$id, Request $req):Response
    {
        $b=$rep->find($id); 
        $form=$this->createForm(BookType::class,$b);
        $form->handleRequest($req);
        if($form->isSubmitted()){
          
         $em=$mr->getManager();
        
         $em->flush();
         return $this->redirectToRoute('lsBooks');
      }   
      return $this->render("book/updateBook.html.twig",[
        "fr"=>$form->createView()
       ]);
    }
    /**
     * @Route("removeBook/{id}",name="rmvBook")
     */
    public function remove(ManagerRegistry $mr,BookRepository $rep,$id):Response
    {
        $b=$rep->find($id);
        $em=$mr->getManager();
        $em->remove($b);
        $em->flush();
        return $this->redirectToRoute("lsBooks");
        
    }
    /**
     * @Route("showDetails/{id}",name="shdt")
     */
    public function show(BookRepository $rep,$id):Response
    {
        $result=$rep->find($id);
        return $this->render('book/showDetails.html.twig',[
            "res"=>$result
        ]);
    }

    /**
     * @Route("nbLivre",name="affnblivre")
     */
    public function aff(EntityManagerInterface $em):Response
    {
                $count = $em->createQuery(' SELECT COUNT(b) FROM App\Entity\Book b WHERE b.category = :category')
                ->setParameter('category', 'Romance');
                dd($count);
    }

}
