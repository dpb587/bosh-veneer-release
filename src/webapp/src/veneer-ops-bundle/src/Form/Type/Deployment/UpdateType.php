<?php

namespace Veneer\OpsBundle\Form\Type\Deployment;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints;
use SYmfony\Component\OptionsResolver\OptionsResolverInterface;
use Veneer\OpsBundle\Form\Type\AbstractDeploymentManifestPathType;

class UpdateType extends AbstractDeploymentManifestPathType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'canaries',
                'integer',
                [
                    'label' => 'Canaries',
                    'veneer_help_html' => '<p>The number of canary instances.</p>',
                ]
            )
            ->add(
                'max_in_flight',
                'integer',
                [
                    'label' => 'Max In-Flight',
                    'veneer_help_html' => '<p>The maximum number of non-canary instances to update in parallel.</p>',
                ]
            )
            ->add(
                'canary_watch_time',
                'text',
                [
                    'label' => 'Canary Watch Time',
                    'veneer_help_html' => '<p>If an integer, the Director sleeps for that many milliseconds, then checks whether the canary instances are healthy. If a range (low-high), the Director: waits for low milliseconds, then waits until instances are healthy or high milliseconds have passed since instances started updating.</p>',
                ]
            )
            ->add(
                'update_watch_time',
                'text',
                [
                    'label' => 'Canary Watch Time',
                    'veneer_help_html' => '<p>If an integer, the Director sleeps for that many milliseconds, then checks whether the instances are healthy. If a range (low-high), the Director: waits for low milliseconds, then waits until instances are healthy or high milliseconds have passed since instances started updating.</p>',
                ]
            )
            ->add(
                'serial',
                'checkbox',
                [
                    'label' => 'Serial Job Deployments',
                    'veneer_help_html' => '<p>If disabled, deployment jobs will be deployed in parallel. Instances within a deployment job will still follow Canaries and Max In-Flight configuration.</p>',
                ]
            )
            ;
    }

    public function getName()
    {
        return 'veneer_ops_deployment_update';
    }
}
