<?php

namespace App\Controller;

use App\classe\Cart;
use App\Entity\Address;
use App\Form\AddressType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountAddressController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    #[Route('/compte/adresses', name: 'app_account_address')]
    public function index(): Response
    {
        
        return $this->render('account/address.html.twig');
    }

    #[Route('/compte/ajouter-une-adresse', name: 'app_account_address_add')]
    public function add(Cart $cart, Request $request): Response
    {
        $adresses = new Address();

        $form = $this->createForm(AddressType::class, $adresses);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $adresses->setUser($this->getUser());
            $this->entityManager->persist($adresses);
            $this->entityManager->flush();
            if ($cart->get()) {
                return $this->redirectToRoute('app_order');
            } else {
                return $this->redirectToRoute('app_account_address');
            }
        }

        return $this->render('account/address_form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/compte/modifier-une-adresse/{id}', name: 'app_account_address_edit')]
    public function edit(Request $request, $id): Response
    {
        $adresses = $this->entityManager->getRepository(Address::class)->findOneById($id);

        if (!$adresses || $adresses->getUser() != $this->getUser()) {
            return $this->redirectToRoute('app_account_address');
        }

        $form = $this->createForm(AddressType::class, $adresses);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
            return $this->redirectToRoute('app_account_address');
        }

        return $this->render('account/address_form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/compte/supprimer-une-adresse/{id}', name: 'app_account_address_delete')]
    public function delete($id): Response
    {
        $adresses = $this->entityManager->getRepository(Address::class)->findOneById($id);

        if ($adresses && $adresses->getUser() == $this->getUser()) {
            $this->entityManager->remove($adresses);
            $this->entityManager->flush();
        }         
            return $this->redirectToRoute('app_account_address');
    }

}
