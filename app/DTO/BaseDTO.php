<?php

namespace App\DTO;

use Illuminate\Support\Arr;
use ReflectionClass;
use ReflectionProperty;

abstract class BaseDTO
{
    /**
     * Cria uma nova instância do DTO a partir dos dados fornecidos em um array.
     * @param array $data Os dados para preencher o DTO.
     */
    public function __construct(array $data = [])
    {
        $this->fill($data);
    }

    /**
     * Preenche o DTO com os dados fornecidos em um array.
     *
     * @param array $data Os dados para preencher o DTO.
     */
    public function fill(array $data)
    {
        // Obtém todas as propriedades públicas do DTO atual
        $properties = (new ReflectionClass($this))->getProperties(ReflectionProperty::IS_PUBLIC);

        // Itera sobre todas as propriedades públicas do DTO atual
        foreach ($properties as $property) {
            $propertyName = $property->getName();

            // Verifica se a propriedade tem a anotação @var com "[]" indicando que é um array de objetos
            $isArray = strpos($property->getDocComment(), '@var ') !== false && strpos($property->getDocComment(), '[]') !== false;

            if ($isArray) {
                // Extrai o tipo da propriedade do texto da anotação
                $type = str_replace(['@var', '[]', '/**', '*/'], '', $property->getDocComment());
                $type = trim($type);

                if (class_exists($type)) {
                    // Obtém os valores do array de objetos, se existirem, ou um array vazio
                    $values = $data[snakelize($propertyName)] ?? $data[camelize($propertyName)] ?? [];

                    // Cria um array para armazenar os objetos DTO preenchidos com os dados de $values
                    $dtos = [];

                    foreach ($values as $value) {
                        $dto = new $type();

                        if (is_array($value)) {
                            $dto->fill($value);
                        } elseif (str($value)->isUuid()) {
                            if ($dto->fill(["id" => $value]));
                        }

                        $dtos[] = $dto;
                    }

                    $this->{$propertyName} = $dtos;
                }
            } else {
                // Se a propriedade não é um array de objetos, preenche-a diretamente com o valor correspondente do array $data
                $value = $data[snakelize($propertyName)] ?? $data[camelize($propertyName)] ?? null;
                $this->{$propertyName} = $value;
            }
        }
    }

    /**
     * Retorna um array associativo contendo as propriedades públicas do DTO e seus valores.
     *
     * @param array|string|null $except Um ou mais nomes de propriedades que devem ser excluídas do array retornado.
     * @param array|string|null $only Um ou mais nomes de propriedades que devem ser extraídas e incluídas no array retornado.
     * @return array
     */
    public function toArray(array|string $except = null, array|string $only = null): array
    {
        // Obtém a classe do DTO atual
        $reflection = new ReflectionClass($this);
        // Obtém todas as propriedades públicas do DTO atual
        $properties = $reflection->getProperties(ReflectionProperty::IS_PUBLIC);

        // Transforma os valores de exceção e apenas propriedades em arrays se não forem arrays
        $exceptProperties = (array) $except;
        $onlyProperties = (array) $only;

        // Itera sobre todas as propriedades públicas do DTO atual
        $result = [];
        foreach ($properties as $property) {
            $name = $property->getName();
            $padronizedName = snakelize($name);

            if (in_array($name, $exceptProperties)) continue;

            if (!empty($onlyProperties) && !in_array($name, $onlyProperties)) continue;

            // Verifica se a propriedade tem a anotação @var com "[]" indicando que é um array de objetos
            $isArrayProperty = strpos($property->getDocComment(), '@var ') !== false && strpos($property->getDocComment(), '[]') !== false;

            if ($isArrayProperty) {
                $result[$padronizedName] = $this->extractArrayProperty($property, $exceptProperties);
            } else {
                $result[$padronizedName] = $this->{$name};
            }
        }

        // Retorna o resultado filtrado
        if (count($onlyProperties) === 1) {
            $result = $result[$padronizedName];
            unset($result[$padronizedName]);
        }

        return $result;
    }

    /**
     * Extrai e retorna o valor de uma propriedade que é um array de objetos.
     *
     * @param ReflectionProperty $property Propriedade a ser extraída.
     * @param array $exceptProperties Um ou mais nomes de propriedades que devem ser excluídas do array retornado.
     * @return array
     */
    private function extractArrayProperty(ReflectionProperty $property, array $exceptProperties): array
    {
        // Extrai o tipo da propriedade do texto da anotação
        $type = str_replace(['@var', '[]', '/**', '*/'], '', $property->getDocComment());
        $type = trim($type);
        // Verifica se o tipo existe como classe
        if (!class_exists($type)) {
            return [];
        }

        // Obtém os objetos do array de objetos
        $dtos = $this->{$property->getName()} ?? [];

        return array_map(function ($dto) use ($exceptProperties) {
            return $dto instanceof BaseDTO ? $dto->toArray($exceptProperties) : $dto;
        }, $dtos);
    }


    /**
     * Retorna um array com os valores da propriedade especificada em todas as instâncias de DTO contidas no array.
     *
     * @param string $property A propriedade que se deseja extrair dos DTOs.
     * @return array Os valores da propriedade especificada em todas as instâncias de DTO contidas no array.
     */
    public function pluck(string $property): array
    {
        $data = $this; // $data recebe a instância do DTO
        $properties = explode('.', $property); // divide a string de propriedades em um array

        foreach ($properties as $property) { // itera sobre o array de propriedades
            if (is_array($data)) { // verifica se a variável $data é um array
                $values = $this->pluckArrayProperty($data, $property);
                return $values;
            }
            if (isset($data->$property)) { // verifica se a propriedade existe na instância do DTO
                $data = $data->$property; // atualiza a variável $data com a propriedade correspondente
            } else {
                return []; // retorna um array vazio se a propriedade não existe
            }
        }

        return [$data]; // retorna um array com o valor da propriedade caso ela seja a última do array de propriedades
    }

    /**
     * Retorna um array com os valores da propriedade especificada em todas as instâncias de DTO contidas no array.
     *
     * @param array $data Um array de objetos DTO.
     * @param string $property A propriedade que se deseja extrair dos DTOs.
     * @return array Os valores da propriedade especificada em todas as instâncias de DTO contidas no array.
     */
    private function pluckArrayProperty(array $data, string $property): array
    {
        $values = []; // cria um array para armazenar os valores
        foreach ($data as $item) { // itera sobre cada item do array
            if ($item instanceof BaseDTO) { // verifica se o item é uma instância de BaseDTO
                $value = $item->$property; // armazena o valor da propriedade na variável $value
                if ($value !== null) { // verifica se o valor não é nulo
                    $values[] = $value; // adiciona o valor ao array de valores
                }
            }
        }
        return $values; // retorna o array de valores
    }
}
