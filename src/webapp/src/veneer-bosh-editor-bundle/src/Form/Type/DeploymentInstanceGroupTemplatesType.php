<?php

namespace Veneer\BoshEditorBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\OptionsResolver\Options;
use Veneer\BoshEditorBundle\Form\DataTransformer\DeploymentInstanceGroupTemplateTransformer;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr;

class DeploymentInstanceGroupTemplatesType extends AbstractDeploymentManifestPathType
{
    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new DeploymentInstanceGroupTemplateTransformer());
    }

    public function setDefaultOptions(OptionsResolverInterface $options)
    {
        parent::setDefaultOptions($options);

        $em = $this->em;

        $options->setDefaults([
            'label' => 'Templates',
            'expanded' => true,
            'multiple' => true,
            'choices' => function (Options $options) use ($em) {
                $ors = new Expr\Orx();
                $params = [];

                foreach ($options['manifest']['releases'] as $i => $release) {
                    $params['r'.$i] = $release['name'];
                    $params['v'.$i] = $release['version'];
                    $ors->add(new Expr\Andx([
                        new Expr\Comparison('r.name', '=', ':r'.$i),
                        new Expr\Comparison('rv.version', '=', ':v'.$i),
                    ]));
                }

                $available = $em
                    ->getRepository('VeneerBoshBundle:ReleaseVersionsTemplates')
                    ->createQueryBuilder('rvt')
                    ->select('r.name AS release')
                    ->addSelect('t.name AS template')
                    ->join('rvt.releaseVersion', 'rv')
                    ->join('rv.release', 'r')
                    ->join('rvt.template', 't')
                    ->where($ors)
                    ->orderBy('r.name', 'ASC')
                    ->addOrderBy('t.name', 'ASC')
                    ->setParameters($params)
                    ->getQuery()
                    ->getArrayResult();

                $choices = [];

                foreach ($available as $choice) {
                    $choices[$choice['release'].'/'.$choice['template']] = $choice['release'].'/'.$choice['template'];
                }

                return $choices;
            },
        ]);
    }

    public function getParent()
    {
        return 'choice';
    }

    public function getName()
    {
        return 'veneer_bosh_editor_deployment_instancegroup_templates';
    }
}
