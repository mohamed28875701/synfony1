<?php

namespace App\Controller;

use App\Form\BookType;
use App\Repository\BookRepository;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Book;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Entity\Author;

class BookController extends AbstractController
{
    #[Route('/book', name: 'app_book')]
    public function index(): Response
    {
        return $this->render('book/index.html.twig', [
            'controller_name' => 'BookController',
        ]);
    }

    #[Route('/afficheBook', name: 'book_affiche')]
    public function affiche(BookRepository $repo)
    {
        $publishedBooks = $repo->findBy(['published' => true]);
        
        $numPublishedBooks = count($publishedBooks);
        $numUnPublishedBooks = count($repo->findBy(['published' => false]));


        return $this->render('book/afficheBook.html.twig', ['publishedBooks' => $publishedBooks, 'numPublishedBooks' => $numPublishedBooks, 'numUnPublishedBooks' => $numUnPublishedBooks]);
    }
    #[Route('/addBook',name:'book_add')]
    public function addBook(Request $req,ManagerRegistry $man){
        $book=new Book();
        $form=$this->createForm(BookType::class,$book);
        $form->add("add",submitType::class);
        $form->handleRequest($req);
        if($form->isSubmitted()&&$form->isValid()){
            $author=$book->getAuthor();
            if($author instanceof Author){
                $author->setNbBooks($author->getNbBooks()+1);
            }
            $em=$man->getManager();
            $em->persist($book);
            $em->flush();
            return $this->redirectToRoute("book_affiche");
        }
        return $this->render("book/addBook.html.twig",['f'=>$form->createView()]);
    }
    #[Route('/editBook/{id}',name:'book_edit')]
    public function editBook(BookRepository $bookRepository,$id,ManagerRegistry $registry,Request $request){
        $book=$bookRepository->find($id);
        $form=$this->createForm(BookType::class,$book);
        $form->add("edit",SubmitType::class);
        $form->handleRequest($request);
        if($form->isSubmitted()&&$form->isValid()){
            $em=$registry->getManager();
            $em->flush();
            return $this->redirectToRoute("book_affiche");
        }
        return $this->render("book/editBook.html.twig",['f'=>$form->createView()]);
    }
    #[Route('/deleteBook/{id}', name: 'book_delete')]
    public function delete($id, BookRepository $repository,ManagerRegistry $registry)
    {
        $book = $repository->find($id);
        if(!$book) return;
        $em = $registry->getManager();
        $em->remove($book);
        $em->flush();
        return $this->redirectToRoute("book_affiche");

        
    }
    #[Route('/showBook/{id}', name: 'book_detail')]

    public function showBook($id, BookRepository $repository)
    {
        $book = $repository->find($id);
        if (!$book) {
            return $this->redirectToRoute('book_affiche');
        }

        return $this->render('book/showDetail.html.twig', ['b' => $book]);

}
}
