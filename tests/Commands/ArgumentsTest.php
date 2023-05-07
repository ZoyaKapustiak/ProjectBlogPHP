<?php

namespace ZoiaProjects\ProjectBlog\Commands;

use ZoiaProjects\ProjectBlog\Blog\Commands\Arguments;
use PHPUnit\Framework\TestCase;
use ZoiaProjects\ProjectBlog\Blog\Exceptions\ArgumentsException;

/**
 *
 */
class  ArgumentsTest extends TestCase
{
  public function testItReturnsArgumentsValueByName(): void
  {
  // Подготовка
  $arguments = new Arguments(['some_key' => 'some_value']);
  // Действие
  $value = $arguments->get('some_key');
  // Проверка
      // assertEquals - проверяет равенство значений, но не типов
  $this->assertEquals('some_value', $value);
  }
  public function testItReturnsValuesAsString(): void
  {
      $arguments = new Arguments(['some_key' => 123]);

      $value = $arguments->get('some_key');
      //assertSame - проверяет значения и тип
      $this->assertSame('123', $value);
//      assertIsString проверяет является ли значение строкой
//      $this->assertIsString($value);
  }
  public function testItThrowsAnExceptionWhenArgumentIsAbsent(): void
  {
      $arguments = new Arguments([]);
      $this->expectException(ArgumentsException::class);
      $this->expectExceptionMessage("No such argument: some_key");
      $arguments->get('some_key');
  }

    /**
     * @return iterable
     */
    public function argumentsProvider(): iterable
  {
      return [
          ['some_string', 'some_string'],
          ['some_string', 'some_string'],
          ['some_string', 'some_string'],
          [123, '123'],
          [12.3, '12.3'],
      ];
  }

    /**
     * @dataProvider argumentsProvider
     */
    public function testItConvertsArgumentsToString($inputValue, $expectedValue): void
  {
      $arguments = new Arguments(['some_key' => $inputValue]);
      $value = $arguments->get('some_key');
      $this->assertEquals($expectedValue, $value);
  }
}