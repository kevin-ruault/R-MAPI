<?php

namespace App\Controller\Api;

use App\Entity\Character;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/character')]
final class CharacterController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    #[Route('', name: 'api_character_list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $page = max(1, (int) $request->query->get('page', 1));
        $name = trim((string) $request->query->get('name', ''));

        $qb = $this->em->getRepository(Character::class)->createQueryBuilder('c');

        if ($name !== '') {
            $qb->andWhere('LOWER(c.name) LIKE :name')
               ->setParameter('name', '%'.mb_strtolower($name).'%');
        }

        // count total
        $countQb = clone $qb;
        $total = (int) $countQb->select('COUNT(c.id)')->getQuery()->getSingleScalarResult();

        $perPage = 20;
        $pages = $total === 0 ? 0 : (int) ceil($total / $perPage);

        // Si page demandée hors bornes (et qu'il y a des résultats), on renvoie 404 comme la vraie API
        if ($pages > 0 && $page > $pages) {
            return $this->json(['error' => 'There is nothing here'], 404);
        }

        $results = $qb
            ->orderBy('c.id', 'ASC')
            ->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage)
            ->getQuery()
            ->getResult();

        $base = $request->getSchemeAndHttpHost(); // ex http://127.0.0.1:8000
        $path = '/api/character';
        $queryBase = [];
        if ($name !== '') {
            $queryBase['name'] = $name;
        }

        $makeUrl = function (?int $p) use ($base, $path, $queryBase): ?string {
            if ($p === null) return null;
            $q = array_merge($queryBase, ['page' => $p]);
            return $base.$path.'?'.http_build_query($q);
        };

        $next = ($pages > 0 && $page < $pages) ? $makeUrl($page + 1) : null;
        $prev = ($pages > 0 && $page > 1) ? $makeUrl($page - 1) : null;

        return $this->json([
            'info' => [
                'count' => $total,
                'pages' => $pages,
                'next' => $next,
                'prev' => $prev,
            ],
            'results' => array_map([$this, 'serializeCharacter'], $results),
        ]);
    }

    #[Route('/{id}', name: 'api_character_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $character = $this->em->getRepository(Character::class)->find($id);

        if (!$character) {
            return $this->json(['error' => 'Character not found'], 404);
        }

        return $this->json($this->serializeCharacter($character));
    }

    private function serializeCharacter(Character $c): array
    {
        return [
            'id' => $c->getId(),
            'name' => $c->getName(),
            'status' => $c->getStatus(),
            'species' => $c->getSpecies(),
            'gender' => $c->getGender(),
            'image' => $c->getImage(),
        ];
    }
}
