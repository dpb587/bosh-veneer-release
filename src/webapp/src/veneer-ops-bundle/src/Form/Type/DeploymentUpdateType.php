<?php

namespace Veneer\OpsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints;
use SYmfony\Component\OptionsResolver\OptionsResolverInterface;

class DeploymentUpdateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'canaries',
                'integer',
                [
                    'label' => 'Canaries',
                    'helptext' => 'The number of canary instances.',
                ]
            )
            ->add(
                'max_in_flight',
                'integer',
                [
                    'label' => 'Max In-Flight',
                    'helptext' => 'The maximum number of non-canary instances to update in parallel.',
                ]
            )
            ->add(
                'canary_watch_time',
                'text',
                [
                    'label' => 'Canary Watch Time',
                    'helptext' => 'If an integer, the Director sleeps for that many milliseconds, then checks whether the canary instances are healthy. If a range (low-high), the Director: waits for low milliseconds, then waits until instances are healthy or high milliseconds have passed since instances started updating.',
                ]
            )
            ->add(
                'update_watch_time',
                'text',
                [
                    'label' => 'Canary Watch Time',
                    'helptext' => 'If an integer, the Director sleeps for that many milliseconds, then checks whether the instances are healthy. If a range (low-high), the Director: waits for low milliseconds, then waits until instances are healthy or high milliseconds have passed since instances started updating.',
                ]
            )
            ->add(
                'serial',
                'checkbox',
                [
                    'label' => 'Reuse Compilation VMs',
                    'helptext' => 'If enabled, deployment jobs will be deployed in parallel. Instances within a deployment job will still follow Canaries and Max In-Flight configuration.',
                ]
            )
            ;
    }

    public function getName()
    {
        return 'veneer_ops_deployment_update';
    }
}
