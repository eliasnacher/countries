<?php

namespace App\Controller;

use App\Entity\Region;
use App\Entity\Country;
use App\Form\CountryType;
use App\Repository\RegionRepository;
use App\Repository\CountryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/country')]
class CountryController extends AbstractController
{
    #[Route('/', name: 'app_country_index', methods: ['GET'])]
    public function index(CountryRepository $countryRepository): Response
    {
        return $this->render('country/index.html.twig', [
            'countries' => $countryRepository->findAll(),
        ]);
    }

    #[Route('/import', name: 'app_country_import', methods: ['GET'])]
    public function import(CountryRepository $countryRepository, RegionRepository $regionRepository): Response
    {
        // Load local stored regions
        $localRegions = [];
        foreach($regionRepository->findAll() as $localRegion)
        $localRegions[$localRegion->getName()] = $localRegion;

        // Load local stored countries
        $localCountries = [];
        foreach($countryRepository->findAll() as $localCountry)
        $localCountries[$localCountry->getOfficialName()] = $localCountry;

        // Load remote countries
        $countryApiUrl = 'https://restcountries.com/v3.1/all';
        $remoteCountries = json_decode(file_get_contents($countryApiUrl));

        foreach($remoteCountries as $remoteCountry)
        {
            $countryToPersist = $localCountries[$remoteCountry->name->official] ?? new Country ();

            // If region is not stored, save it.
            if(!isset($localRegions[$remoteCountry->region]))
            {
                $regionToPersist = new Region ();
                $regionToPersist->setName($remoteCountry->region);
                $regionRepository->add($regionToPersist, true);
                $localRegions[$remoteCountry->region] = $regionToPersist;
            }

            // Fill country entity with remote data.
            $countryToPersist->setCommonName($remoteCountry->name->common);
            $countryToPersist->setOfficialName($remoteCountry->name->official);
            $countryToPersist->setFlag($remoteCountry->flag);
            $countryToPersist->setPopulation($remoteCountry->population);
            $countryToPersist->setRegion($localRegions[$remoteCountry->region]);

            $countryRepository->add($countryToPersist, true);
        }


        return $this->redirectToRoute('app_country_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/new', name: 'app_country_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CountryRepository $countryRepository): Response
    {
        $country = new Country();
        $form = $this->createForm(CountryType::class, $country);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $countryRepository->add($country, true);

            return $this->redirectToRoute('app_country_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('country/new.html.twig', [
            'country' => $country,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_country_show', methods: ['GET'])]
    public function show(Country $country): Response
    {
        return $this->render('country/show.html.twig', [
            'country' => $country,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_country_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Country $country, CountryRepository $countryRepository): Response
    {
        $form = $this->createForm(CountryType::class, $country);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $countryRepository->add($country, true);

            return $this->redirectToRoute('app_country_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('country/edit.html.twig', [
            'country' => $country,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_country_delete', methods: ['POST'])]
    public function delete(Request $request, Country $country, CountryRepository $countryRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$country->getId(), $request->request->get('_token'))) {
            $countryRepository->remove($country, true);
        }

        return $this->redirectToRoute('app_country_index', [], Response::HTTP_SEE_OTHER);
    }
}
