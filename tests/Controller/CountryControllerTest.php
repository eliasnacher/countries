<?php

namespace App\Test\Controller;

use App\Test\AbstractWebTest;

class CountryControllerTest extends AbstractWebTest
{
    private string $path = '/country/';

    public function testIndex(): void
    {
        $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Countries');
    }

    public function testNew(): void
    {
        $originalNumObjectsInRepository = count($this->countryRepository->findAll());

        $dummyRegion = $this->createDummyRegion('testNew Region');
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'country[commonName]' => 'Test Common',
            'country[officialName]' => 'Test Official',
            'country[region]' => 0,
            'country[population]' => 25121521,
            'country[flag]' => '🇵🇷'
        ]);

        self::assertResponseRedirects('/country/');

        $presistedCountries = $this->countryRepository->findAll();

        self::assertSame('Test Common', $presistedCountries[0]->getCommonName());
        self::assertSame('Test Official', $presistedCountries[0]->getOfficialName());
        self::assertSame(25121521, $presistedCountries[0]->getPopulation());
        self::assertSame('🇵🇷', $presistedCountries[0]->getFlag());
        self::assertSame($dummyRegion->getId(), $presistedCountries[0]->getRegion()->getId());

        self::assertSame($originalNumObjectsInRepository + 1, count($presistedCountries));
    }

    public function testShow(): void
    {
        $dummyRegion = $this->createDummyRegion('testShow Region');

        $dummyCountry = $this->createDummyCountry(
            'Test Common',
            'Test Official',
            $dummyRegion,
            215215,
            '🇵🇷'
        );

        $this->client->request('GET', sprintf('%s%s', $this->path, $dummyCountry->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Country');

        $presistedCountries = $this->countryRepository->findAll();

        self::assertSame('Test Common', $presistedCountries[0]->getCommonName());
        self::assertSame('Test Official', $presistedCountries[0]->getOfficialName());
        self::assertSame(215215, $presistedCountries[0]->getPopulation());
        self::assertSame('🇵🇷', $presistedCountries[0]->getFlag());
        self::assertSame($dummyRegion->getId(), $presistedCountries[0]->getRegion()->getId());
    }

    public function testEdit(): void
    {
        $dummyRegionFirst = $this->createDummyRegion('testEdit Region 1');
        $dummyRegionSecond = $this->createDummyRegion('testEdit Region 2');

        $dummyCountry = $this->createDummyCountry(
            'Test Common',
            'Test Official',
            $dummyRegionFirst,
            2152152,
            '🇵🇷'
        );

        $this->countryRepository->add($dummyCountry, true);

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $dummyCountry->getId()));

        $this->client->submitForm('Update', [
            'country[commonName]' => 'Test Common Edited',
            'country[officialName]' => 'Test Official Edited',
            'country[region]' => 1,
            'country[population]' => 32532532,
            'country[flag]' => '🇮🇳'
        ]);

        self::assertResponseRedirects('/country/');

        $presistedCountries = $this->countryRepository->findAll();

        self::assertSame('Test Common Edited', $presistedCountries[0]->getCommonName());
        self::assertSame('Test Official Edited', $presistedCountries[0]->getOfficialName());
        self::assertSame(32532532, $presistedCountries[0]->getPopulation());
        self::assertSame('🇮🇳', $presistedCountries[0]->getFlag());
        self::assertSame($dummyRegionSecond->getId(), $presistedCountries[0]->getRegion()->getId());
    }

    public function testRemove(): void
    {
        $originalNumObjectsInRepository = count($this->countryRepository->findAll());

        $dummyRegion = $this->createDummyRegion('testRemove Region');

        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $dummyCountry = $this->createDummyCountry(
            'Test Common',
            'Test Official',
            $dummyRegion,
            564894,
            '🇵🇷'
        );

        $this->countryRepository->add($dummyCountry, true);

        self::assertSame($originalNumObjectsInRepository + 1, count($this->countryRepository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $dummyCountry->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->countryRepository->findAll()));
        self::assertResponseRedirects('/country/');
    }
}