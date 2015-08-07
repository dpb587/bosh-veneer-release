<?php

namespace Bosh\CoreBundle\Service;

use Bosh\CoreBundle\Entity\Deployments;
use Bosh\CoreBundle\Entity\Vms;
use Doctrine\ORM\EntityManager;

class DatabaseHelper
{
    protected $em;
    
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }
    
    public function buildJobList(Deployments $deployment)
    {
        $results = [];
        
        foreach ($this->findDeploymentVms($deployment) as $vm) {
            $applySpec = $vm['applySpecJsonAsArray'];

            if (!isset($results[$applySpec['job']['name']])) {
                $results[$applySpec['job']['name']] = [
                    'name' => $applySpec['job']['name'],
                    'count' => 0,
                ];
            }
            
            $results[$applySpec['job']['name']]['count'] += 1;
        }
        
        return array_values($results);
    }
    
    public function findDeploymentVms(Deployments $deployment)
    {
        $vms = $this->em->getRepository('BoshCoreBundle:Vms')->findBy(
            [
                'deployment' => $deployment,
            ]
        );
    
        usort(
            $vms,
            function (Vms $a, Vms $b) {
                return strcmp(
                    $a['applySpecJsonAsArray']['job']['name'] . '/' . $a['applySpecJsonAsArray']['index'],
                    $b['applySpecJsonAsArray']['job']['name'] . '/' . $b['applySpecJsonAsArray']['index']
                );
            }
        );
        
        return $vms;
    }
    
    public function findVmByDeploymentJobIndex(Deployments $deployment, $jobName, $jobIndex)
    {
        foreach ($this->findDeploymentVms($deployment) as $vm) {
            if ($jobName != $vm['applySpecJsonAsArray']['job']['name']) {
              continue;
            } elseif ($jobIndex != $vm['applySpecJsonAsArray']['index']) {
              continue;
            }
            
            return $vm;
        }
    }
}