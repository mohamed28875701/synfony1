<?php

namespace App\Controller;
use App\Entity\Author;
use App\Form\AuthorType;
use App\Repository\AuthorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;



class AuthorController extends AbstractController
{
    public $authors = array(
        
    );
    #[Route('/author', name: 'app_author')]
    public function index(): Response
    {
        return $this->render('author/index.html.twig', [
            'controller_name' => 'AuthorController',
        ]);
    }
    #[Route('/list', name: 'list')]
    public function showAuthor($name){
        return $this->render('author/show.html.twig', ["name" => $name]);
    }
    #[Route('/author/{name}', name: 'list')]
    public function list(AuthorRepository $repository){
        
        
        return $this->render('author/list.html.twig',["authors"=>$repository->findAll()]);  
    }
    #[Route('/authorDetails/{id}',name:'authorDetails')]
    public function authorDetails($id){
        $author=null;
        foreach ($this->authors as $var ){
            if($var['id']==$id){
                $author=$var;
            }
        }
        return $this->render('author/authorDetails.html.twig',["author"=>$author]);
    }
    #[Route('/affiche',name:'auth_affiche')]
    public function affiche(AuthorRepository $repo){
        $author=$repo->findAll();
        return $this->render('author/affiche.html.twig',['author'=>$author]);
    }
    #[Route('/add',name:'add_author')]
    public function add_author(ManagerRegistry $reg,Request $request){
        $author=new Author();
        $form =$this->CreateForm(AuthorType::class,$author);
        $form->add('add',SubmitType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            $em=$reg->getManager();
            $em->persist($author);
            $em->flush();
            return $this->redirectToRoute('auth_affiche');
        }
        return $this->render('author/Add.html.twig',['f'=>$form->createView()]);
    }
    #[Route('/edit/{id}',name:'auth_edit')]
    public function editAuthor($id,AuthorRepository $repo,ManagerRegistry $registry,Request $req){
        $author=$repo->find($id);
        $form=$this->createForm(AuthorType::class,$author);
        $form->add('edit',SubmitType::class);
        $form->handleRequest($req);
        if($form->isSubmitted() && $form->isValid()){
            $em=$registry->getManager();
            $em->flush();
            return $this->redirectToRoute("auth_affiche");
        }
        return $this->render("author/edit.html.twig",["f"=>$form->createView()]);
    }
    #[Route('/delete/{id}',name:'auth_delete')]
    public function deleteAuthor($id,AuthorRepository $repo,ManagerRegistry $registry){
        $author=$repo->find($id);
        if(!$author) return;
        $em = $registry->getManager();
        $em->remove($author);
        $em->flush();
        return $this->redirectToRoute("auth_affiche");
    }
}
