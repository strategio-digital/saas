<?php
/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
declare(strict_types=1);

namespace Saas\Http\Request\User;

use Doctrine\ORM\AbstractQuery;
use Saas\Database\EntityManager;
use Saas\Http\Request\IRequest;
use Saas\Http\Response\Response;
use Nette\Schema\Expect;
use Saas\Security\Permissions\DefaultRole;

class ShowOneRequest implements IRequest
{
    public function __construct(
        private readonly EntityManager $em,
        private readonly Response      $response,
    )
    {
    }
    
    public function schema(): array
    {
        return [
            'id' => Expect::string()->required(),
        ];
    }
    
    public function process(array $data): void
    {
        $repo = $this->em->getUserRepo();
        
        $qb = $repo->createQueryBuilder('U')
            ->select('U.id, R.name as role, U.email, U.createdAt, U.updatedAt, U.lastLogin')
            ->leftJoin('U.role', 'R')
            ->where('U.id = :id')
            ->andWhere('R.name != :admin_role')
            ->setParameter('admin_role', DefaultRole::Admin->name())
            ->setParameter('id', $data['id']);
        
        $res = $qb->getQuery()->getOneOrNullResult(AbstractQuery::HYDRATE_ARRAY);
        
        if (!$res) {
            $this->response->sendError(["User id '{$data['id']}' not found"], 404);
        }
        
        $this->response->send($res);
    }
}