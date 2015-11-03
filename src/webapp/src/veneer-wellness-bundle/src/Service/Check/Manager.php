<?php

namespace Veneer\WellnessBundle\Service\Check;

use Doctrine\ORM\EntityManager;
use Veneer\CoreBundle\Service\Workspace\GitRepository;
use Veneer\WellnessBundle\Entity\CheckState;
use Veneer\WellnessBundle\Entity\CheckStateContext;

class Manager
{
    protected $repository;
    protected $em;
    protected $sourceFactory;
    protected $triggerFactory;

    public function __construct(
        GitRepository $repository,
        EntityManager $em,
        Source\SourceFactory $sourceFactory,
        Trigger\TriggerFactory $triggerFactory
    ) {
        $this->repository = $repository;
        $this->em = $em;
        $this->sourceFactory = $sourceFactory;
        $this->triggerFactory = $triggerFactory;
    }

    public function trigger($workspacePath, $dispatch = true)
    {
        $config = json_decode($this->repository->showFile($workspacePath), true);

        $sourceName = key($config['source']);
        $sourceConfig = $this->sourceFactory->compileConfig($sourceName, $config['source'][$sourceName]);

        $check = new Check();
        $check->setContextValue('source_path', $workspacePath);
        $check->setSourceConfig($sourceConfig);

        foreach ($this->sourceFactory->get($sourceName)->load($check) as $check) {
            // @todo conditional

            // @todo sync up

            $context = $check['context'];
            ksort($context);
            $contextRef = json_encode($context);

            $this->em->beginTransaction();

            $checkState = $this->em->getRepository('VeneerWellnessBundle:CheckState')->findOneBy([
                'workspacePath' => $workspacePath,
                'contextRef' => $contextRef,
            ]);

            if (!$checkState) {
                $checkState = new CheckState();
                $checkState->setWorkspacePath($workspacePath);
                $checkState->setContextRef($contextRef);

                foreach ($context as $contextKey => $contextValue) {
                    $checkStateContext = new CheckStateContext();
                    $checkStateContext->setWorkspacePath($workspacePath);
                    $checkStateContext->setContextRef($contextRef);
                    $checkStateContext->setContextKey($contextKey);
                    $checkStateContext->setContextValue($contextValue);

                    $this->em->persist($checkStateContext);
                }
            }

            // @todo act on the event

            if ($dispatch) {
                $this->dispatcher->dispatch();
            }
        }
    }
}
