<?php

namespace App\Controller;

use Utils\Str\Str;
use App\Entity\Url;
use App\Repository\UrlRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Constraints\Url as UrlConstraints;

class UrlsController extends AbstractController
{

    private $urlRepo;
    function __construct(UrlRepository $urlRepo)
    {
        $this->urlRepo = $urlRepo;
    }

    #[Route("/", name: 'app_home', methods: ["GET", "POST"])]
    #[Route('/', name: 'app_urls_creation', methods: ["GET", "POST"])]
    public function creation(Request $request, UrlRepository $urlRepo, EntityManagerInterface $em): Response
    {
        $form = $this->createFormBuilder()
            ->add(
                'original',
                null,
                [
                    'label' => false,


                    'attr' => ['placeholder' => 'Please enter a URL '],
                    'constraints' => [
                        new NotBlank(),
                        new UrlConstraints()
                    ]



                ]
            )
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $url = $this->urlRepo->findOneBy(['original' => $form['original']->getData()]);

            if ($url) {
                return $this->redirectToRoute('app_urls_preview', ['shortener' => $url->getShortener()]);
            }

            $url = new Url;
            $url->setOriginal($form['original']->getData());
            $url->setShortener($this->getUniqueShortenedString());
            $em->persist($url);
            $em->flush();

            return $this->redirectToRoute('app_urls_preview', ['shortener' => $url->getShortener()]);
        }



        return $this->render('urls/creation.html.twig', ["form" => $form->createView()]);
    }



    #[Route('/{shortener}/preview', name: 'app_urls_preview', methods: ['GET'])]
    public function preview(Url $url): Response
    {
        return $this->render('urls/preview.html.twig', compact('url'));
    }

    #[Route('/{shortener}', name: 'app_urls_show', methods: ['GET'])]
    public function show(Url $url): Response
    {
        return $this->redirect($url->getOriginal());
    }

    public function getUniqueShortenedString(): string
    {
        $shortener = Str::random(6);

        if ($this->urlRepo->findOneBy(['shortener' => $shortener])) {
            return $this->getUniqueShortenedString();
        }
        return $shortener;
    }
}
