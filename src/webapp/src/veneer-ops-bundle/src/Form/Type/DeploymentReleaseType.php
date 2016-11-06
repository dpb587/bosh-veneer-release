<?php

namespace Veneer\OpsBundle\Form\Type;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Veneer\OpsBundle\Form\DataTransformer\DeploymentReleaseTransformer;
use Doctrine\ORM\EntityManager;

class DeploymentReleaseType extends AbstractDeploymentManifestPathType
{
    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $installed = $this->em
            ->getRepository('VeneerBoshBundle:ReleaseVersions')
            ->createQueryBuilder('rv')
            ->distinct()
            ->select('rv.version')
            ->addSelect('r.name')
            ->join('rv.release', 'r')
            ->addOrderBy('r.name', 'ASC')
            ->addOrderBy('rv.version', 'ASC')
            ->getQuery()
            ->getArrayResult();

        $mapped = [];
        $choices = [];

        foreach ($installed as $choice) {
            $mapped[$choice['name']][$choice['version']] = $choice;
            $choices[$choice['name'].'/'.$choice['version']] = $choice['version'];
        }

        $builder
            ->add(
                'picker',
                'choice',
                [
                    'choices' => $choices,
                    'expanded' => true,
                ]
            )
            ->setAttribute('mapped_releases', $mapped)
            ->addModelTransformer(new DeploymentReleaseTransformer())
            ;
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['mapped_releases'] = $form->getConfig()->getAttribute('mapped_releases');
        $view->vars['model_data'] = $form->getData();
    }

    public function setDefaultOptions(OptionsResolverInterface $options)
    {
        parent::setDefaultOptions($options);

        $options->setDefaults([
            'label' => 'Release',
        ]);
    }

    public function getName()
    {
        return 'veneer_ops_deployment_release';
    }
}
