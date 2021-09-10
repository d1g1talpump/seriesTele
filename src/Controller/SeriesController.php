<?php

namespace App\Controller;

use App\Entity\Series;
use App\Form\SeriesType;
use App\Repository\SeriesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route ("/series", name="series_")
 */
class SeriesController extends AbstractController
{
    /**
     * @Route("", name="list")
     */
    public function list(SeriesRepository $seriesRepository): Response
    {
        $series = $seriesRepository->findBestSeries();

        return $this->render('series/list.html.twig', [
            "series" => $series
        ]);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(int $id, SeriesRepository $seriesRepository): Response
    {
        $show = $seriesRepository->find($id);

        if (!$show) {
            throw $this->createNotFoundException('oH fuck!!!');
        }

        return $this->render("series/details.html.twig", [
            "show" => $show
        ]);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(
        Request                $request,
        EntityManagerInterface $entityManager
    ): Response
    {
        $series = new Series();
        $series->setDateCreated(new \DateTime());

        $seriesForm = $this->createForm(SeriesType::class, $series);

        $seriesForm->handleRequest($request);

        if ($seriesForm->isSubmitted() && $seriesForm->isValid()) {
            $entityManager->persist($series);
            $entityManager->flush();

            $this->addFlash('success', 'Series added! Fantastic.');
            return $this->redirectToRoute('series_details', ['id' => $series->getId()]);
        }

        return $this->render("series/create.html.twig", [
            'seriesForm' => $seriesForm->createView()
        ]);
    }

    /**
     * @Route("/demo", name="em-demo")
     */
    public function demo(EntityManagerInterface $entityManager): Response
    {
        //create an instance of the entity series
        $series = new Series();

        //hydrate all the properties
        /*        $series->setName('pif');
                $series->setBackdrop('paf');
                $series->setPoster('pof');
                $series->setDateCreated(new \DateTime());
                $series->setFirstAirDate(new \DateTime('- 1 year'));
                $series->setLastAirDate(new \DateTime('- 6 months'));
                $series->setGenre('horror');
                $series->setOverview('puf');
                $series->setPopularity(123.00);
                $series->setVote(8.2);
                $series->setStatus('Cancelled');
                $series->setTmdbId(69420);

                dump($series);

                $entityManager->persist($series);
                $entityManager->flush();

                dump($series);

                $entityManager->remove($series);

                $series->setGenre('comedy');
                $entityManager->flush();

                $entityManager = $this->getDoctrine()->getManager();*/

        return $this->render('series/create.html.twig');
    }

    /**
     * @Route("/delete/{id}", name="detele")
     */
    public function delete(Series $series, EntityManagerInterface $entityManager)
    {

        $entityManager->remove($series);
        $entityManager->flush();

        return $this->redirectToRoute('main_home');
    }
}
