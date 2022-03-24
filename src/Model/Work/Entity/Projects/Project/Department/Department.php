<?php

declare(strict_types=1);

namespace App\Model\Work\Entity\Projects\Project\Department;

use App\Model\Work\Entity\Projects\Project\Project;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'work_projects_project_departments')]
class Department
{
    /** @var Project */
    #[ORM\ManyToOne(targetEntity: 'App\Model\Work\Entity\Projects\Project\Project', inversedBy: 'departments')]
    #[ORM\JoinColumn(name: 'project_id', referencedColumnName: 'id', nullable: false)]
    private $project;

    /** @var Id */
    #[ORM\Column(type: 'work_projects_project_department_id')]
    #[ORM\Id]
    private $id;

    /** @var string */
    #[ORM\Column(type: 'string')]
    private $name;

    /**
     * @param Project $project
     * @param Id $id
     * @param string $name
     */
    public function __construct(Project $project, Id $id, string $name)
    {
        $this->project = $project;
        $this->id = $id;
        $this->name = $name;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function isNameEqual(string $name): bool
    {
        return $this->name === $name;
    }

    /**
     * @return Id
     */
    public function getId(): Id
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return void
     */
    public function edit(string $name): void
    {
        $this->name = $name;
    }
}