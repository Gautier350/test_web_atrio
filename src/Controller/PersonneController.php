<?php

namespace App\Controller;

use App\Entity\Personne;
use App\Form\PersonneType;
use App\Repository\PersonneRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PersonneController extends AbstractController
{
    /**
     * @Route("/", name="app_personne", methods={"GET", "POST"})
     */
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $personne = new Personne();
        $form = $this->createForm(PersonneType::class, $personne);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $date = $form['date_naissance']->getData();
            $today = new \DateTime("now");

            $interval = date_diff($date, $today);
            if ($interval->y < 150) {
                $em->persist($personne);
                $em->flush();
                $this->addFlash('success', "L'enregistrement s'est bien effectué");
            } else {
                $this->addFlash('success', "La date ne peut être supérieure à 150 ans");
            }

        }

        return $this->render('personne/index.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/show", name="app_personne_show",methods={"GET"})
     */
    public function show(PersonneRepository $personneRepository): Response
    {
        $personnes=$personneRepository->findBy([], ['nom' => 'DESC']);
        return $this->render('personne/show.html.twig',compact("personnes"));
    }
}
