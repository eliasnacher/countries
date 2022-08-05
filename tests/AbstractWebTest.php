<?php

namespace App\Test;

use App\Entity\Region;
use App\Entity\Country;
use Doctrine\ORM\Tools\SchemaTool;
use App\Repository\RegionRepository;
use App\Repository\CountryRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class AbstractWebTest extends WebTestCase
{
    public RegionRepository $regionRepository;
    public CountryRepository $countryRepository;
    public KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        // Avoid deleting the database during execution.
        $this->client->disableReboot();

        $entityManager = static::getContainer()->get('doctrine.orm.entity_manager');

        $this->regionRepository = $entityManager->getRepository(Region::class);
        $this->countryRepository = $entityManager->getRepository(Country::class);

        // Generate/Update schema in test database
        $metadatas = $entityManager->getMetadataFactory()->getAllMetadata();
        $schemaTool = new SchemaTool($entityManager);
        $schemaTool->updateSchema($metadatas);

        // Clear all data before each test
        foreach ($this->regionRepository->findAll() as $object) $this->regionRepository->remove($object, true);
        foreach ($this->countryRepository->findAll() as $object) $this->countryRepository->remove($object, true);
    }

    public function createDummyRegion (string $name) : Region
    {
        $dummyRegion = new Region ();
        $dummyRegion->setName($name);

        $this->regionRepository->add($dummyRegion, true);

        return $dummyRegion;
    }

    public function createDummyCountry (string $commonName, string $officialName, Region $region, int $population = 123456, string $flag = 🇪🇸) : Country
    {
        $dummyCountry = new Country ();
        $dummyCountry->setCommonName($commonName);
        $dummyCountry->setOfficialName($officialName);
        $dummyCountry->setRegion($region);
        $dummyCountry->setPopulation($population);
        $dummyCountry->setFlag($flag);

        $this->countryRepository->add($dummyCountry, true);

        return $dummyCountry;
    }
}