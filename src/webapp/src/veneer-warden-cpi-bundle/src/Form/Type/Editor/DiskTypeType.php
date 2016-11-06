<?php

namespace Veneer\WardenCpiBundle\Form\Type\Editor;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints;
use SYmfony\Component\OptionsResolver\OptionsResolverInterface;

class DiskTypeType extends AbstractType
{
    public function getName()
    {
        return 'veneer_warden_cpi_editor_disktype';
    }
}
