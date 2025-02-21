<?php
namespace App\Tests\Entity;

use App\Entity\Bestiary;
use App\Entity\Creature;
use PHPUnit\Framework\TestCase;
use Lcobucci\JWT\Validation\Validator;
use RecursiveDirectoryIterator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validation;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CreatureTest extends WebTestCase
{
    private function getEntity(): Creature
    {
        return (new Creature())->setName("Allo")->setLifepoint(1000)->setMaxLifepoint(1200);
    }

    private function getBestiary(): Bestiary
    {
        return (new Bestiary())
            ->setName("Olla")
            ->setMaxLifePoint(255)
            ->setMinLifePoint(100);
    }
    public function testNameisValid()
    {


        $creature = $this->getEntity();
        $validator = Validation::createValidatorBuilder()->enableAttributeMapping()->getValidator();
        $errors = $validator->validate($creature);

        $this->assertCount(0, $errors);
        $creature->setName("");
        $errors = $validator->validate($creature);
        $this->assertCount(2, $errors);

        $this->assertEquals("This value should not be blank.", $errors[0]->getMessage());
        $this->assertEquals("Your Chimpoko name must be at least 3 characters long", $errors[1]->getMessage());
    }
    public function testChimpokoGeneration()
    {

        $creature = $this->getEntity();
        $creature->setBestiary($this->getBestiary());

        $this->assertGreaterThanOrEqual(($this->getBestiary())->getMinLifePoint(), $creature->getMaxLifePoint());
        $this->assertLessThanOrEqual(($this->getBestiary())->getMaxLifePoint(), $creature->getMaxLifePoint());
    }

    public function testChimpokoGenerationName()
    {

        $creature = $this->getEntity();
        $creature->setName(null);
        $creature->setBestiary($this->getBestiary());
        $this->assertEquals("Olla", $creature->getName());
        $creature->setName("Toto");
        $creature->setBestiary($this->getBestiary());
        $this->assertEquals("Toto", $creature->getName());

    }

    public function testEndtoEnd()
    {
        $client = static::createClient();
        // $req = new Request(["/"]);
        // $html = $client->doRequest($req);
        $html = $client->request("GET", "/api/login_check");
        $response = $client->getResponse();
        $this->assertSame(JsonResponse::HTTP_NOT_FOUND, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);


        $this->assertSame(JsonResponse::HTTP_NOT_FOUND, $data["status"]);
        $this->assertSame('Unable to find the controller for path "/api/login_check". The route is wrongly configured.', $data["message"]);
        $
        // var_dump($response);
        // foreach ($html as $domElement) {
        //     var_dump($domElement->nodeName);
        // }
        // die();
    }
}
