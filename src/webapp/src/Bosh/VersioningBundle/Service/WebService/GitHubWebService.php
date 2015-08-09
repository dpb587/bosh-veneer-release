<?php

namespace Bosh\VersioningBundle\Service\WebService;

class GitHubWebService implements WebServiceInterface
{
    protected $options;

    public function __construct($orgName, $repoName, array $options = [])
    {
        $this->orgName = $orgName;
        $this->repoName = $repoName;
        $this->options = array_merge(
            static::getDefaultOptions(),
            $options
        );
    }

    protected static function getDefaultOptions()
    {
        return [
            'https' => true,
            'hostname' => 'github.com',
            'default_branch' => 'master',
        ];
    }

    public function getRepositoryLink()
    {
        return $this->getUrl();
    }

    public function getPathLink($path, $tree = null)
    {
        return $this->getUrl(
            sprintf(
                'blob/%s/%s',
                (null === $tree) ? $this->options['default_branch'] : $tree,
                $path
            )
        );
    }

    public function getCommitLink($commit, $path = null)
    {
        return $this->getUrl(
            sprintf(
                'commit/%s%s',
                $commit,
                $path ? ('#diff-' . md5($path)) : ''
            )
        );
    }

    protected function getUrl($path = '')
    {
        return sprintf(
            '%s://%s/%s/%s/%s',
            $this->options['https'] ? 'https' : 'http',
            $this->options['hostname'],
            $this->orgName,
            $this->repoName,
            $path
        );
    }
}
