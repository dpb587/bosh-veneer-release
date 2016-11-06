<?php

namespace Veneer\BoshEditorBundle\Form\Type;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Veneer\BoshEditorBundle\Form\DataTransformer\DeploymentStemcellTransformer;
use Doctrine\ORM\EntityManager;

class DeploymentResourcePoolStemcellType extends AbstractDeploymentManifestPathType
{
    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $installed = $this->em
            ->getRepository('VeneerBoshBundle:Stemcells')
            ->createQueryBuilder('s')
            ->distinct()
            ->select('s.name')
            ->addSelect('s.version')
            ->addOrderBy('s.name', 'ASC')
            ->addOrderBy('s.version', 'ASC')
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
            ->setAttribute('mapped_stemcells', $mapped)
            ->addModelTransformer(new DeploymentStemcellTransformer())
            ;
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['mapped_stemcells'] = $form->getConfig()->getAttribute('mapped_stemcells');
        $view->vars['model_data'] = $form->getData();
    }

    public function setDefaultOptions(OptionsResolverInterface $options)
    {
        parent::setDefaultOptions($options);

        $options->setDefaults([
            'label' => 'Stemcell',
        ]);
    }

    public function getName()
    {
        return 'veneer_bosh_editor_deployment_resourcepool_stemcell';
    }
}
