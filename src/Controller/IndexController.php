<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Ticket;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Form\FormBuilderInterface;
use Knp\Component\Pager\PaginatorInterface;

class IndexController extends AbstractController
{
    /**
     * @Route("/ticket/save")
     */
    public function save() {
        $entityManager = $this->getDoctrine()->getManager();
        $Ticket = new Ticket();
        $Ticket->setTitre('Ticket 1');
        $Ticket->setPersonne('personne 1');
        $Ticket->setDescription('des  1');
        $Ticket->setStatut('status 1');
        $Ticket->setDate(new \DateTime('now'));
        $entityManager->persist($Ticket);
        $entityManager->flush();
        return new Response('Ticket enregisté avec id '.$Ticket->getId());
}
// lister les tickets
    /**
     *@Route("/",name="ticket_list")
     */
    public function home(Request $request, PaginatorInterface $paginator)
    {
//récupérer tous les articles de la table article de la BD
// et les mettre dans le tableau $articles
        $Ticket= $this->getDoctrine()->getRepository(Ticket::class)->findAll();
        $Ticket= $paginator->paginate(
            $Ticket,
            $request -> query -> getInt('page', 1), 5
        );
        return $this->render('index/index.html.twig',['Ticket'=> $Ticket]);
    }
    // Details du ticket
    /**
     * @Route("/Ticket/{id}", name="Ticket_show")
     */
    public function show($id) {
        $Ticket = $this->getDoctrine()->getRepository(Ticket::class)->find($id);
        return $this->render('index/show.html.twig', array('Ticket' => $Ticket));
    }
    //Supprimer Ticket
    /**
     * @Route("/Ticket/delete/{id}",name="delete_article")
     * @Method({"DELETE"})
     */
    public function delete(Request $request, $id) {
        $Ticket = $this->getDoctrine()->getRepository(Ticket::class)->find($id);
$entityManager = $this->getDoctrine()->getManager();
$entityManager->remove($Ticket);
$entityManager->flush();
$response = new Response();
$response->send();
return $this->redirectToRoute('ticket_list');
}
// creation ticket
    /**
     * @Route("/Ticket/new/{id}", name="new_Ticket")
     * Method({"GET", "POST"})
     */
    public function new(Request $request) {
        $Ticket = new Ticket();
        $Ticket->setdate(new \DateTime('now'));
        $Ticket->setStatut('En Attente');
        $form = $this->createFormBuilder($Ticket)
            ->add('titre', TextType::class)
            ->add('personne', TextType::class)
            ->add('description', TextType::class)
            ->add('save', SubmitType::class, array('label' => 'Créer'))->getForm();
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $Ticket= $form->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($Ticket);
            $entityManager->flush();
            return $this->redirectToRoute('ticket_list');
        }
        return $this->render('index/new.html.twig',['form' => $form->createView()]);
}
    // Modification Ticket
    /**
     * @Route("/Ticket/edit/{id}", name="edit_Ticket")
     * Method({"GET", "POST"})
     */
    public function edit(Request $request, $id) {
        $Ticket = new Ticket();
        $Ticket = $this->getDoctrine()->getRepository(Ticket::class)->find($id);
        $Ticket->setdate(new \DateTime('now'));
        $Ticket->setStatut('Modifier');
        $form = $this->createFormBuilder($Ticket)
            ->add('titre', TextType::class)
            ->add('personne', TextType::class)
            ->add('description', TextType::class)
            ->add('save', SubmitType::class, array('label' => 'Modifier' ))->getForm();
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
            return $this->redirectToRoute('ticket_list');
        }

        return $this->render('index/edit.html.twig', ['form' => $form->createView()]);

    }
}
