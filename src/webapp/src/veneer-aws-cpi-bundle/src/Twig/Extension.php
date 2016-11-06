<?php

namespace Veneer\AwsCpiBundle\Twig;

use Veneer\AwsCpiBundle\Service\ConsoleHelper;

class Extension extends \Twig_Extension
{
    protected $consoleHelper;

    public function __construct(ConsoleHelper $consoleHelper)
    {
        $this->consoleHelper = $consoleHelper;
    }

    public function getFilters()
    {
        return array(
            'aws_subnet_gateway' => new \Twig_Filter_Method($this, 'filterSubnetGateway'),
        );
    }

    public function getFunctions()
    {
        return [
            'aws_console_ec2_instance' => new \Twig_Function_Function([ $this->consoleHelper, 'getEc2InstanceSearch' ]),
            'aws_console_ec2_volume' => new \Twig_Function_Function([ $this->consoleHelper, 'getEc2VolumeSearch' ]),
            'aws_console_ec2_nic' => new \Twig_Function_Function([ $this->consoleHelper, 'getEc2NicSearch' ]),
            'aws_console_ec2_securitygroup' => new \Twig_Function_Function([ $this->consoleHelper, 'getEc2SecurityGroupSearch' ]),
            'aws_console_vpc_subnet' => new \Twig_Function_Function([ $this->consoleHelper, 'getVpcSubnetSearch' ]),
        ];
    }

    public function filterSubnetGateway($cidr)
    {
        list($network, $mask) = explode('/', $cidr, 2);

        // @todo invalid assumptions here...
        return preg_replace('/(\d+)\.(\d+)\.(\d+)\.(\d+)/', '$1.$2.$3.1', $network);
    }

    public function getName()
    {
        return 'veneer_aws_cpi';
    }
}
