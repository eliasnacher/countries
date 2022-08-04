<?php

namespace App\Test\Controller;

use App\Test\AbstractWebTest;

class RegionControllerTest extends AbstractWebTest
{
    private string $path = '/region/';

    public function testIndex(): void
    {
        $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Regions');
    }

    public function testNew(): void
    {
        $originalNumObjectsInRepository = count($this->regionRepository->findAll());

        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'region[name]' => 'testNew Region',
        ]);

        self::assertResponseRedirects('/region/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->regionRepository->findAll()));
    }

    public function testShow(): void
    {
        $dummyCountry = $this->createDummyRegion('testShow Region');

        $this->client->request('GET', sprintf('%s%s', $this->path, $dummyCountry->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Region');

        $presistedRegions = $this->regionRepository->findAll();

        self::assertSame('testShow Region', $presistedRegions[0]->getName());
    }

    public function testEdit(): void
    {
        $dummyCountry = $this->createDummyRegion('testEdit Region');

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $dummyCountry->getId()));

        $this->client->submitForm('Update', [
            'region[name]' => 'testEdit Region Edited',
        ]);

        self::assertResponseRedirects('/region/');

        $presistedRegions = $this->regionRepository->findAll();

        self::assertSame('testEdit Region Edited', $presistedRegions[0]->getName());
    }

    public function testRemove(): void
    {
        $originalNumObjectsInRepository = count($this->regionRepository->findAll());

        $dummyCountry = $this->createDummyRegion('testRemove Region');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->regionRepository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $dummyCountry->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->regionRepository->findAll()));
        self::assertResponseRedirects('/region/');
    }
}