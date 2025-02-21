<?php
namespace App\Tests\Entity;

use App\Entity\Creature;
use PHPUnit\Framework\TestCase;
use Lcobucci\JWT\Validation\Validator;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CreatureTest extends TestCase
{
    private function getEntity(): Creature
    {
        return (new Creature())->setName("Allo")->setLifepoint(10)->setMaxLifepoint(12);
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
    }


}
