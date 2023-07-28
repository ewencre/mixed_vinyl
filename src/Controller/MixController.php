<?php

namespace App\Controller;

use App\Entity\VinylMix;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MixController extends AbstractController
{
    #[Route('/mix/new')]
    public function new(EntityManagerInterface $entityManager): Response
    {
        $mix = new VinylMix();
        $mix->setTitle('The feels');
        $mix->setDescription('Les coeurs chavirent');
        $genres = [
            'pop',
            'rock',
            'all',
        ];
        $mix->setGenre($genres[array_rand($genres)]);
        $mix->setTrackCount(rand(5, 20));
        $mix->setVotes(rand(-50, 50));

        $entityManager->persist($mix);
        $entityManager->flush();

        return new Response(
            sprintf(
                'Le mix %d comprend %d sons rappelant des temps meilleurs',
                $mix->getId(),
                $mix->getTrackCount()
            )
        );
    }

    #[Route('/mix/{slug}', name: 'app_mix_show')]
    public function show(VinylMix $mix): Response
    {
        return $this->render(
            'mix/show.html.twig',
            ['mix' => $mix]
        );
    }

    #[Route('/mix/{id}/vote', name: 'app_mix_vote', methods: ['POST'])]
    public function vote(VinylMix $mix, Request $request, EntityManagerInterface $entityManager): Response
    {
        $direction = $request->request->get('direction', 'up');

        if ($direction === 'up')
        {
            $mix->upVote();
        }
        else
        {
            $mix->downVote();
        }

        $this->addFlash('success', 'Vote comptabilisé !');

        $entityManager->flush();

        return $this->redirectToRoute('app_mix_show', ['slug' => $mix->getSlug()]);
    }
}
