<?php

namespace App\ApiPlatform;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\AbstractFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\QueryBuilder;

class RadiusLocationSearchFilter extends AbstractFilter
{
    protected function filterProperty(string $property, $value, QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null)
    {
        if ($property !== 'radius') {
            return;
        }

        $latitude = $_GET['latitude'];
        $longitude = $_GET['longitude'];

        // como por defecto pasa por cada propiedad, solo lo haremos una vez
        if ($property === 'radius') {
            $alias = $queryBuilder->getRootAliases()[0];
            $queryBuilder
                ->select('o')
                ->where('(((acos(sin(( :latitude * pi() / 180))*sin(( o.latitude * pi() / 180)) + cos(( :latitude * pi() /180 ))*cos(( o.latitude * pi() / 180)) * cos((( :longitude - o.longitude) * pi()/180)))) * 180/pi()) * 60 * 1.1515 * 1.609344) <= :distance')
                ->setParameter('distance', $value)
                ->setParameter('longitude', $longitude)
                ->setParameter('latitude', $latitude);
        } else return;
    }
    public function getDescription(string $resourceClass): array
    {

        // Parametros que espera recibir, ponemos todos para testear en la documentación, pero en realidad solo procesaremos uno, ya que se cogerá la úbicación real del usuario
        return [
            'radius' => [
                'property' => null,
                'type' => 'float',
                'required' => false
            ],
            'longitude' => [
                'property' => null,
                'type' => 'string',
                'required' => false
            ],
            'latitude' => [
                'property' => null,
                'type' => 'string',
                'required' => false
            ],
        ];
    }
}
