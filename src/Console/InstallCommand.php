<?php
/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
declare(strict_types=1);

namespace Framework\Console;

use Framework\Database\Entity\Role\Resource;
use Framework\Database\Entity\Role\Role;
use Framework\Database\EntityManager;
use Framework\Security\Permissions\DefaultAccess;
use Framework\Security\Permissions\DefaultResource;
use Framework\Security\Permissions\DefaultRole;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:install:permissions', description: 'Create default roles an permissions in database', aliases: ['install:permissions'])]
class InstallCommand extends Command
{
    public function __construct(private readonly EntityManager $em)
    {
        parent::__construct();
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $roleRepo = $this->em->getRoleRepo();
        $resourceRepo = $this->em->getRoleResourceRepo();
        
        /** @var Role[] $roles */
        $roles = $roleRepo->findAll();
        
        /** @var Resource[] $resources */
        $resources = $resourceRepo->findAll();
        
        /** @var array<int, string> $roleNames */
        $roleNames = array_map(fn(Role $role) => $role->getName(), $roles);
        
        /** @var array<int, string> $resourceNames */
        $resourceNames = array_map(fn(Resource $resource) => $resource->getName(), $resources);
        
        // Create default roles
        foreach (DefaultRole::cases() as $role) {
            if (!in_array($role->name(), $roleNames)) {
                $row = (new Role())->setPrimary(true)->setName($role->name());
                $this->em->persist($row);
                $roles[] = $row;
            }
        }
        
        // Create default permissions
        foreach (DefaultResource::cases() as $resource) {
            if (!in_array($resource->name(), $resourceNames)) {
                $row = (new Resource())->setName($resource->name());
                $this->em->persist($row);
                $resources[] = $row;
            }
        }
        
        // Create default roles & resources by Access table
        foreach (DefaultAccess::accesses() as $roleName => $defaultResources) {
            /** @var Role $role */
            $role = current(array_filter($roles, fn(Role $role) => $role->getName() === $roleName));
            foreach ($defaultResources as $resourceName) {
                /** @var Resource $resource */
                $resource = current(array_filter($resources, fn(Resource $resource) => $resource->getName() === $resourceName));
                $role->addResource($resource);
                $this->em->persist($role);
            }
        }
        
        $this->em->flush();
        
        $output->writeln('<info>Installation successfully completed.</info>');
        return Command::SUCCESS;
    }
}