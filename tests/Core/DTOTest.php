<?php

namespace Core;

use InvalidArgumentException;
use VerifyMyContent\SDK\Core\DTO;
use PHPUnit\Framework\TestCase;

/***
 * @property int $id
 * @property-read string $name
 * @property-read int $age
 * @property-read sampleDto $child
 */
class sampleDto extends DTO
{
    protected $fillable = ['name', 'age', 'child'];

    protected $required = ['id'];

    protected $casts = [
        'child' => sampleDto::class
    ];
}

class DTOTest extends TestCase
{
    public function testDtoFill(){
        $child = new sampleDto([
            'id' => 2,
            'name' => "John's child",
            'age' => 5
        ]);

        $dto = new sampleDto(['id' => 1, 'name' => 'John', 'age' => 20, 'child' => $child]);

        $this->assertEquals(1, $dto->id);
        $this->assertEquals('John', $dto->name);
        $this->assertEquals(20, $dto->age);
        $this->assertEquals($dto->getAttributes(), [
            'id' => 1,
            'name' => 'John',
            'age' => 20,
            'child' => $child
        ]);

        $this->assertEquals($dto->child->getAttributes(), [
            'id' => 2,
            'name' => "John's child",
            'age' => 5
        ]);

        $this->assertEquals($dto->toArray(), [
            'id' => 1,
            'name' => 'John',
            'age' => 20,
            'child' => [
                'id' => 2,
                'name' => "John's child",
                'age' => 5
            ]
        ]);

        $this->assertEquals($dto->child->toArray(), [
            'id' => 2,
            'name' => "John's child",
            'age' => 5
        ]);
    }

    public function testDtoShouldThrowExceptionWhenRequiredFieldIsMissing(){
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing required field: id');

        new sampleDto(['name' => 'John', 'age' => 20]);
    }

    public function testDtoShouldThrowExceptionWhenInvalidPropertyIsAccessed(){
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid property: invalid');

        $dto = new sampleDto(['id' => 1, 'name' => 'John', 'age' => 20]);
        $dto->invalid;
    }

    public function testDtoShouldThrowExceptionWhenAnyPropertyIsSet(){
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Cannot set property: id');

        $dto = new sampleDto(['id' => 1, 'name' => 'John', 'age' => 20]);
        $dto->id = 2;
    }

    public function testDtoShouldThrowExceptionWhenInvalidCastIsUsed(){
        $this->expectException(InvalidArgumentException::class);

        $dto = new sampleDto(['id' => 1, 'name' => 'John', 'age' => 20, 'child' => 'invalid']);
    }
}